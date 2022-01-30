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
    public function checkExists($ads)
    {
        return !empty($this->storage->getHash($this->getStorageKey($ads)));
    }

    public function add($ads)
    {
        $lastId = $this->storage->get('lastId');
        if(empty($lastId)){
            $lastId = 0;
        }

        $storageKey = $this->getStorageKey($ads);
        $ads['id'] = ++$lastId;
        if($this->storage->setHash($storageKey, $ads)){
            $this->storage->set('lastId', $lastId);
            $this->storage->set($ads['id'], $storageKey);
            return $this->checkOnlyFields($ads);
        }

        return false;
    }

    // ключ для хранилища
    private function getStorageKey($ads)
    {
        return md5(serialize($ads));
    }

    // ключи, которые отдают клиенту
    private function responseOnly()
    {
        return ['id', 'text', 'banner'];
    }

    // фильтрация лишних полей и сортировка, согласно их порядку
    private function checkOnlyFields($ads)
    {
        if(!$onlyFields = $this->responseOnly()){
            return $ads;
        }

        $output = [];
        foreach($onlyFields as $fieldName){
            $output[$fieldName] = $ads[$fieldName];
        }

        return $output;
    }
}
