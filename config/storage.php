<?php

// хранилище данных (по умолчанию Redis) cо своими параметрами подключения
return [
    'default' => 'redis',
    'connections' => [
        'redis' => [
            'scheme' => 'tcp',
            'host'   => 'redis',
            'port'   => 6379,
        ]
    ],
];
