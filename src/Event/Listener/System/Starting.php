<?php

namespace Nails\Common\ErrorHandler\Rollbar\Event\Listener\System;

use Nails\Common\Events;
use Nails\Common\Events\Subscription;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Service\Asset;
use Nails\Config;
use Nails\Environment;
use Nails\Factory;
use Nails\RelatedContent\Constants;
use Nails\RelatedContent\Service\Engine;

/**
 * Class Starting
 *
 * @package Nails\Common\ErrorHandler\Rollbar\Event\Listener\System
 */
class Starting extends Subscription
{
    /**
     * Starting constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Events::SYSTEM_STARTING)
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    /**
     * @throws FactoryException
     */
    public function execute(): void
    {
        if (Config::get('ROLLBAR_ACCESS_TOKEN_JS')) {
            /** @var Asset $oAsset */
            $oAsset = Factory::service('Asset');

            $sSnippetPath = implode(DIRECTORY_SEPARATOR, [
                __DIR__,
                '..',
                '..',
                '..',
                '..',
                'resources',
                'snippet.js',
            ]);

            $aSnippet = [
                sprintf(
                    'var _rollbarConfig = %s;',
                    json_encode([
                        'accessToken'                => Config::get('ROLLBAR_ACCESS_TOKEN_JS'),
                        'captureUncaught'            => true,
                        'captureUnhandledRejections' => true,
                        'payload'                    => [
                            'environment' => Environment::get(),
                        ],
                    ])
                ),
                file_get_contents($sSnippetPath),
            ];

            $oAsset->inline(implode(PHP_EOL, $aSnippet), Asset::TYPE_JS_INLINE_HEADER);
        }
    }
}
