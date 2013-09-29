<?php

$host='127.0.0.1';
$dbu='root';
$dbp='toor';

define('IS_SYNC', false);


return CMap::mergeArray(
		require(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../smoothy/protected/config/main.php')
		, array(
// 		'smoothyPath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'../../smoothy',
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	'theme'=>'classic',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'wkImport'=>array('utils','hybrids','services','models'),
	'import'=>array(
		'application.components.*',
		'application.hybrids.*',
	),
	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'11',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),

	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
// 		'db'=>array(
// 			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
// 		),
		'db'=>array(
				'class' => 'CDbConnection',
				'connectionString' => "mysql:host=$host;dbname=yuyctn",
				'emulatePrepare' => true,
				'username' => $dbu,
				'password' => $dbp,
				'charset' => 'utf8',
		),
		'service'=>array(
				'class' => 'CDbConnection',
				'connectionString' => "mysql:host=$host;dbname=yuysvc",
				'emulatePrepare' => true,
				'username' => $dbu,
				'password' => $dbp,
				'charset' => 'utf8',
		),
		'address'=>array(
				'class' => 'CDbConnection',
				'connectionString' => "mysql:host=$host;dbname=yuyads",
				'emulatePrepare' => true,
				'username' => $dbu,
				'password' => $dbp,
				'charset' => 'utf8',
		),
		'yuy'=>array(
				'class' => 'CDbConnection',
				'connectionString' => "mysql:host=$host;dbname=yuyuu",
				'emulatePrepare' => true,
				'username' => $dbu,
				'password' => $dbp,
				'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				array(
					'class'=>'CWebLogRoute',
				),

			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
));