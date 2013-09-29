<?php


class Debugger {

	/**
	 * 打印一切垃圾
	 * dumping everything...
	 *
	 * 中文不乱码
	 * 数组格式化显示
	 * 数组调用print_r打印
	 * CActiveRecord 打印attributes
	 * null值打印"null"
	 *
	 * @author Brook
	 * @since weike. Ver 2.0
	 */
	public static function print_r($obj,$indent=' '){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo '<pre>';
		if(is_array($obj)){
			echo $indent."array(";
			foreach ($obj as $item){
// 				if($key)echo "\r\n".$indent.'    '.$key.'=>';
				self::print_r($item,$indent.'    ');
			}
			echo "$indent\r\n)";
		}else{
			if(!obj){
				print 'null';
				return;
			}

			$str='';
			if(is_object($obj)){
				$str = $indent.get_class($obj);
			}
			if($obj instanceof CActiveRecord){
				$str .= " attributes:{\r\n";
				$str .= print_r($obj->attributes,true);
				$str .= "}///   end attributes";
			}else{
				$str = print_r($obj,true);
			}

			$str=str_replace("\n", "\r\n".$indent.'    ', $str);
			echo($str);
		}
	}

	public static function print_exit($obj){
		self::print_r($obj);
		exit;
	}


}

?>
