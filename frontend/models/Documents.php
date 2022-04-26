<?php

namespace frontend\models;

use Yii;
use yii\base\ErrorException;
use \yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class Documents extends ActiveRecord
{
	//---------------------------------------------------------------------------
	public static function primaryKey()
	//---------------------------------------------------------------------------
	{
		return ['order_id', 'document_type'];
	}

	//---------------------------------------------------------------------------
	public function rules()
	//---------------------------------------------------------------------------
	{
		return [
			[['order_id'], 'string', 'max' => 36],
			[['document_type'], 'string', 'max' => 150],
		];
	}

	public static function getOrderDocuments(string $order_id): array
	{
		$documents = static::find()
			->where(compact('order_id'))
			->orderBy('alias')
			->all();

		if (is_null($documents))
			throw new NotFoundHttpException("Не удалось найти печатные формы заказа по uid {$order_id}.");

		return $documents;
	}

	private function getDownloadPath(): string
	{
		if (!$this->name) {
			return '';
		}

		$originalPath = Yii::getAlias("@webroot") . Yii::getAlias("@files/documents/{$this->name}");

		if (!file_exists($originalPath)) {
			return '';
		}

		$assetManager = Yii::$app->assetManager;
		if (file_exists($assetManager->getPublishedPath($originalPath))) {
			$downloadPath = $assetManager->getPublishedUrl($originalPath);
		} else {
			try {
				$downloadPath = $assetManager->publish($originalPath)[1];
			} catch (ErrorException $e) {
				throw $e;
			}
		}

		return Yii::getAlias("@web{$downloadPath}");
	}

	public function afterFind()
	{
		$this->name = $this->getDownloadPath();

		return parent::afterFind();
	}
}
