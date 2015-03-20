<?php namespace Tmountjr\MutedLog;

/**
 * Setup in order to provide a custom logger instance.
 *
 * NOTE: This is essentially a duplicate of Illuminate\Log\LogServiceProvider. It's setup to call a customized instance
 * of the "Log\Logger" class instead of the Monolog\Logger class.
 */

use App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Log\Writer;

class MutedLogServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$logger = new Writer(
			new MutedLogger($this->app['env']), $this->app['events']
		);

		// Once we have an instance of the logger we'll bind it as an instance into
		// the container so that it is available for resolution. We'll also bind
		// the PSR Logger interface to resolve to this Monolog implementation.
		$this->app->instance('muted-log', $logger);

		if (version_compare(\Illuminate\Foundation\Application::VERSION, '4.2') > -1) {
			$this->app->bind('Psr\Log\LoggerInterface', function($app)
			{
				return $app['log']->getMonolog();
			});
		}
		

		// If the setup Closure has been bound in the container, we will resolve it
		// and pass in the logger instance. This allows this to defer all of the
		// logger class setup until the last possible second, improving speed.
		if (isset($this->app['log.setup']))
		{
			call_user_func($this->app['log.setup'], $logger);
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		if (version_compare(\Illuminate\Foundation\Application::VERSION, '4.2') > -1) {
			return array('muted-log', 'Psr\Log\LoggerInterface');
		} else {
			return array('muted-log');
		}
	}

}