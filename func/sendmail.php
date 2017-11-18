<?php
/**
 * Created by PhpStorm.
 * User: hawkenliu
 * Date: 2017/11/12
 * Time: 17:06
 */
require_once APP_PATH."/lib/email.class.php";
// header("Content-type: text/html; charset=utf-8");
/**
 * @param $smtpemailto 收件人邮箱
 * @param $mailtitle    邮件地址
 * @param $mailcontent  邮箱内容
 * @param string $mailtype
 * @return bool
 */
function send_mail($smtpemailto, $mailtitle, $mailcontent, $mailtype='HTML'){
    //******************** 配置信息 ********************************
    $smtpserver = "smtp.ym.163.com";//SMTP服务器
    $smtpserverport =25;//SMTP服务器端口
    $smtpusermail = "wecqupt@liuhao.bid";//SMTP服务器的用户邮箱
    // $smtpemailto = $_POST['toemail'];//发送给谁
    $smtpuser = "wecqupt@liuhao.bid";//SMTP服务器的用户帐号
    $smtppass = "qq470401911";//SMTP服务器的用户密码
    // $mailtitle = $_POST['title'];//邮件主题
    // $mailcontent = "<h1>".$_POST['content']."</h1>";//邮件内容
    // $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
    //************************ 配置信息 ****************************
    $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    $smtp->debug = false;//是否显示发送的调试信
    $state = $smtp->sendmail($smtpemailto, $smtpusermail,'We重邮', $mailtitle, $mailcontent, $mailtype);

    if($state==""){
        return false;
    }
    return true;
}