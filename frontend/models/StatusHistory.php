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
		return ['order_id', 'status'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['order_id'], 'string', 'max' => 36],
			[['date'], 'datetime'],
			[['status'], 'string', 'max' => 150],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'order_id' => 'Идентификатор заказа',
			'date' => 'Дата изменения статуса',
			'alias' => 'Статус',
		];
	}

	public static function getOrderStatusHistory(string $order_id)
	{
		return self::find()
			->where(['order_id' => $order_id])
			->orderBy('date, status_ordering')
			->all();
	}
}
