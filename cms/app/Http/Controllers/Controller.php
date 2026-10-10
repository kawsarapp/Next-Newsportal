<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Standard success response envelope.
     * All API responses MUST use this shape.
     */
    protected function success(mixed $data, int $status = 200, array $meta = []): JsonResponse
    {
        $portal = app()->bound('current.portal') ? app('current.portal') : null;

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => array_merge([
                'portal'       => $portal?->slug,
                'generated_at' => now()->toISOString(),
            ], $meta),
            'errors'  => null,
        ], $status);
    }

    protected function created(mixed $data, array $meta = []): JsonResponse
    {
        return $this->success($data, 201, $meta);
    }

    protected function notFound(string $message = 'Not found.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data'    => null,
            'errors'  => ['message' => $message],
        ], 404);
    }

    protected function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data'    => null,
            'errors'  => ['message' => $message],
        ], 403);
    }

    protected function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data'    => null,
            'errors'  => $errors,
        ], 422);
    }
}
