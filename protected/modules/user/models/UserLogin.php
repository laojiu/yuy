<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserLogin extends CFormModel {
	public $username;
	public $password;
	public $rememberMe;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array (
				// username and password are required
				array (
						'username, password',
						'required' 
				),
				// rememberMe needs to be a boolean
				array (
						'rememberMe',
						'boolean' 
				),
				// password needs to be authenticated
				array (
						'password',
						'authenticate' 
				) 
		);
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array (
				'rememberMe' => UserModule::t ( "记住账号" ),
				'username' => UserModule::t ( "用户名或邮箱" ),
				'password' => UserModule::t ( "密码" ) 
		);
	}
	
	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params) {
		if (! $this->hasErrors ()) 		// we only want to authenticate when no input errors
		{
			$identity = new UserIdentity ( $this->username, $this->password );
			$identity->authenticate ();
			switch ($identity->errorCode) {
				case UserIdentity::ERROR_NONE :
					$duration = $this->rememberMe ? Yii::app ()->controller->module->rememberMeTime : 0;
					Yii::app ()->user->login ( $identity, $duration );
					break;
				case UserIdentity::ERROR_EMAIL_INVALID :
					$this->addError ( "username", UserModule::t ( "邮箱错误." ) );
					break;
				case UserIdentity::ERROR_USERNAME_INVALID :
					$this->addError ( "username", UserModule::t ( "用户名错误." ) );
					break;
				case UserIdentity::ERROR_STATUS_NOTACTIV :
					$this->addError ( "status", UserModule::t ( "账号没有激活." ) );
					break;
				case UserIdentity::ERROR_STATUS_BAN :
					$this->addError ( "status", UserModule::t ( "账号被锁定." ) );
					break;
				case UserIdentity::ERROR_PASSWORD_INVALID :
					$this->addError ( "password", UserModule::t ( "密码错误." ) );
					break;
			}
		}
	}
}
