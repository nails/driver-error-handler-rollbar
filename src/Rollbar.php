<?php

namespace Nails\Common\ErrorHandler;

use Nails\Common\Exception\NailsException;
use Nails\Common\Interfaces\ErrorHandlerDriver;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;

class Rollbar implements ErrorHandlerDriver
{
    /**
     * Whether the driver is configured or not
     *
     * @var bool
     */
    protected static $bIsAvailable = false;

    // --------------------------------------------------------------------------

    /**
     * Instantiates the driver
     *
     * @return void
     * @throws NailsException
     */
    public static function init()
    {
        /**
         * If the Rollbar token is provided then we'll instantiate the appropriate classes; if it's not
         * then we'll not do anything and let errors bubble through to the default handler.
         */
        if (Config::get('ROLLBAR_ACCESS_TOKEN')) {
            static::$bIsAvailable = true;
            \Rollbar\Rollbar::init(
                [
                    'access_token' => Config::get('ROLLBAR_ACCESS_TOKEN'),
                    'environment'  => Environment::get(),
                    'person_fn'    => '\Nails\Common\ErrorHandler\Rollbar::getPerson',
                ],
                false,
                false,
                false
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Called when a PHP error occurs
     *
     * @param int    $iErrorNumber The error number
     * @param string $sErrorString The error message
     * @param string $sErrorFile   The file where the error occurred
     * @param int    $iErrorLine   The line number where the error occurred
     *
     * @return void
     */
    public static function error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine)
    {
        if ($iErrorNumber == E_STRICT) {
            return;
        }

        if (static::$bIsAvailable) {
            \Rollbar\Rollbar::warning(
                $sErrorString,
                [
                    'error_number' => $iErrorNumber,
                    'file'         => $sErrorFile,
                    'line'         => $iErrorLine,
                ]
            );
        }

        //  Bubble to the default driver
        $oErrorHandler        = Factory::service('ErrorHandler');
        $sDefaultHandlerClass = $oErrorHandler->getDefaultDriverClass();
        $sDefaultHandlerClass::error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches uncaught exceptions
     *
     * @param \Exception $oException     The uncaught exception
     * @param bool       $bHaltExecution Whether to show the error screen and halt execution
     *
     * @return void
     */
    public static function exception($oException, $bHaltExecution = true)
    {
        if (static::$bIsAvailable) {
            \Rollbar\Rollbar::error($oException);
        }

        //  Bubble to the default driver
        $oErrorHandler        = Factory::service('ErrorHandler');
        $sDefaultHandlerClass = $oErrorHandler->getDefaultDriverClass();
        $sDefaultHandlerClass::exception($oException, $bHaltExecution);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches fatal errors on shut down
     *
     * @return void
     */
    public static function fatal()
    {
        if (static::$bIsAvailable) {

            $aError = error_get_last();

            if (!is_null($aError) && $aError['type'] === E_ERROR) {
                \Rollbar\Rollbar::critical(
                    'Fatal error: ' . $aError['message'] . ' in ' . $aError['file'] . ' on line ' . $aError['line'],
                    [
                        'type' => 'Fatal Error',
                        'code' => $aError['type'],
                        'msg'  => $aError['message'],
                        'file' => $aError['file'],
                        'line' => $aError['line'],
                    ]
                );
            }
        }

        //  Bubble to the default driver
        $oErrorHandler        = Factory::service('ErrorHandler');
        $sDefaultHandlerClass = $oErrorHandler->getDefaultDriverClass();
        $sDefaultHandlerClass::fatal();
    }

    // --------------------------------------------------------------------------

    /**
     * Get's the active user, if any
     *
     * @return array
     */
    public static function getPerson()
    {
        return [
            'id'       => activeUser('id') ?: null,
            'username' => activeUser('username') ?: null,
            'email'    => activeUser('email') ?: null,
        ];
    }
}
