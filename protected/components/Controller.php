<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	
	
	

	public function redirect_message($url = false, $message = '成功', $status = 'success', $time = 2)
	{
		$this->layout = '//version2/layouts/main_pithy';
		$back_color = '#ff0000';
		if ($status == 'success') {
			$back_color = 'blue';
		}
		if (is_array($url)) {
			$route = isset($url[0]) ? $url[0] : '';
			$url = $this->createUrl($route, array_splice($url, 1));
		}
		$this->render('/public/success', array(
				'url' => $url,
				'back_color' => $back_color,
				'message' => $message,
				'status' => $status,
				'time' => $time));
	
	
	}
	
	/**
	 * 用户登录状态(没登录将跳转到登录页面) author cenyc
	 */
	public function verifyLogin($returnUrl = null)
	{
		if (Yii::app()->user->isGuest) {
			Yii::app()->user->returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
			$this->redirect_message(array('/login'), '对不起，请先登录');
			Yii::app()->end();
		}
	}
	
	/**用户身份验证
	 * @param $userType 要验证的用户身份ＩＤ　１－学生　２－老师　３－教师
	* @param null $url 不符合该身份的验证所跳转的ＵＲＬ　默认为空
	* @param string $message   跳转页面提示信息
	* @return bool 若URL为空　则返回布尔值　true 符合身份验证 flase　不符合身份验证
	* @author cenyc
	*/
	public function isIdentity($userType, $url = NULL, $message = '该用户身份无此操作权限!')
	{
		self::verifyLogin();
		if (Yii::app()->user->fdTypeID == $userType) {
			return true;
		} elseif (!empty($url)) {
			$this->redirect_message($url, $message);
			Yii::app()->end();
		} else {
			return false;
		}
	}
	
	
	/**
	 * (可以废弃，改用Yii::app->user->fdTypeID 2013年6月21日)
	 * 获取用户类型 author cenyc
	* @return int
	*/
	public function getUserType()
	{
		self::verifyLogin();
		//type过期后重新获取
		if (!Yii::app()->user->hasState('type')) {
			$model = User::model()->findByPk(Yii::app()->user->id);
			Yii::app()->user->setState("type", $model->fdTypeID);
		}
		return Yii::app()->user->fdTypeID;
	}
	
	/**
	 * 获取用户信息 有昵称返回昵称 没有这返回帐号名
	 * 必须先登录,否则会跳转到登录页面
	 * @return mixed
	 *
	 * @author cenyc
	 */
	public function getPriorName()
	{
		self::verifyLogin();
		if (!Yii::app()->user->hasState('priorName')) {
			$model = User::model()->findByPk(Yii::app()->user->id);
			Yii::app()->user->setState("priorName", $model->name());
		}
		return Yii::app()->user->priorName;
	}
	
	/**
	 * 判断用户是否已经完善资料
	 *
	 * @param null $userID 不填则为当前登录用户的ID
	 * @return bool 已经完善資料 true 还没完善資料 false
	 *
	 * @author cenyc
	 */
	public function verifyFinishData($userID = NULL)
	{
		$userID = is_numeric($userID) ? $userID : Yii::app()->user->id;
		$user = User::model()->findByPk($userID);
		if ($user && $user->fdCardID != 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * ajax表单验证
	 *
	 * @param $formName 表单名称
	 * @param $model    model名称
	 *
	 * @author cenyc
	 */
	public function performAjaxValidation($formName, $model)
	{
		if ((isset($_POST['ajax']) && $_POST['ajax'] === $formName)) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	
	}
	
	/**
	 * 将数字替换成中文数字
	 *
	 * @param int $num
	 * @return string
	 * @author yucheng
	 */
	public function num2chinanum($num)
	{
		$chinanum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
		return $chinanum[$num];
	}
	
	/**
	 * 将数字替换成字母
	 *
	 * @param int $num
	 * @return Ambigous <string>
	 * @author yucheng
	 */
	public function num2letter($num)
	{
		$letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		return $letter[$num];
	}
	
	/**
	 * 获取满足要求的主键数组
	 *
	 * @param object $model 模型
	 * @param object $criteria 条件
	 * @param string $column 字段
	 * @return array
	 * @author yucheng
	 */
	public function getIds($model, $criteria, $column)
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
	 * @author yucheng
	 */
	public function getAutoIncrement($model)
	{
		if (class_exists($model)) {
			$class = new $model;
			$tableName = $class->tableName();
			if (strpos($tableName, ".")) {
				$table = ltrim(strrchr($tableName, "."), ".");
			} else {
				$table = $tableName;
			}
			$command = $class->getDbConnection()->createCommand("SHOW TABLE STATUS LIKE '$table'");
			$res = $command->queryRow();
			$nextId = $res['Auto_increment'];
			return $nextId;
		} else {
			return 0;
		}
	}
	
	//学科
	public function getSubjectList()
	{
		return Subject::model()->findAll();
	}
	
	//学段
	public function getSchoolType()
	{
		return wkeSchoolType::model()->findAll();
	}
	
	//年级
	public function getGradeList()
	{
		return Grade::model()->findAll();
	}
	
	//版本
	public function getVersionList()
	{
		return wkeVersion::model()->findAll();
	}
	
	/**
	 * 根据路径递归创建多级文件
	 *
	 * @param $dir  文件路径
	 * @param int $mode 读写权限
	 * @return bool true 创建成功 false创建失败
	 *
	 * @author  hcf
	 *          cenyc整合
	 */
	public function mkdirs($dir, $mode = 0777)
	{
		if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
	
		if (!self::mkdirs(dirname($dir), $mode)) return FALSE;
	
		return @mkdir($dir, $mode);
	}
	
	/**
	 * 调用分页
	 *
	 * @param $data 分页数据
	 * @param $viewPath 渲染文件路径
	 * @param $tableName    主表名
	 * @param int $maxButtonCount   最多显示多少页
	 * @param array $viewData
	 * @return mixed
	 *
	 * @author cenyc
	 */
	public function pageWidget($data, $viewPath, $tableName, $viewData = array(), $maxButtonCount = 5)
	{
		return $this->widget('application.library.WeikeListView', array(
				'dataProvider' => $data,
				'itemView' => $viewPath,
				'template' => '{items}{pager}{summary}',
				'summaryText' => '({page}/{pages}){jump}',
				'summaryCssClass' => 'summary_container',
				'viewData' => $viewData,
				//'summaryText'=>'',
				'model' => $tableName,
				'emptyText' => '<div class="tc mt10">暂无内容!</div>',
				'pager' => array(
						'header' => '',
						'maxButtonCount' => $maxButtonCount,
						'prevPageLabel' => '上一页',
						'nextPageLabel' => '下一页',
						'cssFile' => Yii::app()->request->baseUrl . '/css/page.css',
				),
		));
	}
	
	/**
	 * 表情字符串转化成图片
	 * @param $strText表情字符串
	 * @author Cyrus
	 */
	public function portraitInto($strText)
	{
		if (preg_match_all("/\[([^]]+)]/is", $strText, $matches)) {
			foreach ($matches[1] as $match) {
				$faces = Yii::app()->params['face'];
				if (array_key_exists($match, $faces)) {
					$strText = str_replace('[' . $match . ']', "<img alt='" . $match . "' src='" . Yii::app()->request->baseUrl . $faces[$match] . "'/>", $strText);
				}
			}
		}
		return $strText;
	}
}