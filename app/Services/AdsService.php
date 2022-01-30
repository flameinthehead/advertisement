<?php

namespace App\Services;

use App\Entity\Ads;
use App\Exceptions\AdsException;

class AdsService
{
    private $entity;

    public function __construct(Ads $entity)
    {
        $this->entity = $entity;
    }

    public function add($adsRequest = [])
    {
        if($this->entity->checkExists($adsRequest)){
            throw new AdsException('Same advertisement is already exists');
        }

        if(!$ads = $this->entity->add($adsRequest)){
            throw new AdsException('Error during adding advertisement');
        }

        return $this->casts($ads);
    }

    public function relevant()
    {
        if(!$ads = $this->entity->getMostExpensive()){
            throw new AdsException('There is no available ads');
        }

        if($this->entity->update($ads['id'], ['limit' => --$ads['limit']])){
            return $this->casts($ads);
        }

        return false;
    }

    public function update($id, $fields)
    {
        if(!$this->entity->update($id, $fields)){
            throw new AdsException('Invalid during update with id = '.$id);
        }

        return $this->casts($this->entity->get($id));
    }

    public function get($id)
    {
        return $this->entity->get($id);
    }

    // приведение значений полей к типу для отправки клиенту
    private function casts($ads)
    {
        $ads = $this->checkOnlyFields($ads);
        $casts = $this->entity->getCasts();
        foreach($ads as $fieldName => $value){
            if(!isset($casts[$fieldName])){
                continue;
            }

            if($casts[$fieldName] == $this->entity::CAST_INT){
                $ads[$fieldName] = (int)$value;
            }
        }

        return $ads;
    }

    // фильтрация лишних полей и сортировка, согласно их порядку
    private function checkOnlyFields($ads)
    {
        if(!$onlyFields = $this->entity->getResponseOnly()){
            return $ads;
        }

        $output = [];
        foreach($onlyFields as $fieldName){
            $output[$fieldName] = $ads[$fieldName];
        }

        return $output;
    }
}
