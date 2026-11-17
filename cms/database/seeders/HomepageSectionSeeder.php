<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Portal;
use App\Http\Controllers\Dashboard\FrontendController;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $portals = Portal::all();
        $controller = new FrontendController();

        foreach ($portals as $portal) {
            $controller->seedDefaultSections($portal->id);
        }
    }
}
