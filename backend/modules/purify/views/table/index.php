<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<!--<h1>table/index</h1>

<p>
    You may change the content of this page by modifying
    the file <code><?= __FILE__; ?></code>.
</p>-->

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
