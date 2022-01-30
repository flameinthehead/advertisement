<?php

namespace App\Storage;

interface StorageInterface
{
    public function save($key, $value);

    public function find($key);

    public function findAll();
}
