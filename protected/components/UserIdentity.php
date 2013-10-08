<?php

require_once '../models/services/User.php';

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	
	
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		
		$username=strtolower($this->username);
		//输入类型判断
		if($this->checkEmail($username)){
			$type = 'email';
		}else if($this->checkMobile($username)){
			$type = 'mobile';
		}else{
			$type = 'username';
		}

		if(IS_SYNC && $type == 'username'){//如果开启同步登陆并且使用用户名登陆
			Yii::import('application.vendors.*');
			include_once 'ucenter.php';
			//查找当前数据库是否有当前用户
			$user=User::model()->find('LOWER(fdLogin)=?',array($username));
			if($user){
				if ($user->validatePassword($this->password)) {
					$this->_id=$user->id;
					$this->username=$user->fdLogin;
					$this->errorCode=self::ERROR_NONE;
				}else{
					$this->errorCode=self::ERROR_PASSWORD_INVALID;
				}
			}else{
				//判断是否存在ucenter记录
				list($uid, $username, $password, $email) = uc_user_login($this->username, $this->password);
				if($uid > 0){//uc中存在此用户名
					$user = new User();
					$user->fdLogin = $username;
					$user->fdPassword = $this->password;
					$user->fdTypeID = Yii::app()->params['TEACHER_TYPE_ID'];
					$user->save();
					//echo '<pre>';print_r($user->attributes);
					$user->refresh();
					$this->_id=$user->id;
					$this->username=$user->fdLogin;
					$this->errorCode=self::ERROR_NONE;
				}else{
					$this->errorCode=self::ERROR_USERNAME_INVALID;
				}
			}


		}else{
			//判断用户类型
			if($type == 'email'){//login by email
				$email = Email::model()->find('LOWER(fdEmail)=?',array($username));
				if(!empty($email)){
					$user = User::model()->findByPk($email->fdUserID);
				}
			}elseif ($type == 'mobile'){// login by mobile
				$phone = Phone::model()->find('LOWER(fdPhone)=?',array($username));
				if(!empty($phone)){
					$user = User::model()->findByPk($phone->fdUserID);
				}
			}else {// login by username
				$user=User::model()->find('LOWER(fdLogin)=?',array($username));
			}

			if ($user===null) {
				$this->errorCode=self::ERROR_USERNAME_INVALID;
			}else {
				if (!$user->validatePassword($this->password)) {
					$this->errorCode=self::ERROR_PASSWORD_INVALID;
				}else {
					$this->_id=$user->id;
					$this->username=$user->fdLogin;
					$this->errorCode=self::ERROR_NONE;
				}
			}
		}


		$this->setPersistentStates($user->attributes);
		return $this->errorCode===self::ERROR_NONE;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	//正则验证 用户名 邮箱 电话 author cenyc 2013年5月20日
	public function checkLoginName($name){
		return preg_match('/^[a-zA-Z0-9_]{4,16}$/u',$name);
	}
	public 	function checkEmail($email)
	{
		return preg_match("/^[0-9a-z][a-z0-9\._-]{1,}@[a-z0-9-]{1,}[a-z0-9]\.[a-z\.]{1,}[a-z]$/", $email);
	}
	public function checkMobile($str){
		return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $str);
	}
	///~正则验证 end
}