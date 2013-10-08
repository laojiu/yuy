<?php
class DefaultController extends Controller {
	public function actionIndex() {
		$user = Yii::app ()->user;
		$this->redirect ($user->isGuest?
				$this->createUrl('/user/login')
				 :$this->createUrl('/back/index'));
	}
	
	public function actionXX(){
		echo 'sdf';
	}
}