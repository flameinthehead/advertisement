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
        return !empty($this->storage->find($this->getStorageKey($ads), true));
    }

    // добавление в хранилище
    public function add($ads)
    {
        $lastId = $this->storage->find('lastId');
        if(empty($lastId)){
            $lastId = 0;
        }

        $storageKey = $this->getStorageKey($ads);
        $ads['id'] = ++$lastId;
        if($this->storage->save($storageKey, $ads)){
            $this->storage->save('lastId', $lastId);
            $this->storage->save($ads['id'], $storageKey);
            return $ads;
        }

        return false;
    }

    // получения объявления по числовому id
    public function get($id)
    {
        $hashId = $this->storage->find($id);
        $ads = $this->storage->find($hashId, true);
        if(!$ads){
            throw new AdsException(sprintf('Ads with id = %s not found', $id));
        }

        return $ads;
    }

    // обновление
    public function update($id, $fields = [])
    {
        $ads = $this->get($id);
        return $this->storage->save(
            $this->storage->find($id),
            array_merge($ads, $fields)
        );
    }

    // получение самого дорогого объявления
    public function getMostExpensive()
    {
        $all = $this->filterNoLimit($this->storage->findAll(self::REGEX_ADS_KEY));


        if(!$all || !is_array($all)){
            return;
        }

        $all = $this->sortByPrice($all);
        return reset($all);
    }

    public function getCasts()
    {
        return $this->casts;
    }

    public function getResponseOnly()
    {
        return $this->responseOnly;
    }

    // ключ для хранилища
    private function getStorageKey($ads)
    {
        if(isset($ads['id'])){
            unset($ads['id']);
        }

        return self::ADS_PREFIX_KEY.md5(serialize($ads));
    }

    // отсеивание объявлений, у которых закончились показы
    private function filterNoLimit($list = [])
    {
        return array_filter($list, function ($item){
            return (isset($item['limit']) && $item['limit'] > 0);
        });
    }

    // сортировка по стоимости по убыванию
    private function sortByPrice($list = [])
    {
        usort($list, function ($a, $b){
            if($a['price'] > $b['price']){
                return -1;
            }
            return 1;
        });

        return $list;
    }
}
