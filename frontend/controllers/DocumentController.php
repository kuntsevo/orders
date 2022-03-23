<?php

namespace frontend\controllers;

use common\components\DocumentHandler;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use frontend\models\Orders;
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
						'actions' => ['index', 'show'],
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
			'show' => ['GET'],
		];
	}

	//---------------------------------------------------------------------------
	public function actionIndex()
	//---------------------------------------------------------------------------
	{
		$order_id = Yii::$app->request->get('order');
		$order = Orders::findOrderByUid($order_id);
		$document_list = (new DocumentHandler())->getDocumentTypes($order);

		if (!is_array($document_list)) {
			throw new ServerErrorHttpException('Не удалось получить список печатных форм документа');
		}

		asort($document_list);

		$this->view->title = 'Документы';

		return $this->render('documents.pug', compact('order', 'document_list'));
	}

	public function actionShow()
	{
		$document_type = Yii::$app->request->get('component');
		$order_id = Yii::$app->request->get('order');
		$order = Orders::findOrderByUid($order_id);

		$binairy = (new DocumentHandler())->download($order, $document_type);

		if (!is_string($binairy) or empty($binairy)) {
			throw new ServerErrorHttpException('Не удалось получить документ');
		}

		return Yii::$app->response->sendContentAsFile($binairy, "$order->number $document_type.pdf");
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}
}
