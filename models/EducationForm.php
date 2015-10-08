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
    public $ethics = 1;
    public $certificate;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['organization', 'number_of_units', 'date', 'ethics'], 'required'],
            [['organization'], 'string', 'max' => 255],
            [['certificate'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 1],
            [['ethics'], 'integer'],
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
            'ethics' => 'Ethics'
        ];
    }
    
    
    public function saveData(){
        $education = new Education();
        $education->organization = $this->organization;
        $education->number_of_units = $this->number_of_units;
        $education->date = $this->date;
        $education->ethics = $this->ethics;
        $education->certificate = $this->certificate;
        $education->created = new \yii\db\Expression('NOW()');
        $education->modified = new \yii\db\Expression('NOW()');
        $education->user_id = Yii::$app->user->id;
        
        if($education->save()){
            return $education;
        }
        return false;
        
    }


}
