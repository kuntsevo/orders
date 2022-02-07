<?php
 
namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
 
class Ext1c extends Component 
{
    const HOST = 'http://192.168.0.52';
    const GURL = 'order_';
	
    public $login;
    public $password;   
      
	//---------------------------------------------------------------------------  
    public function init()
	//---------------------------------------------------------------------------
    {
        // if (!function_exists ('curl_init'))
        // {
            // throw new CException('Для работы расширения требуется cURL');
        // }
        parent::init();
    }
        
	//---------------------------------------------------------------------------
    public function sendOrderAction($id, $hs)
	//---------------------------------------------------------------------------
    {	
		$params = [];
		$params['id'] = $id;
		//$res_data = $this->request(self::HOST.'/'.self::GURL.''.$hs.'/hs/OrderCallback/{action}', $params);
        		
		return $res_data;
    }
	
	
	//---------------------------------------------------------------------------
    protected function request($url, $params) 
	//---------------------------------------------------------------------------
    {			
		$postdata = json_encode($params);

		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_USERPWD, $this->login.':'.$this->password);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;'));
		
		$result = curl_exec($ch);
		
		curl_close($ch);		
		
        return $result;
    }
}