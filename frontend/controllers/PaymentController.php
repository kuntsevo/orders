<?php

namespace frontend\controllers;

use common\components\Payment;
use common\components\payment_types\fabrics\InternetAcquiringFabric;
use frontend\models\Orders;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Payment controller
 */
class PaymentController extends Controller
{
	private $_baseurl_redirect = 'https://kuntsevo.com';
	private $payment_handler;

	//---------------------------------------------------------------------------
	public function behaviors()
	//---------------------------------------------------------------------------
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'only' => [],
				'rules' => [
					[
						'actions' => ['index', 'pay'],
						'allow' => true,
						'roles' => ['?'],
					],
				],
			],
		];
	}

	//---------------------------------------------------------------------------
	protected function verbs()
	//---------------------------------------------------------------------------
	{
		return [
			'index' => ['GET'],
			'pay' => ['GET'],
		];
	}

	//---------------------------------------------------------------------------
	public function actionIndex()
	//---------------------------------------------------------------------------
	{
		$order_id = Yii::$app->request->get('order');
		if (is_null($order_id))
			return $this->redirect($this->baseUrlRedirect);

		$order = Orders::findOrderByUid($order_id);

		$payment_types = Payment::$payment_types;

		return $this->render('index.pug', compact('order', 'payment_types'));
	}

	public function actionPay()
	{
		$order_id = Yii::$app->request->get('order');
		if (is_null($order_id))
			return $this->redirect($this->baseUrlRedirect);

		$payment_type = Yii::$app->request->get('component');
		if (is_null($payment_type))
			return $this->redirect($this->baseUrlRedirect);

		switch ($payment_type) {
			case 'internet_acquiring':
				$this->payment_handler = new InternetAcquiringFabric($this);
				break;
			default:
				new NotFoundHttpException('Неизвестный тип платежа');
				break;
		}
		
		$order = Orders::findOrderByUid($order_id);

		return $this->payment_handler->payOrder($order);
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}
}
