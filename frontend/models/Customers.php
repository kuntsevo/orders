<?php

namespace frontend\models;

use frontend\traits\DataExtractor;
use Yii;
use \yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class Customers extends ActiveRecord implements IdentityInterface
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
			[['code'], 'string', 'max' => 8],
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		return [
			'uid' => 'GUID в 1С',
			'code' => 'Код элемента в 1С',
			'first_name' => 'Имя',
			'last_name' => 'Фамилия',
			'patronymic' => 'Отчество',
		];
	}

	public static function findIdentity($id)
	{
		return static::findCustomer($id);
	}

	public function getId()
	{
		return $this->getPrimaryKey();
	}

	public function getAuthKey()
	{
		return $this->auth_key;
	}

	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

	public function validateAuthCode(string $code): bool
	{
		return trim($code) === InteractionLog::findLastAuthCode($this->uid);
	}

	public static function findIdentityByAccessToken($token, $type = null)
	{
	}

	//---------------------------------------------------------------------------
	public static function findCustomer($uid): self
	//---------------------------------------------------------------------------
	{
		$customer = static::findOne($uid);
		if (is_null($customer))
			throw new NotFoundHttpException("Не удалось найти клиента по id {$uid}.");

		return $customer;
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

	public function beforeLogin($event)
	{
		$code = Yii::$app->request->post('code');

		if (is_null($code))
			throw new UnauthorizedHttpException('В запросе отсутствует параметр "code".');

		if (!($code && $this->validateAuthCode($code))) {
			Yii::$app->sessionHandler->sendWarning("Неправильный код");
			$event->isValid = false;
			return;
		}

		$authPhoneNumber = InteractionLog::findAuthPhoneNumber($this->uid);

		if (!$authPhoneNumber) {
			throw new NotFoundHttpException("Не удалось получить номер телефона, использовавшийся для аутентификации клиента {$this->uid}.");
		}
	}

	public function afterLogin($event)
	{
		$session = Yii::$app->session;
		$session->set('authorizationCode', trim(Yii::$app->request->post('code')));
		$session->set('phoneNumber', trim(InteractionLog::findAuthPhoneNumber($this->uid)));
	}
}
