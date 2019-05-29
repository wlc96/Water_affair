<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WaterCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$datas = [];
       	$datas['a'] = ['安阳','安庆','鞍山','安顺'];
		$datas['b'] = ['北京','蚌埠','亳州','白山','白城','本溪','北海'];
		$datas['c'] = ['重庆','长春','长沙','成都','池州','滁州','常德'];
		$datas['d'] = ['东莞','大连','东阳','都匀','大庆'];
		$datas['e'] = ['鄂州市'];
		$datas['f'] = ['福州','阜阳','抚州','佛山','阜新','抚顺','防城港'];
		$datas['g'] = ['赣州','贵阳','桂林','广州','贵港','甘南'];
		$datas['h'] = ['杭州','海口','合肥','哈尔滨','淮北','湖州','怀化'];
		$datas['j'] = ['济南','嘉兴','金华','九江','吉安','吉林','金昌'];
		$datas['k'] = ['昆明','开封','凯里'];
		$datas['l'] = ['龙岩','丽水','六安','娄底','辽源','洛阳','柳州'];
		$datas['m'] = ['绵阳','马鞍山','牡丹江'];
		$datas['n'] = ['南昌','南宁','南平','南京','宁波','宁德','邯郸'];
		$datas['p'] = ['莆田','萍乡','平顶山','盘锦'];
		$datas['q'] = ['泉州','衢州','青岛','齐齐哈尔','七台河','秦皇岛'];
		$datas['s'] = ['石家庄','深圳','苏州','沈阳','上饶','上海'];
		$datas['t'] = ['天津','台州','太原','铜陵','通化','铁岭','铜仁'];
		$datas['w'] = ['温州','无锡','武汉','芜湖'];
		$datas['x'] = ['西宁','厦门','襄樊','新余','西安','宣城市','湘潭'];
		$datas['y'] = ['扬州','宜昌','岳阳','鹰潭','烟台','益阳','延边州'];
		$datas['z'] = ['镇江','舟山','珠海','漳州','郑州','张家界','淄博'];

		return DB::transaction(function() use($datas)
        {
        	foreach ($datas as $key => $data) 
        	{
        		foreach ($data as $city) 
        		{
        			DB::table('water_citys')->insert([
			            'initials' => $key,
			            'name' => $city,
			        ]);
        		}
        	}
        });
        
    }
}
