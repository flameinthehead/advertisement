<?php
return [
    App\Validators\AdsValidator::class => DI\create()->constructor($_REQUEST),
];
