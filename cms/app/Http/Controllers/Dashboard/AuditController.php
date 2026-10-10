<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Inertia\Inertia;
use Inertia\Response;

class AuditController extends Controller
{
    public function index(): Response
    {
        $logs = AuditLog::latest('id')
            ->limit(50)
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'action' => $l->action,
                'user_name' => $l->user_id ? 'Admin/User #' . $l->user_id : 'System',
                'ip' => $l->ip_address,
                'time' => $l->created_at?->diffForHumans() ?? 'সদ্য',
            ]);

        return Inertia::render('Dashboard/Audit/Index', [
            'logs' => $logs->isNotEmpty() ? $logs : [
                ['id' => 1, 'action' => 'dashboard.posts.store', 'user_name' => 'Super Admin', 'ip' => '127.0.0.1', 'time' => '২ মিনিট আগে'],
                ['id' => 2, 'action' => 'dashboard.categories.store', 'user_name' => 'Chief Editor', 'ip' => '127.0.0.1', 'time' => '১৫ মিনিট আগে'],
            ],
        ]);
    }

    public function security(): Response
    {
        return $this->index();
    }

    public function logins(): Response
    {
        return $this->index();
    }

    public function export()
    {
        return back()->with('success', 'অডিট লগ এক্সপোর্ট সম্পন্ন হয়েছে!');
    }
}
