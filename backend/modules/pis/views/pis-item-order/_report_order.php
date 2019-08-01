<?php

use appxq\sdii\utils\SDdate;
use backend\modules\api\v1\classes\LogStash;
use yii\helpers\Html;

$fontSize = '13pt';
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="vertical-align: top;" width="60%">
      <table width="100%" border="" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100%" style="text-align: left; font-size: 16pt;">
            <b><?= $data[0]['fullname']; ?></b>
            &nbsp; <b>อายุ : </b><?php
            if (isset($data[0]['pt_bdate'])) {
                try {
                    echo backend\modules\thaihis\classes\ThaiHisQuery::calAge($data[0]['pt_bdate']);
                } catch (Exception $ex) {
                    echo '-';
                }
            }
            ?> ปี
          </td>
        </tr>
        <tr>
          <td width="100%"  style="text-align: left; font-size: 16pt;">
            <b>HN :</b>  <?= $data[0]['pt_hn'] ?>
          </td>
        </tr>
        <tr>
          <td width="100%" style="text-align: left; font-size: 16pt;">
            <b>สิทธิการักษา :</b> <?= $data[0]['right_name'] ?>
          </td>
        </tr> 
        <tr>
          <td width="100%" style="text-align: left; font-size: 16pt;">
            <b>ข้อมูลแพ้ยา :</b> <?php
            $allergyData = backend\modules\pis\classes\PisQuery::getDrugAllergyShow($data[0]['pt_id']);
            foreach ($allergyData as $value) {
                ?>
                <strong><?= $value['ezf_choicelabel']; ?> </strong>
                <span class="text-danger"><?= $value['drug_allergy']; ?> </span>        
                <?php
            }
            ?>
          </td>
        </tr>    
      </table>
    </td>

    <td style="vertical-align: top;" width="40%">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100%" style="font-size: 16pt;">
            <b>ใบสั่งยา เลขที่ :</b> <?= $data[0]['order_no'] ?>
          </td>
        </tr>
        <tr>
          <td>
            <b>วันที่ :</b> <?= isset($data[0]['receive_date']) ? SDdate::mysql2phpThDate($data[0]['receive_date']) : null ?>
          </td>
        </tr>
        <tr>
          <td width="100%" style="font-size: 16pt;">
            <b>Dx :</b> <?php
            $txtDiag = \backend\modules\thaihis\controllers\BtnReportController::removeTag($data[0]['di_txt']);
            echo $txtDiag;
//            echo backend\modules\pis\controllers\PisItemOrderController::replaceTagP($data[0]['di_txt']);
            if ($data[0]['di_icd10']) {
                echo '<br>' . \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($data[0]['di_icd10']);
            }
            ?>
          </td>
        </tr>
        <tr>
          <td style="font-size: 16pt;">
            <b>แพทย์ :</b>&nbsp; <?= $data[0]['doctor_name']; ?>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
<div></div>
<!--content-->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th width="3%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;">
          <?= Html::label('#') ?>
      </th>
<!--      <th width="10%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
      <?= Html::label('ประเภท') ?>
      </th>-->
      <th width="8%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
          <?= Html::label('รหัสสินค้า') ?>
      </th>
      <th width="45%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
          <?= Html::label('รายการ') ?>
      </th>
      <th width="8%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
          <?= Html::label('จำนวน') ?>
      </th>
      <th width="10%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
          <?= Html::label('ราคา/หน่วย') ?>
      </th>
      <th width="10%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
          <?= Html::label('จำนวนเงิน') ?>
      </th>
      <th width="10%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;">
          <?= Html::label('เบิกได้') ?>
      </th>
      <th width="10%" style="font-size: <?= $fontSize ?>;text-align: center;border-bottom: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;">
          <?= Html::label('เบิกไม่ได้') ?>
      </th>
    </tr>
  </thead>
  <tbody>
      <?php
      $i = 1;
      $chkType = '';
      $sum = [0, 0, 0];
      LogStash::Log(Yii::$app->user->id, '_reportOrder', var_export($data, true), '', 'thaihis');
      foreach ($data as $value) :
