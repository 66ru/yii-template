<?php

Yii::setPathOfAlias('lib', realpath(__DIR__ . '/../../lib'));
Yii::setPathOfAlias('vendor', realpath(__DIR__ . '/../../vendor'));

$params = require('params.php');

$components = array();
$logRoutes = array(
    array(
        'class' => 'CFileLogRoute',
        'logFile' => 'console.log',
        'levels' => 'error,warning',
    ),
);
if ($params['useSentry']) {
    $logRoutes[] = array(
        'class'=>'vendor.m8rge.yii-sentry-log.RSentryLog',
        'levels'=>'error, warning',
        'except' => 'exception.*',
        'dsn' => $params['sentryDSN'],
    );
    $components[] = array(
        'RSentryException' => array(
            'dsn' => $params['sentryDSN'],
            'class' => 'vendor.m8rge.yii-sentry-log.RSentryComponent',
        ),
    );
}

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => $params['appName'],
    'preload' => array('log', 'RSentryException'),
    'import' => array(
        'application.models.*',
        'application.models.forms.*',
        'application.components.*',
        'application.helpers.*',
        'lib.CurlHelper.*',
        'lib.ImageHelper.*',
    ),
    'modules' => require(__DIR__.'/modules.php'),
    'components' => array_merge(
        array(
            'db' => array(
                'connectionString' => 'mysql:host=' . $params['dbHost'] . ';dbname=' . $params['dbName'],
                'emulatePrepare' => true,
                'username' => $params['dbLogin'],
                'password' => $params['dbPassword'],
                'charset' => 'utf8',
            ),
            'authManager' => array(
                'class' => 'CDbAuthManager',
                'connectionID' => 'db',
            ),
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => $logRoutes,
            ),
        ),
        $components
    ),
    'params' => $params,
    'commandMap' => array(
        'migrate' => array(
            'class' => 'vendor.yiisoft.yii.framework.cli.commands.MigrateCommand',
            'migrationTable' => 'migration',
        ),
    ),
);