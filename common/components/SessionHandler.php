<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\UnauthorizedHttpException;

class SessionHandler extends Component
{
    const CUSTOMER_ID_NAME = 'customer_id';
    const ERROR_MESSAGE_KEY = 'errorMessage';
    const WARNING_KEY = 'warning';
    private $session;

    public function __construct()
    {
        $this->session = Yii::$app->session;
    }

    public function getCustomerId()
    {
        $customer_id = $this->session->get(self::CUSTOMER_ID_NAME);

        if (!$customer_id) {
            throw new UnauthorizedHttpException();
        }

        return $customer_id;
    }

    public function sendWarning(string $message)
    {
        $this->session->setFlash(self::WARNING_KEY, $message);
    }

    public function getWarning()
    {
        return $this->session->getFlash(self::WARNING_KEY);
    }

    public function sendErrorMessage(string $message)
    {
        $this->session->setFlash(self::ERROR_MESSAGE_KEY, $message);
    }

    public function getErrorMessage()
    {
        return $this->session->getFlash(self::ERROR_MESSAGE_KEY);
    }
}
