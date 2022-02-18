<?php

namespace frontend\traits;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

trait DataExtractor
{
    private $dataObject;
    private $attr = [];
    private $tables = [];

    public function __get($name)
    {
        if ($this->getAttribute($name)) {
            return parent::__get($name);
        }

        $this->initializeData();

        if (!empty($this->attr) && array_key_exists($name, $this->attr)) {
            return $this->attr[$name];
        }

        if (!empty($this->tables) && array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        }

        return parent::__get($name);
    }

    private function initializeData()
    {
        if (!is_null($this->dataObject)) {
            return;
        }

        $this->dataObject = Json::decode($this->data, true);
        $this->attr = $this->dataObject['attributes'];
        $this->tables = $this->dataObject['tables'];
    }
}
