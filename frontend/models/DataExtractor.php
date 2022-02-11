<?php

namespace frontend\models;

use yii\base\Behavior;

class DataExtractor extends Behavior
{
    public function dataAttributes()
    {
        return json_decode($this->owner->data)->attributes;
    }

    public function dataTables()
    {
        return json_decode($this->owner->data)->tables;
    }
}
