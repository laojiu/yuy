<?php

/**
 *
 * @deprecated 请勿使用该类,将该类的函数移至对应的工具类中.
 */
class Utils {

	//////////////// CDB utils

	/**
	 * 在对象数组$data中,根据对象的某一个属性$attribute,
	 * 把$data中所有$attribute保存到一个数组并返回.
	 *
	 * @param array[object] $data 对象数组
	 * @param string $attribute 属性名称
	 * @return array
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 *
	 * @deprecated 已经移动到类CDbUtils
	 */
	public static function columnToArray($data,$attribute){
		$array = array();
		if(!empty($data)){
			foreach ($data as $entry){
				$array[]=$entry->__get($attribute);
			}
		}
		return $array;
	}


	////////////////  request utils

	/**
	 * 获取请求$key对应的值,如果没有则返回0长度字符串('').
	 * 该方法保证永不返回空值null. 并将'undefined'转换成''.
	 *
	 * @param string $key
	 * @return string 如果请求存在.否则返回0长度字符串.
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public static function getNormalRequest($key){//针对js
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
	 * 判断$request是否为'合法'值.
	 * 这里的不合法指:
	 * <li>null
	 * <li>''
	 * <li>'undefined'
	 *
	 * @param string $request
	 * @return boolean true 如果$request是'合法'的,否则返回false.
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public static function isEmptyRequest($request){
		return !isset($request) || $request=='' || $request=='undefined';
	}

	/**
	 * 将数字替换成中文数字
	 *
	 * @param int $num
	 * @return string
	 *
	 * @author yucheng
	 */
	public function num2chinanum($num)
	{
		$chinanum =  array('零','一','二','三','四','五','六','七','八','九','十');
		return $chinanum[$num];
	}
	/**
	 * 将数字替换成字母
	 *
	 * @param int $num
	 * @return Ambigous <string>
	 *
	 * @author yucheng
	 */
	public function num2letter($num)
	{
		$letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		return $letter[$num];
	}

	/**
	 * 获取满足要求的主键数组
	 *
	 * @param object $model 模型
	 * @param object $criteria 条件
	 * @param string $column 字段
	 * @return array
	 *
	 * @author Yucheng
	 */
	public static function getIds($model, $criteria, $column)
	{
		if (class_exists($model)) {
			$class = new $model;
			$data = $class->findAll($criteria);
			$ids = array();
			if (!empty($data)) {
				foreach ($data as $val) {
					array_push($ids, $val->$column);
				}
			}
			return $ids;
		}
	}

	/**
	 * 获取mysql数据库中下一个自增主键的值
	 *
	 * @param object $model
	 * @return number
	 *
	 * @author Yucheng
	 */
	public static function getAutoIncrement($model){
		if (class_exists($model)) {
			$class = new $model;
			$tableName = $class->tableName();
			if (strpos($tableName, ".")) {
				$table = ltrim(strrchr($tableName,"."),".");
			} else {
				$table = $tableName;
			}
			$command = $class->getDbConnection()->createCommand("SHOW TABLE STATUS LIKE '$table'");
			$res=$command->queryRow();
			$nextId = $res['Auto_increment'];
			return $nextId;
		} else {
			return 0;
		}
	}

	/**
	* 获取文件扩展名
	* @author chengx
	* @param $fileName 文件路径
	* @return string
	*/
	public static function extension($fileName)
	{
		return substr($fileName, strrpos($fileName, '.')+1, strlen($fileName) - strrpos($fileName, '.'));
	}
}

?>