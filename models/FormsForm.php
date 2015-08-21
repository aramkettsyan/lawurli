<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Forms;

/**
 * LoginForm is the model behind the login form.
 */
class FormsForm extends Model {

    public $label;
    public $type;
    public $placeholder;
    public $numeric;
    public $options;
    public $created;
    public $modified;
    public $sub_section_id;
    public $id;
    public $isNewRecord = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_section_id', 'numeric','id'], 'integer'],
            [['type', 'options'], 'string'],
            [['created', 'modified'], 'safe'],
            [['label', 'placeholder'], 'string', 'max' => 255],
            ['options','checkOptions','skipOnEmpty'=> false],
            ['placeholder', 'required','on'=>'input'],
            ['placeholder', 'required','on'=>'textarea'],
            [['options', 'label'], 'required','on'=>'select'],
            [['label','options'], 'required','on'=>'radio'],
            [['label','options'], 'required','on'=>'checkbox'],
        ];
    }
    
    public function checkOptions($attr,$param){
        if($this->type === 'select' || $this->type === 'checkbox' || $this->type === 'radio'){
            if(empty($this->options)){
                $this->addError($attr,'You must type options');
            }
        }
    }
    
    public function save($validation=true){
        if(!$this->validate()){
            return false;
        }
        if($this->id){
            $form = Forms::findOne($this->id);
        }else{
            $form = new Forms();
        }
        $form->label= $this->label;
        $form->placeholder = $this->placeholder;
        $form->type= $this->type;
        $form->numeric= $this->numeric;
        $form->sub_section_id= $this->sub_section_id;
        $form->options= $this->options;
        return $form->save(false);
    }
    
    public function update(){
        
    }
    
//    public function update($validation){
//        $form = new Forms();
//        $form = $form->findOne($this->id);
//        $form->label= $this->label;
//        $form->placeholder = $this->placeholder;
//        $form->type= $this->type;
//        $form->numeric= $this->numeric;
//        $form->id= $this->id;
//        $form->sub_section_id= $this->sub_section_id;
//        $form->options= $this->options;
//        return $form->save(false);
//    }

}
