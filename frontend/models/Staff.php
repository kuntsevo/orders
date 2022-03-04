<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use Yii;
use \yii\db\ActiveRecord;
use yii\helpers\Html;
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
		$originalPath = Yii::getAlias("@images/$this->photo");
		$published_url = Yii::$app->assetManager->getPublishedUrl($originalPath);
		if (!file_exists($published_url)) {
			list($width, $height, $imageType) = getImageSize($originalPath);
			$published_url = Yii::$app->assetManager->publish($originalPath)[1];
			$new_height = ($this->PHOTO_WIDTH / $width) * $height;
			$new_width = $this->PHOTO_WIDTH;

			$thumb = imageCreateTrueColor($new_width, $new_height);

			switch ($imageType) {
				case IMG_JPG:
					$imageCreate = 'imageCreateFromJpeg';
					break;
				case IMG_PNG:
					$imageCreate = 'imagecreatefrompng';
					break;
				default:
					// TODO ошибка
					break;
			}

			$original = $imageCreate($originalPath);

			imageCopyResampled($thumb, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			switch ($imageType) {
				case IMG_JPG:
					$imageSave = 'imageJpeg';
					break;
				case IMG_PNG:
					$imageSave = 'imagePng';
					break;
				default:
					// TODO ошибка
					break;
			}

			$imageSave($thumb, Yii::getAlias("@webroot{$published_url}"), 100);

			imageDestroy($thumb);
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
		$this->photo = empty($this->photo) ? Url::to('@staffPhotoBlanc') : $this->photoThumb;
		return parent::afterFind();
	}
}
