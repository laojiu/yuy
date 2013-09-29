<?php

class ArrayUtils {

	public static function is_int_array($target){
		if(!is_array($target)){
			return false;
		}
		foreach($target as $key=>$value){
			if(!$value || !is_int($value)){
				return  false;
			}
		}
		return true;
	}


	/**
	 * 根据$key获取$array对应的值,如果找不到则返回null
	 *
	 * 该函数用于去除页面<code><p>PHP notice<p>Undefined index:</code>
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public static function getValue($key,$array){
		if(!$key){
			return null;
		}
		if(array_key_exists($key, $array)){
			return $array[$key];
		}
		return null;
	}


}

?>