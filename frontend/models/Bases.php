<?php

namespace frontend\models;

use \yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class Bases extends ActiveRecord
{
	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['id'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['id'], 'string', 'max' => 36],
			[['name'], 'string', 'max' => 40],
			[['hs'], 'string', 'max' => 255],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'id' => 'ID',
			'name' => 'Имя',
			'hs' => 'HTTP Сервис',
		];
	}

	//---------------------------------------------------------------------------
	public static function findBase($id)
	//---------------------------------------------------------------------------
	{
		$base = static::findOne($id);

		if (!$base)
			throw new ServerErrorHttpException("Не удалось найти базу {$id}.");

		return $base;
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
