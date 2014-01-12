<?php
// fix for fcgi
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii/framework/yii.php');

$config = require(__DIR__ . '/config/console.php');
$app = Yii::createConsoleApplication($config);
$app->commandRunner->addCommands(__DIR__ . '/commands');
foreach ($config['modules'] as $module) {
    $app->commandRunner->addCommands(__DIR__ . '/modules/' . $module . '/commands');
}

$env = @getenv('YII_CONSOLE_COMMANDS');
if (!empty($env)) {
    $app->commandRunner->addCommands($env);
}

$app->run();