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

Route::get('test123', function() {});

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
Route::get('cron/create-search-index', 'CronController@createSearchIndex');

Route::get('file/plain-list', 'OtrkeyFileController@plainList');

Route::resource('promotion', 'PromotionController');

Route::get('filme/blockbuster', 'FilmCatalogueController@blockbuster');
Route::get('filme/dokus', 'FilmCatalogueController@dokus');
Route::get('filme/komoedie', 'FilmCatalogueController@comedy');
Route::get('filme/action', 'FilmCatalogueController@action');
Route::get('filme/thriller', 'FilmCatalogueController@thriller');
Route::get('filme/action-thriller-crime', 'FilmCatalogueController@action_thriller_crime');
Route::get('filme/familie', 'FilmCatalogueController@family');
Route::get('filme/animation', 'FilmCatalogueController@animation');
Route::get('filme/animation-familie', 'FilmCatalogueController@animation_family');
Route::get('filme/sci-fi', 'FilmCatalogueController@scifi');
Route::get('filme/fantasy', 'FilmCatalogueController@fantasy');
Route::get('filme/sci-fi-fantasy', 'FilmCatalogueController@scifi_fantasy');
Route::get('filme/horror', 'FilmCatalogueController@horror');
Route::get('filme/geheimtipp', 'FilmCatalogueController@insiderstip');
Route::get('filme/fantasy', 'FilmCatalogueController@fantasy');
Route::get('filme/drama', 'FilmCatalogueController@drama');
Route::get('filme/horror-mystery', 'FilmCatalogueController@horror_mystery');
Route::get('film/view', function(){ return redirect('/filme/alle')->withInput(); });
Route::get('filme/alle', ['as' => 'filmview', 'uses' =>  'FilmCatalogueController@all']);
Route::get('my/films', function() {return redirect('/filme/meine');});
Route::get('filme/meine', ['as' => 'myfilmsview', 'uses' =>  'FilmCatalogueController@my']);

Route::get('filme/genre/{genre}', 'FilmController@genre');

Route::get('serien/blockbuster', 'SeriesCatalogueController@blockbuster');
Route::get('serien/dokus', 'SeriesCatalogueController@dokus');
Route::get('serien/komoedie', 'SeriesCatalogueController@comedy');
Route::get('serien/action', 'SeriesCatalogueController@action');
Route::get('serien/thriller', 'SeriesCatalogueController@thriller');
Route::get('serien/action-thriller-crime', 'SeriesCatalogueController@action_thriller_crime');
Route::get('serien/familie', 'SeriesCatalogueController@family');
Route::get('serien/animation', 'SeriesCatalogueController@animation');
Route::get('serien/animation-familie', 'SeriesCatalogueController@animation_family');
Route::get('serien/sci-fi', 'SeriesCatalogueController@scifi');
Route::get('serien/fantasy', 'SeriesCatalogueController@fantasy');
Route::get('serien/sci-fi-fantasy', 'SeriesCatalogueController@scifi_fantasy');
Route::get('serien/horror', 'SeriesCatalogueController@horror');
Route::get('serien/geheimtipp', 'SeriesCatalogueController@insiderstip');
Route::get('serien/fantasy', 'SeriesCatalogueController@fantasy');
Route::get('serien/drama', 'SeriesCatalogueController@drama');
Route::get('serien/horror-mystery', 'SeriesCatalogueController@horror_mystery');
Route::get('series/view', function(){ return redirect('/serien/alle')->withInput(); });
Route::get('serien/alle', ['as' => 'seriesview', 'uses' =>  'SeriesCatalogueController@all']);
Route::get('my/series', function() {return redirect('/serien/meine');});
Route::get('serien/meine', ['as' => 'myseriesview', 'uses' =>  'SeriesCatalogueController@my']);


Route::get('film/imdb-data/{imdbId}', 'FilmController@imdbData');
Route::get('film/refresh-imdb-data', 'FilmController@refreshImdbData');
Route::get('film/amazon-description/{asin}', 'FilmController@amazonDescription');
Route::get('film/amazon-data/{asin}', 'FilmController@amazonData');
Route::get('film/search-for-select', 'FilmController@searchForSelect');
Route::resource('film', 'FilmController');
Route::get('film/{id}/{title?}', 'FilmController@show');

Route::get('film-mapper/from-tv-program/{tv_program_id}', 'FilmMapperController@fromTvProgram');
Route::get('film-mapper/map-all', 'FilmMapperController@applyMapRules');
Route::get('film-mapper/find-mapper-rules', 'FilmMapperController@findMapperRules');
Route::get('film-mapper/verifier-index/{language?}', 'FilmMapperController@verifierIndex');
Route::put('film-mapper/{id}/verify', 'FilmMapperController@verify');
Route::put('film-mapper/{id}/skip', 'FilmMapperController@skip');
Route::put('film-mapper/{id}/skip-plus-10-minutes', 'FilmMapperController@skipPlus10Minutes');
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
Route::get('payment/create', 'PaymentController@create');
Route::post('payment/store', 'PaymentController@store');

Route::get('download/{user}/{token}/{filename}', 'TvProgramController@download');
Route::get('download-link/{user}/{token}/{filename}', 'TvProgramController@downloadLink');

Route::delete('tvprogram/{tv_program_id}', 'TvProgramController@destroy');
Route::get('tvprogram/verify-view/{key}', 'TvProgramController@verifyDownloadView');
Route::get('tvprogram/view-download/{tv_program_id}', 'TvProgramController@hiveCoinRedirect');
Route::get('tvprogram/show/{id}', 'TvProgramController@show');
Route::get('tvprogram/film/{film_id}/{language?}/{quality?}', 'TvProgramController@film');
Route::get('tvprogram/search', ['as' =>'search', 'uses' => 'TvProgramController@search']);
Route::get('tvprogram/select', 'TvProgramController@select');
Route::get('tvprogram/top100', 'TvProgramController@top100');
Route::get('tvprogram/table', 'TvProgramController@table');
Route::get('tvprogram/table-data/{station}/{date}', 'TvProgramController@tableData');
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
Route::get('system/session-dump', 'SystemController@sessionDump');

Route::get('user/select', 'UserController@select');

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
