<?php


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
