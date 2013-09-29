<?php


class CDbActiveUtils {

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
				$array[]=$entry->__get($attribute);
			}
		}
		return $array;
	}
	
}

?>