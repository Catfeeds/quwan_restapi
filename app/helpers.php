<?php


if (!function_exists('xcx_send_template')){

    function xcx_send_template($star,$token)
    {
        $wwwB = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$token;

        $ch = curl_init($wwwB);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$star);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($star))
        );
        $result = curl_exec($ch);


        return $result;
    }
}
if (!function_exists('new_array_sort'))
{
    function new_array_sort($arr, $keys, $type = 'asc')
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v)
        {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc')
        {
            asort($keysvalue);
        }
        else
        {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v)
        {
            $new_array[$k] = $arr[$k];
        }

        return $new_array;
    }
}
if (!function_exists('lose_space'))
{
    //过滤所有空格，回车，换行
    function lose_space($pcon)
    {
        // $pcon = preg_replace("/ /","",$pcon);
        // $pcon = preg_replace("/&nbsp;/","",$pcon);
        // $pcon = preg_replace("/　/","",$pcon);
        $pcon = preg_replace("/\t/", "", $pcon);
        $pcon = preg_replace("/\r\n/", "", $pcon);

        $pcon = str_replace(array("/r/n", "/r", "/n", "/t"), "", $pcon);

        // $pcon = str_replace(chr(13),"",$pcon);
        // $pcon = str_replace(chr(10),"",$pcon);
        // $pcon = str_replace(chr(9),"",$pcon);

        // $pcon=preg_replace("/\s+/", " ", $pcon);

        return $pcon;
    }
}
if (!function_exists('get_web_contents'))
{


    /**
     * 名称：cURL网页抓取
     * 版本：v0.3
     * 作者：吣碎De人(http://www.qs5.org)
     * 最后更新时间：2013年2月4日
     * 获取更新：http://www.qs5.org/
     */


    //使用方法：
    /*
    $_Url = "http://www.baidu.com";
    $_Data = "u=admin&p=123456";
    $_Cookies = "0a63b_lastvisit=176%091359981539%09%2Flogin.php; 0a63b_winduser=BlEOUFpoCgUAAgAHWlVSDQZUCgMOUQcABwgAClFXUQFfCABTVlow; 0a63b_ck_info=%2F%09; 0a63b_lastvisit=deleted";
    $Proxy = array("Proxy" => "124.160.133.2:80", "UserNmae" => "Root", "PassWord" => "Root");
    $Head = array("User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)", "Accept-Language: en-us");

    //                         地址  访问方式 Post数据
    $_Str = Get_Web_Contents($_Url, "GET", $_Data, $_Cookies, $Proxy, 30, $Head);
    print_r($_Str);
    */


    function get_web_contents($_Get_Url, $_Method = "GET", $_Form_Data = "", $_Cookie = "", $_Proxy = array("Proxy" => ""), $_Time_Out = 30, $_Headers = array())
    {
        $ch = curl_init();    //创建cURL对象
        curl_setopt($ch, CURLOPT_URL, $_Get_Url);    //设置读取URL
        curl_setopt($ch, CURLOPT_HEADER, 1);    //是否输出头信息，0为不输出，非零则输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //设置输出方式, 0为自动输出返回的内容, 1为返回输出的内容,但不自动输出.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $_Time_Out);    // 设置超时 30秒
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 设置代理
        if (isset($_Proxy["Proxy"]))
        {
            curl_setopt($ch, CURLOPT_PROXY, $_Proxy["Proxy"]);    //设置代理地址
            if (isset($_Proxy["UserNmae"]) and isset($_Proxy["PassWord"]))
            {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_Proxy["UserNmae"] . ":" . $_Proxy["PassWord"]);    // 设置代理用户名与密码
            }
        }
        // 设置 POST 数据
        if (strtoupper($_Method) == "POST")
        {
            curl_setopt($ch, CURLOPT_POST, 1);    //设置为 POST 提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_Form_Data);    //设置POST数据
        }
        // 设置 Cookies 数据
        if (strlen($_Cookie))
        {
            curl_setopt($ch, CURLOPT_COOKIE, $_Cookie);    // 设置 Cookies
        }
        // 设置附加协议头
        if (isset($_Headers))
        {
            //设置 User-Agent
            if (isset($_Headers['User-Agent']))
            {
                curl_setopt($ch, CURLOPT_USERAGENT, $_Headers['User-Agent']);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $_Headers);    // 设置附加协议头
        }

        @$html = curl_exec($ch);  //执行
        if ($html === false)
        {    //获取错误,
            $ret["Error"] = curl_error($ch);

            return $ret;
        }
        $ret["Info"] = curl_getinfo($ch);    //获取详细信息
        curl_close($ch);//关闭对象
        // 区分头信息与正文
        $_wz           = strpos($html, "\r\n\r\n");
        $ret["Header"] = substr($html, 0, $_wz);    //截取头信息
        // 获取Cookies 信息
        if (preg_match_all("/set-cookie:\s?(.*?=.*?);/i", $ret["Header"], $cookie))
        {
            $cookie = $cookie[1];
        }
        $ret["Cookies"] = "";
        foreach ($cookie as $value)
        {
            if (!is_array($value))
            {
                $ret["Cookies"] .= $value . "; ";
            }
        }
        $ret["Cookies"] = substr($ret["Cookies"], 0, -1);

        $ret["Body"] = substr($html, $_wz + 4);    //获取正文

        return $ret;
    }

}
if (!function_exists('random_float'))
{
    //获得指定区间随机浮点数
    function random_float($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
if (!function_exists('create_order_code'))
{

    function create_order_code()
    {
        //16位兑换码，兑换码组成：时间日期+8位序号。
        return date('Y') . '-' . date('md') . '-' . random_int(1000, 9999) . '-' . random_int(1000, 9999);
    }

}


if (!function_exists('post_curl_content'))
{
    /*
    * 访问网址并取得其内容
    * @param $url String 网址
    * @param $postFields Array 将该数组中的内容用POST方式传递给网址中
        * @param $cookie_file string cookie文件
    * @param $r_or_w string 写cookie还是读cookie或是两都都有，r读，w写，a两者，null没有cookie
    * @return String 返回网址内容
    */
    function post_curl_content($url, $postFields = null, $cookie_file = null, $r_or_w = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (false === empty($_SERVER['HTTP_USER_AGENT']))
        {

            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($cookie_file && ($r_or_w == 'a' || $r_or_w == 'w'))
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); // 存放Cookie信息的文件名称
        }
        if ($cookie_file && ($r_or_w == 'a' || $r_or_w == 'r'))
        {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($postFields) && 0 < count($postFields))
        {
            // $postBodyString = "";
            // foreach ($postFields as $k => $v)
            // {
            //
            //     $postBodyString .= "$k=" . urlencode($v) . "&";
            // }
            // unset($k, $v);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));

            // 把post的变量加上
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));  //所需传的数组用http_bulid_query()函数处理一下，就ok了


        }

        $reponse = curl_exec($ch);
        if (curl_errno($ch))
        {
            throw new Exception(curl_error($ch), 0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            /*if (200 !== $httpStatusCode){
                throw new Exception($reponse,$httpStatusCode);
                //print_r($reponse);exit;
            }*/
        }
        curl_close($ch);

        return $reponse;
    }

}
if (!function_exists('get_distance'))
{
    /**
     * 计算经纬度距离
     *
     * @param float $lon1 原点经度
     * @param float $lat1 原点纬度
     * @param float $lon2 目标点经度
     * @param float $lat2 目标点纬度
     *
     * @return float 两点大致距离，单位：米
     */
    function get_distance($lon1, $lat1, $lon2, $lat2)
    {
        if (!$lon1 || !$lat1)
        {
            return 0;
        }
        $dx = $lon1 - $lon2;
        $dy = $lat1 - $lat2;
        $b  = ($lat1 + $lat2) / 2;
        $lx = 6367000.0 * deg2rad($dx) * cos(deg2rad($b));
        $ly = 6367000.0 * deg2rad($dy);

        return sqrt($lx * $lx + $ly * $ly);
    }
}
if (!function_exists('uuid'))
{

    /**
     * 序列号生成,随机数生成 (类似mysql的UUID)
     *
     * @param      string  自定义字符串
     *
     * @return     string  uuid
     */
    function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid  = substr($chars, 0, 4) . '-';
        $uuid  .= substr($chars, 4, 4) . '-';
        $uuid  .= substr($chars, 8, 4) . '-';
        $uuid  .= substr($chars, 12, 4);

        return $prefix . $uuid;
    }
}
if (!function_exists('build_order_no'))
{
    /**
     * 订单号生成
     */
    function build_order_no()
    {
        return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}


if (!function_exists('convert_underline'))
{

    //将下划线命名转换为驼峰式命名
    function convert_underline($str, $ucfirst = true)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches)
        {
            return strtoupper($matches[2]);
        }, $str
        );

        return $ucfirst ? ucfirst($str) : $str;
    }

}
if (!function_exists('object_to_array'))
{
    //对象转数组
    function object_to_array($e)
    {
        $e = (array)$e;
        foreach ($e as $k => $v)
        {
            if ((string)gettype($v) === 'resource')
            {
                return;
            }

            if ((string)gettype($v) === 'object' || (string)gettype($v) === 'array')
            {

                $e[$k] = (array)object_to_array($v);
            }
        }

        return $e;
    }
}


