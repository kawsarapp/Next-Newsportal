<?php

declare(strict_types=1);

namespace Modules\ReporterBilling\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\ReporterBill;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BillingController extends Controller
{
    public function index(Request $request): Response
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $month = $request->query('month');
        $status = $request->query('status');

        $query = ReporterBill::where('portal_id', $portalId)
            ->with(['reporter:id,name,email,avatar,reporter_type'])
            ->latest();

        if ($month) {
            $query->where('billing_month', $month);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bills = $query->paginate(20)->withQueryString();

        // Statistics
        $totalPaid = ReporterBill::where('portal_id', $portalId)->where('status', 'paid')->sum('total_payable');
        $totalPending = ReporterBill::where('portal_id', $portalId)->where('status', 'pending')->sum('total_payable');
        $totalArticlesBilled = ReporterBill::where('portal_id', $portalId)->sum('total_articles');

        // Reporters list for the create modal
        $reporters = User::withoutGlobalScopes()
            ->where(function ($q) use ($portalId) {
                if ($portalId) {
                    $q->where('portal_id', $portalId);
                }
            })
            ->get(['id', 'name', 'email', 'reporter_type']);

        return Inertia::render('Dashboard/Modules/ReporterBilling/Index', [
            'bills' => $bills,
            'reporters' => $reporters,
            'stats' => [
                'total_paid'    => (float) $totalPaid,
                'total_pending' => (float) $totalPending,
                'total_articles'=> (int) $totalArticlesBilled,
                'total_bills'   => (int) ReporterBill::where('portal_id', $portalId)->count(),
            ],
            'filters' => [
                'month'  => $month,
                'status' => $status,
            ],
        ]);
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'reporter_id'      => 'required|exists:users,id',
            'billing_month'    => 'required|string',
            'rate_per_article' => 'nullable|numeric|min:0',
        ]);

        $reporterId = (int) $request->input('reporter_id');
        $month = $request->input('billing_month'); // e.g. "2026-10"
        $rate = (float) ($request->input('rate_per_article') ?? 500);

        // Count published articles by this reporter in this month
        $articlesCount = Post::withoutGlobalScopes()
            ->where('author_id', $reporterId)
            ->where('status', 'published')
            ->where('published_at', 'like', "{$month}%")
            ->count();

        $subtotal = $articlesCount * $rate;

        return response()->json([
            'total_articles'   => $articlesCount,
            'rate_per_article' => $rate,
            'subtotal'         => $subtotal,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $portalId = auth()->user()?->getEffectivePortalId() ?? 1;

        $validated = $request->validate([
            'reporter_id'      => 'required|exists:users,id',
            'billing_month'    => 'required|string',
            'total_articles'   => 'required|integer|min:0',
            'rate_per_article' => 'required|numeric|min:0',
            'bonus_amount'     => 'nullable|numeric|min:0',
            'deduction_amount' => 'nullable|numeric|min:0',
            'payment_method'   => 'required|string|in:bkash,nagad,rocket,bank,cash',
            'account_number'   => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ]);

        $bonus = (float) ($validated['bonus_amount'] ?? 0);
        $deduction = (float) ($validated['deduction_amount'] ?? 0);
        $totalPayable = ($validated['total_articles'] * $validated['rate_per_article']) + $bonus - $deduction;

        ReporterBill::create([
            'portal_id'        => $portalId,
            'reporter_id'      => $validated['reporter_id'],
            'billing_month'    => $validated['billing_month'],
            'total_articles'   => $validated['total_articles'],
            'rate_per_article' => $validated['rate_per_article'],
            'bonus_amount'     => $bonus,
            'deduction_amount' => $deduction,
            'total_payable'    => max(0, $totalPayable),
            'payment_method'   => $validated['payment_method'],
            'account_number'   => $validated['account_number'] ?? null,
            'status'           => 'pending',
            'notes'            => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'রিপোর্টারের বিল সফলভাবে তৈরি করা হয়েছে!');
    }

    public function updateStatus(Request $request, ReporterBill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'status'         => 'required|in:pending,approved,paid',
            'transaction_id' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'paid') {
            $updateData['paid_at'] = now();
            if (!empty($validated['transaction_id'])) {
                $updateData['transaction_id'] = $validated['transaction_id'];
            }
            if (!empty($validated['payment_method'])) {
                $updateData['payment_method'] = $validated['payment_method'];
            }
        }

        $bill->update($updateData);

        return back()->with('success', 'বিলের স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে!');
    }

    public function destroy(ReporterBill $bill): RedirectResponse
    {
        $bill->delete();
        return back()->with('success', 'বিল রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
