<?php
/**
 * yii中cookie操作类
 *
 * @author Yucheng
 *
 */
class CookieUtils
{
	/**
	 * 设置cookie
	 *
	 * @param string $name cookie的名称
	 * @param mix $value cookie的值
	 * @param integer $expire cookie的有效期
	 */
	public static function setCookie($name, $value, $expire=0)
	{
		$cookie = new CHttpCookie($name, $value);
		$cookie->expire = $expire;
		Yii::app()->request->cookies[$name] = $cookie;
	}

	/**
	 * 获取cookie的值
	 *
	 * @param string $name
	 */
	public static function getCookie($name)
	{
		$cookie = Yii::app()->request->getCookies();
		return $cookie[$name]->value;
	}

	/**
	 * 判断cookie是否存在
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function existCookie($name)
	{
		if (self::getCookie($name)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 销毁cookie
	 *
	 * @param string|array $name
	 */
	public static function destoryCookie($name)
	{
		$cookie = Yii::app()->request->getCookies();
		if (is_string($name)) {
		    unset($cookie[$name]);
		} elseif (is_array($name)) {
		    foreach ($name as $n) {
		        unset($cookie[$n]);
		    }
		}
	}
}