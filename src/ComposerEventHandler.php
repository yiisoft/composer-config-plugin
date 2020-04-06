<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

final class ComposerEventHandler implements PluginInterface, EventSubscriberInterface
{
    /**
     * Returns list of events the plugin is subscribed to.
     *
     * @return array list of events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => [
                ['onPostAutoloadDump', 0],
            ],
        ];
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->plugin = new Plugin($composer, $io);
    }

    public function onPostAutoloadDump()
    {
        $this->plugin->build();
    }
}
