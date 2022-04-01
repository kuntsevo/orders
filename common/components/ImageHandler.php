<?php

namespace common\components;

use SplFileInfo;
use Yii;
use yii\base\BaseObject;

class ImageHandler extends BaseObject
{
	public $path;
	private $image_extensions = ['png', 'jpg', 'jpeg'];

	public function __construct(string $path, $config = [])
	{
		$this->path = $path;

		parent::__construct($config);
	}

	public function init()
	{
		parent::init();

		if (!(file_exists($this->path)
			and in_array((new SplFileInfo($this->path))->getExtension(),
				$this->image_extensions,
				true
			))) {
			$this->path = null;
		}
	}

	public function resampledImage(float $width, float $height)
	{
		if (is_null($this->path)) {
			return '';
		}

		list($imageWidth, $imageHeight, $imageType) = getImageSize($this->path);
		$published_url = Yii::$app->assetManager->publish($this->path)[1];
		$new_height = ($width / $imageWidth) * $imageHeight;
		$new_width = $width;

		$thumb = imageCreateTrueColor($new_width, $new_height);

		switch ($imageType) {
			case IMG_JPG:
				$imageCreate = 'imageCreateFromJpeg';
				break;
			case IMG_PNG:
				$imageCreate = 'imageCreateFromPng';
				break;
			default:
				// TODO ошибка
				break;
		}

		$original = $imageCreate($this->path);

		imageCopyResampled($thumb, $original, 0, 0, 0, 0, $new_width, $new_height, $imageWidth, $imageHeight);

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

		return $published_url;
	}
}
