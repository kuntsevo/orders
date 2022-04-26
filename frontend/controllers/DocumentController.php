<?php

namespace frontend\controllers;

use frontend\models\Documents;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ServerErrorHttpException;

/**
 * Document controller
 */
class DocumentController extends Controller
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
						'actions' => ['index'],
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
		];
	}

	//---------------------------------------------------------------------------
	public function actionIndex()
	//---------------------------------------------------------------------------
	{
		$order_id = Yii::$app->request->get('order');
		$customer_id = Yii::$app->request->get('customer');

		if (is_null($order_id))
			throw new ServerErrorHttpException('В запросе отсутствует параметр "order".');

		$document_list = Documents::getOrderDocuments($order_id);

		$this->view->title = 'Документы';

		return $this->render('documents.pug', compact('order_id', 'customer_id', 'document_list'));
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}
}
