<?php

namespace frontend\models;

use \yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
	const PTYPE_EKV_SBER = 1;
	const PTYPE_EK_MKB = 2;
	const PTYPE_QR_SBER = 3;

	//---------------------------------------------------------------------------
	public static function tableName()
	//---------------------------------------------------------------------------
	{
		return 'Orders';
	}

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
			[['number'], 'string', 'max' => 11],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'uid' => 'GUID в 1с',
			'number' => 'Номер',
		];
	}

	//---------------------------------------------------------------------------
	public static function findOrderByUid($uid)
	//---------------------------------------------------------------------------
	{
		return static::findOne($uid);
		// return static::find()
		// 		->where(['uid' => $uid])		
		// 		->orderBy('uid DESC')
		// 		->one();
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
