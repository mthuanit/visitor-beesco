<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factories = ['BV', 'LN', 'BD', 'PL'];
        $countPerFactory = 100;

        foreach ($factories as $factory) {
            for ($i = 1; $i <= $countPerFactory; $i++) {
                $code = $factory . str_pad($i, 3, '0', STR_PAD_LEFT);
                \App\Models\Card::firstOrCreate([
                    'code' => $code
                ], [
                    'status' => \App\Models\Card::STATUS_AVAILABLE
                ]);
            }
        }
    }
}
