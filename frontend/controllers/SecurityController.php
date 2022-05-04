<?php

namespace frontend\controllers;

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
		$customer = Yii::$app->request->get('customer');

		if (is_null($customer))
			throw new UnauthorizedHttpException('В запросе отсутствует параметр "customer".');

		return $this->render('verification.pug', compact('customer'));
	}
	//---------------------------------------------------------------------------
	public function actionVerification()
	//---------------------------------------------------------------------------
	{
		return 'OK!';
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}
}
