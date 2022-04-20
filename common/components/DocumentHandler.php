<?php

namespace common\components;

use frontend\models\Orders;
use yii\base\BaseObject;
use yii\web\ServerErrorHttpException;

class DocumentHandler extends BaseObject
{
	const HTTP_TEMPLATE_DOCUMENTS = 'documents';
	const HTTP_TEMPLATE_DOCUMENT = 'document';

	public function download(Orders $order, string $document_type)
	{	
		$config = [$order->document_type, $document_type, $order->uid];
		$request_handler = new Ext1c(self::HTTP_TEMPLATE_DOCUMENT);

		$binairy = $request_handler->downloadFile($order->base_id, $config);

		if (!is_string($binairy) or strlen($binairy) < 1000) {
			throw new ServerErrorHttpException('Не удалось получить документ');
		}

		return $binairy;
	}

	public function getDocumentTypes(Orders $order)
	{
		$config = [$order->document_type, $order->uid];
		$request_handler = new Ext1c(self::HTTP_TEMPLATE_DOCUMENTS);

		return $request_handler->getJSON($order->base_id, $config);
	}
}