if (!function_exists('lose_space'))
{

    //过滤所有空格，回车，换行
    function lose_space($pcon)
    {
        $pcon = preg_replace("/ /", "", $pcon);
        $pcon = preg_replace("/&nbsp;/", "", $pcon);
        $pcon = preg_replace("/　/", "", $pcon);
        $pcon = preg_replace("/\r\n/", "", $pcon);
        $pcon = str_replace(array("/r/n", "/r", "/n"), "", $pcon);
        $pcon = str_replace(chr(13), "", $pcon);
        $pcon = str_replace(chr(10), "", $pcon);
        $pcon = str_replace(chr(9), "", $pcon);
        $pcon = preg_replace("/\s+/", " ", $pcon);

        return $pcon;
    }
}

if (!function_exists('cn_substr'))
{

    /**
     * +----------------------------------------------------------
     * 字符串截取，支持中文和其他编码
     * +----------------------------------------------------------
     * @static
     * @access public
     * +----------------------------------------------------------
     *
     * @param string $str     需要转换的字符串
     * @param string $start   开始位置
     * @param string $length  截取长度
     * @param string $charset 编码格式
     * @param string $suffix  截断显示字符
     *                        +----------------------------------------------------------
     *
     * @return string
    +----------------------------------------------------------
     */
    function cn_substr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr"))
        {
            if ($suffix && cn_strlen($str) > $length)
            {
                return mb_substr($str, $start, $length, $charset) . "…";
            }
            else
            {
                return mb_substr($str, $start, $length, $charset);
            }
        }
        else
        {
            if (function_exists('iconv_substr'))
            {
                if ($suffix && cn_strlen($str) > $length)
                {
                    return iconv_substr($str, $start, $length, $charset) . "…";
                }
                else
                {
                    return iconv_substr($str, $start, $length, $charset);
                }
            }
        }
        $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix)
        {
            return $slice . "…";
        }

        return $slice;
    }


}
if (!function_exists('config_path'))
{
    function config_path($path = '')
    {
        if ($path)
        {
            return app()->basePath('config') . '/' . $path;
        }

        return app()->basePath('config');
    }
}
if (!function_exists('file_size_format'))
{
    /**
     * 文件大小格式化
     *
     * @param integer $size 初始文件大小，单位为byte
     *
     * @return array 格式化后的文件大小和单位数组，单位为byte、KB、MB、GB、TB
     */
    function file_size_format($size = 0)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}
