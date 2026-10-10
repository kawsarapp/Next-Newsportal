<?php

declare(strict_types=1);

namespace Modules\JobsAndObituary\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JobCircular;
use App\Models\Obituary;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class JobsAndObituaryController extends Controller
{
    public function index(Request $request): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;
        $tab = $request->query('tab', 'jobs'); // jobs or obituaries

        $jobs = JobCircular::where('portal_id', $portalId)
            ->latest()
            ->paginate(15, ['*'], 'jobs_page');

        $obituaries = Obituary::where('portal_id', $portalId)
            ->latest()
            ->paginate(15, ['*'], 'obituaries_page');

        return Inertia::render('Dashboard/Modules/JobsAndObituary/Index', [
            'tab'        => $tab,
            'jobs'       => $jobs,
            'obituaries' => $obituaries,
            'stats'      => [
                'total_jobs'        => JobCircular::where('portal_id', $portalId)->count(),
                'active_jobs'       => JobCircular::where('portal_id', $portalId)->where('status', 'published')->count(),
                'total_obituaries'  => Obituary::where('portal_id', $portalId)->count(),
            ],
        ]);
    }

    public function storeJob(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'title'           => 'required|string|max:200',
            'organization'    => 'required|string|max:200',
            'job_type'        => 'required|in:govt,private,bank,ngo,education',
            'vacancy_count'   => 'nullable|string|max:50',
            'salary_range'    => 'nullable|string|max:100',
            'location'        => 'nullable|string|max:100',
            'deadline'        => 'nullable|date',
            'application_url' => 'nullable|url|max:500',
            'description'     => 'nullable|string',
            'status'          => 'required|in:published,draft,expired',
        ]);

        JobCircular::create(array_merge($validated, ['portal_id' => $portalId]));

        return back()->with('success', 'চাকরির বিজ্ঞপ্তি সফলভাবে তৈরি করা হয়েছে!');
    }

    public function destroyJob(JobCircular $job): RedirectResponse
    {
        $job->delete();
        return back()->with('success', 'বিজ্ঞপ্তিটি মুছে ফেলা হয়েছে।');
    }

    public function storeObituary(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'deceased_name'     => 'required|string|max:200',
            'age'               => 'nullable|integer|min:0|max:150',
            'death_date'        => 'nullable|date',
            'passed_away_place' => 'nullable|string|max:200',
            'bio'               => 'nullable|string',
            'funeral_details'   => 'nullable|string',
            'photo'             => 'nullable|url|max:500',
            'family_contact'    => 'nullable|string|max:100',
            'status'            => 'required|in:published,draft',
        ]);

        Obituary::create(array_merge($validated, ['portal_id' => $portalId]));

        return back()->with('success', 'শোকবার্তা সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function destroyObituary(Obituary $obituary): RedirectResponse
    {
        $obituary->delete();
        return back()->with('success', 'শোকবার্তাটি মুছে ফেলা হয়েছে।');
    }

    // Public API
    public function publicJobs(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $query = JobCircular::where('status', 'published');
        if ($type) {
            $query->where('job_type', $type);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->latest()->paginate(12),
        ]);
    }

    public function publicObituaries(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => Obituary::where('status', 'published')->latest('death_date')->paginate(12),
        ]);
    }
}
