<?php
/**
 * Created by PhpStorm.
 * User: hawkenliu
 * Date: 2017/11/12
 * Time: 17:08
 */
include "../conf/config.php";
include APP_PATH."func/sendmail.php";
//include "/yjdata/www/www/volunteer/func/sendmail.php";

send_mail('470401911@qq.com', "邮件发送测试", "内容");
