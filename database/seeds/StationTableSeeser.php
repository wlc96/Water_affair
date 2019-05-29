<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StationTableSeeser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 30; $i++) 
    	{ 
    		DB::table('stations')->insert([
	            'company_id' => 1,
	            'status' => 0,
	            'user_id' => 1,
	            'name' => '测试站点',
	            'linkman' => '吴立川',
	            'phone' => '123',
	            'city_id' => 123,
	            's_city_id' => 123,
	            't_city_id' => 123,
	            'lng' => '123',
	            'lat' => '123',
	            'pic' => '123',
	            'business_hours' => '123',
	            'address' => '123',
	            'history_num' => '123',
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	        ]);
    	}
    }
}
