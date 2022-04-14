<?php

use common\components\SessionHandler;
use yii\helpers\Url;
use yii\web\UrlNormalizer;

$idPattern = '(\w|-)+';

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'order/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'kuntsevo-orders',
            'timeout' => 86400 * 7,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => YII_DEBUG ? ['error', 'warning', 'info', 'trace'] : ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'order/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true, //только перечисленные ниже
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            ],
            'rules' => [
                "order/error" => 'order/error',
                "<customer:$idPattern>/order/<order:$idPattern>/<action>/<component:\w+>" => 'order/<action>',
                "<customer:$idPattern>/order/<order:$idPattern>/<action>" => 'order/<action>',
                "<customer:$idPattern>/order/<order:$idPattern>" => 'order/show',
                "<customer:$idPattern>/order" => 'order/index',
                "<customer:$idPattern>/document/<order:$idPattern>/<component:\w+>" => 'document/show',
                "<customer:$idPattern>/document/<order:$idPattern>" => 'document/index',
                "<customer:$idPattern>/payment/<order:$idPattern>/<action>/<component:\w+>" => 'payment/<action>',
                "<customer:$idPattern>/payment/<order:$idPattern>/<action>" => 'payment/<action>',
                "<customer:$idPattern>/payment/<order:$idPattern>" => 'payment/index',
                "<customer:$idPattern>/agreement" => 'agreement/index',
                "agreement/signing" => 'agreement/signing',
                "agreement/view/<view>" => 'agreement/view',
            ],
        ],
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@console/runtime/queue',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 1,
        ],
        'view' => [
            'renderers' => [
                'pug' => [
                    'class' => 'Pug\Yii\ViewRenderer',
                    'systemVariable' => '_yii',
                ],
            ],
        ],
        'urlHelper' => [
            'class' => Url::class,
        ],
        'sessionHandler' => [
            'class' => SessionHandler::class,
        ],
    ],
    'aliases' => [
        //---------------------------------------------------------------------------
        // псевдонимы URL
        '@orders' => 'order/index',
        '@orderItem' => 'order/show',
        '@orderTable' => 'order/table',
        '@statusHistory' => 'order/status-history',
        '@documents' => 'document/index',
        '@document' => 'document/show',
        '@payments' => 'payment/index',
        '@payment' => 'payment/pay',
        '@agreements' => 'agreement/index',
        '@staffPhotoBlanc' => 'https://www.jespo.be/wp-content/uploads/2013/04/vrijwilliger-worden-01-3-1030x728.png',
        //---------------------------------------------------------------------------
        '@files' => '/uploads',
    ],
    'params' => $params,
];
