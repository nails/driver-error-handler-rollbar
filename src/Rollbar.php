<?php

namespace Nails\Common\ErrorHandler;

use Nails\Common\Interfaces\ErrorHandlerDriver;
use Nails\Environment;
use Nails\Factory;
use Rollbar\Payload\Level;

class Rollbar implements ErrorHandlerDriver
{
    /**
     * Instantiates the driver
     * @return void
     */
    public static function init()
    {
        $oErrorHandler = Factory::service('ErrorHandler');

        if (!defined('DEPLOY_ROLLBAR_ACCESS_TOKEN')) {

            $sSubject = 'Rollbar is not configured correctly';
            $sMessage = 'Rollbar is enabled but DEPLOY_ROLLBAR_ACCESS_TOKEN is not defined.';

            $oErrorHandler->sendDeveloperMail($sSubject, $sMessage);
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }

        if (!class_exists('\Rollbar\Rollbar')) {

            $sSubject = 'Rollbar is not configured properly.';
            $sMessage = 'Rollbar is set as the error handler, but the Rollbar class ';
            $sMessage .= 'could not be found. Ensure that it is in composer.json.';

            $oErrorHandler->sendDeveloperMail($sSubject, $sMessage);
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }

        $aConfig = [
            'access_token' => DEPLOY_ROLLBAR_ACCESS_TOKEN,
            'environment'  => Environment::get(),
            'person_fn'    => '\Nails\Common\ErrorHandler\Rollbar::getPerson',
        ];

        \Rollbar\Rollbar::init($aConfig, false, false, false);
    }

    // --------------------------------------------------------------------------

    /**
     * Called when a PHP error occurs
     *
     * @param  int    $iErrorNumber The error number
     * @param  string $sErrorString The error message
     * @param  string $sErrorFile   The file where the error occurred
     * @param  int    $iErrorLine   The line number where the error occurred
     *
     * @return void
     */
    public static function error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine)
    {
        //  Ignore strict errors
        if ($iErrorNumber == E_STRICT) {
            return;
        }

        //  Send report to Rollbar
        \Rollbar\Rollbar::log(
            Level::WARNING,
            $sErrorString,
            [
                'error_number' => $iErrorNumber,
                'file'         => $sErrorFile,
                'line'         => $iErrorLine,
            ]
        );

        //  Let this bubble to the normal Nails error handler
        //  @todo (Pablo - 2018-03-07) - fix this
        Nails::error($iErrorNumber, $sErrorString, $sErrorFile, $iErrorLine);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches uncaught exceptions
     *
     * @param  \Exception $oException The caught exception
     *
     * @return void
     */
    public static function exception($oException)
    {
        \Rollbar\Rollbar::log(Level::ERROR, $oException);

        $oDetails = (object) [
            'type' => get_class($oException),
            'code' => $oException->getCode(),
            'msg'  => $oException->getMessage(),
            'file' => $oException->getFile(),
            'line' => $oException->getLine(),
        ];

        $sMessage = 'Uncaught Exception with message "' . $oDetails->msg . '" and code "';
        $sMessage .= $oDetails->code . '" in ' . $oDetails->file . ' on line ' . $oDetails->line;

        //  Show we log the item?
        if (function_exists('config_item') && config_item('log_threshold') != 0) {
            log_message('error', $sMessage);
        }

        //  Show something to the user
        if (Environment::not('PRODUCTION')) {
            $sSubject = 'Uncaught Exception';
        } else {
            $sSubject = '';
            $sMessage = '';
        }

        $oErrorHandler = Factory::service('ErrorHandler');
        $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage, $oDetails);
    }

    // --------------------------------------------------------------------------

    /**
     * Catches fatal errors on shut down
     * @return void
     */
    public static function fatal()
    {
        \Rollbar\Rollbar::fatalHandler();

        $aError = error_get_last();

        if (!is_null($aError) && $aError['type'] === E_ERROR) {

            //  Show something to the user
            if (Environment::not('PRODUCTION')) {
                $sSubject = 'Fatal Error';
                $sMessage = $aError['message'] . ' in ' . $aError['file'] . ' on line ' . $aError['line'];
            } else {
                $sSubject = '';
                $sMessage = '';
            }

            $oErrorHandler = Factory::service('ErrorHandler');
            $oErrorHandler->showFatalErrorScreen($sSubject, $sMessage);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get's the active user, if any
     * @return array
     */
    public static function getPerson()
    {
        return [
            'id'       => activeUser('id'),
            'username' => activeUser('username'),
            'email'    => activeUser('email'),
        ];
    }
}