if (!function_exists('get_week'))
{


    /**
     * 获取某年的每周第一天和最后一天
     *
     * @param  [int] $year [年份]
     *
     * @return [arr]       [每周的周一和周日]
     */
    function get_week($year)
    {
        $year_start = $year . "-01-01";
        $year_end   = $year . "-12-31";
        $startday   = strtotime($year_start);
        if ((int)date('N', $startday) !== 1)
        {
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期
        }
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期

        $endday = strtotime($year_end);
        if ((int)date('W', $endday) === 7)
        {
            $endday = strtotime("last sunday", strtotime($year_end));
        }

        $num = (int)date('W', $endday);
        for ($i = 1; $i <= $num; $i++)
        {
            $j          = $i - 1;
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week "));

            $end_day        = date("Y-m-d", strtotime("$start_date +6 day"));
            $week_array[$i] = array($start_date . ' 00:00:00', $end_day . ' 23:59:59');
        }

        return $week_array;
    }
}
if (!function_exists('format_price'))
{

    /**
     * 格式化金额
     *
     * @param int $price
     *
     * @return float
     */
    function format_price($price = 0)
    {
        return (float)sprintf('%0.2f', $price / 100.0);
    }
}

if (!function_exists('number_avg'))
{
    /**
     * 将一个数值切成N份(随机分配)
     *
     * @param  int $number    切的数值
     * @param  int $avgNumber 份数
     *
     * @return array
     */
    function number_avg($number, $avgNumber)
    {
        if ($number === 0)
        {
            $array = array_fill(0, $avgNumber, 0);
        }
        else
        {
            $avg     = floor($number / $avgNumber);
            $ceilSum = $avg * $avgNumber;
            $array   = array();
            for ($i = 0; $i < $avgNumber; $i++)
            {
                if ($i < $number - $ceilSum)
                {
                    array_push($array, $avg + 1);
                }
                else
                {
                    array_push($array, $avg);
                }
            }
        }

        return $array;
    }
}


