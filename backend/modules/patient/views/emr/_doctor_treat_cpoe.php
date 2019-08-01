<?php if (!empty($view)) : ?>
    <dl class="dl-horizontal">
      <dt style="width: 35px;">MD:</dt>
      <dd style="margin-left: 40px;">
          <?= isset($data['doctor_treat']) ? $data['doctor_treat'] : '' ?>
      </dd>
    </dl>
<?php else : ?>
    <div class="col-md-12">
      <strong>MD :</strong>
      <?= isset($data['doctor_treat']) ? $data['doctor_treat'] : '' ?>
    </div>
<?php endif; ?>
