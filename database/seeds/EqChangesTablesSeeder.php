<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EqChangesTablesSeeder extends Seeder
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
	    		$old_num = '';
	            $new_num = '';
	            $start_num = '';
	            $end_num = '';
	            $move_time = '';
	    		$f = mt_rand(1, 4);
	    		if (($f == 1 )|| ($f == 4)) 
	    		{
	    			$new_num = 'YMZH'.substr($a,5,10);
	    		}
	    		elseif (($f == 2) || ($f == 3) || ($f == 4)) 
	    		{
	    			$end_num = mt_rand(1,3).mt_rand(0,9).mt_rand(0,9).'.'.mt_rand(0,9);
	    			$move_time = Carbon::now()->toDateTimeString();
	    		}
	    		$surplus_water = $f*(mt_rand(1,3).mt_rand(0,9).'.'.mt_rand(0,9));
	    		$a = str_shuffle($a);
	    		$order_num = 'EQCH'.substr($a,5,10);
	    		$order = DB::table('eq_changes')->insertGetId([
		            'company_id' => 1,
		            'status' => mt_rand(0,1),
		            'type' => mt_rand(1,4),
		            'old_num' => 'YMZH'.substr($a,3,10),
		            'new_num' => $new_num,
		            'start_num' => 0,
		            'end_num' => $end_num,
		            'move_time' => $move_time,
		            'user_id' => mt_rand(2,20),
		            'station_id' => mt_rand(1,10),
		            'order_num' => $order_num,
		            'servicing_id' => 1,
		            'explain' => '',
		            'admin_id' => 1,
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);

		        DB::table('orders')->insert([
		            'company_id' => 1,
		            'order_id' => $order,
		            'order_num' => $order_num,
		            'type' => 'EqChange',
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);

		        DB::table('events')->insert([
		            'company_id' => 1,
		            'order_id' => $order,
		            'number' => $order_num,
		            'type' => 'EqChange',
		            'report_user_id' => '巡检人',
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);
	    	}
	    });
    }
}
