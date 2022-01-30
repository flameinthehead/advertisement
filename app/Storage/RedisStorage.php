<?php

namespace App\Storage;

use App\Exceptions\StorageException;
use Predis\Client;

class RedisStorage implements StorageInterface
{
    /* @var Client */
    private $storage;

    public function __construct()
    {
        $this->storage = \App::storage();
        if(!$this->storage || !$this->storage instanceof Client){
            throw new StorageException('Init RedisStorage failed');
        }
    }

    public function get($key)
    {
        return $this->storage->get($key);
    }

    public function set($key, $value)
    {
        /* @var $status \Predis\Response\Status */
        $status = $this->storage->set($key, $value);
        return ($status->getPayload() == 'OK');
    }

    public function setHash($key, $hash = [])
    {
        /* @var $status \Predis\Response\Status */
        $status = $this->storage->hmset($key, $hash);
        return ($status->getPayload() == 'OK');
    }

    public function getHash($key, $fields = [])
    {
        if(empty($fields)){
            return $this->storage->hgetall($key);
        }

        return $this->storage->hget($key, $fields);
    }

    public function getAll($searchKey = '')
    {
        $list = $this->storage->keys($searchKey);
        $result = [];
        foreach ($list as $key) {
            $result[] = $this->getHash($key);
        }

        return $result;
    }
}
