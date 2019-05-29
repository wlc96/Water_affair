<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EquipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	return DB::transaction(function()
        {
	        $a = '9123091823456746435809123123123118429182';
	    	$b = '1234';
	    	$c = '345';
	    	for ($i=0; $i < 20; $i++) 
	    	{ 
	    		$f = rand(-1, 1);
	    		if (!$f) 
	    		{
	    			$f = 1;
	    		}
	    		$surplus_water = $f*(mt_rand(1,3).mt_rand(0,9).'.'.mt_rand(0,9));
	    		$a = str_shuffle($a);
	    		DB::table('equipment')->insert([
		            'id' => 'YMZH'.substr($a,3,10),
		            'type' => 2,
		            'equipment_type_id' => 1,
		            'status' => 0,
		            'eq_addr' => substr($a,3,10),
		            'eq_num' => 'YMZH'.substr($a,3,10),
		            'station_id' => mt_rand(1,4),
		            'use_type' => 1,
		            'total_water' => mt_rand(1,3).mt_rand(0,9).mt_rand(0,9).'.'.mt_rand(0,9),
		            'last_month' => mt_rand(1,3).mt_rand(0,9).'.'.mt_rand(0,9),
		            'this_month' => mt_rand(1,3).mt_rand(0,9).'.'.mt_rand(0,9),
		            'surplus_water' => $surplus_water,
		            'surplus_money' => $surplus_water/3,
		            'address' => '设备地址',
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);
	    	}
	    });
    }
}
