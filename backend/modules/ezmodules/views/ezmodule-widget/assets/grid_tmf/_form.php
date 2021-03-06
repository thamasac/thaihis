

<div id="mainDiv">
    <div class="form-group row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
            <?= Html::textInput('options[title][]', '', ['class' => 'form-control','id'=> \appxq\sdii\utils\SDUtility::getMillisecTime()]) ?>
        </div>
        <div class="col-md-6">
            <?= Html::button("<i class='glyphicon glyphicon-remove'></i>", ['class' => 'btn btn-danger btnbtn-remove']); ?>
        </div>
    </div>

    <div class="form-group row">

        <!--<div class="clearfix"></div>-->
        <div class="col-md-6 ">
            <?php
            $attrname_ezf_id = 'options[ezf_id][]';
            $value_ezf_id = '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_ezf_id,
                'value' => $value_ezf_id,
                'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => \appxq\sdii\utils\SDUtility::getMillisecTime()],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-md-6 ">
            <?php
            $attrname_fields = 'options[fields][]';
            $value_fields = '{}';
            ?>
            <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
            <div id="ref_field_box">

            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_fields_search = 'options[fields_search][]';
            $value_fields_search =  '{}';
            ?>
            <?= Html::label(Yii::t('ezform', 'Fields Search'), $attrname_fields_search, ['class' => 'control-label']) ?>
            <div id="fields_search_box">

            </div>
        </div>
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_image_field = 'options[image_field][]';
            $value_image_field = '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Image Field'), $attrname_image_field, ['class' => 'control-label']) ?>
            <div id="pic_field_box">

            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::label(Yii::t('ezform', 'Template Items'), 'options[template_items]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[template_items][]', '', ['class' => 'form-control', 'row' => 3]) ?>
    </div>

    <div class="form-group">
        <?= Html::label(Yii::t('ezform', 'Template Selection'), 'options[template_selection]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[template_selection][]',  '', ['class' => 'form-control', 'row' => 3]) ?>
    </div>
</div>