if (!function_exists('check_social_url_type'))
{
    /**
     * 检测入库连接类型
     *
     * @param $url
     *
     * @return bool|int
     */
    function check_social_url_type($url)
    {
        $allowList = [
            1 => 'weibo',
            2 => 'facebook',
            3 => 'twitter',
            4 => 'instagram',
            5 => 'douyu',
            6 => 'huomao',
            7 => 'panda',
            8 => 'huya',
        ];

        $info = parse_url($url);

        if (true === empty($info['host']))
        {
            return false;
        }

        if (substr_count($info['host'], 'weibo'))
        {
            return 1;
        }
        else
        {
            if (substr_count($info['host'], 'facebook'))
            {
                return 2;
            }
            else
            {
                if (substr_count($info['host'], 'twitter'))
                {
                    return 3;
                }
                else
                {
                    if (substr_count($info['host'], 'instagram'))
                    {
                        return 4;
                    }
                    else
                    {
                        if (substr_count($info['host'], 'douyu'))
                        {
                            return 5;
                        }
                        else
                        {
                            if (substr_count($info['host'], 'huomao'))
                            {
                                return 6;
                            }
                            else
                            {
                                if (substr_count($info['host'], 'panda'))
                                {
                                    return 7;
                                }
                                else
                                {
                                    if (substr_count($info['host'], 'huya'))
                                    {
                                        return 8;
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }
}

if (!function_exists('check_social_url'))
{
    /**
     * 检测入库连接格式
     *
     * @param $url
     * @param $obj
     *
     * @return bool
     */
    function check_social_url($url, $obj)
    {

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            return false;
        }

        $allowList = [
            1 => 'weibo',
            2 => 'facebook',
            3 => 'twitter',
            4 => 'instagram',
            5 => 'douyu',
            6 => 'huomao',
            7 => 'panda',
            8 => 'huya',
        ];

        $info = parse_url($url);

        if (true === empty($info['host']))
        {
            return false;
        }

        if (true === empty($allowList[$obj]))
        {
            return false;
        }

        if (!substr_count($info['host'], $allowList[$obj]))
        {
            return false;
        }

        return true;
    }
}
if (!function_exists('cn_strlen'))
{
    /**
     *  UTF8编码下 字符串的字数统计
     *
     * @param type $str
     *
     * @return int
     */
    function cn_strlen($str)
    {
        $count   = 0;
        $str_len = strlen($str);
        for ($i = 0; $i < $str_len; $i++)
        {
            $now_word = ord(substr($str, $i, 1));
            if ($now_word > 0xa0)
            {
                $i += 2; //GB2312编码下为 $++
                $count++;
            }
            else
            {
                $count++;
            }
        }

        return $count;
    }
}

if (!function_exists('response_error'))
{
    /**
     * 返回错误的响应信息
     *
     * @param string  $code       错误码
     * @param string  $message    错误信息
     * @param integer $statusCode 状态码
     * @param array   $extra      报错时额外信息返回
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function response_error($code, $message = '', $statusCode = 500, array $extra = [])
    {
        $body = [
            'code'    => $code,
            'message' => $message,
        ];
        if ($extra)
        {
            $body['extra'] = $extra;
        }

        return response()->json($body, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('response_success'))
{

    /**
     * 返回正确的响应信息
     *
     * @param array $data       返回的数据
     * @param int   $statusCode 状态码
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    function response_success(array $data = [], $statusCode = 200)
    {
        $body = empty($data)
            ? new stdClass()
            : $data;

        if ($statusCode === 204)
        {
            return response()->make('', 204);
        }

        return response()->json($body, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('filter_zh_mobile'))
{
    /**
     * 验证中国大陆手机号
     *
     * @param $mobile
     *
     * @return bool
     */
    function filter_zh_mobile($mobile)
    {
        return preg_match('/^(1)[34578]{1}\d{9}$/', $mobile) ? true : false;
    }
}

if (!function_exists('filter_password'))
{
    /**
     * 判断密码是否符合要求
     *
     * @param $password
     *
     * @return bool
     */
    function filter_password($password)
    {
        return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,14}$/', $password) ? true : false;
    }
}

if (!function_exists('maskEmail'))
{
    /**
     * 隐藏邮箱部分
     *
     * @param $email
     *
     * @return string
     */
    function maskEmail($email)
    {
        $email_array = explode('@', $email);
        $prefix      = (strlen($email_array[0]) < 3) ? '' : substr($email, 0, 2);
        $suffix      = substr($email_array[0], -1, 1);

        return $prefix . '***' . $suffix . '@' . $email_array[1];
    }
}

if (!function_exists('filter_cn_id_card_num'))
{

    /**
     * 身份证号验证(15,18位)
     *
     * @param $idCard
     *
     * @return bool
     */
    function filter_cn_id_card_num($idCard)
    {

        $idCard    = strtoupper($idCard);
        $regx      = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();

        if (!preg_match($regx, $idCard))
        {
            return false;
        }

        if (15 === strlen($idCard))
        { //检查15位
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            preg_match($regx, $idCard, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        { //检查18位
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            preg_match($regx, $idCard, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth))
            { //检查生日日期是否正确
                return false;
            }
            else
            { //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch  = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign    = 0;
                for ($i = 0; $i < 17; $i++)
                {
                    $b    = (int)$idCard{$i};
                    $w    = $arr_int[$i];
                    $sign += $b * $w;
                }

                $n       = $sign % 11;
                $val_num = $arr_ch[$n];

                if ($val_num !== $idCard[17])
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
        }
    }

}

if (!function_exists('ecode'))
{
    /**
     * 返回错误码
     *
     * @param int    $fileCode
     * @param string $errCode
     *
     * @return int
     */
    function ecode($fileCode, $errCode)
    {
        return (int)($fileCode . str_pad($errCode, 3, 0, STR_PAD_LEFT));
    }
}

if (!function_exists('encryptPwd'))
{
    /**
     * 加密密码
     *
     * @param $password
     *
     * @return bool|string
     */
    function encryptPwd($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}


if (!function_exists('generation_password'))
{
    /**
     * 用户密码生成
     *
     * @param        $password
     * @param string $salt
     *
     * @return string
     */
    function generation_password($password, $salt = '')
    {
        return md5(trim($password . $salt));
    }
}

//路由使用,获取head头中的API版本信息
if (!function_exists('getVersion'))
{
    function getVersion($versionAccept, $allowVersion)
    {
        preg_match('/application\/vnd\.vpgame\.v(\d)\+json/', $versionAccept, $matches);
        $version = '';
        if ($matches)
        {
            $version = (int)$matches[1];
            if (!in_array($version, $allowVersion, true))
            {
                throw new \App\Exceptions\NotFoundHttpException(852000);
            }
            $version = '\V' . $version;
        }

        return $version;
    }
}

if (!function_exists('check_strlen'))
{
    /**
     * 检测字符串长度
     *
     * @param     $str
     * @param     $min
     * @param int $max
     *
     * @return bool
     */
    function check_strlen($str, $min, $max = 20)
    {
        $lens = strlen($str);

        return !($lens < $min || $lens > $max);
    }
}

if (!function_exists('isPrivateIP'))
{
    /**
     * 检测给定 IP 是否是私有网段内
     *
     * @param $ip
     *
     * @return bool
     */
    function isPrivateIP($ip)
    {
        $ip = ip2long($ip);
        $a  = ip2long('10.255.255.255') >> 24;
        $b  = ip2long('172.31.255.255') >> 20;
        $c  = ip2long('192.168.255.255') >> 16;

        return ($ip >> 24 === $a || $ip >> 20 === $b || $ip >> 16 === $c);
    }
}

if (!function_exists('image_cdn_path'))
{
    function image_cdn_path($value, $type = 'avatar')
    {
        if ($value)
        {
            if (stripos($value, 'http://') !== false || stripos($value, 'https://') !== false)
            {
                $url = $value;
            }
            else
            {
                $url = 'http://thumb.vpgcdn.com/' . $value;
            }
        }
        else
        {
            $value = ($type === 'avatar') ? 'avatar.png' : 'empty.png';
            $url   = 'http://thumb.vpgcdn.com/' . $value;
        }

        return $url;
    }
}
if (!function_exists('create_order_no'))
{
    /**
     * 生成指定长度带前缀的订单号
     *
     * @param null $prefix
     * @param int  $len
     *
     * @return string
     */
    function create_order_no($prefix = null)
    {

        $orderNo = $prefix . date('Ymdhis') . date('d') . substr(time(), -3) . substr(microtime(), 2, 5) . sprintf('%02d', mt_rand(0, 99));

        return $orderNo;

    }
}
