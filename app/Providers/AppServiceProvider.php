<?php namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{

		\Blade::directive('byteToSize', function($expression) {
			return "<?php echo byteToSize{$expression}; ?>";
		});

		if (config('app.debug')) {

			\DB::listen(function (QueryExecuted $queryExecuted) {
				if (!config('app.debug')) return;

				$sql = $queryExecuted->sql;

				foreach ($queryExecuted->bindings as $value) {
					$sql = preg_replace('/\?/', "'" . substr($value, 0, 50) . "'", $sql, 1);
				}
				\Log::debug('[' . str_pad($queryExecuted->time, 10, " ", STR_PAD_LEFT) . '] ' . $sql);
			});

		}
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);
	}

}
