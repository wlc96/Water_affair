<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EqStopsTableSeeder extends Seeder
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
	    		$a = str_shuffle($a);
	    		$order_num = 'EQST'.substr($a,5,10);
	    		$order = DB::table('eq_stops')->insertGetId([
		            'company_id' => 1,
		            'status' => mt_rand(0,1),
		            'equipment_id' => 'YMZH'.substr($a,3,10),
		            'type' => mt_rand(1,3),
		            'user_id' => mt_rand(2,20),
		            'station_id' => mt_rand(1,10),
		            'order_num' => $order_num,
		            'way' => mt_rand(1,2),
		            'servicing_id' => 1,
		            'affected_area' => '影响区域',
		            'explain' => '说明',
		            'admin_id' => 1,
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);

		        DB::table('orders')->insert([
		            'company_id' => 1,
		            'order_id' => $order,
		            'order_num' => $order_num,
		            'type' => 'EqStop',
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);

		        DB::table('events')->insert([
		            'company_id' => 1,
		            'order_id' => $order,
		            'number' => $order_num,
		            'type' => 'EqStop',
		            'report_user_id' => '巡检人',
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);
	    	}
	    });
    }
}
