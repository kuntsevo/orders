<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;

class Vehicles extends ActiveRecord
{
	use DataExtractor;

	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['vin'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['vin'], 'string', 'max' => 17],
			[['registration_number'], 'string', 'max' => 10],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'vin' => 'ВИН',
			'registration_number' => 'Государственный регистрационный номер',
			'model' => 'Модель',
			'brand' => 'Марка',
		];
	}

	//---------------------------------------------------------------------------
	public static function findVehicle($vin)
	//---------------------------------------------------------------------------
	{
		return static::findOne($vin);
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
