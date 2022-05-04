<?php

namespace frontend\behaviors;

use Yii;
use frontend\models\Agreements;
use frontend\models\Orders;
use yii\base\Behavior;
use yii\base\Controller;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class AgreementsSigning extends Behavior
{
    const UNSIGNED_AGREEMENTS_KEY = 'unsignedAgreements';
    private const REQUESTED_CACHE_KEY = 'requestedOrders';
    private const DEFAULT_REQUESTED_TYPES = ['Cookies',];

    public $requested_types = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    public function beforeAction($event)
    {
        Yii::$app->cache->delete(static::REQUESTED_CACHE_KEY);
    }

    public function afterAction($event)
    {
        $cache = Yii::$app->cache;
        $requestedOrders = $cache->getOrSet(static::REQUESTED_CACHE_KEY, function () {
            return [];
        });

        if (!$requestedOrders) {
            return $event->result;
        }

        $dealer_ids = array_unique(array_map(
            [self::class, 'extractDealerIds'],
            $requestedOrders,
        ));

        $customer_id = Yii::$app->sessionHandler->getCustomerId();
        $unsignedAgreements = Agreements::getUnsignedAgreements(
            $dealer_ids,
            $customer_id,
            array_merge(static::DEFAULT_REQUESTED_TYPES, $this->requested_types)
        );

        $cache->delete(static::REQUESTED_CACHE_KEY);

        /*
        если есть соглашения, требующие "подписания", 
        то перенаправляем пользователя на специальную страницу для выполнения этого действия
        */
        if ($unsignedAgreements) {
            // запоминаем
            // список соглашений
            $cache->set(static::UNSIGNED_AGREEMENTS_KEY, $unsignedAgreements);
            // страницу, на которую изначально заходил клиент - на нее будем переходить после подписания соглашений
            Url::remember($event->sender->request->url);

            return $event->sender->redirect(Url::to(['@agreements', 'customer' => $customer_id]));
        }

        return $event->result;
    }

    public function afterFind($event)
    {
        $cache = Yii::$app->cache;
        $requestedOrders = $cache->getOrSet(static::REQUESTED_CACHE_KEY, function () {
            return [];
        });

        array_push($requestedOrders, $event->sender);

        $cache->set(static::REQUESTED_CACHE_KEY, $requestedOrders);
    }

    private static function extractDealerIds(Orders $order)
    {
        return $order->dealer_id;
    }
}
