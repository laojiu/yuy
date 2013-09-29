<?php


class RequestUtils {

	/**
	 * 获取请求$key对应的值,如果没有则返回0长度字符串('').
	 * 该方法保证永不返回空值null. 并将'undefined'转换成''.
	 *
	 * FIXME
	 * <li>如果获得的值为0,不可通过if(getNormalRequest())来判断.
	 *
	 * @param string $key
	 * @return string 如果请求存在.否则返回0长度字符串.
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public static function getNormalRequest($key){
		if(!$key){
			return '';
		}
		$request=Yii::app()->request->getParam($key);
		if(!isset($request) || $request=='undefined' ){
			$request='';
		}
		return $request;
	}


	/**
	 * 判断$request是否为'非法'值.
	 * 这里的不合法指:
	 * <li>null
	 * <li>''
	 * <li>'undefined'
	 *
	 * @param string $request
	 * @return boolean true 如果$request是'非法(null)'的;否则返回false.
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public static function isEmptyRequest($request){
		foreach(func_get_args() as $request){
			if( !(!isset($request) || $request=='' || $request=='undefined')){
				return false;
			}
		}
		return true;
	}



	/**
	 * 将url请求参数转换成数组
	 *
	 * @author Brook
	 * @since weike. Ver 2.0
	 */
	public static function requestToArray(){
		$result=array();

		$parameters=  parse_url(Yii::app()->request->url);
		$arr =explode('&', $parameters['query']);

		foreach ($arr as $item){
			$param = explode('=', $item);
			$result[$param[0]]=$param[1];
		}
		unset($result['r']);
		return $result;
	}

}

?>