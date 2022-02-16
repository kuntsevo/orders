<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
	//const PTYPE_EKV_SBER = 1;
	//const PTYPE_EK_MKB = 2;
	//const PTYPE_QR_SBER = 3;

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
		return [
			'uid' => 'GUID в 1C',
			'number' => 'Номер',
		];
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
}
