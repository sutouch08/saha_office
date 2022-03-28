<!-- แสดงผลกล่อง  -->
<div class="row">
  <div class="col-lg-7 col-md-6 col-sm-6-harf col-xs-12 padding-5" id="pallet-row">
  <?php if(!empty($pallet_list)) : ?>
  <?php   foreach($pallet_list as $rs) : ?>
        <button type="button"
        class="btn btn-sm pallet-btn"
        style="margin-bottom:3px;"
        id="btn-pallet-<?php echo $rs->id; ?>"
        onclick="setPallet(<?php echo $rs->id; ?>)">
          <?php echo $rs->code; ?>
        </button>
  <?php   endforeach; ?>
  <?php endif; ?>
  </div>
  <div class="divider visible-xs"></div>

  <div class="col-lg-5 col-md-6 col-sm-5-harf col-xs-12">
    <div class="row">
      <div class="col-lg-4-harf col-md-4-harf col-sm-5 col-xs-5 padding-5">
        <input type="text" class="form-control input-sm text-center" id="pallet-code" placeholder="Pallet Code" autofocus />
      </div>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2 col-xs-2 padding-5">
        <button type="button" class="btn btn-xs btn-success btn-block visible-sm visible-xs" style="margin-bottom:3px;" onclick="getPallet()">Set</button>
        <button type="button" class="btn btn-xs btn-success btn-block hidden-sm hidden-xs" style="margin-bottom:3px;" onclick="getPallet()">Set Pallet</button>
      </div>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2 col-xs-2 padding-5">
        <button type="button" class="btn btn-xs btn-primary btn-block visible-sm visible-xs" style="margin-bottom:3px;" onclick="addPallet()">Add</button>
        <button type="button" class="btn btn-xs btn-primary btn-block hidden-sm hidden-xs" style="margin-bottom:3px;" onclick="addPallet()">Add Pallet</button>
      </div>
      <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-3 padding-5">
        <button type="button" class="btn btn-xs btn-info btn-block" style="margin-bottom:3px;" onclick="showPalletOption()">Option</button>
      </div>
    </div>

  </div>
</div>

<input type="hidden" id="pallet_id" value="" />
<hr class="padding-5"/>

<script id="pallet-template" type="text/x-handlebars-template">
  {{#each this}}
<button type="button"
class="btn btn-sm pallet-btn {{ class }}"
id="btn-pallet-{{id}}"
style="margin-bottom:3px;"
onclick="setPallet({{id}})">
  {{ code }}
</button>
{{/each}}
</script>
<!-- แสดงผลกล่อง  -->
