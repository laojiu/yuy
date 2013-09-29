<?php



/**
 *  微课应用入口
 *
 * @version 130710 实现自动import utils,daos和domain等文件夹.   #setWkImport
 * @author Brook
 * @since weike. Ver 1.0
 */
class YUYApplication extends CWebApplication {

	private $wrappers;

	public $tempUser;//XXX

	/**
	 * 装载配置 wkImport
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public function setWkImport($_import){
		$base = Yii::getPathOfAlias('application');
		if(substr($base, -1)!= PATH_SEPARATOR){
			$base.=DIRECTORY_SEPARATOR;
		}
		foreach ($_import as $package){
			$this->importDirectory($base.$package);
		}
	}
	private function importDirectory($dir){
		$toImport = str_replace( Yii::getPathOfAlias('application'), 'application', $dir);
		Yii::import(str_replace(DIRECTORY_SEPARATOR,'.',$toImport.'.*'));
		
		$children = opendir($dir);
		while (($file=readdir($children))!=false){
			$child = $dir.DIRECTORY_SEPARATOR.$file;
			if($file == '.' || $file == '..'){
				continue;
			}
			if(is_dir($child)){
				$this->importDirectory($child);
			}
		}
	}



	public function setWrappers($wrappers){
		$this->wrappers = $wrappers;
	}
	public function getWrappers(){
		return $this->wrappers;
	}

	/**
	 * 验证用户是否已经认证,则跳转到登陆页面.
	 *
	 * @param string $returnUrl
	 * 	登录之后回调的地址,如果该参数没有指定,则默认为当前访问的URL:Yii::app()->request->url
	 *
	 * @author Brook update for supporting ReturnUrl and Logging. 2013.06.20.
	 * @author cenyc created.
	 * @see LoginController#actionIndex
	 */
	public function verifyLogin($returnUrl=null){
		if(Yii::app()->user->isGuest){
			if(YII_DEBUG){
				$backtrace = debug_backtrace();
				$backtrace =$backtrace[1];
				Yii::log('unexpected AuthenticationException:'.Yii::app()->request->getUrl(),
					CLogger::LEVEL_WARNING,$backtrace['file'].'#'.$backtrace['function']);
			}
			Yii::app()->user->returnUrl=$returnUrl?$returnUrl:Yii::app()->request->url;
// 			Yii::app()->request->redirect(Yii::app()->getBaseUrl().'/?=login');
 			Yii::app()->controller->redirect_message ( Yii::app()->createUrl('login'), '你还没有登录或者登陆超时','error' );
 			Yii::app()->end();
		}
	}






	//// utils for session.

	/**
	 * 返回$holder默认对应的session.
	 *
	 * @uses
	 * 注意:
	 *   <li>全局session值不能使用该函数获取;
	 *   <li>调用该函数遵循'谁创建,谁销毁'的原则;
	 *
	 * @todo test create and destory
	 *
	 * @see Controller#destoryDefaultSession
	 *
	 * @param boolean $needsCreate 若session中找不到对应的值,是否在session中创建对应的值.
	 * @param mutiltype $defaultValue 默认值. 若该值为空,则返回一个空数组
	 * @param mutiltype $callback 用于创建默认值的回调函数的名称或是回调数组
	 * @param Object $holder
	 *
	 * @return array 或 $defaultValue. 返回一个数组,如果默认session已经创建或创建成功. 否则,返回错误.
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public function getDefaultSession($needsCreate=true,$holder=__CLASS__,$callback=null,$parameter=null){
		$mySessionMap = Yii::app()->session[$this->getDefaultSessionKey($holder)];
		if($needsCreate || (!isset($mySessionMap) && $needsCreate)){
			$this->destoryDefaultSession($holder);
			if($callback){
				$defaultValue=call_user_func($callback,$parameter);
			}
			$mySessionMap =$defaultValue?$defaultValue:array();
			Yii::app()->session[$this->getDefaultSessionKey($holder)] = $mySessionMap;
		}
		return $mySessionMap;
	}



	/**
	 * 销毁控制器默认对应的session
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public function destoryDefaultSession($holder=__CLASS__){
		unset(Yii::app()->session[$this->getDefaultSessionKey($holder)]);
	}

	public function getDefaultSessionKey($holder){
		$key = is_string($holder)?$holder:get_class($holder);
		return '_c'.$key;
	}

	public function __get($name){//FIXME XXX
		if(isset($_GET['r'])){
			$r = $_GET['r'];//$r = Yii::app()->request->getparam('r');
			if(is_int(strpos($r,'vsapi'))){
				if($name =='user'){
					$sessionKey = $_GET['session'];
					$userInfo = $this->checkUser($sessionKey);
					$weikeUser = new WeikeUser();
					if($userInfo){
						$weikeUser->id = $userInfo['id'];
						$weikeUser->type = $userInfo['fdTypeID'];
						$weikeUser->isGuest = false;
					}else{
						$weikeUser->isGuest = true;
					}
					$this->tempUser = $weikeUser;
					return $this->tempUser;
				}
			}
		}
		return parent::__get($name);
	}

	/**
	 * 根据session_key判断用户是否登录
	 * 如果用户已经登陆则返回用户信息
	 * 否则返回false
	 * @param sessionKey string session值
	 * @param duration int 过期时间单位是分钟
	 * @author john
	 * @return array
	 */
	private function checkUser($sessionKey,$duration = 15){
		$criteria = new CDbCriteria();
		$criteria->condition = "fdStatus = 1 AND fdUserID != 0 AND fdSession = '".$sessionKey . "' AND DATE_SUB(NOW(),INTERVAL ".$duration." MINUTE) < fdActive";
		$session = svcSession::model()->find($criteria);
		if($session){
			$user = User::model()->findByPk($session['fdUserID']);
			if($user){
				return $user;
			}
		}
		return false;
	}

// 	public function getUser(){exit();
// 		$r = Yii::app()->request->getparam('r');
// 		if(strpos($r,'vsapi')){
// 			echo 'f';
// 		}else{
// 			return parent::getUser();
// 		}
// 	}


}

class Weike{
	/**
	 * @return WeikeApplication
	 *
	 * @author Brook
	 * @since weike. Ver 2.0
	 */
	public static function app(){
		return Yii::app();
	}

}

class WeikeUser {
	/**
	 * @var int
	 * 用户id
	 */
	public $id;

	/**
	 * @var int
	 * 用户类型
	 */
	public $type;

	/**
	 * @var bool
	 * 是否为游客
	 */
	public $isGuest;


	public function hasState($state){
		return $state == $this->type;
	}


	public function setState($state){
		$this->type= $state;
	}


}
?>