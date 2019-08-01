<?php
use yii\helpers\Html;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="list-group">
    <?php if (isset($result) && is_array($result)): ?>
        <?php foreach ($result as $val): ?>
        <li class="list-group-item list-group-item-action"><?php //Html::checkbox('tasks', 'true')?> <?= $val['task_name'] ?></li>
            <?php
            endforeach;
        endif;
        ?>
</div>

