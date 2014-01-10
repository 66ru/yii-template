<?php

/**
 * Class OneInstanceCommand
 * version 1.1
 */
abstract class OneInstanceCommand extends CConsoleCommand
{
    /**
     * @var string
     */
    protected $lockFileName;

    public function init()
    {
        parent::init();

        $this->onBeforeAction(array($this, 'checkInstance'));
        $this->onAfterAction(array($this, 'cleanUp'));
    }

    /**
     * @param CConsoleCommandEvent $event
     * @throws CException
     */
    protected function checkInstance($event)
    {
        $command = $event->sender;
        if (!($command instanceof OneInstanceCommand)) {
            throw new CException('$event->sender property must be OneInstanceCommand instance');
        }

        $command->lockFileName = sys_get_temp_dir() . '/' . Yii::app()->name . '-' .
            get_called_class() . '-' . $event->action . '.lock';
        if (file_exists($command->lockFileName)) {
            $event->stopCommand = true;
        } else {
            if (!touch($command->lockFileName)) {
                throw new CException('Can\'t touch lock file ' . $command->lockFileName);
            }
        }
    }

    /**
     * @param CConsoleCommandEvent $event
     * @throws CException
     */
    protected function cleanUp($event)
    {
        $command = $event->sender;
        if (!($command instanceof OneInstanceCommand)) {
            throw new CException('$event->sender property must be OneInstanceCommand instance');
        }

        if ($event->exitCode == 0) {
            @unlink($command->lockFileName);
        }
    }

}