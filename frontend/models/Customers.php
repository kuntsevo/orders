<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;

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
		return static::findOne($uid);
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
