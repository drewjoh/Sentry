<?php namespace drewjoh\Sentry;

/*
 * (c) Drew Johnston
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sentry PHP Client
 *
 * @package Sentry
 */

class Sentry
{
    const VERSION = '0.1.0';

    const DEBUG   = 'debug';
    const INFO    = 'info';
    const WARN    = 'warning';
    const WARNING = 'warning';
    const ERROR   = 'error';
    const FATAL   = 'fatal';
    
    public static $dsn          = null;
    public static $default_tags = array();
    public static $tags         = array();
    
    protected static $client    = null;
    
    /**
     * Set up our Raven_Client for all future calls to Sentry
     * @param string $dsn  DSN string for your Sentry account
     * @param array  $tags Default tags to apply to all messages sent to Sentry
     */
    public static function setDSN($dsn = null, $tags = null) {
        if( is_null($dsn))
            self::$dsn = $_SERVER['SENTRY_DSN'];
        else
            self::$dsn = $dsn;
        
        if(is_array($tags))
            self::$default_tags = $tags;
    }
    
    /**
     * Add tags on a per message basis
     * @param array $tags Array of tags to apply to the next message to be sent
     */
    public static function tags($tags = null) {
        self::$tags = is_array($tags) ? $tags : null;
    }
    
    /**
     * Capture a message
     * @param  string       $message Message to be sent
     * @param  CONST        $level   Level of message to be sent
     * @return string|bool  $return  False on failure, event_id on success
     */
    public static function captureMessage($message = null, $level = self::INFO) {
        self::_setupClient();
        
        $event_id = self::$client->getIdent(self::$client->captureMessage($message, array(), $level));
        
        self::_resetTags();
        
        if($event_id)
            return $event_id;
        else
            return false;
    }
    
    /**
     * Capture an exception
     * @param  string|exception $message Message to be sent or an Exception itself
     * @return string|bool      $return  False on failure, event_id on success
     */
    public static function captureException($message = null, $culprit = null, $logger = null) {
        // Convert our string message to an Exception to pass to our Raven_Client
        if(is_string($message))
            $message = new \Exception($message);
        
        self::_setupClient();
        
        $event_id = self::$client->getIdent(self::$client->captureException($message, $culprit, $logger));
        
        self::_resetTags();
        
        if($event_id)
            return $event_id;
        else
            return false;
    }
    
    /**
     * Sets up our Raven_Client to send a message
     */
    private static function _setupClient() {
        // Get our tags, if any
        if(is_array(self::$default_tags) AND is_array(self::$tags))
            $tags = array_merge(self::$default_tags, self::$tags);
        elseif(is_array(self::$default_tags))
            $tags = self::$default_tags;
        elseif(is_array(self::$tags))
            $tags = self::$default_tags;
        
        self::$client = new \Raven_Client(self::$dsn, is_array($tags) ? array('tags' => $tags) : null);
    }
    
    /**
     * Resets our temporary tags that we might use per message
     */
    private static function _resetTags() {
        self::$tags = array();
    }

}
