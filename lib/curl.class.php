<?php
/**
 * Created by PhpStorm.
 * User: Hawkenliu
 * Date: 2017/11/12
 * Time: 10:04
 */
class Tool {
    private $base_url;
    private $short_url;
    private $url;
    private $content;
    private $detail_url;
    private $httpCode;
    /**
     * Tool constructor.
     * @param string $base_url
     * @param string $short_url
     */
    public function __construct($base_url='', $short_url=''){
        $this->base_url = $base_url;
        $this->short_url = $short_url;
        $this->url = $base_url.$short_url;
    }
    /**
     * 魔术方法__get()
     * @param $key
     * @return mixed
     */
    public function __get($key){
        return $this->$key;
    }
    /**
     * 魔术方法__set()
     * @param $key
     * @param $value
     */
    public function __set($key, $value){
        $this->$key = $value;
    }
    /**
     * 添加参数变量
     * @param array $params
     * @return string
     */
    public function add_params(array $params){
        $str = '';
        foreach($params as $key => $value){
            $str .= '&'.$key.'='.$value;
        }
        $this->short_url = substr($str, 1);
        return $this->url = $this->base_url.'?'.$this->short_url;
    }
    /**
     * 指定URL的内容
     * @param $url
     * @return string
     */
    public function cURL($url){
        //设置代理
        $url_params = parse_url($url);
        $headers = array(
            'User-Agent'=>'lanshan-studio',
            // 'CLIENT-IP:202.202.43.139',
            // 'X-FORWARDED-FOR:202.202.43.139',
            "Referer:{$url_params['host']}",
        );
        //初始化一个cURL
        $ch = curl_init();
        //设置url和相应的选项
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //执行cURL
        curl_exec($ch);
        //获取抓到的内容
        $content = curl_multi_getcontent($ch);
        //获取状态码
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //关闭cURL 并释放系统资源
        curl_close($ch);
        return $content;
    }
    /**
     * post数据
     * @param array $data
     * @return mixed
     */
    public function c_post($data=array()) {
        $url_params = parse_url($this->url);
        $headers = array(
            'User-Agent'=>'lanshan-studio',
            // 'CLIENT-IP:202.202.43.139',
            // 'X-FORWARDED-FOR:202.202.43.139',
            "Referer:{$url_params['host']}",
        );
        $url = $this->url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        $content = curl_exec ( $ch );
        curl_close ( $ch );
        return $content;
    }
    public function c_post_json($data) {
        $headers = array(
            'User-Agent: lanshan-studio',
            'Authorization: token ace88bc46820e9ae7f22dd6732878beaa5a7c93d',
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($data))
        );
        $url = $this->url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_POST, 1 );
        //https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //设置header
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
        //post
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec ( $ch );
        curl_close ( $ch );
        return $content;
    }
    /**
     * 重新设置内容,配合跨行正则匹配使用
     * @param $content
     * @return string
     */
    public function resetContent($content){
        $result = explode("\r\n", $content);
        $str = '';
        foreach ($result as $value){
            $str .= $value;
        }
        return $this->content = $str;
    }
    /**
     * 匹配
     * @param $search
     * @param $content
     * @param $result
     * @return mixed
     */
    public function match($start, $end, $content){
        $search = "/{$start}(.*?){$end}/i";
        preg_match_all($search, $content, $result);
        return $result;
    }
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}