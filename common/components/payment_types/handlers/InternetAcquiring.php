<?php

namespace common\components\payment_types\handlers;

use common\components\Ext1c;
use common\components\Payment;
use common\interfaces\IPayment;
use frontend\models\Orders;
use Yii;
use yii\web\Controller;

class InternetAcquiring implements IPayment
{
    const PAYMENT_TYPE = 'internet_acquiring';

    private $controller;

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

        if (isset($result['url']) or !empty($result['url'])) {
            $external_url = trim($result['url']);
            $payment_types = Payment::$payment_types;
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = ['url' => $external_url];
            $response->send();
        }

        if (isset($result['message']) or !empty($result['message'])) {
            $message = trim($result['message']);
            $payment_types = Payment::$payment_types;
            Yii::$app->getSession()->setFlash('warning', $message);
            return $this->controller->render('index', compact('order', 'payment_types'));
        }

        return $this->controller->redirect($this->controller->baseUrlRedirect);
    }
}
