<?php namespace Tmountjr\MutedLog;

use Exception;

class MutedException extends Exception {

	/**
	 * Use this to generate a muted instance.
	 *
	 * @param	Exception	$e
	 * @return	static
	 */
	public static function encapsulate(Exception $e) {
		return new static($e->getMessage(), $e->getCode(), $e);
	}


	/**
	 * Overridden to ensure that the previous exception is always included.
	 *
	 * @param	string		$message
	 * @param 	int			$code
	 * @param	Exception	$previous
	 */
	public function __construct($message, $code, Exception $previous) {
		parent::__construct($message, $code, $previous);
	}


	/**
	 * Secret sauce that generates the contents obfuscated that ends up returned when cast to a string. This should look
	 * exactly like the standard ->__toString() method on exceptions, except it will omit contents of arguments that are
	 * not objects (unlike the generic parent version of this method).
	 *
	 * @return string
	 */
	public function __toString() {
		// Get exception to generate string from.
		$e = $this->getPrevious();

		// Setup first line.
		$class = get_class($e);
		$message = $e->getMessage();
		$file = $e->getFile();
		$line = $e->getLine();
		$string  = "Exception '$class' with message '$message' in $file:$line\n";

		// Generate full stack trace.
		$string .= "Stack trace:";
		$num = 0;
		foreach($e->getTrace() as $segment) {
			// Setup file and line number.
			$curFile = "[internal function]";
			if (isset($segment["file"]) && isset($segment["line"])) {
				$curFile = $segment["file"] . "(" . $segment["line"] . ")";
			}

			// Setup function/class info but skip those arguments.
			$curClass = "";
			if (isset($segment["class"])) $curClass = $segment["class"] . "->";
			$curFunction = $curClass . $segment["function"];

			// Start off with no args.
			$args = $delim = "";
			foreach($segment["args"] as $curArg) {
				if (is_object($curArg)) {
					$curValue = "[" . get_class($curArg) . "]";
				} else {
					$curValue = "[" . gettype($curArg) . "]";
				}
				$args .= $delim . $curValue;
				$delim = ", ";
			}

			// Add to string.
			$string .= "\n#$num $curFile: $curFunction($args)";
			$num++;
		}

		// Finish off...
		$string .= "\n#$num {main}";

		return $string;
	}

}
