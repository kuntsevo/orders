<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => '',
			'username' => '',
			'password' => '',
			'charset' => 'utf8'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'currencyCode' => 'RUB',
        ],
    ],
    'language' => 'ru-RU',
];
