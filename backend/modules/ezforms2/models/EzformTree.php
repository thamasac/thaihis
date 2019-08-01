<?php

namespace backend\modules\ezforms2\models;
 
use Yii;
 
class EzformTree extends \kartik\tree\models\Tree
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ezform_tree';
    }    
    public function afterFind() {
//        if ($this->readonly==0)
//        {
//            $this->name = $this->name . " (<i class='bg-danger'>เฉพาะฉัน</i>)";
//        }
    }

    public function beforeSave($insert) {
        if ($insert) {        
            $this->userid=Yii::$app->user->id;
            if ($this->readonly=="") $this->readonly=0;
            if ($this->collapsed=="") $this->collapsed=0;
        }else{
            $treedata = Yii::$app->request->post('TblTree');
            if ($treedata['userid'] != "") $this->userid = $treedata['userid'];
            
    //            $ezfid = implode(',', $treedata['ezf_id']);
    //            $this->ezf_id = $ezfid;            
    //            $parent = TblTree::find()->where('id=5')->one();
    //            $child = new TblTree(['name' => 'Test add 5','readonly'=>'1','userid'=>Yii::$app->user->id,'icon' => 'newspaper-o']);
    //            $child->prependTo($parent);
        }
        
        if ($this->readonly==1) {
            $this->removable=0;
            $this->movable_d=0;
            $this->movable_l=0;
            $this->movable_r=0;
            $this->movable_u=0;
        }else{
            $this->removable=1;
            $this->movable_d=1;
            $this->movable_l=1;
            $this->movable_r=1;
            $this->movable_u=1;                
        }         
        
        if ($this->readonly==0 && $this->icon_type==1) {
            $this->icon = "user";
        }
        return parent::beforeSave($insert);
    }    

    
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ezform', 'ID'),
            'name' => Yii::t('ezform', 'Name'),
            'icon' => Yii::t('ezform', 'icon'),
            'userid' => Yii::t('ezform', 'Created By'),
            'ezf_id' => Yii::t('ezform', 'Form'),
        ];
    }
//    //    public function beforeSave()
//    {
//        if (parent::beforeSave()) {
//            if ($this->isNewRecord) {
//                
//                $this->userid=Yii::$app->user->id;
//                if ($this->readonly=="") $this->readonly=0;
//                return true;
//            }
//            return true;
//        } else {
//            return false;
//        }
//    }    
    /**
     * Override isDisabled method if you need as shown in the  
     * example below. You can override similarly other methods
     * like isActive, isMovable etc.
     */
//    public function isDisabled()
//    {
//        if (Yii::$app->user->id !== '1435745159010041100') {
//            return true;
//        }
//        return parent::isDisabled();
//    }
}

?>