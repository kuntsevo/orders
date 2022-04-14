<?php

namespace frontend\models;

use Yii;
use yii\base\ErrorException;
use \yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class InteractionLog extends ActiveRecord
{
	const AUTH_CODE_SENDING_ACTION = 'auth_code_sending';
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

	public static function findAuthCode(string $customer_id)
	{
		try {
			$result = static::find()
				->where([
					'customer_id' => $customer_id,
					'action' => self::AUTH_CODE_SENDING_ACTION,
				])
				->orderBy('timestamp DESC')
				->one();
		} catch (ErrorException $e) {
			throw $e;
		}

		if ($result && !($result->object && $result->data)) {
			throw new ServerErrorHttpException('Не заполнен код авторизации или поле data в таблице InteractionLog.');
		}

		return $result;
	}

	public function getCustomer()
	{
		return $this->hasOne(Customers::class, ['uid' => 'customer_id']);
	}
}
