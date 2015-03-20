<?php namespace Tmountjr\MutedLog;

class MutedLogger extends \Monolog\Logger
{
	/**
	 * Adds a log record.
	 *
	 * @param  integer $level   The logging level
	 * @param  string  $message The log message
	 * @param  array   $context The log context
	 * @return Boolean Whether the record has been processed
	 */
	public function addRecord($level, $message, array $context = array()) {
		if ($message instanceof \Exception) $message = MutedException::encapsulate($message);
		return parent::addRecord($level, $message, $context);
	}
}