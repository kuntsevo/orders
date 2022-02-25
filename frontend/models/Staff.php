<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;
use yii\helpers\Url;

class Staff extends ActiveRecord
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
			[['photo'], 'string', 'max' => 250],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'uid' => 'GUID в 1С',
			'name' => 'ФИО',
			'photo' => 'Фотография',
		];
	}

	//---------------------------------------------------------------------------
	public static function findStaff($uid)
	//---------------------------------------------------------------------------
	{
		return static::findOne($uid);
	}

	public function getStaffInfo()
	{
		return $this->hasMany(StaffInfo::class, ['employee_id' => 'employee_id']);
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

	public function afterFind()
	{
		$this->photo = empty($this->photo) ? Url::to('@staffPhotoBlanc') : $this->photo;
		return parent::afterFind();
	}
}
