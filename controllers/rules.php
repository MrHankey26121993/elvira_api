<?php
$api = [
    [
        'class' => \yii\rest\UrlRule::class,
        'pluralize' => false,
        'controller' => 'api/data',
        'extraPatterns' => [
            'GET /' => '/',
            'OPTIONS <action>' => 'options'
        ],
    ],
    [
        'class' => \yii\rest\UrlRule::class,
        'pluralize' => false,
        'controller' => 'api/cp',
        'extraPatterns' => [
            'GET /' => '/',
            'OPTIONS <action>' => 'options'
        ],
    ],


];


return $api;
