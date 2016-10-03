<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('test123', function(\App\Services\NodeService $nodeService) {
    //phpinfo();
    exit;
    Log::info('call test123');

    $node = \App\Node::find(5);
    try {
        $result = $nodeService->ftpUpload($node,
            'gush.avengers.2.an.axel.braun.parody.mp4',
            'ftp://rafael:Utakasoke#1@s03.hq-mirror.de//var/www/otrkeys/import/test.file');
        return $result;
    } catch(Exception $e) {
        var_dump($e->getMessage());
    }
});

/**
 * User -> Film
 */
Route::get('user-films/add/{film_id}', 'FilmUserController@add');
Route::get('user-films/remove/{film_id}', 'FilmUserController@remove');

/**
 * Feeds
 */
Route::get('feed/rss/{lang?}', 'FeedController@rss');
Route::get('feed/twitter/{lang?}', 'FeedController@twitter');
Route::get('feed/facebook/{lang?}', 'FeedController@facebook');


Route::get('mail/compose', 'MailController@compose');
Route::post('mail/send', 'MailController@send');

/**
 * Cron routes
 */
Route::get('cron/node-status', 'CronController@nodeStatus');
Route::get('cron/node-start-downloads', 'CronController@nodeStartDownloads');
Route::get('cron/node-sync-files', 'CronController@nodeSyncFiles');
Route::get('cron/node-delete-old-files', 'CronController@nodeDeleteOldFiles');
Route::get('cron/node-rebalance', 'CronController@nodeRebalance');
Route::get('cron/read-epg-data', 'CronController@readEpgData');
Route::get('cron/distro-sync-files', 'CronController@distroSyncFiles');
Route::get('cron/aws-sync-files', 'CronController@awsSyncFiles');
Route::get('cron/aws-delete-old-files', 'CronController@awsDeleteOldFiles');
Route::get('cron/clean-database', 'CronController@cleanDatabase');
Route::get('cron/refresh-imdb-data', 'CronController@refreshImdbData');
Route::get('cron/find-mapper-rules', 'CronController@findMapperRules');
Route::get('cron/generate-sitemap', 'CronController@generateSitemap');
Route::get('cron/read-highlights', 'CronController@readHighlights');

Route::get('file/plain-list', 'OtrkeyFileController@plainList');

Route::resource('promotion', 'PromotionController');

Route::get('film/imdb-data/{imdbId}', 'FilmController@imdbData');
Route::get('film/refresh-imdb-data', 'FilmController@refreshImdbData');
Route::get('film/amazon-description/{asin}', 'FilmController@amazonDescription');
Route::get('film/amazon-data/{asin}', 'FilmController@amazonData');
Route::get('film/search-for-select', 'FilmController@searchForSelect');
Route::get('film/view', ['as' => 'filmview', 'uses' =>  'FilmController@viewFilms']);
Route::get('my/films', ['as' => 'myfilmsview', 'uses' =>  'FilmController@myFilms']);
Route::get('series/view', ['as' => 'seriesview', 'uses' =>  'FilmController@viewSeries']);
Route::get('my/series', ['as' => 'myseriesview', 'uses' =>  'FilmController@mySeries']);
Route::resource('film', 'FilmController');
Route::get('film/{id}/{title?}', 'FilmController@show');

Route::get('film-mapper/from-tv-program/{tv_program_id}', 'FilmMapperController@fromTvProgram');
Route::get('film-mapper/map-all', 'FilmMapperController@applyMapRules');
Route::get('film-mapper/find-mapper-rules', 'FilmMapperController@findMapperRules');
Route::get('film-mapper/verifier-index/{language?}', 'FilmMapperController@verifierIndex');
Route::put('film-mapper/{id}/verify', 'FilmMapperController@verify');
Route::put('film-mapper/{id}/skip', 'FilmMapperController@skip');
Route::resource('film-mapper', 'FilmMapperController');
Route::get('film-mapper/create/{tv_program_id?}', 'FilmMapperController@create');


Route::get('payment/refund/{transaction_id}/{free?}', 'PaymentController@refund');
Route::get('payment/all-transactions', 'PaymentController@allTransactions');
Route::get('payment/transactions', 'PaymentController@transactions');
Route::get('payment/verify', 'PaymentController@verifyPendingTransactions');
Route::get('payment/test', 'PaymentController@test');
Route::get('payment/prepare', 'PaymentController@prepare');
Route::get('payment/purchase/{product_id}', 'PaymentController@purchase');
Route::get('payment/success', 'PaymentController@success');
Route::get('payment/cancel', 'PaymentController@cancel');

Route::get('download/{user}/{token}/{filename}', 'TvProgramController@download');

Route::delete('tvprogram/{tv_program_id}', 'TvProgramController@destroy');
Route::get('tvprogram/show/{id}', 'TvProgramController@show');
Route::get('tvprogram/film/{film_id}/{language?}/{quality?}', 'TvProgramController@film');
Route::get('tvprogram/search', ['as' =>'search', 'uses' => 'TvProgramController@search']);
Route::get('tvprogram/select', 'TvProgramController@select');
Route::get('tvprogram/top100', 'TvProgramController@top100');
Route::get('tvprogram/{lang?}', 'TvProgramController@index');
Route::get('film/{otrid}', 'TvProgramController@byOtrId');
Route::get('tvprogram/{id}/edit', 'TvProgramController@edit');
Route::patch('tvprogram/{id}', 'TvProgramController@update');

Route::post('user-list/add', 'UserListController@add');
Route::post('user-list/remove', 'UserListController@remove');
Route::get('user-list/favorite', 'UserListController@favorite');
Route::get('user-list/watched', 'UserListController@watched');

Route::get('contact', 'ContactController@form');
Route::post('contact/send', 'ContactController@send');

Route::get('node/planned-downloads', 'NodeController@plannedDownloads');
Route::get('node/list-downloads', 'NodeController@nodesDownloads');
Route::get('node/start-downloads', 'NodeController@startDownloads');
Route::get('node/abort-download/{nodeId}/{downloadId}', 'NodeController@abortDownload');
Route::get('node/rebalance', 'NodeController@rebalanceNodes');
Route::get('node/status', 'NodeController@nodesStatus');
Route::get('node/status-partial/{noodeid}', 'NodeController@nodeStatusPartial');
Route::get('node/sync-files', 'NodeController@syncNodesFiles');
Route::get('node/add-file', 'NodeController@addFile');
Route::post('node/add-file', 'NodeController@pushFile');
Route::get('node/copy-file', 'NodeController@copyFile');
Route::get('node/get-files', 'NodeController@getFiles');
Route::post('node/copy-file', 'NodeController@doCopyFile');
Route::get('node/delete-old-files', 'NodeController@deleteOldFiles');
Route::get('node/delete-plan', 'NodeController@deletePlan');

Route::get('system/refresh-tvprogram-view', 'SystemController@refreshTvProgramView');
Route::get('sitemap.xml', 'SystemController@generateSitemap');

Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index');
Route::get('impressum', 'HomeController@impressum');
Route::get('faq', 'HomeController@faq');
Route::get('news', 'HomeController@news');

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
    'stats'    => 'StatsController',
]);
