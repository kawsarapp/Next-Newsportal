<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    /**
     * GET /api/v1/areas or GET /api/v1/divisions
     * List all 8 divisions with their districts
     */
    public function index(): JsonResponse
    {
        $divisions = Division::with(['districts' => function ($q) {
            $q->orderBy('id');
        }])->orderBy('id')->get()->map(function ($division) {
            return [
                'id'        => $division->id,
                'name'      => $division->name,
                'name_en'   => $division->name_en,
                'bn_name'   => $division->bn_name ?? $division->name,
                'bn_code'   => $division->bn_code,
                'districts' => $division->districts->map(function ($district) {
                    return [
                        'id'          => $district->id,
                        'division_id' => $district->division_id,
                        'name'        => $district->name,
                        'name_en'     => $district->name_en,
                        'bn_name'     => $district->bn_name ?? $district->name,
                        'bn_code'     => $district->bn_code,
                    ];
                }),
            ];
        });

        return $this->success($divisions);
    }
}
