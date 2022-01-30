<?php

namespace App\Storage;

class ArrayStorage implements StorageInterface
{
    private $repository;

    public function __construct()
    {
        $this->repository = [];
    }

    public function find($key)
    {
        if(!isset($this->repository[$key])){
            return null;
        }
        return $this->repository[$key];
    }

    public function save($key, $value)
    {
        $this->repository[$key] = $value;
        return true;
    }

    public function findAll()
    {
        return $this->repository;
    }
}
