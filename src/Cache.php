<?php

namespace Vegetation\Fern;

class Cache
{
    /**
     * @var
     * 实例
     */
    public $instance;

    /**
     * @param $instance
     * 构造函数
     */
    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * @param $key
     * @param $value
     * @param $expire
     * @return mixed
     * 设置缓存
     */
    public function set($key,$value,$expire=true)
    {
        return $this->instance->set($key,$value,$expire);
    }

    /**
     * @param $key
     * @return mixed
     * 获取缓存
     */
    public function get($key)
    {
        return $this->instance->get($key);
    }

    /**
     * @param $key
     * @return mixed
     * 取出值就遗忘
     */
    public function forget($key)
    {
        $val = $this->instance->get($key);
        $this->instance->delete($key);
        return $val;
    }

    /**
     * @param $key
     * @param $expire
     * @return mixed
     * 缓存过期时间
     */
    public function expire($key,$expire)
    {
        return $this->instance->expire($key,$expire);
    }

    /**
     * @param $key
     * @return mixed
     * 缓存剩余时间
     */
    public function ttl($key)
    {
        return $this->instance->ttl($key);
    }

    /**
     * @param $key
     * @return mixed
     * 清除缓存
     */
    public function delete($key)
    {
        return $this->instance->delete($key);
    }
}