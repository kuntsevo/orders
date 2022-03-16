<?php

namespace common\components;

use frontend\models\Orders;
use yii\base\BaseObject;

class Payment extends BaseObject
{
	const HTTP_TEMPLATE_PAYMENTS = 'payments';

	public function payOrder(Orders $order)
	{
		$config = ['order_type' => $order->document_type, 'uid' => $order->uid];
		$request_handler = new Ext1c(self::HTTP_TEMPLATE_PAYMENTS);

		return $request_handler->putJSON($order->base_id, $config);
	}
}
