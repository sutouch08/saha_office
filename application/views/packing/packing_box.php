<!-- แสดงผลกล่อง  -->
<div class="row">
  <div class="col-lg-9-harf col-md-9-harf col-sm-9 col-xs-12 padding-5" id="box-row">
  <?php if(!empty($box_list)) : ?>
  <?php   foreach($box_list as $rs) : ?>
        <button type="button"
        class="btn btn-sm box-btn"
        style="margin-bottom:3px;"
        id="btn-box-<?php echo $rs->id; ?>"
        data-pallet_id="<?php echo $rs->pallet_id; ?>"
        onclick="setBox(<?php echo $rs->id; ?>)">
          กล่องที่ <?php echo $rs->box_no; ?>&nbsp;&nbsp;
          [ <span id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span> ]
        </button>
  <?php   endforeach; ?>
  <?php endif; ?>
  </div>
  <div class="divider visible-xs"></div>

  <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-12">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
        <button type="button" class="btn btn-sm btn-primary btn-block" style="margin-bottom:3px;" onclick="addBox()">Add Box</button>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
        <button type="button" class="btn btn-sm btn-info btn-block" style="margin-bottom:3px;" onclick="showBoxOption()">Option</button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="box_id" value="" />
<hr class="padding-5"/>

<script id="box-template" type="text/x-handlebars-template">
  {{#each this}}
<button type="button"
class="btn btn-sm box-btn {{ class }}"
id="btn-box-{{box_id}}"
data-pallet_id="{{pallet_id}}"
style="margin-bottom:3px;"
onclick="setBox({{box_id}})">
  กล่องที่ {{ no }}&nbsp;&nbsp;
  [ <span id="{{box_id}}">{{qty}}</span> ]
</button>
{{/each}}
</script>
<!-- แสดงผลกล่อง  -->
