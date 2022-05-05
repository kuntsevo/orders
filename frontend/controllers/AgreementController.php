<?php

namespace frontend\controllers;

use frontend\behaviors\AgreementsSigning;
use frontend\models\InteractionLog;
use frontend\models\SignedAgreements;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Agreement controller
 */
class AgreementController extends Controller
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
						'actions' => ['index', 'view', 'signing'],
						'allow' => true,
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
			'view' => ['GET'],
			'signing' => ['POST'],
		];
	}

	//---------------------------------------------------------------------------
	public function actionIndex()
	//---------------------------------------------------------------------------
	{
		$cache = Yii::$app->cache;
		$unsignedAgreements = $cache->get(AgreementsSigning::UNSIGNED_AGREEMENTS_KEY);

		if (!$unsignedAgreements) {
			throw new NotFoundHttpException('Не получены "unsignedAgreements".');
		}

		$agreements = [];
		foreach ($unsignedAgreements as $agreement) {
			if ($agreement->file_path) {
				array_push($agreements, $agreement);
			}
		}

		if (!$agreements) {
			throw new ServerErrorHttpException('Пустой список соглашений на подписание.');
		}

		return $this->render('index.pug', compact('agreements'));
	}

	public function actionSigning()
	{
		$cache = Yii::$app->cache;
		$agreements = Yii::$app->request->post('agreements');

		if (is_null($agreements)) {
			throw new ServerErrorHttpException('В запросе отсутствует параметр "agreements".');
		}

		$transaction = Yii::$app->getDb()->beginTransaction();
		try {
			foreach ($agreements as $agreement_id) {
				$signedAgreement = SignedAgreements::findOrCreate($agreement_id);
				$signedAgreement->updated_at = date('Y-m-d H:i:s');
				$interactionLog = new InteractionLog();

				$interactionLog->action = InteractionLog::AGREEMENT_SIGNING_ACTION;
				$interactionLog->object = $agreement_id;

				$interactionLog->data = Json::encode(Yii::$app->sessionHandler->getClientData());

				$signedAgreement->save();
				$interactionLog->save();
			}
		} catch (ErrorException $e) {
			$transaction->rollBack();
			throw $e;
		}

		$transaction->commit();

		$cache->delete(AgreementsSigning::UNSIGNED_AGREEMENTS_KEY);

		return $this->redirect(Url::previous());
	}

	//---------------------------------------------------------------------------
	protected function getBaseUrlRedirect()
	//---------------------------------------------------------------------------
	{
		return $this->_baseurl_redirect;
	}

	public function actions()
	{
		return [
			'view' => [
				'class' => 'yii\web\ViewAction',
				'viewPrefix' => 'static',
			],
		];
	}
}
