<?php

namespace frontend\controllers;

use frontend\models\Customers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;

/**
 * Security controller
 */
class SecurityController extends Controller
{
	private $_baseurl_redirect = 'https://kuntsevo.com';

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
						'actions' => ['verification', 'verification-form'],
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
			'verification-form' => ['GET'],
			'verification' => ['POST'],
		];
	}

	//---------------------------------------------------------------------------
	public function actionVerificationForm()
	//---------------------------------------------------------------------------
	{
		$customer = Yii::$app->sessionHandler->getCustomerId();

		return $this->render('verification.pug', compact('customer'));
	}
	//---------------------------------------------------------------------------
	public function actionVerification()
	//---------------------------------------------------------------------------
	{
		$customer = Yii::$app->request->get('customer');

		if (is_null($customer))
			throw new UnauthorizedHttpException('В запросе отсутствует параметр "customer".');
		
		$identity = Customers::findCustomer($customer);
		Yii::$app->user->on(Yii::$app->user::EVENT_BEFORE_LOGIN, [$identity, 'beforeLogin']);
		Yii::$app->user->on(Yii::$app->user::EVENT_AFTER_LOGIN, [$identity, 'afterLogin']);

		if (!Yii::$app->user->login($identity, 86400 * 14)){
			return $this->render('verification.pug', compact('customer'));
		}

		return $this->redirect(Yii::$app->user->returnUrl);
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}
}
