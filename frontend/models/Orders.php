<?php

namespace frontend\models;

use \yii\db\ActiveRecord;
use yii\db\Query;

class Orders extends ActiveRecord
{
	//const PTYPE_EKV_SBER = 1;
	//const PTYPE_EK_MKB = 2;
	//const PTYPE_QR_SBER = 3;

	public function behaviors()
	{
		return [
			'class' => DataExtractor::class,
		];
	}

	//---------------------------------------------------------------------------
	public static function tableName()
	//---------------------------------------------------------------------------
	{
		return '{{Orders}}';
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
		$rows = (new Query())
			->select([
				"orders.*",
				"orders.data orders_data",
				"staff.*",
				"staff.data staff_data",
				"staffInfo.*"
			])
			->from(['orders' => Orders::tableName()])
			->where(["orders.uid" => $this->uid])
			->leftJoin(
				['staff' => Staff::tableName()],
				"orders.manager_id = staff.uid"
			)
			->leftJoin(
				['staffInfo' => StaffInfo::tableName()],
				"orders.base_id = staffInfo.base_id and staff.employee_id = staffInfo.employee_id"
			)
			->one();

		$result = (object)$rows;
		$result->manager = json_decode($result->staff_data)->attributes;
		return $result;
	}

	public static function getOrdersByCustomer($id)
	{

		$order = static::find()
			->where(['customer_id' => $id, 'is_archived' => false])
			->all();

		return $order;
	}
	//---------------------------------------------------------------------------
	public static function findOrderByUid($uid)
	//---------------------------------------------------------------------------
	{
		return static::findOne($uid);
	}

	public static function orderWithStaffInfo($uid)
	{
		$result = (new Query())
			->select([
				"orders.*",
				"staff.*",
				"staff.data staff_data",
				"staffInfo.*"
			])
			->from(['orders' => Orders::tableName()])
			->where(["orders.uid" => $uid])
			->leftJoin(
				['staff' => Staff::tableName()],
				"orders.manager_id = staff.uid"
			)
			->leftJoin(
				['staffInfo' => StaffInfo::tableName()],
				"orders.base_id = staffInfo.base_id and staff.employee_id = staffInfo.employee_id"
			)
			->one();

		$result = (object)$result;
		$result->staff_data = json_decode($result->staff_data)->attributes;
				
		return $result;
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
