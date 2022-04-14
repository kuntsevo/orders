<?php

namespace frontend\models;

use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use \yii\db\ActiveRecord;

class SignedAgreements extends ActiveRecord
{
	//---------------------------------------------------------------------------
	public static function tableName()
	//---------------------------------------------------------------------------
	{
		return '{{SignedAgreements}}';
	}

	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['customer_id', 'agreement_id'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['customer_id'], 'string', 'max' => 36],
			[['agreement_id'], 'string', 'max' => 36],
		];
	}

	public function __construct(string $agreement_id = null, $config = [])
	{
		$customer_id = Yii::$app->sessionHandler->getCustomerId();

		$this->customer_id = $customer_id;
		$this->agreement_id = $agreement_id;
		$this->date = date('Y-m-d');

		parent::__construct($config);
	}

	public static function findCustomerAgreementsQuery(string $customer_id): ActiveQuery
	{
		return static::find()->where(compact('customer_id'));
	}

	public static function findCustomerAgreements(string $customer_id)
	{
		try {
			$agreementsInfo = static::findCustomerAgreementsQuery($customer_id)->all();
		} catch (ErrorException $e) {
			throw $e;
		}

		return $agreementsInfo;
	}

	public static function findOrCreate(string $agreement_id)
	{
		$signedAgreement = static::findAgreement($agreement_id);

		if (is_null($signedAgreement)) {
			$signedAgreement = new SignedAgreements($agreement_id);
		}

		return $signedAgreement;
	}

	public static function findAgreement(string $agreement_id)
	{
		$customer_id = Yii::$app->sessionHandler->getCustomerId();

		try {
			$agreementInfo = static::findOne(compact('agreement_id', 'customer_id'));
		} catch (ErrorException $e) {
			throw $e;
		}

		return $agreementInfo;
	}

	public function getAgreements()
	{
		return $this->hasOne(Agreements::class, ['uid' => 'agreement_id']);
	}

	public function getCustomer()
	{
		return $this->hasOne(Customers::class, ['uid' => 'customer_id']);
	}
}
