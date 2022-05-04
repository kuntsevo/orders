<?php

namespace frontend\models;

use Yii;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use \yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class Agreements extends ActiveRecord
{
	/** 
	 * Соглашения, которые не хранятся в таблице Agreements.
	 * Описание каждого из них должно быть в виде ассоциативного массива, 
	 * ключи которого должны совпадать с именами колонок в таблице Agreements (можно указывать только обязательные к заполнению).
	 * Эти же ключи в обязательном порядке должны быть в свойстве rules.
	 * */
	const NON_STORED_AGREEMENTS = [
		[
			'uid' => 'Cookies',
			'agreement_type' => 'Cookies',
			'is_current' => 1,
			'file_path' => '/agreement/view/cookiespolicy.pug',
		],
	];

	private const ALIASES = [
		'ПерсональныеДанные' => 'Согласие на обработку персональных данных',
		'ПравилаРаботы' => 'Согласие с правилами работы',
		'Cookies' => 'Согласие на использование файлов cookie',
	];

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
			[['agreement_type'], 'string', 'max' => 150],
			[['is_current'], 'integer', 'min' => 0, 'max' => 1],
			[['file_path'], 'string', 'max' => 500],
		];
	}

	public function getAlias()
	{
		$alias = self::ALIASES[$this->agreement_type];

		if (!isset($alias)) {
			throw new NotFoundHttpException("Не найден псевдоним для тип согласия {$this->agreement_type}");
		}

		$dealer_name_part = $this->dealers->name ? " ({$this->dealers->name})" : '';
		$alias = "{$alias}{$dealer_name_part}";

		return $alias;
	}

	public static function findAgreement(string $uid)
	{
		try {
			$agreementInfo = static::findOne(compact('uid'));
		} catch (ErrorException $e) {
			throw $e;
		}

		return $agreementInfo;
	}

	public function getDealers()
	{
		return $this->hasOne(Dealers::class, ['uid' => 'dealer_id']);
	}

	public function getSignedAgreements()
	{
		return $this->hasMany(SignedAgreements::class, ['agreement_id' => 'uid']);
	}

	public function getOneSignedAgreement()
	{
		return $this->hasOne(SignedAgreements::class, ['agreement_id' => 'uid']);
	}

	private static function getActualAgreements(array $dealer_ids = [], array $agreement_types = []): ActiveQuery
	{
		$actualAgreements = static::findBySql("SELECT *
		FROM
		  (SELECT * ,
				  MAX([[updated_at]]) OVER (PARTITION BY [[dealer_id]],
													 [[agreement_type]]) AS date_max
		   FROM " . self::tableName() . "
		   WHERE ([[dealer_id]] IN ('" . implode("','", $dealer_ids) . "')
				   OR (:dealer_ids_is_empty = 1))
				   AND ([[agreement_type]] IN ('" . implode("','", $agreement_types) . "')
				   OR (:agreement_types_empty = 1))) AS agreements
		WHERE agreements.updated_at = agreements.date_max", [
			':dealer_ids_is_empty' => (int)empty($dealer_ids),
			':agreement_types_empty' => (int)empty($agreement_types),
		]);


		return $actualAgreements;
	}

	public static function getUnsignedActualAgreements(array $dealer_ids, string $customer_id, array $agreement_types = []): array
	{
		$tableName = self::tableName();

		try {
			$actualAgreementsSql = static::getActualAgreements($dealer_ids, $agreement_types)
				->createCommand()
				->getRawSql();
			$actualAgreementsAlias = 'actualAgreements';

			$customerAgreementsSql = SignedAgreements::findCustomerAgreementsQuery($customer_id)
				->createCommand()
				->getRawSql();
			$customerAgreementsAlias = 'customerAgreements';

			$unsignedActualAgreements = static::find()
				->withQuery($actualAgreementsSql, $actualAgreementsAlias)
				->withQuery($customerAgreementsSql, $customerAgreementsAlias)
				->innerJoin($actualAgreementsAlias, "{$tableName}.[uid]=[{$actualAgreementsAlias}].[uid]")
				->leftJoin($customerAgreementsAlias, "{$tableName}.[uid]=[{$customerAgreementsAlias}].[agreement_id]")
				->joinWith('dealers')
				->where(["{$customerAgreementsAlias}.customer_id" => null])
				->orderBy('agreement_type')
				->all();
		} catch (ErrorException $e) {
			throw $e;
		}

		return $unsignedActualAgreements;
	}

	public static function getUnsignedNonStoredAgreements(string $customer_id, array $agreement_types = []): array
	{
		$result = [];

		$nonStoredUids = array_column(self::NON_STORED_AGREEMENTS, 'uid');

		if (!$nonStoredUids) {
			return $result;
		}

		$unsignedNonStoredAgreements = SignedAgreements::findCustomerAgreementsQuery($customer_id)
			->select(['agreement_id'])
			->column();

		$unsignedNonStoredUids = array_diff($nonStoredUids, $unsignedNonStoredAgreements);

		foreach (self::NON_STORED_AGREEMENTS as $agreement) {
			if ((!$agreement_types || in_array($agreement['agreement_type'], $agreement_types))
				&& in_array($agreement['uid'], $unsignedNonStoredUids)
			) {
				$item = new Agreements();
				$item->attributes = $agreement;
				array_push($result, $item);
			}
		}

		return $result;
	}

	public static function getUnsignedAgreements(array $dealer_ids, string $customer_id, array $agreement_types = []): array
	{
		array_push($dealer_ids, ''); // всегда ищем также и по пустому значению дилерства

		$unsignedActualAgreements = self::getUnsignedActualAgreements($dealer_ids, $customer_id, $agreement_types);
		$unsignedNonStoredAgreements = self::getUnsignedNonStoredAgreements($customer_id, $agreement_types);

		return array_merge($unsignedActualAgreements, $unsignedNonStoredAgreements);
	}

	private function getDownloadPath(): string
	{
		if (!$this->file_path) {
			return '';
		}

		$fileName = "{$this->uid}.pdf";
		$originalPath = Yii::getAlias("@webroot") . Yii::getAlias("@files/{$fileName}");

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

	public static function isNonStored(Agreements $item): bool
	{
		return in_array($item->agreement_type, array_column(self::NON_STORED_AGREEMENTS, 'agreement_type'));
	}

	public function afterFind()
	{
		$this->file_path = $this->getDownloadPath();

		return parent::afterFind();
	}
}
