<?php namespace App\Http\Middleware;

use Closure;

class SingletonMiddleware {

	protected $file;


	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$this->file = storage_path().'/app/singleton_'.str_replace('/','_',$request->decodedPath());

		if(file_exists($this->file)) {
			if(filectime($this->file) > (time()-(60*10))) {
				return response('Locked since '.date('Y-m-d H:i:s', filectime($this->file)), 423);
			}
		}

		touch($this->file);

		register_shutdown_function([$this, 'cleanup']);

		return $next($request);
	}


	public function cleanup()
	{
		unlink($this->file);
	}
}
