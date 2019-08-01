<div class="row">
  <div class="col-md-3 h4 modal-title">
    <div class="">
      <label class="control-label">ชื่อ-สกุล : <?= $data['fullname'] ?></label>  
    </div>
    <div class="">
      <label class="control-label">เพศ : <?= $data['pt_sex_name'] ?><span id="bmi_sex" class="hidden"><?= ($data['pt_sex']) ? $data['pt_sex'] : '1' ?></span></label>  
      <label class="control-label" style="margin-left: 20px;">อายุ : <span id="bmi_age"><?= backend\modules\thaihis\classes\ThaiHisQuery::calAge($data['pt_bdate']) ?></span> ปี</label>
    </div>
  </div>
  <div class="col-md-9">
    <div class="col-md-4" style="position: relative;">
      <div class="">
        <label class="control-label">BW</label>  
        <div class="form-inline" style="padding-left: 10px;display: inline-block;">
          <div class="input-group">
            <input type="text" onkeyup="calAll()" class="form-control" name="bmi_bw" id="bmi_bw" value="<?= $data['bmi_bw'] ?>" maxlength="" style="width: 150px;">
            <span class="input-group-addon">kg</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4" style="position: relative;">
      <div class="">
        <label class="control-label">HT </label>  
        <div class="form-inline" style="padding-left: 10px;display: inline-block;">
          <div class="input-group">
            <input type="text" onkeyup="calAll()" class="form-control" name="bmi_ht" id="bmi_ht" value="<?= $data['bmi_ht'] ?>" maxlength="" style="width: 150px;">
            <span class="input-group-addon">cm</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4" style="position: relative; ">
      <div class="">
        <label class="control-label">Cr</label>  
        <div class="form-inline" style="padding-left: 20px;display: inline-block;">
          <div class="input-group">
              <?php
              $dataCr = \backend\modules\thaihis\classes\OrderQuery::getLabResultLastCr($data['pt_hn']);
              ?>
            <input type="text" onkeyup="calAll()" class="form-control" name="bmi_cr" id="bmi_cr" value="<?= isset($dataCr['result']) ? $dataCr['result'] : '' ?>" maxlength="" style="width: 150px;">
            <span class="input-group-addon"> mg/dL</span>
          </div>
        </div>
      </div>
    </div>  

    <div class="col-md-4" style="position: relative; ">
      <div class="">
        <label class="control-label">BSA</label>  
        <div class="form-inline" style="padding-left: 4px;display: inline-block;">
          <div class="input-group">
            <input type="text" readonly="true" class="form-control" name="bmi_bsa" id="bmi_bsa" value="<?= $data['bmi_bmi'] ?>" maxlength="" style="width: 150px;">
            <span class="input-group-addon"> &nbsp;&nbsp;&nbsp;</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4" style="position: relative;">
      <div class="">
        <label class="control-label">BMI</label>  
        <div class="form-inline" style="padding-left: 3px;display: inline-block;">
          <div class="input-group">
            <input type="text" readonly="true" class="form-control" name="bmi_bmi" id="bmi_bmi" value="<?= $data['bmi_bmi'] ?>" maxlength="" style="width: 150px;">
            <span class="input-group-addon">m<sup>2</sup></span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4" style="position: relative; ">
      <div class="">
        <label class="control-label">GFR</label>  
        <div class="form-inline" style="padding-left: 7px;display: inline-block;">
          <div class="input-group">
            <input type="text" readonly="true" class="form-control" name="bmi_gfr" id="bmi_gfr" value="" maxlength="" style="width: 150px;">
            <span class="input-group-addon"> &nbsp;&nbsp;&nbsp;</span>
          </div>
        </div>
      </div>
    </div>


  </div>
</div>

<script type="text/javascript">
    calAll();

    function calAll() {
      calBsa();
      calBmi();
      calGfr();
    }

    function calGfr() {
      let cr = $("#bmi_cr").val();
      let bw = $("#bmi_bw").val();
      let age = $("#bmi_age").text();
      let sex = $("#bmi_sex").text();
      let gfr = 0;
      if (cr != 0 && cr != "") {
        if ($.isNumeric(bw) && $.isNumeric(cr)) {
          gfr = ((140 - Number(age)) * (Number(bw) / (72 * Number(cr))));
          //sumgfr = gfr * 0.85 //หญิง
          if (sex == '2') {
            gfr = gfr * 0.85
          }
        }
      }
      $("#bmi_gfr").val(gfr.toFixed(2));
      return;
    }

    function calBsa() {
      let bw = $("#bmi_bw").val();
      let ht = $("#bmi_ht").val();
      var bsa = 0;
      if ($.isNumeric(bw) && $.isNumeric(ht)) {
        bsa = (Number(bw) * Number(ht)) / 3600;
      }
      $("#bmi_bsa").val(Math.sqrt(bsa).toFixed(2));
      return;
    }

    function calBmi() {
      let bw = $("#bmi_bw").val();
      let ht = $("#bmi_ht").val();
      let bmi = 0;
      if ($.isNumeric(bw) && $.isNumeric(ht)) {
        bmi = (Number(bw) / ((Number(ht) / 100) * (Number(ht) / 100)));
      }
      $("#bmi_bmi").val(bmi.toFixed(2));
      return;
    }

    function M2(val) {
      let bsa = $('#bmi_bsa').val();
      let M2 = 0;
      if ($.isNumeric(bsa) && $.isNumeric(val)) {
        M2 = val * bsa;
      }
      $('#ez1516073084076581100-order_tran_chemo_result').val(M2.toFixed(0));
      return;
    }

    function AUC(val) {
      let gfr = $("#bmi_gfr").val();
      let AUC = 0;
      console.log(gfr);
      if ($.isNumeric(gfr) && $.isNumeric(val)) {
        AUC = (Number(gfr) + 25) * val;
      }
      $('#ez1516073084076581100-order_tran_chemo_result').val(AUC.toFixed(0));
      return;
    }

    function KG(val) {
      let bw = $("#bmi_bw").val();
      let KG = 0;
      if ($.isNumeric(bw) && $.isNumeric(val)) {
        KG = val * bw.val();
      }
      $('#ez1516073084076581100-order_tran_chemo_result').val(KG.toFixed(0));
      return;
    }
    //show textbox cal chemo
    $('[item-id="1547176276004762100"],[item-id="1547176326093646300"]').removeClass('hidden');

<?= $formula . '(' . $calVal . ');' ?>
</script>