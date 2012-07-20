<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../lib/yii/framework/yii.php';
require_once($yii);

$config=require(dirname(__FILE__).'/../protected/config/main.php');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', $config['params']['yiiDebug']);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

Yii::createWebApplication($config)->run();
