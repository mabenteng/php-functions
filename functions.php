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


/**
 * 打印数组.
 *
 * @param $arr
 */
function p($arr)
{
    //header('content-type:text/html;charset=utf8');
    echo '<pre>' . print_r($arr, true);
}


/**
 * 下载远程文件.
 *
 * @param $url
 * @param $path
 *
 * @author yzm
 *
 * @return bool true false
 */
function download($url, $path = null)
{
    $file = http_get($url);
 
    if (empty($path)) return $file;
 
    $basedir = dirname($path);
    if (!is_dir($basedir)) mkdir($basedir);
 
    // 直接写入文件
    file_put_contents($path, $file);
 
    return file_exists($path);
}



/**
 * 获取文件大小,以kb为单位.
 *
 * @author yzm
 *
 * @param $path 文件路径
 * @return float
 */
function getFilesize($path)
{
    return ceil(filesize($path));
}



/**
 * 数组转换成xml.
 *
 * @author yzm
 *
 * @param $arr 数组
 *
 * @return string xml结果
 */
function arrayToXml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
 
        } else
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
    }
    $xml .= "</xml>";
    return $xml;
}
 
/**
 * 将xml转为数组.
 *
 * @param $xml xml数据
 *
 * @return array|mixed|stdClass
 */
function xmlToArray($xml)
{
    //将XML转为array
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}
 



//获取汉字首字母
function getfirstchar($s0)
{
    $fchar = ord($s0{0});
    if ($fchar >= ord("A") and $fchar <= ord("z")) return strtoupper($s0{0});
    $s1 = iconv("UTF-8", "gb2312", $s0);
    $s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $s0) {
        $s = $s1;
    } else {
        $s = $s0;
    }
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284) return "A";
    if ($asc >= -20283 and $asc <= -19776) return "B";
    if ($asc >= -19775 and $asc <= -19219) return "C";
    if ($asc >= -19218 and $asc <= -18711) return "D";
    if ($asc >= -18710 and $asc <= -18527) return "E";
    if ($asc >= -18526 and $asc <= -18240) return "F";
    if ($asc >= -18239 and $asc <= -17923) return "G";
    if ($asc >= -17922 and $asc <= -17418) return "I";
    if ($asc >= -17417 and $asc <= -16475) return "J";
    if ($asc >= -16474 and $asc <= -16213) return "K";
    if ($asc >= -16212 and $asc <= -15641) return "L";
    if ($asc >= -15640 and $asc <= -15166) return "M";
    if ($asc >= -15165 and $asc <= -14923) return "N";
    if ($asc >= -14922 and $asc <= -14915) return "O";
    if ($asc >= -14914 and $asc <= -14631) return "P";
    if ($asc >= -14630 and $asc <= -14150) return "Q";
    if ($asc >= -14149 and $asc <= -14091) return "R";
    if ($asc >= -14090 and $asc <= -13319) return "S";
    if ($asc >= -13318 and $asc <= -12839) return "T";
    if ($asc >= -12838 and $asc <= -12557) return "W";
    if ($asc >= -12556 and $asc <= -11848) return "X";
    if ($asc >= -11847 and $asc <= -11056) return "Y";
    if ($asc >= -11055 and $asc <= -10247) return "Z";
 
    return null;
}
 
/**
 * 获取天的问候语.
 *
 * @author yzm
 *
 * @return string
 */
function getDayReeting()
{
    // 以上海时区为标准
    date_default_timezone_set('Asia/Shanghai');
 
    $rst = '晚上好';
    $h = date("H");
 
    if ($h < 11) {
        $rst = '早上好';
    } elseif ($h < 13) {
        $rst = '中午好';
    } elseif ($h < 17) {
        $rst = '下午好';
    }
 
    return $rst;
}
 
 
/**
 * 系统非常规MD5加密方法
 *
 * @param  string $str 要加密的字符串
 * @return string
 */
function userMd5($str, $auth_key)
{
    if (!$auth_key) {
        $auth_key = '' ?: '>=diMf;Sbduzn@!NBa~Hpl_@&IeG_w]O&ieZtiDffKTh]pK".doZ`wd,T$$:,Ka(';
    }
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}
 
