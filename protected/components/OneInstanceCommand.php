<?php

/**
 * Class OneInstanceCommand
 * version 1.2
 * @property mixed onBeforeAction
 * @property mixed onAfterAction
 */
abstract class OneInstanceCommand extends CConsoleCommand
{
    /**
     * @var string
     */
    protected $lockFileName;

    /**
     * If process executes more than $longRunningTimeout seconds,
     * it will be considered as long-running
     * and warning message will be logged
     * @var int seconds
     */
    public $longRunningTimeout = 3600; // 1h

    public function init()
    {
        parent::init();

        $this->onBeforeAction = array($this, 'checkInstance');
        $this->onAfterAction = array($this, 'cleanUp');
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
            if (filemtime($command->lockFileName) + $this->longRunningTimeout < time()) {
                throw new CException(
                    get_class($this) .
                    '(' . $command->lockFileName . ') sleeped since ' .
                    date('r', filemtime($command->lockFileName))
                );
            } elseif (YII_DEBUG) {
                echo 'Command locked by ' . $command->lockFileName . "\n";
            }
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