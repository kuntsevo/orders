<?php

namespace common\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\UnauthorizedHttpException;

class CodeVerification extends ActionFilter
{
    public function beforeAction($action)
    {
        $isGuest = Yii::$app->user->isGuest;
		if ($isGuest) {
            $customer_id = Yii::$app->session->get('customer_id') ?? Yii::$app->request->get('customer');
            if (is_null($customer_id))
			    throw new UnauthorizedHttpException('В запросе отсутствует параметр "customer".');
            Yii::$app->session->set('customer_id', $customer_id);
			Yii::$app->user->loginRequired();
		}
        
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}
