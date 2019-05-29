<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkOrderSeeder extends Seeder
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
        	$month = '2017-12-01';
        	$a = '9123091823456746435809123123123118429182';
        	$month = Carbon::parse($month);
	    	for ($i=0; $i < 20; $i++) 
	    	{
	    		$sum = (mt_rand(1,4).mt_rand(0,9))*10;
	    		$a = str_shuffle($a);
	    		$order_num = 'EQWO'.substr($a,5,10);
	    		$order = DB::table('work_orders')->insertGetId([
		            'company_id' => 1,
		            'number' => $order_num,
		            'user_id' => mt_rand(1,20),
		            'station_id' => rand(1,15),
		            'link_phone' => $this->getPhone(),
		            'equipment_id' => 'YMZH'.substr($a,7,10),
		            'type' => rand(1,2),
		            'status' => rand(0,1),
		            'servicing_id' => rand(1,2),
		            'created_at' => Carbon::now()->subMonth(),
		            'updated_at' => Carbon::now()->subMonth(),
		        ]);

		         DB::table('events')->insert([
		            'company_id' => 1,
		            'order_id' => $order,
		            'number' => $order_num,
		            'type' => 'WorkOrder',
		            'report_user_id' => '巡检人',
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);
	    	}


	    });
    }

     public function getPhone()
    {
    	$arr = array(
	    130,131,132,133,134,135,136,137,138,139,
	    144,147,
	    150,151,152,153,155,156,157,158,159,
	    176,177,178,
	    180,181,182,183,184,185,186,187,188,189,
		);
		$tmp = $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);

		return $tmp;
    }
}
