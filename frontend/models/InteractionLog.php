<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use Yii;
use yii\base\ErrorException;
use \yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class InteractionLog extends ActiveRecord
{
	use DataExtractor;
	
	const AUTH_CODE_SENT_ACTION = 'auth_code_sent';
	const AGREEMENT_SIGNING_ACTION = 'agreement_signing';

	//---------------------------------------------------------------------------
	public static function tableName()
	//---------------------------------------------------------------------------
	{
		return '{{InteractionLog}}';
	}

	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['timestamp', 'customer_id', 'action'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['timestamp'], 'datetime'],
			[['customer_id'], 'string', 'max' => 36],
			[['action'], 'string', 'max' => 150],
			[['object'], 'string', 'max' => 150],
		];
	}

	public function __construct()
	{
		$customer_id = Yii::$app->sessionHandler->getCustomerId();

		if (is_null($customer_id)) {
			throw new UnauthorizedHttpException();
		}

		$this->customer_id = $customer_id;
	}

	private static function findLastAuthItem(string $customer_id): self
	{
		try {
			$result = static::find()
				->where([
					'customer_id' => $customer_id,
					'action' => self::AUTH_CODE_SENT_ACTION,
				])
				->orderBy('timestamp DESC')
				->one();
		} catch (ErrorException $e) {
			throw $e;
		}

		if (!$result)
			throw new NotFoundHttpException("Не удалось получить данные об отправке регистрационного СМС для клиента {$customer_id}.");

		if (!($result->object && $result->data)) {
			throw new ServerErrorHttpException('Не заполнен код авторизации или поле "data" в таблице "InteractionLog".');
		}
		
		return $result;
	}

	public static function findLastAuthCode(string $customer_id): string
	{
		return static::findLastAuthItem($customer_id)->object ?? '';
	}

	public static function findAuthPhoneNumber(string $customer_id): string
	{
		return static::findLastAuthItem($customer_id)->phone ?? '';
	}

	public function getCustomer()
	{
		return $this->hasOne(Customers::class, ['uid' => 'customer_id']);
	}
}
