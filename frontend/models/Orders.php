<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;

class Orders extends ActiveRecord
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
			[['number'], 'string', 'max' => 11],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		$commonLabels = [
			'uid' => 'GUID в 1C',
			'number' => 'Номер',
		];

		return array_merge($commonLabels, $this->attributeLabelsByDocumentType());
	}

	public function getCustomer()
	{
		return $this->hasOne(Customers::class, ['uid' => 'customer_id']);
	}

	public function getDealer()
	{
		return $this->hasOne(Dealers::class, ['uid' => 'dealer_id']);
	}

	public function getVehicle()
	{
		return $this->hasOne(Vehicles::class, ['vin' => 'vehicle_id']);
	}

	public function getStaff()
	{
		return $this->hasOne(Staff::class, ['uid' => 'manager_id']);
	}

	public function getStaffInfo()
	{
		// TODO
		// обработать ситуацию, когда нет StaffInfo
		return $this->hasOne(
			StaffInfo::class,
			['base_id' => 'base_id']
		)
			->where(['employee_id' => $this->staff->employee_id]);
	}

	public function getAmountPayable()
	{
		return $this->amount - $this->payment_amount;
	}

	public static function getOrdersByCustomer($id)
	{

		$order = static::find()
			->with(['dealer', 'vehicle'])
			->where(['customer_id' => $id, 'is_archived' => 0])
			->all();

		return $order;
	}
	//---------------------------------------------------------------------------
	public static function findOrderByUid($uid)
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

	private function attributeLabelsByDocumentType()
	{
		switch ($this->document_type) {
			case 'ЗаказНаряд':
				return $this->workOrderAttributeLabels();
				break;
			default:
				return [];
				break;
		}
	}

	private function workOrderAttributeLabels()
	{
		return [
			'number' => '№ заказ-наряда',
			'status' => 'Статус',
			'repair_kind' => 'Вид ремонта',
			'issuance_date' => 'Дата выдачи',
			'works_cost' => 'Сумма по работам',
			'goods_cost' => 'Сумма по товарам',
			'net_price' => 'Сумма без скидки',
			'discount' => 'Сумма скидки',
			'amount' => 'Сумма заказ-наряда',
			'payment_amount' => 'Оплаченная сумма',
			'registration_number' => 'Гос. номер автомобиля',
			'amount_payable' => 'Сумма к оплате',
		];
	}
}
