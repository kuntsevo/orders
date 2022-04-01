<?php

namespace frontend\models;

use common\components\ImageHandler;
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

	public function getPhotoThumb()
	{
		if (empty($this->photo)) {
			return '';
		}

		$originalPath = Yii::getAlias("@webroot") . Yii::getAlias("@files/$this->photo");

		if (file_exists(Yii::$app->assetManager->getPublishedPath($originalPath))) {
			$published_url = Yii::$app->assetManager->getPublishedUrl($originalPath);
		} else {
			$imageHandler = new ImageHandler($originalPath);
			$published_url = $imageHandler->resampledImage($this->PHOTO_WIDTH, $this->PHOTO_HEIGHT);

			if (empty($published_url)) {
				return '';
			}
		}

		return Yii::getAlias("@web{$published_url}");
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
		$photoThumb = $this->photoThumb;
		$this->photo = empty($photoThumb) ? Url::to('@staffPhotoBlanc') : $photoThumb;
		return parent::afterFind();
	}
}
