<?php

namespace common\interfaces;

use frontend\models\Orders;
use yii\web\Controller;

interface IPayment
{
    const HTTP_TEMPLATE_PAYMENTS = 'payments';

    public function __construct(Controller $controller);
    public function payOrder(Orders $order);
}
