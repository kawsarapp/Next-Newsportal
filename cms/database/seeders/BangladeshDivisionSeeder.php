<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BangladeshDivisionSeeder extends Seeder
{
    public function run(): void
    {
        // ── ৮টি বিভাগ ─────────────────────────────────────────────────────────
        $divisions = [
            ['id' => 1, 'name' => 'ঢাকা',     'name_en' => 'Dhaka'],
            ['id' => 2, 'name' => 'চট্টগ্রাম', 'name_en' => 'Chattogram'],
            ['id' => 3, 'name' => 'রাজশাহী',   'name_en' => 'Rajshahi'],
            ['id' => 4, 'name' => 'খুলনা',     'name_en' => 'Khulna'],
            ['id' => 5, 'name' => 'বরিশাল',    'name_en' => 'Barishal'],
            ['id' => 6, 'name' => 'সিলেট',     'name_en' => 'Sylhet'],
            ['id' => 7, 'name' => 'রংপুর',     'name_en' => 'Rangpur'],
            ['id' => 8, 'name' => 'ময়মনসিংহ',  'name_en' => 'Mymensingh'],
        ];

        DB::table('divisions')->insertOrIgnore($divisions);

        // ── ৬৪টি জেলা ─────────────────────────────────────────────────────────
        $districts = [
            // ঢাকা বিভাগ
            ['id'=>1,  'division_id'=>1, 'name'=>'ঢাকা',          'name_en'=>'Dhaka'],
            ['id'=>2,  'division_id'=>1, 'name'=>'ফরিদপুর',       'name_en'=>'Faridpur'],
            ['id'=>3,  'division_id'=>1, 'name'=>'গাজীপুর',       'name_en'=>'Gazipur'],
            ['id'=>4,  'division_id'=>1, 'name'=>'গোপালগঞ্জ',     'name_en'=>'Gopalganj'],
            ['id'=>5,  'division_id'=>1, 'name'=>'কিশোরগঞ্জ',     'name_en'=>'Kishoreganj'],
            ['id'=>6,  'division_id'=>1, 'name'=>'মাদারীপুর',     'name_en'=>'Madaripur'],
            ['id'=>7,  'division_id'=>1, 'name'=>'মানিকগঞ্জ',     'name_en'=>'Manikganj'],
            ['id'=>8,  'division_id'=>1, 'name'=>'মুন্সিগঞ্জ',    'name_en'=>'Munshiganj'],
            ['id'=>9,  'division_id'=>1, 'name'=>'নারায়ণগঞ্জ',   'name_en'=>'Narayanganj'],
            ['id'=>10, 'division_id'=>1, 'name'=>'নরসিংদী',       'name_en'=>'Narsingdi'],
            ['id'=>11, 'division_id'=>1, 'name'=>'রাজবাড়ী',      'name_en'=>'Rajbari'],
            ['id'=>12, 'division_id'=>1, 'name'=>'শরীয়তপুর',     'name_en'=>'Shariatpur'],
            ['id'=>13, 'division_id'=>1, 'name'=>'টাঙ্গাইল',      'name_en'=>'Tangail'],
            // চট্টগ্রাম বিভাগ
            ['id'=>14, 'division_id'=>2, 'name'=>'বান্দরবান',     'name_en'=>'Bandarban'],
            ['id'=>15, 'division_id'=>2, 'name'=>'ব্রাহ্মণবাড়িয়া','name_en'=>'Brahmanbaria'],
            ['id'=>16, 'division_id'=>2, 'name'=>'চাঁদপুর',       'name_en'=>'Chandpur'],
            ['id'=>17, 'division_id'=>2, 'name'=>'চট্টগ্রাম',     'name_en'=>'Chattogram'],
            ['id'=>18, 'division_id'=>2, 'name'=>'কুমিল্লা',      'name_en'=>'Cumilla'],
            ['id'=>19, 'division_id'=>2, 'name'=>'কক্সবাজার',     'name_en'=>'Cox\'s Bazar'],
            ['id'=>20, 'division_id'=>2, 'name'=>'ফেনী',          'name_en'=>'Feni'],
            ['id'=>21, 'division_id'=>2, 'name'=>'খাগড়াছড়ি',    'name_en'=>'Khagrachhari'],
            ['id'=>22, 'division_id'=>2, 'name'=>'লক্ষ্মীপুর',    'name_en'=>'Lakshmipur'],
            ['id'=>23, 'division_id'=>2, 'name'=>'নোয়াখালী',     'name_en'=>'Noakhali'],
            ['id'=>24, 'division_id'=>2, 'name'=>'রাঙ্গামাটি',    'name_en'=>'Rangamati'],
            // রাজশাহী বিভাগ
            ['id'=>25, 'division_id'=>3, 'name'=>'বগুড়া',         'name_en'=>'Bogura'],
            ['id'=>26, 'division_id'=>3, 'name'=>'চাঁপাইনবাবগঞ্জ','name_en'=>'Chapainawabganj'],
            ['id'=>27, 'division_id'=>3, 'name'=>'জয়পুরহাট',     'name_en'=>'Joypurhat'],
            ['id'=>28, 'division_id'=>3, 'name'=>'নওগাঁ',         'name_en'=>'Naogaon'],
            ['id'=>29, 'division_id'=>3, 'name'=>'নাটোর',         'name_en'=>'Natore'],
            ['id'=>30, 'division_id'=>3, 'name'=>'পাবনা',         'name_en'=>'Pabna'],
            ['id'=>31, 'division_id'=>3, 'name'=>'রাজশাহী',       'name_en'=>'Rajshahi'],
            ['id'=>32, 'division_id'=>3, 'name'=>'সিরাজগঞ্জ',     'name_en'=>'Sirajganj'],
            // খুলনা বিভাগ
            ['id'=>33, 'division_id'=>4, 'name'=>'বাগেরহাট',      'name_en'=>'Bagerhat'],
            ['id'=>34, 'division_id'=>4, 'name'=>'চুয়াডাঙ্গা',   'name_en'=>'Chuadanga'],
            ['id'=>35, 'division_id'=>4, 'name'=>'যশোর',          'name_en'=>'Jashore'],
            ['id'=>36, 'division_id'=>4, 'name'=>'ঝিনাইদহ',       'name_en'=>'Jhenaidah'],
            ['id'=>37, 'division_id'=>4, 'name'=>'খুলনা',         'name_en'=>'Khulna'],
            ['id'=>38, 'division_id'=>4, 'name'=>'কুষ্টিয়া',     'name_en'=>'Kushtia'],
            ['id'=>39, 'division_id'=>4, 'name'=>'মাগুরা',        'name_en'=>'Magura'],
            ['id'=>40, 'division_id'=>4, 'name'=>'মেহেরপুর',      'name_en'=>'Meherpur'],
            ['id'=>41, 'division_id'=>4, 'name'=>'নড়াইল',        'name_en'=>'Narail'],
            ['id'=>42, 'division_id'=>4, 'name'=>'সাতক্ষীরা',     'name_en'=>'Satkhira'],
            // বরিশাল বিভাগ
            ['id'=>43, 'division_id'=>5, 'name'=>'বরগুনা',        'name_en'=>'Barguna'],
            ['id'=>44, 'division_id'=>5, 'name'=>'বরিশাল',        'name_en'=>'Barishal'],
            ['id'=>45, 'division_id'=>5, 'name'=>'ভোলা',          'name_en'=>'Bhola'],
            ['id'=>46, 'division_id'=>5, 'name'=>'ঝালকাঠি',       'name_en'=>'Jhalokathi'],
            ['id'=>47, 'division_id'=>5, 'name'=>'পটুয়াখালী',    'name_en'=>'Patuakhali'],
            ['id'=>48, 'division_id'=>5, 'name'=>'পিরোজপুর',      'name_en'=>'Pirojpur'],
            // সিলেট বিভাগ
            ['id'=>49, 'division_id'=>6, 'name'=>'হবিগঞ্জ',       'name_en'=>'Habiganj'],
            ['id'=>50, 'division_id'=>6, 'name'=>'মৌলভীবাজার',   'name_en'=>'Moulvibazar'],
            ['id'=>51, 'division_id'=>6, 'name'=>'সুনামগঞ্জ',     'name_en'=>'Sunamganj'],
            ['id'=>52, 'division_id'=>6, 'name'=>'সিলেট',         'name_en'=>'Sylhet'],
            // রংপুর বিভাগ
            ['id'=>53, 'division_id'=>7, 'name'=>'দিনাজপুর',      'name_en'=>'Dinajpur'],
            ['id'=>54, 'division_id'=>7, 'name'=>'গাইবান্ধা',     'name_en'=>'Gaibandha'],
            ['id'=>55, 'division_id'=>7, 'name'=>'কুড়িগ্রাম',    'name_en'=>'Kurigram'],
            ['id'=>56, 'division_id'=>7, 'name'=>'লালমনিরহাট',   'name_en'=>'Lalmonirhat'],
            ['id'=>57, 'division_id'=>7, 'name'=>'নীলফামারী',     'name_en'=>'Nilphamari'],
            ['id'=>58, 'division_id'=>7, 'name'=>'পঞ্চগড়',       'name_en'=>'Panchagarh'],
            ['id'=>59, 'division_id'=>7, 'name'=>'রংপুর',         'name_en'=>'Rangpur'],
            ['id'=>60, 'division_id'=>7, 'name'=>'ঠাকুরগাঁও',     'name_en'=>'Thakurgaon'],
            // ময়মনসিংহ বিভাগ
            ['id'=>61, 'division_id'=>8, 'name'=>'জামালপুর',      'name_en'=>'Jamalpur'],
            ['id'=>62, 'division_id'=>8, 'name'=>'ময়মনসিংহ',     'name_en'=>'Mymensingh'],
            ['id'=>63, 'division_id'=>8, 'name'=>'নেত্রকোণা',     'name_en'=>'Netrokona'],
            ['id'=>64, 'division_id'=>8, 'name'=>'শেরপুর',        'name_en'=>'Sherpur'],
        ];

        DB::table('districts')->insertOrIgnore($districts);

        $this->command->info('✅ Bangladesh divisions & districts seeded (64 districts, 8 divisions).');
    }
}
