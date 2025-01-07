<?php

namespace Vegetation\Fern\Adapter;

class FileSystem
{
    private $path;

    private $safe;

    /**
     * @param $path
     * @param $safe
     * 构造函数
     */
    public function __construct($path,$safe=null)
    {
        $path = rtrim($path,'/');
        $path = rtrim($path,'\\');
        $this->path = $path;
        $this->safe = $safe;
    }

    /**
     * @param $key
     * @param $value
     * @param $expire
     * @return true
     * 设置缓存
     */
    public function set($key,$value,$expire = true)
    {
        $saveDir = $this->path;
        if(!is_dir($saveDir)){
            mkdir($saveDir,0777, true);
        }
        $savePath = $saveDir . DIRECTORY_SEPARATOR . $key . '.php';

        if($this->safe){
            $value = $this->safe->publicEncrypt($value);
            if(!$value){
                $value = $this->safe->publicEncryptBig($value);
            }
        }

        $data = [];
        $data['content'] = $value;
        $data['expire'] = $expire;
        if($expire!==true){
            $data['expire'] += time();
        }

        file_put_contents($savePath,"<?php \n return ".var_export($data,true).";");
        return true;
    }

    /**
     * @param $key
     * @return mixed|null
     * 获得缓存
     */
    public function get($key){
        $readDir = $this->path;
        $readPath = $readDir .DIRECTORY_SEPARATOR. $key . '.php';
        if(is_dir($readDir)){
            if (is_file($readPath)) {
                $data = include $readPath;
                if ($data['expire']===true||$data['expire']>time()) {
                    $value = $data['content'];
                    if($this->safe){
                        $value = $this->safe->privDecrypt($value);
                        if(!$value){
                            $value = $this->safe->privDecryptBig($value);
                        }
                    }
                    return $value;
                }
            }
        }
        return null;
    }

    /**
     * @param $key
     * @return bool
     * 删除缓存
     */
    public function delete($key)
    {
        $readDir = $this->path;
        $readPath = $readDir .DIRECTORY_SEPARATOR. $key . '.php';
        if(is_dir($readDir)) {
            if (is_file($readPath)) {
                unlink($readPath);
                return true;
            }
        }
        return false;
    }

    /**
     * @param $key
     * @return int|mixed|true|null
     * 获取剩余时间
     */
    public function ttl($key)
    {
        $readDir = $this->path;
        $readPath = $readDir . DIRECTORY_SEPARATOR .$key . '.php';
        if(is_dir($readDir)){
            if (is_file($readPath)) {
                $data = include $readPath;
                if($data['expire']===true){
                    return true;
                }else{
                    return $data['expire']-time();
                }
            }
        }
        return null;
    }

    /**
     * @param $key
     * @param $expire
     * @return bool
     * 设置过期时间
     */
    public function expire($key,$expire)
    {
        $readDir = $this->path;
        $readPath = $readDir . DIRECTORY_SEPARATOR .$key . '.php';
        if(is_dir($readDir)){
            if (is_file($readPath)) {
                $data = include $readPath;
                $this->set($key,$data,$expire);
                return true;
            }
        }
        return false;
    }
}