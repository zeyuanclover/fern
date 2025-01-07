<?php

namespace Vegetation\Fern\Adapter;

class RedisSingle
{
    protected $redis;

    protected $safe;

    public function __construct($redis,$safe=null)
    {
        $this->redis = $redis;
        $this->safe = $safe;
    }

    /**
     * @param $key
     * @param $value
     * @param $expire
     * @return bool
     * 设置缓存
     */
    public function set($key, $value,$expire = true)
    {
        if (is_array($value)){
            $value = json_encode($value);
        }

        // safe
        if($this->safe){
            $value = $this->safe->publicEncrypt($value);
            if(!$value){
                $value = $this->safe->publicEncryptBig($value);
            }
        }

        if($expire!==true){
            $rs = $this->redis->setex($key,$expire,$value);
        }else{
            $rs = $this->redis->set($key,$value);
        }

        if ($rs=='OK'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $key
     * @return mixed|null
     * 获得缓存
     */
    public function get($key)
    {
        $value = $this->redis->get($key);

        if ($this->safe){
            $value = $this->safe->privDecrypt($value);
            if(!$value){
                $value = $this->safe->privDecryptBig($key);
            }
        }

        if(!$value){
            return null;
        }

        if (json_validate($value)){
            return json_decode($value,true);
        }

        return $value;
    }

    /**
     * @param $key
     * @param $expire
     * @return mixed
     * 设置过期时间
     */
    public function expire($key,$expire)
    {
        return $this->redis->expire($key,$expire);
    }

    /**
     * @param $key
     * @return mixed
     * 删除缓存
     */
    public function del($key)
    {
        return $this->redis->del($key);
    }

    /**
     * @param $key
     * @return mixed
     * 获取剩余时间
     */
    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }
}