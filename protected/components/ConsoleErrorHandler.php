<?php

class ConsoleErrorHandler extends CErrorHandler
{
    public function handle($event)
    {
        if (YII_DEBUG) {
            parent::handle($event);
        }
    }
}