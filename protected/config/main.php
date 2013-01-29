<?php

Yii::setPathOfAlias('lib', realpath(__DIR__ . '/../../lib'));
Yii::setPathOfAlias('bootstrap', realpath(__DIR__ . '/../../lib/bootstrap'));

$params = require('params.php');
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => $params['appName'],
    'language' => 'ru',

    'preload' => array('log'),

    'import' => array(
        'application.models.*',
        'application.models.forms.*',
        'application.components.*',
        'application.helpers.*',
        'lib.CurlHelper.*',
    ),

    'modules' => array(// uncomment the following to enable the Gii tool
        /*
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'Enter Your Password Here',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
        ),
        */
    ),

    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'loginUrl' => array('site/login'),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'urlSuffix' => '/',
            'showScriptName' => false,
            'rules' => array(
                '/' => 'site/index',
                'admin/' => 'admin/admin/index',
                'admin/<controller:\w+>/' => 'admin/admin<controller>',
                'admin/<controller:\w+>/<action:\w+>/' => 'admin/admin<controller>/<action>',
                '<_a:(login|logout|error)>/' => 'site/<_a>',
//				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
//				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
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
        'fs' => array(
            'class' => 'FileSystem',
            'nestedFolders' => 1,
        ),
        'viewRenderer' => array(
            'class' => 'lib.twig-renderer.ETwigViewRenderer',
            'twigPathAlias' => 'lib.twig.lib.Twig',
            'options' => array(
                'autoescape' => true,
            ),
            'functions' => array(
                'widget' => array(
                    0 => 'TwigFunctions::widget',
                    1 => array('is_safe' => array('html')),
                ),
                'const' => 'TwigFunctions::constGet',
                'static' => 'TwigFunctions::staticCall',
            ),
        ),
        'bootstrap' => array(
            'class' => 'lib.bootstrap.components.Bootstrap',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'image' => array(
            'class' => 'ext.image.CImageComponent',
            'driver' => $params['imageDriver'],
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*array(
                    'class'=>'CWebLogRoute',
                ),*/
            ),
        ),
    ),

    'params' => array_merge($params, array(
        'md5Salt' => 'ThisIsMymd5Salt(*&^%$#',
    )),
);