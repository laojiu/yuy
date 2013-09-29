<?php
/**
 * Created by cenyc.
 * Date: 13-8-5
 * Time: 下午4:33
 * To change this template use File | Settings | File Templates.
 */
class EmailUtils{
    /**
     * 邮件发送函数
     *
     * @param $email    接收方邮件地址
     * @param $content  邮件正文
     * @param $subject  邮件标题
     * @return bool     true-发送成功   false-发送失败
     *
     * @author cenyc
     */
    public function sendEmail($email,$content,$subject){
        date_default_timezone_set("PRC");
        $mail = Yii::createComponent('application.extensions.mailer.EMailer');
        $mail->IsSMTP();	// set mailer to use SMTP
        $mail->Port = Yii::app()->params->EMAIL_PORT;
        $mail->CharSet = Yii::app()->params->EMAIL_CHARSET;
        $mail->Host = Yii::app()->params->EMAIL_SMTP;	 // 指定主和备份服务器
        $mail->SMTPAuth = Yii::app()->params->EMAIL_SMTPAUTH;     // 启动SMTP验证
        $mail->Username = Yii::app()->params->EMAIL_ADDRESS;  // SMTP 用户名
        $mail->Password = Yii::app()->params->EMAIL_PWD; // SMTP 密码
        $mail->From = Yii::app()->params->EMAIL_ADDRESS;	// 发件人邮箱地址
        $mail->FromName = Yii::app()->params->EMAIL_NAME;			//发件人姓名
        $mail->AddAddress($email);       // 收件人地址和姓名
        $mail->Subject = $subject;
        $mail->MsgHTML($content);

        return $mail->Send();
    }
}