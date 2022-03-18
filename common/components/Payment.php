<?php

namespace common\components;

use common\components\payment_types\handlers\InternetAcquiring;
use common\interfaces\IPayment;
use frontend\models\Orders;
use yii\web\Controller;

class Payment
{
	static $payment_types = ['internet_acquiring' => 'Интернет-эквайринг'];

	private $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

	public function payOrder(Orders $order)
	{
		$payment_handler = $this->createPayment($this->controller);

		return $payment_handler->payOrder($order);
	}

	private function createPayment(Controller $controller): IPayment
	{
		// переопределяем в наследниках
		return new InternetAcquiring($controller); // значение по умолчанию
	}
}
