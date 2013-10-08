<?php
date_default_timezone_set("Asia/Chongqing");

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
$app=dirname(__FILE__).'/protected/components/YUYApplication.php';
$smoothyapp=dirname(__FILE__).DIRECTORY_SEPARATOR.'../smoothy/protected/components/SApplication.php';
$config=dirname(__FILE__).'/protected/config/main.php';

require_once($yii);
require_once($smoothyapp);
require_once($app);

$app = new YUYApplication($config);
$app->run();
