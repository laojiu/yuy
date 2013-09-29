<?php
/**
 * 公用工具类
 *
 * Created by cenyc.
 * Date: 13-8-16
 * Time: 下午2:59
 * To change this template use File | Settings | File Templates.
 */
class CommonUtils extends ClassFactory{

    public static function factory ($className = __CLASS__){
        return parent::factory($className);
    }

    /**根据学段和入学年份计算年级 @author cenyc
     * @param $schoolTypeID 学段ID
     * @param $year 入学年份
     * @return string   返回值是年级
     */
    public function getGrade($schoolTypeID,$year){
        $j = date ( 'n' ) > 9 ? 1 : 0; // 如果大于九月的话当前年级+1
        if ($schoolTypeID == 1) {
            $gradeID = date ( 'Y' ) - $year + $j;
        } elseif ($schoolTypeID == 2) {
            $gradeID = date ( 'Y' ) - $year + $j + 6;
        } elseif ($schoolTypeID == 3) {
            $gradeID = date ( 'Y' ) - $year + $j + 9;
        }
        return  $gradeID;
    }
    /**
     * 获取地区名字数组 @author cenyc
     * @param unknown $id 地区ID
     * @return multitype:NULL array
     */
    public function getAreaName($id){
        static $data;
        $area = Area::model()->findByPk($id);
        if(!empty($area)){
            $data[] = array('id'=>$area->id,'fdName' => $area->fdName);
            $this->getAreaName($area->fdParentID);
        }
        if(empty($data)){
            $data = array();
        }
        return array_reverse($data);
    }
}