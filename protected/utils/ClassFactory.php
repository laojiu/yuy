<?php
class ClassFactory {

	private static $_models = array ();

	public static function factory($className = __CLASS__) {
		if (isset ( self::$_models [$className] ))
			return self::$_models [$className];
		else {
			$model = self::$_models [$className] = new $className ( null );
			return $model;
		}
	}
}