<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use Yii;
use \yii\db\ActiveRecord;
use yii\helpers\Url;

class Staff extends ActiveRecord
{
	use DataExtractor;

	private $PHOTO_WIDTH = 150;
	private $PHOTO_HEIGHT = 200;

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

	public function getPhotoTumb()
	{
		$originalPath = "@images/original/$this->photo";
		$tumbPath = "@images/tumb/$this->photo";
		if (!file_exists($tumbPath)) {			
			$img = imageCreateFromJpeg(Yii::getAlias($originalPath));
			list($width, $height) = getimagesize(Yii::getAlias($originalPath));
			$new_height = ($this->PHOTO_WIDTH / $width) * $height;
			$new_width = $this->PHOTO_WIDTH;
			$tmp = imageCreateTrueColor($new_width, $new_height);						
			imagecopyresampled($tmp, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imageJpeg($tmp, Yii::getAlias($tumbPath), 100);
		}

		return "@web/images/tumb/$this->photo";
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
		$this->photo = empty($this->photo) ? Url::to('@staffPhotoBlanc') : $this->photoTumb;
		return parent::afterFind();
	}
}
