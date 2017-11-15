<?php

if (! function_exists('lose_space')) {

    //过滤所有空格，回车，换行
    function lose_space($pcon){
        $pcon = preg_replace("/ /","",$pcon);
        $pcon = preg_replace("/&nbsp;/","",$pcon);
        $pcon = preg_replace("/　/","",$pcon);
        $pcon = preg_replace("/\r\n/","",$pcon);
        $pcon = str_replace(array("/r/n", "/r", "/n"), "", $pcon);
        $pcon = str_replace(chr(13),"",$pcon);
        $pcon = str_replace(chr(10),"",$pcon);
        $pcon = str_replace(chr(9),"",$pcon);
        $pcon=preg_replace("/\s+/", " ", $pcon);
        return $pcon;
    }
}

if (! function_exists('cn_substr')) {

    /**
    +----------------------------------------------------------
     * 字符串截取，支持中文和其他编码
    +----------------------------------------------------------
     * @static
     * @access public
    +----------------------------------------------------------
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    function cn_substr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        if (function_exists("mb_substr")) {
            if ($suffix && cn_strlen($str) > $length) {
                return mb_substr($str, $start, $length, $charset) . "…";
            } else {
                return mb_substr($str, $start, $length, $charset);
            }
        } elseif (function_exists('iconv_substr')) {
            if ($suffix && cn_strlen($str) > $length) {
                return iconv_substr($str, $start, $length, $charset) . "…";
            } else {
                return iconv_substr($str, $start, $length, $charset);
            }
        }
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix) {
            return $slice . "…";
        }
        return $slice;
    }


}
if (! function_exists('config_path')) {
    function config_path($path = ''){
        if ($path) {
            return app()->basePath('config') . '/'. $path;
        }
        return app()->basePath('config');
    }
}
if (! function_exists('file_size_format')) {
    /**
     * 文件大小格式化
     * @param integer $size 初始文件大小，单位为byte
     * @return array 格式化后的文件大小和单位数组，单位为byte、KB、MB、GB、TB
     */
    function file_size_format($size = 0) {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}
if (! function_exists('get_week')) {


    /**
     * 获取某年的每周第一天和最后一天
     * @param  [int] $year [年份]
     * @return [arr]       [每周的周一和周日]
     */
    function get_week($year) {
        $year_start = $year . "-01-01";
        $year_end = $year . "-12-31";
        $startday = strtotime($year_start);
        if ((int)date('N', $startday) !== 1) {
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期
        }
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期

        $endday = strtotime($year_end);
        if ((int)date('W', $endday) === 7) {
            $endday = strtotime("last sunday", strtotime($year_end));
        }

        $num = (int)date('W', $endday);
        for ($i = 1; $i <= $num; $i++) {
            $j = $i -1;
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week "));

            $end_day = date("Y-m-d", strtotime("$start_date +6 day"));
            $week_array[$i] = array ($start_date.' 00:00:00',$end_day.' 23:59:59');
        }
        return $week_array;
    }
}
if (! function_exists('format_price')) {

    /**
     * 格式化金额
     * @param int $price
     * @return float
     */
    function format_price ($price = 0)
    {
        return (float) sprintf('%0.2f', $price / 100.0);
    }
}

if (! function_exists('number_avg')) {
    /**
     * 将一个数值切成N份(随机分配)
     * @param  int $number    切的数值
     * @param  int $avgNumber 份数
     * @return array
     */
    function number_avg($number, $avgNumber)
    {
        if ($number === 0) {
            $array = array_fill(0, $avgNumber, 0);
        } else {
            $avg     = floor($number / $avgNumber);
            $ceilSum = $avg * $avgNumber;
            $array   = array();
            for ($i = 0; $i < $avgNumber; $i++) {
                if ($i < $number - $ceilSum) {
                    array_push($array, $avg + 1);
                } else {
                    array_push($array, $avg);
                }
            }
        }
        return $array;
    }
}


if (! function_exists('check_social_url_type')) {
    /**
     * 检测入库连接类型
     * @param $url
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

        if (true === empty($info['host'])) {
            return false;
        }

        if (substr_count($info['host'], 'weibo')) {
            return 1;
        } elseif (substr_count($info['host'], 'facebook')){
            return 2;
        }  elseif (substr_count($info['host'], 'twitter')){
            return 3;
        }  elseif (substr_count($info['host'], 'instagram')){
            return 4;
        }  elseif (substr_count($info['host'], 'douyu')){
            return 5;
        }  elseif (substr_count($info['host'], 'huomao')){
            return 6;
        } elseif (substr_count($info['host'], 'panda')){
            return 7;
        } elseif (substr_count($info['host'], 'huya')){
            return 8;
        } else {
            return false;
        }

    }
}

if (! function_exists('check_social_url')) {
    /**
     * 检测入库连接格式
     * @param $url
     * @param $obj
     * @return bool
     */
    function check_social_url($url, $obj)
    {

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
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

        if (true === empty($info['host'])) {
            return false;
        }

        if (true === empty($allowList[$obj])) {
            return false;
        }

        if (!substr_count($info['host'], $allowList[$obj])) {
            return false;
        }

        return true;
    }
}
if (! function_exists('cn_strlen')) {
    /**
     *  UTF8编码下 字符串的字数统计
     * @param type $str
     * @return int
     */
    function cn_strlen($str)
    {
        $count = 0;
        $str_len = strlen($str);
        for ($i = 0; $i < $str_len; $i++) {
            $now_word = ord(substr($str, $i, 1));
            if ($now_word > 0xa0) {
                $i+=2; //GB2312编码下为 $++
                $count++;
            } else {
                $count++;
            }
        }
        return $count;
    }
}

if (! function_exists('response_error')) {
    /**
     * 返回错误的响应信息
     *
     * @param string  $code        错误码
     * @param string  $message     错误信息
     * @param integer $statusCode  状态码
     * @param array $extra         报错时额外信息返回
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function response_error($code, $message = '', $statusCode = 500, array $extra = [])
    {
        $body = [
            'code'   => $code,
            'message' => $message
        ];
        if ($extra) {
            $body['extra'] = $extra;
        }
        
        return response()->json($body, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (! function_exists('response_success')) {

    /**
     * 返回正确的响应信息
     * @param array $data 返回的数据
     * @param int $statusCode 状态码
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    function response_success(array $data = [], $statusCode = 200)
    {
        $body = empty($data)
            ? new stdClass()
            : $data;

        if ($statusCode === 204) {
            return response()->make('', 204);
        }

        return response()->json($body, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (! function_exists('filter_zh_mobile')) {
    /**
     * 验证中国大陆手机号
     *
     * @param $mobile
     * @return bool
     */
    function filter_zh_mobile($mobile)
    {
        return preg_match('/^(1)[34578]{1}\d{9}$/', $mobile) ? true : false;
    }
}

if (! function_exists('filter_password')) {
    /**
     * 判断密码是否符合要求
     *
     * @param $password
     * @return bool
     */
    function filter_password($password)
    {
        return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,14}$/', $password) ? true : false;
    }
}

