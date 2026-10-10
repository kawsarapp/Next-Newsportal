<?php

declare(strict_types=1);

namespace Modules\NewsQuiz\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function index(Request $request): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $quizzes = Quiz::where('portal_id', $portalId)
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(15);

        $totalQuizzes = Quiz::where('portal_id', $portalId)->count();
        $publishedQuizzes = Quiz::where('portal_id', $portalId)->where('status', 'published')->count();
        $totalParticipants = QuizAttempt::whereHas('quiz', fn($q) => $q->where('portal_id', $portalId))->count();
        $avgScore = QuizAttempt::whereHas('quiz', fn($q) => $q->where('portal_id', $portalId))->avg('score') ?? 0;

        return Inertia::render('Dashboard/Modules/NewsQuiz/Index', [
            'quizzes' => $quizzes,
            'stats' => [
                'total_quizzes'      => $totalQuizzes,
                'published_quizzes'  => $publishedQuizzes,
                'total_participants' => $totalParticipants,
                'avg_score'          => round((float) $avgScore, 1),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'title'               => 'required|string|max:200',
            'description'         => 'nullable|string|max:1000',
            'time_limit_minutes'  => 'required|integer|min:1|max:120',
            'passing_score'       => 'required|integer|min:1|max:100',
            'status'              => 'required|in:draft,published,archived',
            'questions'           => 'required|array|min:1',
            'questions.*.question'=> 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.explanation' => 'nullable|string',
        ]);

        $quiz = Quiz::create([
            'portal_id'          => $portalId,
            'title'              => $validated['title'],
            'slug'               => Str::slug($validated['title']) . '-' . Str::random(4),
            'description'        => $validated['description'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'passing_score'      => $validated['passing_score'],
            'status'             => $validated['status'],
        ]);

        foreach ($validated['questions'] as $index => $qData) {
            QuizQuestion::create([
                'quiz_id'     => $quiz->id,
                'question'    => $qData['question'],
                'options'     => $qData['options'],
                'explanation' => $qData['explanation'] ?? null,
                'order'       => $index,
            ]);
        }

        return back()->with('success', 'কুইজ ও প্রশ্নাবলী সফলভাবে তৈরি করা হয়েছে!');
    }

    public function updateStatus(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        $quiz->update(['status' => $validated['status']]);

        return back()->with('success', 'কুইজ স্ট্যাটাস আপডেট হয়েছে!');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();
        return back()->with('success', 'কুইজটি মুছে ফেলা হয়েছে।');
    }

    // Public API for taking quizzes
    public function getPublicQuizzes(): JsonResponse
    {
        $quizzes = Quiz::where('status', 'published')
            ->withCount('questions')
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $quizzes,
        ]);
    }

    public function getQuizDetail(string $slug): JsonResponse
    {
        $quiz = Quiz::where('slug', $slug)
            ->where('status', 'published')
            ->with(['questions' => function ($q) {
                $q->select('id', 'quiz_id', 'question', 'options', 'order');
            }])
            ->firstOrFail();

        // Sanitize options to avoid giving away correct answers on the client before submit
        $quizData = $quiz->toArray();
        foreach ($quizData['questions'] as &$question) {
            $cleanedOptions = [];
            foreach ($question['options'] as $idx => $opt) {
                $cleanedOptions[] = [
                    'index' => $idx,
                    'text'  => is_array($opt) ? ($opt['text'] ?? '') : $opt,
                ];
            }
            $question['options'] = $cleanedOptions;
        }

        return response()->json([
            'success' => true,
            'quiz'    => $quizData,
        ]);
    }

    public function submitQuiz(Request $request, Quiz $quiz): JsonResponse
    {
        $answers = $request->input('answers', []); // map of question_id => selected_option_index
        $questions = $quiz->questions()->get();

        $totalQuestions = $questions->count();
        $correctCount = 0;
        $results = [];

        foreach ($questions as $q) {
            $userAns = $answers[$q->id] ?? null;
            $correctOptionIndex = null;
            foreach ($q->options as $idx => $opt) {
                if (!empty($opt['is_correct'])) {
                    $correctOptionIndex = $idx;
                    break;
                }
            }

            $isCorrect = ($userAns !== null && (int)$userAns === (int)$correctOptionIndex);
            if ($isCorrect) {
                $correctCount++;
            }

            $results[] = [
                'question_id'    => $q->id,
                'is_correct'     => $isCorrect,
                'correct_index'  => $correctOptionIndex,
                'explanation'    => $q->explanation,
            ];
        }

        $scorePercent = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;
        $passed = $scorePercent >= $quiz->passing_score;

        QuizAttempt::create([
            'quiz_id'           => $quiz->id,
            'participant_name'  => $request->input('name', 'Anonymous Reader'),
            'participant_email' => $request->input('email'),
            'score'             => $scorePercent,
            'total_questions'   => $totalQuestions,
            'passed'            => $passed,
            'ip_address'        => $request->ip(),
        ]);

        $quiz->increment('total_attempts');

        return response()->json([
            'success'        => true,
            'score'          => $scorePercent,
            'correct_count'  => $correctCount,
            'total_questions'=> $totalQuestions,
            'passed'         => $passed,
            'details'        => $results,
        ]);
    }
}
