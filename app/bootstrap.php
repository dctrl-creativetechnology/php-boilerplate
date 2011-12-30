<?php

/**
 * [!!] Note: Changes to your php configuration (through ini_set()) are an
 * overhead for each request to your application. For a slight performance
 * increase, place these settings in your php.ini configuration file, and remove
 * said lines.
 */

/**
 * Error reporting level
 *
 * Setting the PHP error reporting level to "-1" essentially forces PHP to
 * report every error, regardless of PHP version. You will want to change this
 * to "0" when in a production environment.
 */
\error_reporting(-1);

/**
 * Display errors
 *
 * Turning off error detail informs PHP that you don't want errors displayed.
 * Since the boilerplate defines an error handler, turning display errors off
 * is ideal, as duplicate information will be displayed (once from PHP, and once
 * by the error handler).
 */
\ini_set('display_errors', 'Off');

/**
 * Default timezone
 *
 * Since PHP 5.1.0, every call to a date/time function will generate an E_NOTICE
 * if the timezone is not valid, and/or an E_WARNING message if the system
 * settings or the TZ environment variable is being used. Unless you have
 * control of the INI setting "date.timezone", it's a good idea to define the
 * application timezone during runtime.
 */
\date_default_timezone_set('UTC');

// Bootstrap the Autoload process
require_once __DIR__.'/autoload.php';

/**
 * Sets the function that should be called in the event an exception is not
 * explicitly caught by a script.
 */
\set_exception_handler($handler = function(\Exception $e)
{
	echo '<h1>Looks like we\'ve caught an exception:</h1>';
	echo '<pre>'.print_r($e, true).'</pre>';
	exit;
});

/**
 * Sets the function that should be called in the event an error is encountered.
 * All PHP errors will fall into this handler, but silently ignored in the
 * event your error reporting level does not include them.
 */
\set_error_handler(function($type, $message, $file = null, $line = 0) use ($handler)
{
	if(!($code & \error_reporting()))
	{
		return;
	}

	$handler(new \ErrorException($message, $type, 0, $file, $line));
});

/**
 * Register a function for execution on shutdown. This function will be called
 * at the end of the PHP script or on a fatal PHP error.
 */
\register_shutdown_function(function() use ($handler)
{
	if(($error = \error_get_last()) !== null)
	{
		$handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
	}
});

/* End of file bootstrap.php */