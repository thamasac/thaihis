<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo \kartik\widgets\FileInput::widget([
                    'name' => $id,
                    'id' => $id,
                    'pluginOptions' => [
                        'showPreview' => false,
                        'showCaption' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'browseLabel' => 'Import CSV'
                    ],
                    'options' => ['accept' => '.csv']
                ]);