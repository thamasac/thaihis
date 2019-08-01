<?php
$url = yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
            'reloadDiv' => $reloadDiv, 'view' => 'modal_chart', 'secname' => $secname, 'date' => $date]);
?>

<div class="modal-body">
  <a href="javascript:void(0)" onclick="modalResulOrder('<?= $url ?>')" class="pull-right">Chart</a>
  <div class="text-center h4"><?= $secname ?> </div>
  <?php if (!empty($secname)) { ?>
      <table class="table table-striped table-bordered" style="margin-top: 20px">       
        <tbody style="font-size:16px">
          <tr class="success">
            <td>Test name</td>
            <td>Values</td>

            <td>Normal range</td>    
          </tr>
          <?php
          foreach ($arrResultLab as $value) {
              $resultValue = "";
              if (empty($value['commt_all'])) {
                  $resultValue = $value['result'] . ' ' . $value['comment'];
              } else {
                  $resultValue = '<strong class="text-danger">' . $value['result'] . '  *' . ' ' . $value['comment'] . '</strong>';
              }
              if ($value['result'] == 'Out lab') {
                  $datareport = backend\modules\patient\classes\PatientQuery::getReportOutlab($visit_id, $value['hiscode']);
                  $ezf_id = \backend\modules\patient\Module::$formID['lab_external'];
                  if (isset($datareport['id'])) {
                      $resultValue = backend\modules\ezforms2\classes\BtnBuilder::btn()
                              ->ezf_id($ezf_id)
                              ->label('<i class="fa fa-wpforms"></i> Result')
                              ->options(['class' => 'btn btn-md btn-primary'])
                              ->buildBtnView($datareport['id']);
                  } else {
                      $resultValue = $value['result'] . ' ' . $value['comment'];
                  }
              }
              if ($value['unit']) {
                  $values = $value['test_name'] . ' (' . $value['unit'] . ')';
              } else {
                  $values = $value['test_name'];
              }
              ?>
              <tr <?php if ($value['comment']) { ?> class="danger" <?php } else { ?>class=""<?php } ?>>
                <td><?= $values; ?></td>
                <td><?= $resultValue; ?></td>
                <td><?= $value['normal_range']; ?></td>    
              </tr>
          <?php } ?>
        </tbody>
      </table>
  <?php } else { ?>
      <table class="table table-striped table-bordered">       
        <tbody style="font-size:16px">
            <?php
            $testChk = "";
            foreach ($arrResultLab as $value) {
                $resultValue = "";
                if (empty($value['commt_all'])) {
                    $resultValue = $value['result'] . ' ' . $value['comment'];
                } else {
                    $resultValue = '<strong class="text-danger">' . $value['result'] . '  *' . ' ' . $value['comment'] . '</strong>';
                }
                if ($value['result'] == 'Out lab') {
                    $datareport = backend\modules\patient\classes\PatientQuery::getReportOutlab($visit_id, $value['hiscode']);
                    $ezf_id = \backend\modules\patient\Module::$formID['lab_external'];
                    if (isset($datareport['id'])) {
                        $resultValue = backend\modules\ezforms2\classes\BtnBuilder::btn()
                                ->ezf_id($ezf_id)
                                ->label('<i class="fa fa-wpforms"></i> Result')
                                ->options(['class' => 'btn btn-md btn-primary'])
                                ->buildBtnView($datareport['id']);
                    } else {
                        $resultValue = $value['result'] . ' ' . $value['comment'];
                    }
                }
                if ($value['unit']) {
                    $values = $value['test_name'] . ' (' . $value['unit'] . ')';
                } else {
                    $values = $value['test_name'];
                }
                if ($testChk !== $value['secname']) {
                    ?>
                  <tr class="success">
                    <td><?= $value['secname']; ?></td>
                    <td>ผลการตรวจ</td>
                    <td>ค่าปกติ</td>    
                  </tr>
                  <?php
                  $testChk = $value['secname'];
              }
              ?>
              <tr <?php if ($value['comment']) { ?> class="danger" <?php } else { ?>class=""<?php } ?>>
                <td><?= $values; ?></td>
                <td><?= $resultValue; ?></td>
                <td><?= $value['normal_range']; ?></td>    
              </tr>
          <?php } ?>
        </tbody>
      </table>
  <?php } ?>
</div>