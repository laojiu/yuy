<?php
/**
 * 验证类工具
 * Created by cenyc.
 * Date: 13-8-6
 * Time: 下午6:03
 * To change this template use File | Settings | File Templates.
 */
class VerifyUtils{
    //正则验证 用户名 邮箱 电话 author cenyc 2013年5月20日
    public function checkLoginName($name){
        return preg_match('/^[a-zA-Z0-9_]{4,16}$/u',$name);
    }
    public 	function checkEmail($email)
    {
        return preg_match('/^[_.0-9a-z-a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{1,4}$/',$email);
    }
    public function checkMobile($str){
        return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $str);
    }
    //正则验证 end

    /**
     * 邮箱,手机号码,帐号 认证
     *
     * @param $account  帐号
     * @return string
     *
     * @author cenyc
     */
    public function checkAccount($account){
        $userService = new UserService();
        if(is_numeric($account)){
            if(self::checkMobile($account)){
                if($userService->isExistPhoneNumber($account)){
                    return 'exist';
                }
                return 'successMobile';
            }else{
                return 'mobile_Fail';
            }
        }
        if(is_string($account)&&stristr($account,'@')){
            if(self::checkEmail($account)){
                if($userService->isExistEmail($account)){
                    return 'exist';
                }
                return 'successEmail';
            }else{
                return 'email_Fail';
            }
        }
        if(self::checkLoginName($account)){
            if($userService->isExistAccount($account)){
                return 'exist';
            }
            return 'successLoginName';
        }else{
            return 'loginName_Fail';
        }
    }
}