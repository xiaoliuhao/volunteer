<?php
include "../lib/curl.class.php";

$uid='1865867';

$curl = new Tool('http://zycq.cn/index/index/volunteer_info?uid='.$uid);
$fcontents = $curl->cURL($curl->url);
$volunteer_all=array();
$volunteer_all['total']=get_volunteer_times($fcontents);
$volunteer_all['info']=get_volunteerinformation($fcontents);
$volunteer_all['record']=get_list($fcontents);
if (strpos($volunteer_all['info']['name'], '志愿者不存在') !== false) {
    //uid不存在
    Response::show(400,'error','参数错误');
    exit();
}
//print_r($volunteer_all);
Response::show(200,'ok',$volunteer_all);
//获取志愿者服务记录
function get_list($fcontents){
    $contents=get_substr($fcontents,'tbody','捐赠记录');
    preg_match_all( '/<td[^>]*([\s\S]*?)<\/td>/i', $contents, $arr);
    $i=0;
    $j=0;
    $list=array();
    foreach ($arr[0] as $key => $value) {
        $value=str_replace('/','',$value);
        $value=str_replace('<td>','',$value);
        $value=str_replace("\r\n",'',$value);
        $value = mb_ereg_replace('^(　| )+', '', $value);
        $value = mb_ereg_replace('(　| )+$', '', $value);//清除空格
        $value = mb_ereg_replace('<td colspan=\"5\">', '', $value);//清除暂无服务记录bug
        if ($value=="暂无任何服务记录"){
            unset($list);
            break;
        }
        switch ($key%5){
            case 0:
                $list[$i]['time']=$value;
                break;
            case 1:
                $list[$i]['place']=$value;
                break;
            case 2:
                $list[$i]['content']=$value;
                break;
            case 3:
                $list[$i]['hours']=$value;
                break;
            case 4:
                $list[$i]['organization']=$value;
                break;
        }
        $j++;
        if ($j==5) {
            $i++;
            $j=0;
        }
    }
    return $list;
}
//获取志愿者基本信息
function get_volunteerinformation($fcontents)
{
    $contents=get_substr($fcontents,'排名','星级奖章');
    $user=array();
    $user['name']=get_substr($contents,'姓名：</span>','</p>');
    $user['sex']=get_substr($contents,'性别：</span>','</p>');
    $user['volunteer_time']=get_substr($contents,'服务时间：</span> ','</p>');
    return $user;
}
function get_volunteer_times($fcontents){
    $contents=get_substr($fcontents,'tbody','捐赠记录');
    preg_match_all( '/<td[^>]*([\s\S]*?)<\/td>/i', $contents, $arr);
    return count($arr[0])==1?0:count($arr[0])/5;
}
//获取两个字符串之间的内容
function get_substr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr,$left);
    if($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
}

?>
