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

        return $this->cast($ads);
    }

    public function relevant()
    {
        if(!$ads = $this->entity->getMostExpensive()){
            throw new AdsException('There is no available ads');
        }

        if($this->entity->update($ads['id'], ['limit' => --$ads['limit']])){
            return $this->cast($ads);
        }

        return false;
    }

    public function update($id, $fields)
    {
        if(!$this->entity->update($id, $fields)){
            throw new AdsException('Invalid during update with id = '.$id);
        }

        return $this->cast($this->entity->get($id));
    }

    public function cast($ads)
    {
        return $this->entity->casts($ads);
    }
}
