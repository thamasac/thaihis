<div class="btn btn-primary">sadsad</div>
<div class="text-center h2"> รายงานตรวจสุขภาพประจำปี <?=(substr($data['visit_date'], 0, 4) + 543)?></div>
<table class="table table-striped table-bordered">       
  <tbody style="font-size:16px">
      <?php
      foreach ($resultLab as $value) {
          ?>
        <tr>
          <td></td>
          <td></td>
          <td></td>    
        </tr>
    <?php } ?>
  </tbody>
</table>
