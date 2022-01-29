<?php

return [
    [
        'method' => 'POST',
        'rule' => '|\/ads$|',
        'route' => [App\Controllers\AdsController::class, 'add']
    ],
    [
        'method' => 'GET',
        'rule' => '|\/ads\/relevant|',
        'route' => [App\Controllers\AdsController::class, 'relevant']
    ],
    [
        'method' => 'POST',
        'rule' => '|\/ads\/([0-9])|',
        'route' => [App\Controllers\AdsController::class, 'edit']
    ],
];
