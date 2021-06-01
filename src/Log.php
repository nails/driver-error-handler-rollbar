<?php

namespace Nails\Common\ErrorHandler\Rollbar;

use Rollbar;

/**
 * Class Log
 *
 * @package Nails\Common\ErrorHandler\Rollbar
 */
class Log
{
    /**
     * Utility method which sends a log to Rollbar
     *
     * @param string                   $sLevel The level of log
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    private static function log(string $sLevel, $mToLog, array $aExtra): void
    {
        if (\Nails\Common\ErrorHandler\Rollbar::isAvailable()) {
            Rollbar\Rollbar::log($sLevel, $mToLog, $aExtra);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `emergency` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function emergency($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::EMERGENCY, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `alert` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function alert($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::ALERT, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `critical` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function critical($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::CRITICAL, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `error` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function error($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::ERROR, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `warning` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function warning($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::WARNING, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `notice` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function notice($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::NOTICE, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `info` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function info($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::INFO, $mToLog, $aExtra);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method which sends an `debug` log to Rollbar
     *
     * @param string|\Exception|\Error $mToLog The message to log
     * @param array                    $aExtra Any extra data to pass to Rollbar
     */
    public static function debug($mToLog, array $aExtra = []): void
    {
        static::log(Rollbar\Payload\Level::DEBUG, $mToLog, $aExtra);
    }
}
