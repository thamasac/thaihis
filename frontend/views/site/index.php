<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */

$menu = isset($_GET['menu']) ? $_GET['menu'] : 0;
$ezf_content = \appxq\sdii\utils\SDUtility::string2Array(Yii::$app->params['web_content_form']);

$ezf_id = $ezf_content[0];
$modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);

$host_arr = explode(".", $_SERVER['HTTP_HOST']);


if ($host_arr[0] !== "www" || $host_arr[0] !== "ncrc") {
    //appxq\sdii\utils\VarDumper::dump($host_arr);
    $ezfIdSitecode = "1520514351069551000";
    //$host_arr[0] = 'joker123';
    $modelEzformSitecode = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezfIdSitecode);
    $modelSitecodeConfig = Yii::$app->db->createCommand("SELECT * FROM zdata_sitecode WHERE site_frontend_url = :domain AND rstat < 3", [":domain" => $host_arr[0]])->queryOne();
    $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
    if (!isset($_GET['menu'])) {
        $model_menuall = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, 0, $modelSitecodeConfig["site_name"]);
        Yii::$app->params['current_site'] =  $modelSitecodeConfig["site_name"];
        if ($model_menuall) {
            foreach ($model_menuall as $key => $value) {
                if ($key == 0) {
                    $menu = $value['id'];
                    break;
                }
            }
        }
    }

    if ($modelSitecodeConfig && $modelSitecodeConfig["enable_site_frontend"] == 1) {
        $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenuContent($modelEzf->ezf_table, $menu, ['sitecode' => $modelSitecodeConfig["site_name"]]);
    } else {
        throw new \yii\web\NotFoundHttpException();
    }
} else {
     
    if (!isset($_GET['menu'])) {
        $model_menuall = \backend\modules\subjects\classes\SubjectManagementQuery::getMenu($modelEzf->ezf_table, 0,'00');
        if ($model_menuall) {
            foreach ($model_menuall as $key => $value) {
                if ($key == 0) {
                    $menu = $value['id'];
                    break;
                }
            }
        }
    }

    $model_menu = \backend\modules\subjects\classes\SubjectManagementQuery::getMenuContent($modelEzf->ezf_table, $menu, ['sitecode' => '00']);
}
?>

<?php
if ($model_menu) {
    $this->title = $model_menu['menu_name'];

    ?>

    <div class="site-index">

        <?php
        //if (Yii::$app->user->can('adminsite') || Yii::$app->user->can('administrator')):
        //echo \yii\helpers\Html::a('<i class="fa fa-pencil-square-o"></i>', '?act=edit', ['class' => 'btn_edit_content pull-right', 'dataid' => $menu, 'style' => 'font-size:22px;', 'data-toggle' => 'tooltip', 'title' => 'Edit This Content']);
        //endif;
        if ( ($host_arr[0] !== "www" && Yii::$app->user->can('adminsite')) || Yii::$app->user->can('administrator')):
            backend\modules\ezforms2\classes\EzfStarterWidget::begin();
            echo backend\modules\ezforms2\classes\EzfHelper::btn($ezf_id)->modal('modal-ezform-content')
                ->options(['class'=>'btn btn-success  pull-right'])
                ->buildBtnAdd();
            echo backend\modules\ezforms2\classes\EzfHelper::btn($ezf_id)->modal('modal-ezform-content')
                ->options(['class'=>'btn btn-default pull-right','style'=>'margin-right:5px'])
                ->buildBtnEdit($menu);
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
            $mainUrl = isset(Yii::$app->params['main_url']) ?Yii::$app->params['main_url'] : '';
            $modelForm = ['register_url'=>$registerUrl,'project_mainpage'=>"https://{$mainUrl}"];
            $path = [];
            foreach ($modelForm as $key => $value) {
                $path["{" . $key . "}"] = $value;
            }
            $menuContent = strtr($model_menu['menu_content'], $path); 
            echo $menuContent;
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



<?php \appxq\sdii\widgets\CSSRegister::begin()?>
<style>
    @media screen and (max-width: 769px){
        .content{
            margin-top: 130px !important;
        }
        div#ezf-main-app {
            /* background: blue; */
            position: absolute;
            right: 15px;
            top: 220px;
        }
    }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>