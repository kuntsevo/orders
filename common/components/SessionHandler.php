<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\UnauthorizedHttpException;

class SessionHandler extends Component
{
    private const CUSTOMER_ID_NAME = 'customer_id';
    private const PHONE_NUMBER_NAME = 'phoneNumber';
    private const AUTHORIZATION_CODE_NAME = 'authorizationCode';
    private const ERROR_MESSAGE_KEY = 'errorMessage';
    private const WARNING_KEY = 'warning';

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

    public function getPhoneNumber()
    {
        $phoneNumber = $this->session->get(self::PHONE_NUMBER_NAME);

        if (!$phoneNumber) {
            throw new UnauthorizedHttpException();
        }

        return $phoneNumber;
    }

    public function getAuthorizationCode()
    {
        $authorizationCode = $this->session->get(self::AUTHORIZATION_CODE_NAME);

        if (!$authorizationCode) {
            throw new UnauthorizedHttpException();
        }

        return $authorizationCode;
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

    public function getClientData()
    {
        $request = Yii::$app->request;

        $client_data = [
            'HTTP_USER_AGENT' => $request->userAgent,
            'HTTP_ACCEPT' => $request->acceptableContentTypes,
            'REMOTE_ADDR' => $request->remoteIP,
            'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'],
            'REQUEST_TIME' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
            'agreement_page' => $request->referrer,
            'phone_number' => Yii::$app->sessionHandler->getPhoneNumber(),
            'authorization_code' => Yii::$app->sessionHandler->getAuthorizationCode(),
        ];

        return $client_data;
    }
}
