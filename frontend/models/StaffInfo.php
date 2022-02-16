<?php

namespace frontend\models;

use \yii\db\ActiveRecord;

class StaffInfo extends ActiveRecord
{	
	//---------------------------------------------------------------------------
    public static function primaryKey()
	//---------------------------------------------------------------------------
    {
        return ['employee_id', 'base_id'];
    }

	//---------------------------------------------------------------------------
    public function rules()
	//---------------------------------------------------------------------------
    {
        return [
			[['employee_id'], 'string', 'max' => 15],
			[['base_id'], 'string', 'max' => 150],     
			[['position'], 'string', 'max' => 150],
			[['work_phone'], 'string', 'max' => 4],
        ];
    }
	
	//---------------------------------------------------------------------------
    public function attributeLabels()
	//---------------------------------------------------------------------------
    {
        return [
            'employee_id' => 'GUID в 1С',
            'base_id' =>'GUID базы 1С сотрудника',                   
			'position' => 'Должность',
			'work_phone' => 'Рабочий номер телефона',
        ];
    }
	
	//---------------------------------------------------------------------------
	public function beforeSave($insert)
	//---------------------------------------------------------------------------
	{
		if(parent::beforeSave($insert))
		{ 
			//
			return true;
		}
		else
			return false;
	}
	
	
}
