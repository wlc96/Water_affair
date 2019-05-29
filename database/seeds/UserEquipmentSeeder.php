<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserEquipmentSeeder extends Seeder
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
        	$month = '2018-03-24';
        	$a = '9123091823456746435809123123123118429182';
        	$month = Carbon::parse($month);
        	while ( $month < Carbon::now()) 
        	{
        		$total_water = DB::select("select total_water from equipment where id = 'YMZH2313242182'")[0]->total_water;
        		$new_water = (mt_rand(84,120)/100);
        		$sum_water = $new_water + $total_water;
        		DB::table('water_datas')->insert([
		    		'equipment_id' => 'YMZH2313242182',
		    		'water_num' => $new_water,
		    		'all_num' => $sum_water,
		    		'created_at' => Carbon::parse($month),
	            	'updated_at' => Carbon::parse($month),
		    	]);

		    	DB::update("update equipment set total_water = $sum_water where id = 'YMZH2313242182'");
		    	$start_year = Carbon::parse($month)->startOfYear();
		    	$end_year = Carbon::parse($month)->endOfYear();
		    	$year_water_end = DB::table('water_datas')->whereBetween('created_at', [$start_year, $end_year])->orderBy('created_at', 'desc')->first()->all_num;
		    	$year_water_start = DB::table('water_datas')->whereBetween('created_at', [$start_year, $end_year])->orderBy('created_at')->first()->all_num;

		    	$year_water = $year_water_end-$year_water_start;
		    	if ($year_water <= 180) 
		    	{
		    		$shuijia = 5;
		    	}
		    	if (($year_water <= 260) && ($year_water > 180)) 
		    	{
		    		$shuijia = 7;
		    	}
		    	if ($year_water > 260)
		    	{
		    		$shuijia = 9;
		    	}

		    	$money = $new_water*$shuijia;

		    	$shengyu = DB::select("select surplus_water from equipment where id = 'YMZH2313242182'")[0]->surplus_water;
		    	$end_water = $shengyu-$new_water;

		    	$shengyum = DB::select("select surplus_money from equipment where id = 'YMZH2313242182'")[0]->surplus_money;
		    	$end_money = $shengyum-$money;

		    	DB::update("update equipment set surplus_water = $end_water where id = 'YMZH2313242182'");
		    	DB::update("update equipment set surplus_money = $end_money where id = 'YMZH2313242182'");

		    	if (Carbon::parse($month) == Carbon::parse($month)->endOfMonth()->startOfDay()) 
		    	{
		    		$qian = mt_rand(2,5)*50;
		    		$a = str_shuffle($a);
		    		DB::table('recharges')->insert([
		    			'company_id' => 1,
			            'number' => 'YMZH'.substr($a,3,10),
			            'user_id' => 3,
			            'station_id' => 1,
			            'equipment_id' => 'YMZH2313242182',
			            'water_quantity' => $qian/$shuijia,
			            'sum' => $qian,
			            'type' => rand(1,2),
			            'created_at' => Carbon::parse($month),
			            'updated_at' => Carbon::parse($month),
		    		]);

		    		$add_water = $qian/$shuijia + $shengyu;
		    		$add_money = $qian + $shengyum;

		    		DB::update("update equipment set surplus_water = $add_water where id = 'YMZH2313242182'");
		    		DB::update("update equipment set surplus_money = $add_money where id = 'YMZH2313242182'");
		    	}

		    	$month = $month->addDay();

        	}
	    	


	    });
    }
}
