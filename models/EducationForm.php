<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * EducationForm is the model behind the education form.
 */
class EducationForm extends Model {

    public $id;
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
            [['id'], 'required','on'=>'update'],
            [['id'], 'integer','on'=>'update'],
            [['organization'], 'string', 'max' => 255],
            [['certificate'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png,jpg,doc,pdf,docx', 'maxFiles' => 1,'on'=>'create'],
            [['certificate'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,doc,pdf,docx', 'maxFiles' => 1,'on'=>'update'],
            [['ethics'], 'number'],
            [['number_of_units'], 'number']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels() {
        return [
            'organization' => 'Organization name',
            'number_of_units' => '# of Units',
            'date' => 'Date',
            'ethics' => 'Legal Ethics',
            'certificate' => 'Upload certificate'
        ];
    }
    
    
    public function saveData(){
        
        if((int)$this->id){
            $education = Education::findOne(['id'=>(int)$this->id,'user_id'=>  Yii::$app->user->id]);
        }else{
            $education = new Education();
        }
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
