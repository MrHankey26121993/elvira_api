<?php
$api_v1 = [
    [
        'class' => \yii\rest\UrlRule::class,
        'pluralize' => false,
        'controller' => [
            'api/data',
        ],
        'extraPatterns' => [
            'GET /' => '/',
            'OPTIONS <action>' => 'options'
        ],
    ],
];


return $api_v1;
