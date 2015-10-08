<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * EducationForm is the model behind the education form.
 */
class EducationForm extends Model {

    public $organization;
    public $number_of_units;
    public $date;
    public $ethics;
    public $certificate;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['organization', 'number_of_units', 'date', 'ethics','certificate'], 'required'],
            [['organization', 'certificate'], 'string', 'max' => 255],
            [['user_id', 'ethics'], 'integer'],
            [['number_of_units'], 'number']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels() {
        return [
            'organization' => 'Organization',
            'number_of_units' => 'Number Of Units',
            'date' => 'Date',
            'ethics' => 'Ethics',
            'certificate' => 'Certificate'
        ];
    }
    
    
    public function saveData(){
        $education = new Education();
        
    }


}