/**
 * 生成随机字符串，不生成大写字母
 * @param $length
 * @return null|string
 */
function getRandChar($length){
    $str = null;
    $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;
 
    for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }
 
    return $str;
}


/**
 * 遍历文件夹找到.app目录
 * @param  [type] $dir      [遍历目录]
 * @param  [type]           [文件夹名]
 * @return [type]           [icon数组]
 */
function read_all_dir($dir, $type, $icon)
{
    delDirAndFile($dir . '/__MACOSX');
    $handle = opendir($dir);
    $result = '';
    if ($handle) {
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                $cur_path = $dir . DIRECTORY_SEPARATOR . $file;//构建子目录路径
                if (is_dir($cur_path)) {  //如果是目录则继续遍历直到找到.app目录
                    if (strpos($cur_path, '.app')) {
                        if ($type == 1) {
                            $all_dir = scandir($cur_path);
                            if (in_array('Info.plist', $all_dir)) {
                                $result['plist_url'] = $cur_path . '/Info.plist';
                            }
                        } elseif ($type == 2) {
                            $result = read_png($cur_path, $icon);
                        } else {
                            $result = getagentinfo($cur_path);
                        }
                    } else {
                        $t = read_all_dir($cur_path, $type, $icon);
                        if (!empty($t)) {
                            $result = $t;
                        }
                    }
                }
            }
        }
    }
 
    closedir($handle);
    return $result;
}
 
/**
 * 删除当前文件夹和文件
 * @param  [type]  $path   [description]
 * @param  boolean $delDir [description]
 * @return [type]          [description]
 */
function delDirAndFile($path, $delDir = TRUE)
{
    if ($delDir && !is_dir($path)) {
        return true;
    }
 
    $handle = opendir($path);
    if ($handle) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    } else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}



/**
 * 递归移动文件及文件夹.
 *
 * @param [string] $source 源目录或源文件
 * @param [string] $target 目的目录或目的文件
 * @return boolean true
 */
function moveFile($source, $target)
{
    // 如果源目录/文件不存在返回false
    if (!file_exists($source)) return false;
 
    // 如果要移动文件
    if (filetype($source) == 'file') {
        $basedir = dirname($target);
        if (!is_dir($basedir)) mkdir($basedir); //目标目录不存在时给它创建目录
        copy($source, $target);
        unlink($source);
 
    } else { // 如果要移动目录
 
        if (!file_exists($target)) mkdir($target); //目标目录不存在时就创建
 
        $files = array(); //存放文件
        $dirs = array(); //存放目录
        $fh = opendir($source);
 
        if ($fh != false) {
            while ($row = readdir($fh)) {
                $src_file = $source . '/' . $row; //每个源文件
                if ($row != '.' && $row != '..') {
                    if (!is_dir($src_file)) {
                        $files[] = $row;
                    } else {
                        $dirs[] = $row;
                    }
                }
            }
            closedir($fh);
        }
 
        foreach ($files as $v) {
            copy($source . '/' . $v, $target . '/' . $v);
            unlink($source . '/' . $v);
        }
 
        if (count($dirs)) {
            foreach ($dirs as $v) {
                moveFile($source . '/' . $v, $target . '/' . $v);
            }
        }
    }
 
    return true;
}
 
/**
 * 转换为url参数.
 *
 * @author yzm
 *
 * @param $params
 * @return string
 */
function toUrlParams($params)
{
    $buff = "";
 
    if (empty($params)) return $buff;
 
    foreach ($params as $k => $v) {
        if (!is_array($v)) {
            $buff .= $k . "=" . urlencode($v) . "&";
        }
    }
 
    $buff = trim($buff, "&");
 
    return $buff;
}



/**
 * 获取访问的平台.
 *
 * @author yzm
 *
 * @return int
 */
function getPlatform()
{
    // 全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $rst = PLATFORM_PC;
 
    if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
        $rst = PLATFORM_IOS;
    }
 
    if (strpos($agent, 'android')) {
        $rst = PLATFORM_ANDROID;
    }
 
    return $rst;
}
 
