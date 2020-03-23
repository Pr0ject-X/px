<?php

declare(strict_types=1);

namespace Pr0jectX\Px\EventSubscriber;

use Robo\Task\Base\loadTasks;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HookForCommand implements EventSubscriberInterface
{
    use loadTasks;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [ConsoleEvents::COMMAND => ['onCommand']];
    }


    public function onCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        $name = $command->getName();
    }
}
