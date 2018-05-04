<?php

use Illuminate\Database\Seeder;

class NodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nodes = array(
			array(
				"id" => 1,
				"short_name" => "s01",
				"url" => "http://s01.hq-mirror.de/index.php",
				"key" => "gkY1?ZIey|F8p fM)lHa-] {:;}Fgr<+-:;u-]t=lK|Gq10]?-Kv;?`Wek,ZZ3|_",
				"free_disk_space" => 0,
				"busy_workers" => 0,
				"status" => "active",
			),
			array(
				"id" => 4,
				"short_name" => "s05",
				"url" => "http://s05.hq-mirror.de/index.php",
				"key" => "w]TA1L&Ux,/Im1A?6mY|RGFA9*|q-=j}o`bV2;p-mgIKN-p}?#/D||(s:,9[O4+c",
				"free_disk_space" => 0,
				"busy_workers" => 0,
				"status" => "active",
			),
			array(
				"id" => 5,
				"short_name" => "s02",
				"url" => "http://s02.hq-mirror.de/index.php",
				"key" => "eVkZ|9xtKu,s9A H^vA+#}U#m]Y,japE~ IK-d>UO-t :}G)LR-8Cw|by-++;We}",
				"free_disk_space" => 0,
				"busy_workers" => 0,
				"status" => "active",
			),
		);
		
		foreach($nodes as $node) {
			$data = array_merge($node, [
				'created_at' => DB::raw('NOW()'),
				'updated_at' => DB::raw('NOW()'),
			]);
	        DB::table('nodes')->insert($data);
		}

    }
}
