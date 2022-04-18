<?php

namespace frontend\controllers;

use common\components\DocumentHandler;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use frontend\models\Orders;
use yii\base\ErrorException;
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
		if (!Yii::$app->request->isAjax) {
			return $this->redirect(Yii::$app->request->referrer);
		}

		$document_type = Yii::$app->request->get('component');
		$order_id = Yii::$app->request->get('order');
		$order = Orders::findOrderByUid($order_id);

		$fileName = "{$order->number} {$document_type}.pdf";

		$originalPath = Yii::getAlias("@runtime") . Yii::getAlias("\\{$fileName}");

		if (file_exists(Yii::$app->assetManager->getPublishedPath($originalPath))) {
			$published_url = Yii::$app->assetManager->getPublishedUrl($originalPath);
		} else {
			$binairy = (new DocumentHandler())->download($order, $document_type);
			try {
				file_put_contents($originalPath, $binairy);
				$published_url = Yii::$app->assetManager->publish($originalPath)[1];
				unlink($originalPath);
			} catch (ErrorException $e) {
				throw $e;
			}
		}		

		$response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['filePath' => $published_url];

        return $response->send();
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}
}
