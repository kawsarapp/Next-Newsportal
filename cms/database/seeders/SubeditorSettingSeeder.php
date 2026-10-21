<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Portal;
use App\Models\PortalSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubeditorSettingSeeder extends Seeder
{
    public function run(): void
    {
        $portals = Portal::all();

        foreach ($portals as $portal) {
            $existingSecret = PortalSetting::where('portal_id', $portal->id)
                ->where('key', 'subeditor_api_secret')
                ->first();

            $secret = $existingSecret ? $existingSecret->value : ('sub_sec_' . bin2hex(random_bytes(16)));

            $settings = [
                [
                    'key'   => 'subeditor_api_secret',
                    'value' => $secret,
                    'type'  => 'string',
                    'group' => 'subeditor',
                ],
                [
                    'key'   => 'subeditor_auto_publish',
                    'value' => '1',
                    'type'  => 'boolean',
                    'group' => 'subeditor',
                ],
                [
                    'key'   => 'subeditor_enabled',
                    'value' => '1',
                    'type'  => 'boolean',
                    'group' => 'subeditor',
                ],
                [
                    'key'   => 'subeditor_default_category_id',
                    'value' => '1',
                    'type'  => 'integer',
                    'group' => 'subeditor',
                ],
                [
                    'key'   => 'subeditor_default_author_id',
                    'value' => '1',
                    'type'  => 'integer',
                    'group' => 'subeditor',
                ],
            ];

            foreach ($settings as $setting) {
                PortalSetting::firstOrCreate(
                    [
                        'portal_id' => $portal->id,
                        'key'       => $setting['key'],
                    ],
                    $setting
                );
            }
        }
    }
}
