<?php


class CDbUtils {

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
	 */
	public static function columnToArray($data,$attribute){
		$array = array();
		if(!empty($data)){
			foreach ($data as $entry){
				$array[]=$entry->$attribute;
			}
		}
		return $array;
	}


	/**
	 * 为指定的CDbCriteria添加条件.
	 * 如果$value为null,则不做任何操作;
	 * 如果$value为数组,则执行$criteria->addInCondition();
	 * 否则,则执行$criteria->addCondition();
	 *
	 * @param CDbCriteria $criteria
	 * @param string $name 要查询的列名
	 * @param multiple $value 查询的值
	 * @param string $operator 操作类型,可以为'AND'或'OR';
	 *
	 * @author Brook
	 * @since weike. Ver 1.0
	 */
	public static function addCondition($criteria,$name,$value,$conditionOP='=:',$operator='AND'){
		if(!$value){
			return;
		}
		if(is_array($value) ){
			$count = count($value);
			if($count>1){
				$criteria->addInCondition($name,$value,$operator);
				return;
			}else if($count==1){
				$value=end($value);
			}else {
				return;
			}
		}

		$criteria->addCondition($name.$conditionOP.str_replace('.', '', $name),$operator);
		$criteria->params[str_replace('.', '', $name)]=$value;
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
	 * 将数字替换成中文数字
	 *
	 * @param int $num
	 * @return string
	 * @author yucheng
	 */
	public static function num2chinanum($num)
	{
	    $chinanum =  array('零','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五');
	    return $chinanum[$num];
	}
	/**
	 * 将数字替换成字母
	 *
	 * @param int $num
	 * @return Ambigous <string>
	 * @author yucheng
	 */
	public static function num2letter($num)
	{
	    $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	    return $letter[$num];
	}

}

?>