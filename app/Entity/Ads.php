<?php

namespace App\Entity;

// сущность объявления

use App\Storage\StorageInterface;

class Ads
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    // проверка на дубль
    public function checkExists($data)
    {
        return $this->storage->getHash($this->getStorageKey($data));
    }

    public function add($data)
    {
        $lastId = $this->storage->get('lastId');
        if(empty($lastId)){
            $lastId = 0;
        }

        $data['id'] = ++$lastId;

        $storageKey = $this->getStorageKey($data);
        if($this->storage->setHash($storageKey, $data)){
            $this->storage->set('lastId', $lastId);
            $this->storage->set($data['id'], $storageKey);
            return $data;
        }

        return false;
    }

    private function getStorageKey($data)
    {
        return md5(serialize($data));
    }
}
