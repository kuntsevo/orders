<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use frontend\models\Orders;
use frontend\models\Customers;
use frontend\models\StatusHistory;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Order controller
 */
class OrderController extends Controller
{
	private $_baseurl_redirect = 'https://kuntsevo.com';
	private $_ip = '';
	private $_referer = null;
	private $_action_started_at = null;

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
						'actions' => ['test', 'index', 'show', 'table', 'status-history'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['error'],
						'allow' => true,
					],
				],
			],
			// [
			// 	'class' => AgreementsSigning::class,
			// 	'requested_types' => [
			// 		'ПерсональныеДанные',
			// 		'ПравилаРаботы',
			// 	],
			// ],
		];
	}

	//---------------------------------------------------------------------------
	protected function verbs()
	//---------------------------------------------------------------------------
	{
		return [
			'test' => ['GET'],
			'error' => ['GET'],
			'index' => ['GET'],
			'show' => ['GET'],
			'table' => ['GET'],
			'status-history' => ['GET'],
		];
	}

	//---------------------------------------------------------------------------
	public function actionTest()
	//---------------------------------------------------------------------------
	{
		echo 'WORK(' . YII_DEBUG . ')';
	}

	//---------------------------------------------------------------------------
	public function actionIndex()
	//---------------------------------------------------------------------------
	{
		$customer_id = Yii::$app->request->get('customer');

		if (is_null($customer_id))
			throw new UnauthorizedHttpException('В запросе отсутствует параметр "customer".');

		$customer = Customers::findCustomer($customer_id);

		$active_orders = Orders::getActiveOrdersByCustomer($customer_id);
		$finished_orders = Orders::getArchivedOrdersByCustomer($customer_id);

		$this->view->title = 'История обслуживания';

		return $this->render('index.pug', compact('active_orders', 'finished_orders', 'customer'));
	}

	public function actionShow()
	{
		$order_id = Yii::$app->request->get('order');
		if (is_null($order_id))
			throw new ServerErrorHttpException('В запросе отсутствует параметр "order".');

		$order = Orders::findOrderByUid($order_id);

		if ($order->is_child) {
			$customer_id = Yii::$app->request->get('customer');
			return $this->redirect(
				Url::to(
					['@orderItem', 'customer' => $customer_id, 'order' => $order->uid]
				)
			);
		}

		$this->view->title = "№{$order->number}";

		return $this->render('order.pug', compact('order'));
	}

	public function actionTable()
	{
		$order_id = Yii::$app->request->get('order');
		$table_name = Yii::$app->request->get('component');

		if (is_null($order_id) || is_null($table_name))
			throw new ServerErrorHttpException('В запросе отсутствует параметр "order" или "component".');

		$order = Orders::findOrderByUid($order_id);

		$tableAttributes = $order->tableAttributesSequence($table_name);

		return $this->render('table.pug', compact(['order', 'table_name', 'tableAttributes']));
	}

	public function actionStatusHistory()
	{

		$order_id = Yii::$app->request->get('order');
		if (is_null($order_id))
			throw new NotFoundHttpException('В запросе отсутствует параметр "order".');

		$order = Orders::findOrderByUid($order_id);

		$status_history = StatusHistory::getOrderStatusHistory($order_id);

		return $this->render('status.pug', compact('status_history', 'order'));
	}

	//---------------------------------------------------------------------------
	protected function setIP($ip)
	//---------------------------------------------------------------------------
	{
		$this->_ip = $ip;
	}

	//---------------------------------------------------------------------------
	protected function setReferer()
	//---------------------------------------------------------------------------
	{
		$this->_referer = isset($_SERVER["HTTP_REFERER"]) ? ($_SERVER["HTTP_REFERER"] != '' ? $_SERVER["HTTP_REFERER"] : null) : null;
	}

	//---------------------------------------------------------------------------
	protected function setActionStartedAt($started_at)
	//---------------------------------------------------------------------------
	{
		$this->_action_started_at = $started_at;
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}

	//---------------------------------------------------------------------------
	protected function getIP()
	//---------------------------------------------------------------------------
	{
		return $this->_ip;
	}

	//---------------------------------------------------------------------------
	protected function getReferer()
	//---------------------------------------------------------------------------
	{
		return $this->_referer;
	}

	//---------------------------------------------------------------------------
	protected function getActionStartedAt()
	//---------------------------------------------------------------------------
	{
		return $this->_action_started_at;
	}

	//---------------------------------------------------------------------------
	public function beforeAction($action)
	//---------------------------------------------------------------------------
	{
		$this->setActionStartedAt(date('Y-m-d H:i:s'));
		$this->setIP(Yii::$app->request->userIP);
		$this->setReferer();

		return parent::beforeAction($action);
	}

	//---------------------------------------------------------------------------
	public function afterAction($action, $result)
	//---------------------------------------------------------------------------
	{
		$params = [
			'_GET' => Yii::$app->request->get(),
			'_POST' => Yii::$app->request->post(),
		];

		return parent::afterAction($action, $result);
	}

	public function actionError()
	{
		$exception = Yii::$app->errorHandler->exception;
		if (!$exception) {
			return $this->redirect($this->_baseurl_redirect);
		}

		if ($exception->statusCode === 401) {
			$customer_id = null;
		} else {
			$customer_id = Yii::$app->sessionHandler->getCustomerId();
		}

		return $this->render('//errors/error.pug', compact('exception', 'customer_id'));
	}
}
