<?php

Yii::setPathOfAlias('lib', realpath(dirname(__FILE__).'/../../lib'));

$params = require('params.php');
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host='.$params['dbHost'].';dbname='.$params['dbName'],
			'emulatePrepare' => true,
			'username' => $params['dbLogin'],
			'password' => $params['dbPassword'],
			'charset' => 'utf8',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),

	'params'=> $params,
);