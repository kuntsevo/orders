<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use frontend\models\Bases;
use yii\web\ServerErrorHttpException;

class Ext1c extends Component
{
	const HOST = YII_ENV == 'prod' ? 'http://192.168.0.52' : 'http://192.168.0.66';
	const GURL = 'orders';

	public $login;
	public $password;
	public $template;
	private $options = [];
	private $http_1c_usr;
	private $http_1c_passwd;

	public function __construct(string $template = '', $config = [])
	{
		$this->template = $template;

		parent::__construct($config);
	}

	//---------------------------------------------------------------------------  
	public function init()
	//---------------------------------------------------------------------------
	{
		// if (!function_exists ('curl_init'))
		// {
		// throw new CException('Для работы расширения требуется cURL');
		// }
		parent::init();

		$this->http_1c_usr = Yii::$app->params['http_1c_usr'];
		$this->http_1c_passwd = Yii::$app->params['http_1c_passwd'];
	}

	//---------------------------------------------------------------------------
	public function sendOrderAction($id, $hs)
	//---------------------------------------------------------------------------
	{
		$params = [];
		$params['id'] = $id;

		$postdata = json_encode($params);

		$options = [
			CURLOPT_USERPWD => "$this->login:$this->password",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $postdata,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json;'),
		];

		$res_data = $this->request(self::HOST . '/' . self::GURL . '' . $hs . '/hs/OrderCallback/{action}', $options);

		return $res_data;
	}

	public function getJSON(string $base_id, array $config = [], bool $asArray = true)
	{
		$result = $this->httpService1CRequest($base_id, $config);
		return Json::decode($result, $asArray);
	}

	public function putJSON(string $base_id, array $body = [], bool $asArray = true)
	{
		$body_json = Json::encode($body);
		$this->options = [
			CURLOPT_CUSTOMREQUEST => 'PUT',
			CURLOPT_POSTFIELDS => $body_json,
		];

		$result = $this->httpService1CRequest($base_id);
		return Json::decode($result, $asArray);
	}

	public function downloadFile(string $base_id, array $config = [])
	{
		return $this->httpService1CRequest($base_id, $config);
	}

	public function httpService1CRequest(string $base_id, array $config = [])
	{
		$base_url = rtrim($this->getBaseUrl($base_id), '/');
		$url = self::HOST . '/' . $base_url . '/hs/' . self::GURL . '/' . $this->template . '/' . implode('/', $config);
		$options = [CURLOPT_USERPWD => "$this->http_1c_usr:$this->http_1c_passwd"] + $this->options;

		return $this->request($url, $options);
	}

	private function getBaseUrl(string $base_id): string
	{
		$base = (new Bases())::findOne($base_id);

		if (is_null($base)) {
			throw new ServerErrorHttpException("Не найдена база 1С по идентификтору");
		}

		return $base->hs;
	}

	//---------------------------------------------------------------------------
	protected function request(string $url, array $options = [])
	//---------------------------------------------------------------------------
	{
		$curl = curl_init(rtrim($url, '/'));

		$default_options = [
			CURLOPT_TIMEOUT => 60,
			CURLOPT_RETURNTRANSFER => 1
		];

		curl_setopt_array($curl, $options + $default_options);

		$result = curl_exec($curl);
		$info = curl_getinfo($curl);

		curl_close($curl);

		return $result;
	}
}
