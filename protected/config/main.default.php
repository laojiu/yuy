<?php

$host='127.0.0.1';
$dbu='root';
$dbp='root';

define('IS_SYNC', false);
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

return CMap::mergeArray(
		require(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../smoothy/protected/config/main.php')
		, array(
// 		'smoothyPath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'../../smoothy',
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',
	'theme'=>'classic',
	'language'=>'zh_cn',//中文提示

	// preloading 'log' component
	'preload'=>array('log'),

	'import'=>array(
			'application.models.yuy.*',
			'application.models.form.*',
			'application.components.*',
	),
	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'11',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		'back'=>array(
			'class'=>'application.modules.back.BackModule'
		),
		'user'=>array(
				'tableUsers' => 'wksvc.tbAuthUsers',
				'tableProfiles' => 'wksvc.tbAuthProfiles',
				'tableProfileFields' => 'wksvc.tbAuthProfilesFields',
				# encrypting method (php hash function)
				'hash' => 'md5',

				# send activation email
				'sendActivationMail' => true,

				# allow access for non-activated users
				'loginNotActiv' => false,

				# activate user on registration (only sendActivationMail = false)
				'activeAfterRegister' => false,

				# automatically login from registration
				'autoLogin' => true,

				# registration path
				'registrationUrl' => array('/user/registration'),

				# recovery password path
				'recoveryUrl' => array('/user/recovery'),

				# login form path
				'loginUrl' => array('/user/login'),

				# page after login
				'returnUrl' => array('/user/profile'),

				# page after logout
				'returnLogoutUrl' => array('/user/login'),
		),

		//Modules Rights
		'rights'=>array(
				'superuserName'=>'Admin', // Name of the role with super user privileges.
				'authenticatedName'=>'Authenticated',  // Name of the authenticated user role.
				'userIdColumn'=>'id', // Name of the user id column in the database.
				'userNameColumn'=>'username',  // Name of the user name column in the database.
				'enableBizRule'=>true,  // Whether to enable authorization item business rules.
				'enableBizRuleData'=>true,   // Whether to enable data for business rules.
				'displayDescription'=>true,  // Whether to use item description instead of name.
				'flashSuccessKey'=>'RightsSuccess', // Key to use for setting success flash messages.
				'flashErrorKey'=>'RightsError', // Key to use for setting error flash messages.

				'baseUrl'=>'/rights', // Base URL for Rights. Change if module is nested.
				'layout'=>'rights.views.layouts.main',  // Layout to use for displaying Rights.
				'appLayout'=>'', // Application layout.
				//               'cssFile'=>'rights.css', // Style sheet file to use for Rights.
				'install'=>false,  // Whether to enable installer.
				'debug'=>false,
		),
	),
	'wrappers'=>array(
	// 			'ContentService'=>array(
			// 					'class'=>'ContentWrapper',
			// 					'decorations'=>array(
					// 							'DasaiContentDecoration',
					// 					)
			// 			),
	// 			'AOPWorker'=>array(//for test
			// 				'class'=>'DiagnosisWrapper',
			// 				'decorations'=>array(
					// 					'DasaiDiagnosisDecoration'
					// 				),
			// 			),
	),
	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',


			),
			'urlSuffix'=>'.html',
		),
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
				'enableProfiling'=>true,
		),
		'service'=>array(
				'class' => 'CDbConnection',
				'connectionString' => "mysql:host=$host;dbname=yuysvc",
				'emulatePrepare' => true,
				'username' => $dbu,
				'password' => $dbp,
				'charset' => 'utf8',
				'enableProfiling'=>true,
		),
		'address'=>array(
				'class' => 'CDbConnection',
				'connectionString' => "mysql:host=$host;dbname=yuyads",
				'emulatePrepare' => true,
				'username' => $dbu,
				'password' => $dbp,
				'charset' => 'utf8',
				'enableProfiling'=>true,
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