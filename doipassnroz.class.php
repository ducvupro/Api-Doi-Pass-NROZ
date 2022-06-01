<?php

/* *
   * @filename doipassnroz.class.php
   * @author DucVuPro
   * @version 1.0.0
   * @description code api đổi password nroz
   * Để Tôn Trọng Tác Giả Vui Lòng Không Xoá Hoặc Chỉnh Sửa Các Dòng Này
   * */

class DoiPassNROZ {

    private static $cookie = './cookie.txt';
    private static $ua = 'Mozilla/5.0 (Linux; Android 5.1; HUAWEI LUA-U22 Build/HUAWEILUA-U22; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/92.0.4515.115 Mobile Safari/537.36';
    private static $stt = '';
    private static $msg = '';

    public static function DucVuPro($user, $pass, $newpass) {
        self::ChangePassword(self::LoginToken(), $user, $pass, $newpass);
        unlink(self::$cookie);
        return json_encode(["stt" => self::$stt, "msg" => self::$msg]);
    }

    public static function LoginToken() {
        $cookie = self::$cookie;
        $ua = self::$ua;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_URL => 'https://nroz.me/login',
            CURLOPT_REFERER => 'https://nroz.me/login',
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        $res = curl_exec($ch);
        preg_match('#name="_token" value="(.*?)"#is', $res, $token);
        $token = trim($token[1]);
        return $token;
        curl_close($ch);
    }

    public static
    function ChangePassword($token, $user, $pass, $newpass) {
        $cookie = self::$cookie;
        $ua = self::$ua;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_COOKIEFILE => $cookie,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_URL => 'https://nroz.me/login',
            CURLOPT_REFERER => 'https://nroz.me/login',
            CURLOPT_FOLLOWLOCATION => true
        ]);
        $pa = array(
            '_token' => $token,
            'username' => $user,
            'password' => $pass,
            'remember' => 'forever'
        );
        curl_setopt($ch, CURLOPT_POST, count($pa));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pa);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        $res = curl_exec($ch);
        preg_match('#<div class="alert alert-danger alert-block" role="alert">(.*?)</div>#is', $res, $stt);
        $stt = trim($stt[1]);
        if ($stt) {
            self::$stt = "error";
            self::$msg = $stt;
        } else {
            curl_setopt_array($ch, [
                CURLOPT_USERAGENT => $ua,
                CURLOPT_COOKIEFILE => $cookie,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_URL => 'https://nroz.me/user/change-password',
                CURLOPT_REFERER => 'https://nroz.me',
                CURLOPT_FOLLOWLOCATION => true
            ]);
            curl_setopt($ch, CURLOPT_POST, 0);
            $res = curl_exec($ch);
            preg_match('#name="_token" type="hidden" value="(.*?)"#is', $res, $token);
            $token = trim($token[1]);
            curl_setopt_array($ch, [
                CURLOPT_USERAGENT => $ua,
                CURLOPT_COOKIEFILE => $cookie,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_URL => 'https://nroz.me/user/change-password',
                CURLOPT_REFERER => 'https://nroz.me/user/change-password',
                CURLOPT_FOLLOWLOCATION => true
            ]);
            $pa = array(
                'change-password' => '',
                '_token' => $token,
                'password' => $pass,
                'new_password' => $newpass,
                'new_password_confirmation' => $newpass
            );
            curl_setopt($ch, CURLOPT_POST, count($pa));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pa);
        $res = curl_exec($ch);
        preg_match('#<div class="alert alert-danger alert-block" role="alert">(.*?)</div>#is', $res, $stt);
        preg_match('#<div class="alert alert-success alert-block" role="alert">(.*?)</div>#is', $res, $stt2);
        $sttt = trim($stt[1]);
        $sttt2 = trim($stt2[1]);
            if ($sttt) {
                self::$stt = "error";
                self::$msg = $sttt;
            } else {
                self::$stt = "success";
                self::$msg = $sttt2;
            }
        }
        curl_close($ch);
    }

}

?>