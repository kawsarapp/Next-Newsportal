<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function public(): JsonResponse
    {
        $portal = app()->bound('current.portal')
            ? app('current.portal')
            : (Portal::first());

        if (!$portal) {
            return $this->notFound('Portal not found');
        }

        return $this->success([
            'name'        => $portal->name,
            'slug'        => $portal->slug,
            'tagline'     => $portal->tagline ?? 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক',
            'logo'        => $portal->logo_path ? url($portal->logo_path) : null,
            'favicon'     => $portal->favicon_path ? url($portal->favicon_path) : null,
            'language'    => $portal->language ?? 'bn',
            'timezone'    => $portal->timezone ?? 'Asia/Dhaka',
        ]);
    }
}
