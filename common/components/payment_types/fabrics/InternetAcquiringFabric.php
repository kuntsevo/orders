<?php

namespace common\components\payment_types\fabrics;

use common\components\Payment;
use common\components\payment_types\handlers\InternetAcquiring;
use common\interfaces\IPayment;
use yii\web\Controller;

class InternetAcquiringFabric extends Payment
{
    private function createPayment(Controller $controller) : IPayment
    {
        return new InternetAcquiring($controller);
    }
}
