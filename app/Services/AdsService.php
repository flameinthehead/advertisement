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

        if(!$this->entity->add($adsRequest)){
            throw new AdsException('Error during adding advertisement');
        }

        return true;
    }
}
