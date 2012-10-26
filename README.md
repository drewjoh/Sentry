# Sentry

This is a simple wrapper class for the raven-php experimental PHP client for [Sentry](http://getsentry.com).

I prefer the use of simple static methods throughout my app for things like logging.  This wrapper makes that workable, instead of having to make an instance of the client when you need it.

## Usage
    // Setup our class, with optional persistent tags
    Sentry::setDSN('{YOUR_DSN}', array('php_version' => phpversion()) );

    // Capture a message
    $event_id = Sentry::captureMessage('my log message');

    // Capture an exception
    $event_id = Sentry::captureException($exception);
    
    // Capture with one time use tags
    Sentry::tags(array('test_tag' => 'true'));
	Sentry::captureMessage('Test message 3 from Sentry class');
	
    // Give the user feedback
    echo "Sorry, there was an error! Your reference ID is " . $event_id;

## Installation

Install with Composer

If you're using [Composer](https://github.com/composer/composer) to manage
dependencies, you can add Raven with it.

    {
        "require": {
            "drewjoh/Sentry": ">=0.1.0"
        }
    }
