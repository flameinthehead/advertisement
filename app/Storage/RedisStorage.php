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

    public function save($key, $value)
    {
        if(is_array($value)){
            $status = $this->storage->hmset($key, $value);
        } else {
            $status = $this->storage->set($key, $value);
        }

        return ($status->getPayload() == 'OK');
    }

    public function find($key, $isHash = false)
    {
        if(!$isHash){
            return $this->storage->get($key);
        }

        return $this->storage->hgetall($key);
    }

    public function findAll($searchKey = '')
    {
        $list = $this->storage->keys($searchKey);
        $result = [];
        foreach ($list as $key) {
            $result[] = $this->find($key, true);
        }

        return $result;
    }
}
