<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * 请求接口服务器
     */
    public function sign()
    {
        $key = "1907";

        // $str = $_GET['str'];
        // echo "签名前的数据".$str;echo "<br>";
        $data = "Hier";
        
        $sign = md5($data.$key);
        echo "计算后签名".$sign;echo "<br>";echo "<hr>";

        //请求路径
        $url = "http://1905liujingjing.comcto.com/test/verifySign?data=".$data.'&sign='.$sign;
        echo $url;echo "<hr>";

        //签名
        $response = file_get_contents($url);
        var_dump($response);    
    }
    
    
    /**
     *openssl 加密
     */
    public function  encrypt()
    {
       $str = 'Hello';
       $length = strlen($str); //获取长度
       $new_str = '';
       for($i=0;$i<$length;$i++)
       {
          //echo $str[$i].'>'.ord($str[$i]);echo "<br>";
           $code = ord($str[$i]) + 1;
          //echo "编码 $str[$i]".'>'.$code.'>'.chr($code);echo "<br>";
            $new_str .= chr($code);
       }
       //请求接口将加密数据发送出去
       $url = 'http://api.1907.com/test/decrypt?data='.$new_str;
       $response = file_get_contents($url);
       var_dump($response);
    }

    /**
     * 解密
     */
    public function decrypt()
    {
        $data = 'Ifmmp'; //密文
        echo "密文:".$data;echo "<hr>";

        //解密
        $length = strlen($data);

        $str = '';
        for($i=0;$i<$length;$i++)
        {
            echo $data[$i].'>'.ord($data[$i]);echo "<br>";
            $code = ord($data[$i]) - 1;
            echo "解码:".$data[$i].'>'.chr($code);echo "<br>";
            $str .= chr($code);
        }

        echo "解码后的数据:".$str;
    }

    /**
     * base64加密
     */
    public function encrypt1()
    {
        $key = '1907';

        $data = 'Hello';
        $method = 'aes-128-cbc'; //加算法
        $iv = '123456abc123456a';  //vi 必须为16个字节 (16个ascii字符)
        
        //加密
        $enc_str = openssl_encrypt($data,$method,$key,OPENSSL_RAW_DATA,$iv);
        echo "加密前的密文:".$data;echo "<br>";
        echo "加密后的密文:".$enc_str;echo "<br>";
        
        //将加密后的数据发送出去
        $base64_str = base64_encode($enc_str);   //将秘闻base64编码
        echo "base64编码后的密文:".$base64_str;

        //将加密的数据发出去
        $url = 'http://api.1907.com/test/decrypt1?data='.$base64_str;
        $response = file_get_contents($url);
        var_dump($response);
    }

    /** 
     * 加密 + 验签
     */
    public function yan()
    {
        $key = "1907";
        $data = "Hi";
        $sign = md5($data.$key);

        $method = 'aes-128-cbc'; //加算法
        $iv = 'abc123456a123456';  //vi 必须为16个字节 (16个ascii字符)
        $enc_str = openssl_encrypt($data,$method,$key,OPENSSL_RAW_DATA,$iv);
        echo "加密后的密文:".$enc_str;echo "<br>";
        $base64_str = base64_encode($enc_str);   //将密文base64编码
        echo "base64编码后的密文:".$base64_str;echo "<hr>";

        //将加密的数据发出去
        $url = 'http://api.1907.com/test/decrypt1?data='.$base64_str;
        $response = file_get_contents($url);
        var_dump($response);
        //请求路径
        $url1 = "http://api.1907.com/test/task?data=".$base64_str.'&sign='.$sign;
        echo $url;echo "<br>";

        //签名
        $response = file_get_contents($url1);
        var_dump($response);   
    }

    /**
     * 非对称加密  使用公钥加密 这个公钥指的是对方的公钥而不是自己的公钥
     */ 
    public function Asymmetric()
    {
        $data = 'Hello';

        //这个路径是公钥的路径
        $key = file_get_contents(storage_path('keys/pub-a.key'));
        // echo $key;

        //加密
        openssl_public_encrypt($data,$enc_data,$key);
        // var_dump($enc_data);
       
        //将加密数据  base64_encode()
        $send_data = base64_encode($enc_data);
        // echo "base64加密后的数据:".$send_data;echo "<br>";

        //将编码后的加密的数据发送个对方
        $url = "http://api.1907.com/test/Connection?data=".urlencode($send_data);
        $response = file_get_contents($url);
        echo "<hr>";
        echo "收到的数据:".$response;

        $arr = json_decode($response,true);
        echo "密文:".$arr['data'];

        //base64_decode(收到的是encode)
        $enc_str = base64_decode($arr['data']);
        //对数据进行解密
        $key = file_get_contents(storage_path('keys/priv-b.key'));
        openssl_private_decrypt($enc_str,$dec_str,$key);

        echo "解密后数据:".$dec_str;
    }

    /** 
     * 非对称 验签
     */
    public function rsaSign()
    {
        $data = 'Hello';

        $priv_key_id = openssl_pkey_get_private("file://".storage_path('keys/priv-b.key'));

        //生成签名
        openssl_sign($data,$sign,$priv_key_id,OPENSSL_ALGO_SHA256);
        var_dump($sign);echo "<br>";

        $b64_sign_str = base64_encode($sign);
        echo "base64后的签名:".$b64_sign_str;echo "<hr>";

        //发送数据
        $url = 'http://api.1907.com/test/verify?data='.$data.'&sign='.$b64_sign_str;
        $response = file_get_contents($url);
        var_dump($response);
    }

}
