<div class="row">
  <div class="col-sm-6 col-xs-12 table-responsive" style="height:600px;">
    <table class="table table-striped table-bordered border-1">
      <thead>
        <tr>
          <th class="width-10 text-center">#</th>
          <th class="">Property Name</th>
          <th class="width-5"></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($properties)) : ?>
          <?php foreach($properties as $rs) : ?>
            <tr>
              <td class="middle text-center"><?php  echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->name; ?></td>
              <td class="middle text-center">
                <label>
                  <input type="checkbox" class="ace props" data-label="QryGroup<?php echo $rs->code; ?>"  name="qryGroup[<?php echo $rs->code; ?>]" id="qryGroup-<?php echo $rs->code; ?>" />
                  <span class="lbl"></span>
                </label>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr><td colspan="3" class="text-center">-- No Properties --</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div><!--- end right column -->
  <div class="col-sm-2 col-xs-6">
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="selectAll()">Select All</button>
      </div>
      <div class="divider-hidden">

      </div>
      <div class="col-sm-12 col-xs-6">
        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="clearAll()">Clear Selection</button>
      </div>
    </div>
  </div>
</div>
