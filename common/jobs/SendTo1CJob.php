<?php
namespace common\jobs;

use \Yii;
use \yii\base\BaseObject;

class SendTo1CJob extends BaseObject implements \yii\queue\JobInterface
{
    public $id;
    public $hs;
    
    public function execute($queue)
    {
       Yii::$app->ext1c->sendOrderAction($this->id, $this->hs);
    }
}