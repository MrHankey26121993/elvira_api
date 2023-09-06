<?php
$api = [
    [
        'class' => \yii\rest\UrlRule::class,
        'pluralize' => false,
        'controller' => 'api/data',
        'extraPatterns' => [
            'GET /' => '/',
            'GET content' => 'content',
            'POST login' => 'login',
            'POST slide' => 'slide',
            'POST works' => 'works',
            'DELETE slide' => 'remove-slide',
            'DELETE service' => 'remove-service',
            'DELETE works' => 'remove-work',
            'POST service' => 'service',
            'POST form' => 'form',
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
