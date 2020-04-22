<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

final class ComposerEventHandler implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;

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
        $this->composer = $composer;
        $this->io = $io;
    }

    public function onPostAutoloadDump(Event $event): void
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';

        $plugin = new Plugin($this->composer, $this->io);
        $plugin->build();
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // do nothing
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // do nothing
    }
}
