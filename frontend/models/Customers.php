<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class Customers extends ActiveRecord
{
	use DataExtractor;

	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['uid'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['uid'], 'string', 'max' => 36],
			[['code'], 'string', 'max' => 8],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'uid' => 'GUID в 1С',
			'code' => 'Код элемента в 1С',
			'first_name' => 'Имя',
			'last_name' => 'Фамилия',
			'patronymic' => 'Отчество',
		];
	}

	//---------------------------------------------------------------------------
	public static function findCustomer($uid)
	//---------------------------------------------------------------------------
	{
		$customer = static::findOne($uid);
		if (is_null($customer))
			throw new ServerErrorHttpException("Не удалось найти клиента по id {$uid}.");

		return $customer;
	}

	//---------------------------------------------------------------------------
	public function beforeSave($insert)
	//---------------------------------------------------------------------------
	{
		if (parent::beforeSave($insert)) {
			//
			return true;
		} else
			return false;
	}
}
