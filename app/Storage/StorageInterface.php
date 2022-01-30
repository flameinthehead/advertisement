<?php

namespace App\Storage;

interface StorageInterface
{
    public function set($key, $value);

    public function get($key);

    public function setHash($key, $hash = []);

    public function getHash($key, $fields = []);

    public function getAll();
}
