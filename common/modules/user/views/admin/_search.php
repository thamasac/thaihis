<?php yii\bootstrap\ActiveForm::begin(['options'=>['class'=>'form-inline']]);?>
<div class="pull-right">
    <div class="row">
        <div class="col-md-12 col-sm-12">
          
                <div class="form-group">
                    <label><?php echo Yii::t('chanpan','Search')?></label>
                    <input type="email" class="form-control" id="email" placeholder="username, ชื่อ-สกุล และ sitecode">
                </div>  
             
            <div class="form-group">
              <label for="pwd"><?php echo Yii::t('chanpan','Role')?></label>
              <select id="usersearch-role" class="form-control" name="UserSearch[role]">
                  <option value="">All</option>
                  <option value="0">ผู้รับบริการ</option>
                  <option value="1">บุคลากร</option>
                  <option value="2">-- ผู้ดูแลระบบฐานข้อมูล</option>
                  <option value="3">-- ผู้ให้บริการด้านการแพทย์และสาธารณสุข</option>
                  <option value="4">-- ผู้บริหาร</option>
                  <option value="5">----- หน่วยงานระดับประเทศ</option>
                  <option value="6">----- หน่วยงานระดับเขต</option>
                  <option value="7">----- หน่วยงานระดับจังหวัด</option>
                  <option value="8">----- หน่วยงานระดับอำเภอ</option>
                  <option value="9">----- หน่วยงานทางการแพทย์และสาธารณสุข</option>
                  <option value="10">-- นักวิจัย</option>
                  <option value="11">-- อื่นๆ </option>
                  </select>
            </div>
       
    <button class="btn btn-primary"><?= Yii::t('chanpan','Search')?></button>
        </div>
    </div>
</div>

<?php yii\bootstrap\ActiveForm::end()?>


