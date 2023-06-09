<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SimsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $limit = 50;

        $networks = [123, 321, 213];
        for ($i = 0; $i < $limit; $i++) {
            $bytes = random_bytes(20);
            $bytes2 = random_bytes(20);
            DB::table('sims')->insert([
                'uniqueId' => substr(bin2hex($bytes), 0, 10),
                'phone' => rand(1000000000, 9999999999),
                'networkId' => $networks[rand(0,2)],
                'countryCode' => 84,
                'status' => 1,
                'success' => 0,
                'failed' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
