<?php

namespace App\Entity;

// сущность объявления

use App\Exceptions\AdsException;
use App\Storage\StorageInterface;

class Ads
{
    const CAST_INT = 'int';
    const REGEX_ADS_KEY = 'ads_*';
    const ADS_PREFIX_KEY = 'ads_';
    private $storage;
    private $casts = [
        'id' => self::CAST_INT,
        'price' => self::CAST_INT,
        'limit' => self::CAST_INT,
    ];

    private $responseOnly = ['id', 'text', 'banner'];

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
            return $this->casts($this->checkOnlyFields($ads));
        }

        return false;
    }

    public function getMostExpensive()
    {
        $all = $this->filterNoLimit($this->storage->getAll(self::REGEX_ADS_KEY));

        if(!$all || !is_array($all)){
            return;
        }

        $all = $this->sortByPrice($all);
        $expensiveAds = reset($all);

        return $this->casts($expensiveAds);
    }

    public function update($id, $fields = [])
    {
        $hashId = $this->storage->get($id);
        $ads = $this->storage->getHash($hashId);
        if(!$ads){
            throw new AdsException(sprintf('Ads with id = %s not found', $id));
        }

        foreach($fields as $field => $value){
            $ads[$field] = $value;
        }

        return $this->storage->setHash($hashId, $ads);
    }

    // ключ для хранилища
    private function getStorageKey($ads)
    {
        if(isset($ads['id'])){
            unset($ads['id']);
        }

        return self::ADS_PREFIX_KEY.md5(serialize($ads));
    }

    // фильтрация лишних полей и сортировка, согласно их порядку
    private function checkOnlyFields($ads)
    {
        if(!$onlyFields = $this->responseOnly){
            return $ads;
        }

        $output = [];
        foreach($onlyFields as $fieldName){
            $output[$fieldName] = $ads[$fieldName];
        }

        return $output;
    }

    private function filterNoLimit($list = [])
    {
        return array_filter($list, function ($item){
            return ($item['limit'] > 0);
        });
    }

    private function sortByPrice($list = [])
    {
        usort($list, function ($a, $b){
            if($a['price'] < $b['price']){
                return -1;
            }
            return 1;
        });

        return $list;
    }

    private function casts($ads)
    {
        foreach($ads as $fieldName => $value){
            if(!isset($this->casts[$fieldName])){
                continue;
            }

            if($this->casts[$fieldName] == self::CAST_INT){
                $ads[$fieldName] = (int)$value;
            }
        }

        return $ads;
    }
}
