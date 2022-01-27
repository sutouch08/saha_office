<!-- แสดงผลกล่อง  -->
<div class="row">
  <div class="col-lg-10 col-md-9 col-sm-8 col-xs-6 padding-5" id="box-row">
  <?php if(!empty($box_list)) : ?>
  <?php   foreach($box_list as $rs) : ?>
        <button type="button" class="btn btn-sm btn-default" id="btn-box-<?php echo $rs->id; ?>" onclick="printBox(<?php echo $rs->id; ?>)">
          <i class="fa fa-print"></i>&nbsp;กล่องที่ <?php echo $rs->box_no; ?>&nbsp; : &nbsp;
          <span id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span>&nbsp; Pcs.
        </button>
  <?php   endforeach; ?>
  <?php else : ?>
    <span id="no-box-label">ยังไม่มีการตรวจสินค้า</span>
  <?php endif; ?>
  </div>
</div>

<hr class="padding-5"/>

<script id="box-template" type="text/x-handlebars-template">
  {{#each this}}
<button type="button" class="btn btn-sm {{ class }}" id="btn-box-{{box_id}}" onclick="printBox({{box_id}})">
  <i class="fa fa-print"></i> &nbsp; กล่องที่ {{ no }}&nbsp; : &nbsp;
  <span id="{{box_id}}">{{qty}}</span>&nbsp; Pcs.
</button>
{{/each}}
</script>
<!-- แสดงผลกล่อง  -->
