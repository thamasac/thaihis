<?php
namespace backend\modules\ezforms2\classes;

/**
 * Description of EzformJob
 *
 * @author appxq
 */
class EzformJob extends \yii\base\BaseObject implements \yii\queue\Job {
    public $target;
    public $ezf_field_ref;
    public $value;
    
    public function execute($queue)
    {
        $error = EzfFunc::updateDataRefField($this->target, $this->ezf_field_ref, $this->value);
    }
}
