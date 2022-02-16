<?php

namespace frontend\traits;

trait DataExtractor
{
    public function dataAttributes()
    {
        return json_decode($this->data)->attributes;
    }

    public function dataTables()
    {
        return json_decode($this->data)->tables;
    }
}
