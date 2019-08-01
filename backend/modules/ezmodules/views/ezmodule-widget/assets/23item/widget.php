<?php
use backend\modules\ezforms2\classes\EzfAuthFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */
$default_column = isset($options['default_column']) ? $options['default_column'] : 1;
$pagesize = isset($options['pagesize']) ? $options['pagesize'] : 50;
$order = isset($options['order']) ? $options['order'] : [];
$order_by = isset($options['order_by']) ? $options['order_by'] : 4;
$db2 = isset($options['db2']) ? $options['db2'] : 0;
$reloadDiv = $reloadDiv . '-custom';

?>
<div class="panel panel-primary" >
    <div class="panel-heading">
        <h3 class="panel-title">
            <a class="btn btn-success btn-sm" style="color: white;" href="#" data-toggle='modal' data-target='#modal-Terms' ><i class="fa fa-plus"></i> Register a new trial</a></h3>
    </div>
    <div class="panel-body">
        <?php
        $uiView = \backend\modules\tctr\classes\TctrHelper::ui($options['ezf_id'])
                ->data_column($options['fields'])
                ->reloadDiv($reloadDiv)
                ->default_column($default_column)
                ->pageSize($pagesize)
                ->order_column($order)
                ->orderby($order_by);
        if (!EzfAuthFunc::canManage($module, '') && !EzfAuthFunc::canReadWrite($module, '')) {
            $uiView->disabled(true);
        }
        if ($db2 == 1) {
            echo $uiView->buildDb2Grid();
        } else {
            echo $uiView->buildGrid();
        }
        ?>
    </div>
</div>

<div id="modal-Terms" class="fade modal" role="dialog">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-dismiss="modal" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="col-md-12">
                        <h3 class="text-center">Terms and Conditions of Use</h3>
                    </div>
                    <div class="col-md-12">
                        <p> By using this web site, you are agreeing to comply with the current&nbsp;Terms and Conditions of Use. The content of these Terms and Conditions&nbsp;of Use can be updated at any time without prior notice. The Terms and&nbsp;Conditions are as follows, without any particular order:</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px"> - You must comply with any applicable local laws; those from where you&nbsp;originate, where the research might be carried out and Thai laws.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px">- You will not share your username/password with anybody.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px"> - You acknowledge that the data this site (Thai Clinical Trials&nbsp;Registry) provides is "as is" and that TCTR has no responsibilities&nbsp;for the accuracy, the currency or the validity of the data.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px">- In no circumstances shall TCTR be liable to damages caused by loss of&nbsp;data, disruption of service, technical failure, breach in security, or&nbsp;delay of responses in any jurisdictions.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px">- Once entered into our database, no data will be deleted. However,&nbsp;only the most current data may be displayed.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px">- We might share the data you enter with other persons, organizations,&nbsp;institutions, websites or anybody we deem appropriate without&nbsp;informing anybody.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px">- If you are a registrant of a trial, you must also</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 75px">1. Acknowledge that to comply with ICMJE's clinical trials&nbsp;registration requirements, the registration must be done and completed&nbsp;before the enrollment of the first subject.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 75px">2. Once you start the registration process but have not completed it, please complete it as soon as possible. You will be reminded by email to complete the registration every 15 days for 3 times after which time your incomplete record will be deleted from the system. And if you want to continue with registration, you will have to re-enter all the information again.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 75px">3. Update the data of your registration in a timely manner and at&nbsp;least once every 6 months after the completion of your registration.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 75px">4. Be responsible for the accuracy, the currency and the validity of&nbsp;the data you enter.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 75px">5. Make sure that your registration will not be and has not been&nbsp;entered into our database more than once either by you or others.</p>
                    </div>
                    <div class="col-md-12">
                        <p style="padding-left: 50px"> To use this website, you must agree to all the aforementioned terms&nbsp;and conditions without exception.</p>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <label class="radio-inline">
                                <input type="radio" name="agree1" id="agree1"  value="1" checked=""> Agree   
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="agree1" id="agree2" value="2"> Don't agree
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button class="btn btn-success btn-sm " id="submit-agree">Submit</button>
                        <?php
                        if ($db2 == 0) {
                            echo \backend\modules\ezforms2\classes\EzfHelper::btn($options['ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->options([
                                        'class' => 'btn btn-success btn-sm hidden', 'data-dismiss' => 'modal'
                                    ])
                                    ->label('Submit')
                                    ->buildBtnAdd();
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="cancel" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJS("
    $('#submit-agree').on('click',function(){
        if($('#agree1').is(':checked')) { 
            $('.ezform-main-open').click();
            setTimeout(function() {
                $('body').addClass('modal-open');
            }, 1500);
        }else{
            $('#modal-Terms').modal('hide');
        }
    });
");
?>
