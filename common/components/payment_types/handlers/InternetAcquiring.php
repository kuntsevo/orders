<?php

namespace common\components\payment_types\handlers;

use common\components\Ext1c;
use common\interfaces\IPayment;
use frontend\models\Orders;
use Yii;
use yii\web\Controller;

class InternetAcquiring implements IPayment
{
    const PAYMENT_TYPE = 'internet_acquiring';

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function payOrder(Orders $order)
    {
        $config = [
            'order_type' => $order->document_type,
            'uid' => $order->uid,
            'payment_type' => self::PAYMENT_TYPE
        ];

        $request_handler = new Ext1c(self::HTTP_TEMPLATE_PAYMENTS);

        $result = $request_handler->putJSON($order->base_id, $config);

        $data = '';

        if (isset($result['url'])) {
            $data = trim($result['url']);
        } elseif ($result['message']) {
            $message = trim($result['message']);
            Yii::$app->sessionHandler->sendWarning($message);
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['url' => $data];
        return $response->send();
    }
}