/**
 * 是否是微信,如果是则返回微信版本.
 *
 * @author yzm
 *
 * @return bool
 */
function isWeiXin()
{
    $rst = false;
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($user_agent, 'MicroMessenger') !== false) {
        // 获取版本号
        preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
        $rst = @$matches[2];
    }
 
    return $rst;
}
 


/**
 * 获取省市基础信息
 *
 * 优先从淘宝获取,获取不到再从新浪获取.
 *
 * @author yzm
 *
 * @param $ip
 * @return array
 */
function getPCInfoByIp($ip)
{
    $taobao = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
    $sina = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip={$ip}";
 
    $rest = @json_decode(http_get($taobao, 5), true);
    if ($rest && !empty($rest['data'])) {
        $rst = [
            'p' => $rest['data']['region'],
            'pcode' => $rest['data']['region_id'],
            'c' => $rest['data']['city'],
            'ccode' => $rest['data']['city_id'],
        ];
    } else {
        $rest = @json_decode(http_get($sina, 5), true);
        $rst = [
            'p' => @$rest['province'],
            'pcode' => 0,
            'c' => @$rest['city'],
            'ccode' => 0,
        ];
    }
 
    return $rst;
}
 


/**
 * 友好的时间显示
 *
 * @author yzm
 *
 * @param int $sTime 待显示的时间
 * @param string $type 类型. normal | mohu | full | ymd | other
 * @param string $alt 已失效
 *
 * @return string
 */
function friendly_date($sTime, $type = 'normal', $alt = 'false')
{
    if (!$sTime) return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
 
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
 
    //normal：n秒前，n分钟前，n小时前，日期
    switch ($type) {
        case 'normal':
            if ($dTime < 60) {
                if ($dTime < 10) {
                    return '刚刚';
                } else {
                    return intval(floor($dTime / 10) * 10) . "秒前";
                }
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
                //今天的数据.年份相同.日期相同.
            } elseif ($dYear == 0 && $dDay == 0) {
                //return intval($dTime/3600)."小时前";
                return '今天' . date('H:i', $sTime);
            } elseif ($dYear == 0) {
                return date("m月d日 H:i", $sTime);
            } else {
                return date("Y-m-d H:i", $sTime);
            }
            break;
        case 'mohu':
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay > 0 && $dDay <= 7) {
                return intval($dDay) . "天前";
            } elseif ($dDay > 7 && $dDay <= 30) {
                return intval($dDay / 7) . '周前';
            } elseif ($dDay > 30) {
                return intval($dDay / 30) . '个月前';
            }
            break;
        case 'full':
            return date("Y-m-d , H:i:s", $sTime);
            break;
        case 'ymd':
            return date("Y-m-d", $sTime);
            break;
        default:
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dYear == 0) {
                return date("Y-m-d H:i:s", $sTime);
            } else {
                return date("Y-m-d H:i:s", $sTime);
            }
            break;
    }
}



/**
 * 执行shell脚本.
 *
 * @author yzm
 *
 * @param $cmd
 * @return string
 */
function execShell($cmd)
{
    $res = '';
    if (function_exists('system')) {
        ob_start();
        system($cmd);
        $res = ob_get_contents();
        ob_end_clean();
    } elseif (function_exists('shell_exec')) {
        $res = shell_exec($cmd);
    } elseif (function_exists('exec')) {
        exec($cmd, $res);
        $res = join("\n", $res);
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($cmd);
        $res = ob_get_contents();
        ob_end_clean();
    } elseif (is_resource($f = @popen($cmd, "r"))) {
        $res = '';
        while (!feof($f)) {
            $res .= fread($f, 1024);
        }
        pclose($f);
    }
 
    return $res;
}
 
/**
 * 生成token.
 *
 * @author yzm
 *
 * @param $signKey
 * @param $params
 *
 * @return string
 */
function makeToken($signKey, $params)
{
    $params = __stripcslashes($params);
 
    ksort($params);
 
    $str = '';
    foreach ($params as $key => $item) {
        $str .= "{$key}={$item}&";
    }
 
    $str = trim($str, '&');
 
    return strtolower(md5($str . $signKey));
}


































































