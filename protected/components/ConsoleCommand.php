<?php

class ConsoleCommand extends CConsoleCommand
{
    public function log($text)
    {
        if (YII_DEBUG) {
            echo date('r') . ": " . $text . "\n";
        }
    }
}