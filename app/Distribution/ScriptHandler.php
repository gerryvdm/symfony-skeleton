<?php

namespace Netshark\Symfony\Distribution;

use Composer\Script\Event;

final class ScriptHandler
{
    public static function configureProject(Event $event)
    {
        $configurator = new ProjectConfigurator($event->getComposer(), $event->getIO());
        $configurator->run();
    }
}
