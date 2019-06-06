<?php

//常用函数库整理

/**
 * 编码转换
 *
 * @param $value
 *
 * @author yzm
 */
function str_iconv(&$value)
{
    if (!(is_numeric($value) || is_float($value))) {
        $value = (string)"\t" . $value;
        mb_convert_encoding($value, 'GBK');
    }
}



/**
 *  获取客户端ip
 *
 * @param int $type
 * @return mixed
 */
function getclientip($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if (@$_SERVER['HTTP_X_REAL_IP']) {//nginx 代理模式下，获取客户端真实IP
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}



/**
 * 发送get请求.
 *
 * @param $url
 * @param $timeout
 * @param array $data
 *
 * @author yzm.
 *
 * @return bool|mixed|null|string
 */
function http_get($url, $timeout = 10, $data = [])
{
    $rst = null;
    if (!empty($data)) {
        $data =is_array($data)?toUrlParams($data):$data;
        $url .= (strpos($url, '?') === false ? '?' : '&') .$data;
    }
//    if (function_exists('file_get_contents') && !is_null($timeout)) {
//        $rst = file_get_contents($url);
//        debug('rst'.$rst);
//    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
 
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 这个是重点。
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
 
 
        $rst = curl_exec($ch);
        curl_close($ch);
//    }
 
    return $rst;
}




/**
 * 执行一个 HTTP 请求
 *
 * @param string 	$Url 	执行请求的Url
 * @param mixed	$Params 表单参数
 * @param string	$Method 请求方法 post / get
 * @return array 结果数组
 */
function sendRequest($Url, $Params, $Method='post'){
 
    $Curl = curl_init();//初始化curl
 
    if ('get' == $Method){//以GET方式发送请求
        curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
    }else{//以POST方式发送请求
        curl_setopt($Curl, CURLOPT_URL, $Url);
        curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);//设置传送的参数
    }
 
    curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
    curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
    //curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间
 
    $Res = curl_exec($Curl);//运行curl
 
    curl_close($Curl);//关闭curl
 
    return $Res;
}


/**
 * 发送post请求.
 *
 * @param $url 地址
 * @param $args 参数
 * @param $timeout 过期时间 秒
 *
 * @author yzm
 *
 * @return mixed
 */
function http_post($url, $args, $timeout = 30)
{
    $_header = [
//       'Content-Type: application/json; charset=utf-8',
//        'Content-Length: ' . strlen($args)
    ];
 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    $ret = curl_exec($ch);
    curl_close($ch);
 
    return $ret;
}



/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}




/**
 * 随机生成编码.
 *
 * @author
 *
 * @param $len 长度.
 * @param int $type 1:数字 2:字母 3:混淆
 * @return string
 */
function rand_code($len, $type = 1)
{
    $output = '';
    $str = ['a', 'b', 'c', 'd', 'e', 'f', 'g',
        'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];
    $num = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
 
    switch ($type) {
        case 1:
            $chars = $num;
            break;
        case 2:
            $chars = $str;
            break;
        default:
            $chars = array_merge($str, $num);
    }
 
    $chars_len = count($chars) - 1;
    shuffle($chars);
 
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $chars_len)];
    }
 
    return $output;
}






































