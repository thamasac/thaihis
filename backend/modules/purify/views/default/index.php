<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>
<!--<div class="purify-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
</div>-->

<div class="purify-default-index">
    <h1>Purify</h1>
    <p>
        Download purify for your project
        <?php 
        echo Html::a( '<code>[Download].</code>',
                'https://tools.cascap.in.th/download/purify/ncrc/2019-01/Purify.zip',
                [
                    'target' => '_self',
                ]);
        ?> 
    </p>
    
</div>