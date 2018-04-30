<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TeamTNT\TNTSearch\TNTSearch;

class IndexTvPrograms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:tvprograms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index the tv_programs table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


	    $tnt = new TNTSearch;

	    $defaultConnection = config('database.default');
	    $tnt->loadConfig([
		    'driver'    => config("database.connections.$defaultConnection.driver"),
		    'host'      => config("database.connections.$defaultConnection.host"),
		    'database'  => config("database.connections.$defaultConnection.database"),
		    'username'  => config("database.connections.$defaultConnection.username"),
		    'password'  => config("database.connections.$defaultConnection.password"),
		    'charset'   => 'utf8',
		    'storage'   => storage_path()
	    ]);

	    $tnt->setDatabaseHandle(app('db')->connection()->getPdo());
	    $indexer = $tnt->createIndex('index.pre.tvprograms');

	    $indexer->query("select 
				tv_programs_view.tv_program_id as id,
				tv_programs_view.year,
				tv_programs_view.country,
				tv_programs_view.genre,
				tv_programs_view.fsk,
				tv_programs_view.title,
				tv_programs_view.start,
				tv_programs_view.description,
				tv_programs_view.station,
				tv_programs_view.`language`,
				tv_programs_view.name,
				tv_programs_view.quality,
				GROUP_CONCAT(filmstars.star) as star,
				GROUP_CONCAT(filmstars.role) as role
			from tv_programs_view
				left join filmstars
					on tv_programs_view.film_id = filmstars.film_id
			group by tv_programs_view.tv_program_id
			order by tv_programs_view.film_id desc");

	    $indexer->setLanguage('german');
	    $indexer->run();

	    copy(storage_path().'/index.pre.tvprograms', storage_path().'/index.tvprograms');
    }
}

