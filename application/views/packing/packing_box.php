<!-- แสดงผลกล่อง  -->
<div class="row">
  <div class="col-lg-9-harf col-md-9 col-sm-8 col-xs-9 padding-5" id="box-row">
  <?php $box_id = ""; ?>
  <?php if(!empty($box_list)) : ?>
  <?php $active = count($box_list) == 1 ? 'btn-success' : ''; ?>
  <?php $box_id = count($box_list) == 1 ? $box_list[0]->id : ""; ?>
  <?php   foreach($box_list as $rs) : ?>
        <button type="button"
        class="btn btn-sm box-btn <?php echo $active; ?>"
        style="margin-bottom:3px;"
        id="btn-box-<?php echo $rs->id; ?>"
        onclick="setBox(<?php echo $rs->id; ?>)">
          กล่องที่ <?php echo $rs->box_no; ?>&nbsp;&nbsp;
          [ <span id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span> ]
        </button>
  <?php   endforeach; ?>
  <?php endif; ?>
  </div>
  <div class="col-lg-2-harf col-md-3 col-sm-4 col-xs-3 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-md btn-primary pull-right visible-xs" style="margin-bottom:3px;" onclick="addBox()"><i class="fa fa-plus"></i></button>
      <button type="button" class="btn btn-md btn-info pull-right visible-xs" style="margin-bottom:3px;" onclick="showBoxOption()"><i class="fa fa-ellipsis-v"></i></button>
      <button type="button" class="btn btn-sm btn-primary hidden-xs" style="margin-bottom:3px;" onclick="addBox()">เพิ่มกล่อง</button>
      <button type="button" class="btn btn-sm btn-info hidden-xs" style="margin-bottom:3px;" onclick="showBoxOption()">Box Option</button>
    </p>
  </div>
</div>

<input type="hidden" id="box_id" value="<?php echo $box_id; ?>" />
<hr class="padding-5"/>

<script id="box-template" type="text/x-handlebars-template">
  {{#each this}}
<button type="button"
class="btn btn-sm box-btn {{ class }}"
id="btn-box-{{box_id}}"
style="margin-bottom:3px;"
onclick="setBox({{box_id}})">
  กล่องที่ {{ no }}&nbsp;&nbsp;
  [ <span id="{{box_id}}">{{qty}}</span> ]
</button>
{{/each}}
</script>
<!-- แสดงผลกล่อง  -->
