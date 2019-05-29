<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RechargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$a = '9123091823456746435809123123123118429182';
    	$b = '1234';
    	$c = '345';
    	for ($i=0; $i < 20; $i++) 
    	{ 
            $sum = (mt_rand(1,4).mt_rand(0,9))*10;
    		$a = str_shuffle($a);
    		DB::table('recharges')->insert([
	            'company_id' => 1,
	            'number' => 'YMZH'.substr($a,3,10),
	            'user_id' => mt_rand(1,20),
	            'station_id' => rand(1,15),
	            'equipment_id' => substr($a,12,10),
	            'water_quantity' => $sum/3.5,
	            'sum' => $sum,
	            'type' => rand(1,2),
	            'created_at' => Carbon::now()->subMonth(),
	            'updated_at' => Carbon::now()->subMonth(),
	        ]);
    	}
    }
}
