<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LessonType;

class LessonTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => '30 min', 'duration' => 30],
            ['name' => '45 min', 'duration' => 45],
            ['name' => '60 min', 'duration' => 60],
            ['name' => '90 min', 'duration' => 90],
        ];

        foreach ($types as $type) {
            LessonType::create($type);
        }
    }
}
