<?php

namespace frontend\models;

use \yii\db\ActiveRecord;

class Recommendations extends ActiveRecord
{
	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['recommendation_id', 'uid'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['recommendation_id'], 'string', 'max' => 15],
			[['uid'], 'string', 'max' => 36],
			[['name'], 'string', 'max' => 150],
			[['count'], 'number'],
			[['net_price'], 'number'],
			[['amount'], 'number'],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'name' => 'Рекомендация',
			'count' => 'Количество',
			'net_price' => 'Сумма без скидки',
			'amount' => 'Сумма',
		];
	}
}
