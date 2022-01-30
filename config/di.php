<?php

return [
    App\Validators\AdsValidator::class => DI\create()->constructor($_REQUEST),
    App\Storage\StorageInterface::class => DI\create(App\Storage\RedisStorage::class)
];
