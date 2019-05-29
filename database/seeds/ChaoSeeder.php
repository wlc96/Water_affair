<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChaoSeeder extends Seeder
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
        	$month = Carbon::parse($month);
	    	for ($i=0; $i < 16; $i++) 
	    	{
	    		$real_num = '7'.'9'.mt_rand(0,4).mt_rand(0,9);
		        $should_num = '7'.'9'.mt_rand(5,9).mt_rand(0,9);
		        $mr_rate = '9'.mt_rand(8,9).'.'.mt_rand(5,9).mt_rand(0,9);
	    		$order = DB::table('eq_datas')->insertGetId([
		            'company_id' => 1,
		            'station_id' => mt_rand(1,10),
		            'real_num' => (int)$real_num,
		            'should_num' => (int)$should_num,
		            'completion_rate' => ($real_num/$should_num)*100,
		            'accuracy_rate' => (float)'9'.'9'.'.'.mt_rand(5,9).mt_rand(0,9),
		            'mr_rate' => (float)$mr_rate,
		            'ws_rate' => (float)$mr_rate,
		            'month' => $month->addMonth(),
		            'created_at' => Carbon::now(),
	            	'updated_at' => Carbon::now(),
		        ]);
	    	}
	    });
    }
}
