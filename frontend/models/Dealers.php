<?php

namespace frontend\models;

use \yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class Dealers extends ActiveRecord
{
	//---------------------------------------------------------------------------
	public static function tableName()
	//---------------------------------------------------------------------------
	{
		return '{{Dealers}}';
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
			[['name'], 'string', 'max' => 150],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'uid' => 'GUID в 1с',
			'name' => 'Название ДЦ',
		];
	}

	//---------------------------------------------------------------------------
	public static function findDealer($uid)
	//---------------------------------------------------------------------------
	{
		$dealer = static::findOne($uid);

		if (!$dealer)
			throw new ServerErrorHttpException("Не удалось найти ДЦ {$uid}.");

		return $dealer;
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
