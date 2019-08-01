<?php
use yii\helpers\Html;
$act = isset($_get['act'])?$_get['act']:null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
$this->title = "Frontend";
$ezf_content = \appxq\sdii\utils\SDUtility::string2Array(Yii::$app->params['web_content_form']);
$ezf_id = $ezf_content[0];

$getProject = Yii::$app->params['model_dynamic']; 
if (!empty($getProject)) {
    //$myproject = \backend\modules\manageproject\classes\CNSettingProjectFunc::MyProjectByidNoUser($getProject['data_id']);
    $myproject = Yii::$app->params['my_project'];
}
if( isset($myproject['data_create']) ) {
    $myproject = $myproject['data_create'];
}

if(isset($ezf_id)){
    $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);

    if(!empty($tab)){
        $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenuContent($modelEzf->ezf_table, $tab,['sitecode'=> isset($myproject['sitecode']) ? $myproject['sitecode'] : '']);

    }else{
            $model_menuall = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, 0,isset($myproject['sitecode']) ? $myproject['sitecode'] : '');
            if ($model_menuall) {
                foreach ($model_menuall as $key => $value) {
                    if ($key == 0) {
                        $tab = $value['id'];
                        break;
                    }
                }
            }

        $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenuContent($modelEzf->ezf_table, $tab,['sitecode'=>isset($myproject['sitecode']) ? $myproject['sitecode'] : '']);

    }

}else{
    throw new \yii\web\NotFoundHttpException();
}

if($model_menu){
    $this->title = $model_menu['menu_name'];
    ?>

    <div class="site-index" style="margin-top:6em">

        <?php
        //if (Yii::$app->user->can('adminsite') || Yii::$app->user->can('administrator')):
        //echo \yii\helpers\Html::a('<i class="fa fa-pencil-square-o"></i>', '?act=edit', ['class' => 'btn_edit_content pull-right', 'dataid' => $menu, 'style' => 'font-size:22px;', 'data-toggle' => 'tooltip', 'title' => 'Edit This Content']);
        //endif;

        if (Yii::$app->user->can('adminsite') || Yii::$app->user->can('administrator')):
            backend\modules\ezforms2\classes\EzfStarterWidget::begin();
            echo backend\modules\ezforms2\classes\EzfHelper::btn($ezf_id)->modal('modal-ezform-content')
                ->options(['class'=>'btn btn-success  pull-right'])
                ->buildBtnAdd();

            echo backend\modules\ezforms2\classes\EzfHelper::btn($ezf_id)->modal('modal-ezform-content')
                ->options(['class'=>'btn btn-default pull-right','style'=>'margin-right:5px'])
                ->buildBtnEdit($tab);

            backend\modules\ezforms2\classes\EzfStarterWidget::end();
        endif;
        echo "<br/>";
        ?>
        <div class="clearfix"></div>

        <?php
        if ($act == 'edit') {
            $form = ActiveForm::begin([
                'id' => 'frm_content',
                'action' => 'site/update-content'
            ]);
            echo Html::hiddenInput('data_id', $model_menu['id']);

            $settings = [
                'minHeight' => 200,
                'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
                'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
                'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
                'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
                'plugins' => [
                    'fontcolor',
                    'fontfamily',
                    'fontsize',
                    'textdirection',
                    'textexpander',
                    'counter',
                    'table',
                    'definedlinks',
                    'video',
                    'imagemanager',
                    'filemanager',
                    'limiter',
                    'fullscreen',
                ],
                'paragraphize' => false,
                'replaceDivs' => false,
            ];

            echo \vova07\imperavi\Widget::widget([
                'name' => 'web_content',
                'value' => $model_menu['jumb_content'],
                'settings' => $settings,
            ]);

            echo \vova07\imperavi\Widget::widget([
                'name' => 'web_content',
                'value' => $model_menu['menu_content'],
                'settings' => $settings,
            ]);
            echo '<hr>';
            echo '<footer class="footer">';
                echo '<div class="container">';
                    echo '<p class="pull-left">';
                        echo \vova07\imperavi\Widget::widget([
                            'name' => 'web_content',
                            'value' => $model_menu['footer_content'],
                            'settings' => $settings,
                        ]);
                    echo '</p>';
                echo '</div>';
            echo '</footer>';
            echo Html::submitButton('Update', ['class' => 'btn btn-primary pull-right btn_update_content']);

            ActiveForm::end();
        } else {
            if ($model_menu['jumb_enable'] == 1):
                ?>
                <div class="jumbotron topsite">

                    <?= $model_menu['jumb_content'] ?>
                </div>
            <?php
            endif;
            $registerUrl = isset(Yii::$app->params['allow_register_url']) ? Yii::$app->params['allow_register_url'] : '';
            $crrentUrl = isset(Yii::$app->params['current_url']) ?Yii::$app->params['current_url'] : '';
            $modelForm = ['register_url'=>$registerUrl,'project_mainpage'=>"https://{$crrentUrl}/site/index?proj_h=0"];
            $path = [];
            foreach ($modelForm as $key => $value) {
                $path["{" . $key . "}"] = $value;
            }
            $menuContent = strtr($model_menu['menu_content'], $path); 
            echo $menuContent; 

            echo '<hr>';
            echo '<footer class="footer">';
                echo '<div class="container">';
                    echo '<p class="pull-left">';
                        echo $model_menu['footer_content'];
                    echo '</p>';
                echo '</div>';
            echo '</footer>';
        }
        ?>
    </div>
    <br/><br/>
    <?php
} else {
    throw new \yii\web\NotFoundHttpException();
}
?>
<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-ezform-content',
    'size' => 'modal-xxl',
])
?>
<?php
if ($model_menu['menu_js']) {
    \richardfan\widget\JSRegister::begin([
        'position' => \yii\web\View::POS_READY
    ]);
    ?>
    <script>
        $('#modal-ezform-content').on('hidden.bs.modal', function () {
            console.log('OK');
            window.location.reload();
        });
        <?= $model_menu['menu_js'] ?>

    </script>
    <?php
    \richardfan\widget\JSRegister::end();
}
?>

<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
    <script>
        $('#modal-ezform-content').on('hidden.bs.modal', function () {
            window.location.reload();
        });

    </script>
<?php
\richardfan\widget\JSRegister::end();
?>
