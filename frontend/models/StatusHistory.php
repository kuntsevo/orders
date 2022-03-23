<?php

namespace frontend\models;

use \yii\db\ActiveRecord;

class StatusHistory extends ActiveRecord
{
	//---------------------------------------------------------------------------
	public static function tableName()
	//---------------------------------------------------------------------------
	{
		return '{{StatusHistory}}';
	}

	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['order_id', 'date'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['order_id'], 'string', 'max' => 36],
			[['date'], 'datetime'],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'order_id' => 'Идентификатор заказа',
			'date' => 'Дата изменения статуса',
			'status' => 'Статус',
		];
	}

	public static function getOrderStatusHistory(string $order_id)
	{
		return self::find()
			->where(['order_id' => $order_id])
			->orderBy('date')
			->all();
	}
}
