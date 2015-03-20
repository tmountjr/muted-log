# Muted Log #

## Introduction ##

This package extends Laravel's built-in logging facility to ensure that parameters are not included in the stack trace when calling `monolog`. Consider the following example code:


```
function throwE($param1)
{
	throw new Exception;
}

try {
	$example = throwE('cleartext parameter');
} catch (Exception $e) {
	Log::error($e);
}
```

The error log will contain the parameter within the stack trace:

```
[2015-03-18 15:09:49] dev.ERROR: exception 'Exception' in /path/to/function.php:34
Stack trace:
#0 /path/to/app/routes.php(61): throwE('cleartext parameter')
```

By replacing the exception with an instance of `MutedException` from this package, the stack trace will not contain the actual parameter values:

```
[2015-03-18 15:12:11] dev.ERROR: Exception 'Exception' with message '' in /path/to/function.php:34
Stack trace:
#0 /path/to/app/routes.php(61): throwE([string])
```

Note how the argument `'cleartext parameter'` was replaced with `[string]`.

## Installation ##

Install using composer: `composer require tmountjr/muted-log`

After installing the package, make the following changes in `app/config/app.php`:

```
'providers' => array(
    // ...
    'Tmountjr\MutedLog\MutedLogServiceProvider',
),

// ...

'aliases' => array(
    // ...
    // 'Log' => 'Illuminate\Support\Facades\Log',
    'Log' => 'Tmountjr\MutedLog\Facades\MutedLog',
    // ...
),
```

Note that you need to **replace** the current alias for `Log` with the facade from this package.

## Usage ##

No specific action is required to use this package. Once Laravel's built-in `Log` facade is changed, anytime an exception is written to the log, it will be written using the MutedException. **Note that this does not cover xdebug stack traces printed to the screen.**

### Debugging Considerations ###

Clearly you should not be including stack traces with passwords or credit card information in your production logs. However, it may be useful to leave detailed stack traces turned on in pre-production environments, as long as you are not using actual sensitive data in your development environment. You can override which facade is used on a per-environment basis by changing the `alias` directive in `app/config/app.php` (which will set the alias globally) and then overriding this behavior in `app/config/[environment]/app.php` where `[environment]` is an environment found in `bootstrap/start.php`:

```
return array(
    'aliases' => array(
        'Log' => 'Illuminate\Support\Facades\Log',
    )
);
```

## License ##

MutedLog is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
