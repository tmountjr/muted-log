<?php namespace Tmountjr\MutedLog\Facades;

use Illuminate\Support\Facades\Facade;

class MutedLog extends Facade{
	/**
	 * Get the registered name of the component
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'muted-log';
	}
}