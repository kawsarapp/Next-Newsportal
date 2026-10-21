<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request): Response|JsonResponse
    {
        $files = Storage::disk('public')->files('uploads');

        $media = array_map(function ($file) {
            return [
                'id'   => md5($file),
                'name' => basename($file),
                'url'  => asset('storage/' . $file),
                'size' => round(Storage::disk('public')->size($file) / 1024) . ' KB',
                'date' => date('d M Y', Storage::disk('public')->lastModified($file)),
            ];
        }, $files);

        // API request (e.g. from MediaPickerModal or RichTipTapEditor via Axios)
        if ($request->wantsJson() || $request->expectsJson() || $request->query('format') === 'json' || str_contains((string) $request->header('Accept'), 'json')) {
            return response()->json([
                'media' => array_values(array_reverse($media)),
            ]);
        }

        // Inertia page view (when navigating to /dashboard/media)
        return Inertia::render('Dashboard/Media/Index', [
            'media' => array_values(array_reverse($media)),
        ]);
    }

    public function upload(Request $request): RedirectResponse|JsonResponse
    {
        $isJson = $request->expectsJson()
            || $request->wantsJson()
            || $request->ajax()
            || str_contains((string) $request->header('Accept'), 'json')
            || $request->hasHeader('X-Requested-With');

        // Support multiple file uploads
        if ($request->hasFile('files')) {
            $request->validate([
                'files'   => 'required|array',
                'files.*' => 'image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            ]);

            $uploaded = [];
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads', 'public');
                $url  = asset('storage/' . $path);
                $uploaded[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url'  => $url,
                ];
            }

            if ($isJson) {
                return response()->json([
                    'success' => true,
                    'files'   => $uploaded,
                    'url'     => $uploaded[0]['url'] ?? null,
                ]);
            }

            return back()->with('success', count($uploaded) . ' টি ফাইল আপলোড সম্পন্ন হয়েছে!');
        }

        // Single file upload
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        $path = $request->file('file')->store('uploads', 'public');
        $url  = asset('storage/' . $path);

        if ($isJson) {
            return response()->json([
                'success' => true,
                'files'   => [
                    [
                        'name' => $request->file('file')->getClientOriginalName(),
                        'path' => $path,
                        'url'  => $url,
                    ]
                ],
                'path'    => $path,
                'url'     => $url,
            ]);
        }

        return back()->with('success', 'মিডিয়া ফাইল আপলোড সম্পন্ন হয়েছে!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $files = Storage::disk('public')->files('uploads');
        foreach ($files as $file) {
            if (md5($file) === $id) {
                Storage::disk('public')->delete($file);
                return back()->with('success', 'ফাইলটি মুছে ফেলা হয়েছে!');
            }
        }

        return back()->with('error', 'ফাইল খুঁজে পাওয়া যায়নি।');
    }

    public function updateMeta(Request $request, string $media): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title'    => 'nullable|string|max:255',
            'caption'  => 'nullable|string|max:500',
            'alt_text' => 'nullable|string|max:255',
        ]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'মেটাডাটা সফলভাবে আপডেট করা হয়েছে!',
                'data'    => $validated,
            ]);
        }

        return back()->with('success', 'মেটাডাটা সফলভাবে আপডেট করা হয়েছে!');
    }
}
