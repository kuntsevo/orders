<?php

namespace common\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\UnauthorizedHttpException;

class CodeVerification extends ActionFilter
{
    public function beforeAction($action)
    {
        $customer_id = Yii::$app->request->get('customer') ?? Yii::$app->sessionHandler->getCustomerId();
        if (is_null($customer_id))
            throw new UnauthorizedHttpException('В сессии и/или запросе отсутствует параметр id пользователя.');

        // если текущий пользователь отличается от того, что сохранен в сесси, то разлогинимся, чтобы зайти заново
        if ($customer_id !== Yii::$app->sessionHandler->getCustomerId()) {
            Yii::$app->user->logout();
        }

        $isGuest = Yii::$app->user->isGuest;
        if ($isGuest) {
            Yii::$app->sessionHandler->setCustomerId($customer_id);
            Yii::$app->user->loginRequired();
            return false;
        }

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}
