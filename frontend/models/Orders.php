<?php

namespace frontend\models;

use Yii;
use frontend\behaviors\AgreementsSigning;
use frontend\traits\DataExtractor;
use \yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Orders extends ActiveRecord
{
	use DataExtractor;

	const WORK_ORDER = 'ЗаказНаряд';
	const REPAIR_REQUEST = 'ЗаявкаНаРемонт';
	private static $currentOrderType;

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

	public function behaviors()
	{
		return [
			AgreementsSigning::class,
		];
	}

	//---------------------------------------------------------------------------
	public function attributeLabels()
	//---------------------------------------------------------------------------
	{
		// общие псевдонимы свойств; могут переопределяться в зависимости от типа документа
		$commonLabels = [
			'uid' => 'GUID в 1C',
			'number' => 'Номер',
		];

		return array_merge($commonLabels, $this->attributeLabelsByDocumentType());
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
		return $this->hasOne(StaffInfo::class, ['base_id' => 'base_id'])
			->where(['employee_id' => $this->staff->employee_id]);
	}

	public function getRecommendations()
	{
		return $this->hasMany(Recommendations::class, ['uid' => 'uid']);
	}

	public function getStatusHistory()
	{
		return $this->hasMany(StatusHistory::class, ['order_id' => 'uid']);
	}

	public function getOneStatusFromHistory()
	{
		return $this->hasOne(StatusHistory::class, ['order_id' => 'uid']);
	}

	public function getActualStatusFromHistory()
	{
		return $this->getOneStatusFromHistory()->where(['is_actual' => 1]);
	}

	public function getAmountPayable()
	{
		return $this->amount - $this->payment_amount;
	}

	public function getActualStatus()
	{
		switch (self::$currentOrderType) {
			case self::WORK_ORDER:
				return $this->actualStatusFromHistory->alias;
			default:
				return $this->alias;
		}
	}

	public function getIssuanceDate()
	{
		return $this->issuance_date ? Yii::$app->formatter->asDateTime($this->issuance_date) : '-';
	}
	public static function getAllOrdersByCustomer(string $customer_id)
	{
		return static::getOrdersByCustomer($customer_id);
	}

	public static function getActiveOrdersByCustomer(string $customer_id)
	{
		return static::getOrdersByCustomer($customer_id, 0);
	}

	public static function getArchivedOrdersByCustomer(string $customer_id)
	{
		return static::getOrdersByCustomer($customer_id, 1);
	}

	private static function getOrdersByCustomer(string $customer_id, int $is_archived = null): array
	{
		$condition = ['customer_id' => $customer_id];

		if (!is_null($is_archived)) {
			$condition = array_merge($condition, [
				'is_archived' => $is_archived,
			]);
		}

		$orders = static::find()
			->with(['dealer', 'vehicle'])
			->where($condition);
		$orders = self::addActualStatus($orders);

		$orders = $orders->all();

		if (is_null($orders))
			throw new NotFoundHttpException("Не удалось получить заказы клиента {$customer_id}, архивные - {$is_archived}.");

		return $orders;
	}

	//---------------------------------------------------------------------------
	public static function findOrderByUid(string $uid): Orders
	//---------------------------------------------------------------------------
	{
		$order = static::find()->where(['uid' => $uid]);

		$order = self::addActualStatus($order);

		$order = $order->one();

		if (!$order)
			throw new NotFoundHttpException("Не удалось найти заказ {$uid}.");

		return $order;
	}

	private static function addActualStatus($order)
	{
		return $order->joinWith([
			'oneStatusFromHistory' => function ($query) {
				$query->onCondition(['is_actual' => 1]);
			},
		]);
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

	/**
	 * Возвращает псевдонимы свойств заказа в зависимости от типа документа.
	 * "Затирает" псевдонимы, указанные как "общие" в attributeLabels()
	 */
	private function attributeLabelsByDocumentType(): array
	{
		switch ($this->document_type) {
			case self::$currentOrderType:
				return self::workOrderAttributeLabels();
				break;
			default:
				return [];
				break;
		}
	}

	/**
	 * Для каждого типа документа прописать свои псевдонимы свойств
	 * Здесь - для заказ-нарядов
	 */
	private static function workOrderAttributeLabels()
	{
		return [
			'number' => '№ заказ-наряда',
			'status' => 'Статус',
			'repair_kind' => 'Вид ремонта',
			'issuance_date' => 'Дата выдачи',
			'works_cost' => 'Сумма по работам',
			'goods_cost' => 'Сумма по товарам',
			'net_price' => 'Сумма без скидки',
			'discount' => 'Сумма скидки',
			'amount' => 'Сумма заказ-наряда',
			'payment_amount' => 'Оплаченная сумма',
			'registration_number' => 'Гос. номер автомобиля',
			'amount_payable' => 'Сумма к оплате',
			'works' => 'Работы',
			'goods' => 'Товары',
			'recommendations' => 'Рекомендации',
		];
	}

	public function tableAttributesSequence(string $table_name): array
	{
		switch ($this->document_type) {
			case self::WORK_ORDER:
				return $this->workOrderTableAttributesSequence($table_name);
				break;
			default:
				return [];
				break;
		}
	}

	private function workOrderTableAttributesSequence(string $table_name): array
	{
		$attributesSequence = [
			'works' => [
				'work_name' => 'Наименование',
				'work_count' => 'Количество',
				'work_net_price' => 'Сумма без скидки',
				'work_amount' => 'Сумма',
			],
			'goods' => [
				'good_name' => 'Наименование',
				'good_count' => 'Количество',
				'good_net_price' => 'Сумма без скидки',
				'good_amount' => 'Сумма',
			],
			'recommendations' => (new Recommendations())->attributeLabels(),
		];

		return ArrayHelper::getValue($attributesSequence, $table_name, []);
	}

	public function afterFind()
	{
		self::$currentOrderType = $this->document_type;

		return parent::afterFind();
	}
}
