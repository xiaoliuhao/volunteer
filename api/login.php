<?php
/**
 * Created by PhpStorm.
 * User: hawkenliu
 * Date: 2017/11/12
 * Time: 20:06
 */
include "../lib/curl.class.php";

$url = "http://www.zycq.com/login/login";
$tool = new Tool($url);

$login_data = array(
    "account"=>"130925199410175812",
    "password"=>"qq470401911",
    "login_class"=>"volunteer",
    "whether_remember"=>"0"
);

//$res = $tool->c_post_json($login_data);

var_dump($res);