//          appxq\sdii\utils\VarDumper::dump($value);
//          if ($value['order_tran_use_type'] !== $chkType) :
//              $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_use_type', ':ezf_id' => $ezf_id])->one();
//              if (isset(Yii::$app->session['ezf_input'])) {
//                  $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
//              }
          ?>
        <tr style="font-size: //<?= $fontSize ?>;vertical-align: top;background-color: #ddd;">                          
          <td colspan="8" width="104%" style="border: 1px solid black;">
              <?php // echo \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $value);   ?>
          </td>
        </tr>
        <tr>
          <td width="3%" style="font-size: <?= $fontSize ?>;text-align: center;vertical-align: top;border: 1px solid black;">
              <?= $i; ?>
          </td>
    <!--          <td width="10%" style="font-size: <?= $fontSize ?>;">
          <?= $value['generic_type']; ?>
          </td>-->
          <td width="8%" style="font-size: <?= $fontSize ?>;text-align: center;vertical-align: top;border: 1px solid black;">
              <?= $value['trad_tmt']; ?>
          </td>
          <td width="45%" style="font-size: <?= $fontSize ?>;vertical-align: top;border: 1px solid black;">
              <?php
              $html = '';

              if ($value['order_tran_chemo_amount'] && $value['order_tran_chemo_result']) {
                  $txt = '<br/> - สูตร ' . $value['order_tran_chemo_cal'] . ' ปริมาณ ' . $value['order_tran_chemo_amount'] . ' ผลลัพธ์ ' . $value['order_tran_chemo_result'];
                  $html .= Html::tag('span', $txt);
              }

              $html .= '<br/>' . Html::tag('span', ' - ' . $value['order_tran_label'], ['style' => 'color:#333;']);

              if ($value['order_tran_note']) {
                  $html .= '<br/>' . Html::tag('span', ' - หมายเหตุ ' . $value['order_tran_note']);
              }

              if ($value['order_tran_ned']) {
                  $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_ned', ':ezf_id' => $ezf_id])->one();
                  if (isset(Yii::$app->session['ezf_input'])) {
                      $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                  }
                  $txtNed = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $value);
                  $html .= '<br/>' . Html::tag('span', ' - ' . $value['order_tran_ned'] . ' : ' . $txtNed, ['style' => 'color:#333;']);
              }

              echo $value['item_name'] . $html;
              ?>

          </td>
          <td width="8%" style="font-size: <?= $fontSize ?>;text-align: center;vertical-align: top;border: 1px solid black;">
              <?= $value['order_tran_qty']; ?>
          </td>
          <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
              <?= number_format($value['unit_price'], 2); ?>
          </td>
          <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
              <?php
              $sum[0] += (float) ((float) $value['unit_price'] * (float) $value['order_tran_qty']);
              echo number_format($value['unit_price'] * $value['order_tran_qty'], 2);
              ?>
          </td>
          <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
              <?php
              $sum[1] += (float) $value['order_tran_notpay'];
              echo number_format($value['order_tran_notpay'], 2);
              ?>
          </td>
          <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
              <?php
              $sum[2] += (float) $value['order_tran_pay'];
              echo number_format($value['order_tran_pay'], 2);
              ?>
          </td>
        </tr>    
        <?php
        LogStash::Log(Yii::$app->user->id, '_reportOrder::SUM', var_export($sum, true), '', 'thaihis');
        $i++;
    endforeach;
    ?>   
    <tr>          
      <td width="74%" colspan="5" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
          <?= Html::label('รวม') ?>
      </td>
      <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
          <?= number_format($sum[0], 2); ?>
      </td>
      <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
          <?= number_format($sum[1], 2); ?>
      </td>
      <td width="10%" style="font-size: <?= $fontSize ?>;text-align: right;vertical-align: top;border: 1px solid black;">
          <?= number_format($sum[2], 2); ?>
      </td>
    </tr>
  </tbody>
</table>
<div></div>
<table width="90%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="vertical-align: top;" width="60%">
      <table width="100%" border="" cellpadding="0" cellspacing="0">

      </table>
    </td>
    <td>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size: 16pt">
        <tr style="text-align: center">
          <td><strong>ลงชื่อ ....................................................................</strong></td>
        </tr>
        <tr>
          <td style="text-align: center"><strong>( <?= 'ว.'.$data[0]['certificate'] . ' ' . $data[0]['doctor_name'] ?> )</strong></td>
        </tr>
        <tr>
          <td style="text-align: center"><strong> <?= 'แพทย์ผู้สั่งยา ' . date('d/m/') . (date('Y') + 543) ?> </strong></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