if (! function_exists('maskEmail')) {
    /**
     * 隐藏邮箱部分
     *
     * @param $email
     * @return string
     */
    function maskEmail($email)
    {
        $email_array = explode('@', $email);
        $prefix = (strlen($email_array[0]) < 3) ? '' : substr($email, 0, 2);
        $suffix = substr($email_array[0], -1, 1);
        return $prefix.'***'.$suffix.'@'.$email_array[1];
    }
}

if (! function_exists('filter_cn_id_card_num')) {

    /**
     * 身份证号验证(15,18位)
     *
     * @param $idCard
     *
     * @return bool
     */
    function filter_cn_id_card_num($idCard)
    {

        $idCard = strtoupper($idCard);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();

        if (!preg_match($regx, $idCard)) {
            return false;
        }

        if (15 === strlen($idCard)) { //检查15位
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            preg_match($regx, $idCard, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                return true;
            }
        } else { //检查18位
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            preg_match($regx, $idCard, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if (!strtotime($dtm_birth)) { //检查生日日期是否正确
                return false;
            } else { //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b = (int) $idCard{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }

                $n = $sign % 11;
                $val_num = $arr_ch[$n];

                if ($val_num !== $idCard[17]) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

}

if (! function_exists('ecode')) {
    /**
     * 返回错误码
     *
     * @param int $fileCode
     * @param string $errCode
     * @return int
     */
    function ecode($fileCode, $errCode)
    {
        return (int)($fileCode.str_pad($errCode, 3, 0, STR_PAD_LEFT));
    }
}

if (! function_exists('encryptPwd')) {
    /**
     * 加密密码
     *
     * @param $password
     * @return bool|string
     */
    function encryptPwd($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}



if (! function_exists('generation_password')) {
    /**
     * 用户密码生成
     *
     * @param $password
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
if (! function_exists('getVersion')) {
    function getVersion($versionAccept, $allowVersion)
    {
        preg_match('/application\/vnd\.vpgame\.v(\d)\+json/', $versionAccept, $matches);
        $version = '';
        if ($matches) {
            $version = (int)$matches[1];
            if (!in_array($version, $allowVersion, true)) {
                throw new \App\Exceptions\NotFoundHttpException(852000);
            }
            $version = '\V' . $version;
        }
        return $version;
    }
}

if (! function_exists('check_strlen')) {
    /**
     * 检测字符串长度
     *
     * @param $str
     * @param $min
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

if (! function_exists('isPrivateIP')) {
    /**
     * 检测给定 IP 是否是私有网段内
     *
     * @param $ip
     * @return bool
     */
    function isPrivateIP($ip)
    {
        $ip = ip2long($ip);
        $a = ip2long('10.255.255.255') >> 24;
        $b = ip2long('172.31.255.255') >> 20;
        $c = ip2long('192.168.255.255') >> 16;
        return ($ip >> 24 === $a || $ip >> 20 === $b || $ip >> 16 === $c);
    }
}

if (! function_exists('image_cdn_path')) {
    function image_cdn_path($value, $type = 'avatar')
    {
        if ($value) {
            if (stripos($value, 'http://') !== false || stripos($value, 'https://') !== false) {
                $url = $value;
            } else {
                $url = 'http://thumb.vpgcdn.com/' . $value;
            }
        } else {
            $value = ($type === 'avatar') ? 'avatar.png' : 'empty.png';
            $url = 'http://thumb.vpgcdn.com/' . $value;
        }
        return $url;
    }
}
if(!function_exists('create_order_no')){
    /**
     * 生成指定长度带前缀的订单号
     * @param null $prefix
     * @param int $len
     * @return string
     */
    function create_order_no($prefix=null){

        $orderNo = $prefix.date('Ymdhis'). date('d') . substr(time(), -3) . substr(microtime(), 2, 5) . sprintf('%02d', mt_rand(0, 99));

        return $orderNo;

    }
}
