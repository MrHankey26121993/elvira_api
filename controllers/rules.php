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
            'OPTIONS slide' => 'options',
            'POST works' => 'works',
            'OPTIONS works' => 'options',
            'DELETE slide' => 'remove-slide',
            'DELETE service' => 'remove-service',
            'DELETE works' => 'remove-work',
            'POST service' => 'service',
            'OPTIONS service' => 'options',
            'POST form' => 'form',
            'OPTIONS <module:\w+>s/<action>' => 'options',
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
