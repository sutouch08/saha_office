<div class="col-sm-6 col-xs-12">
  <div class="row">
    <div class="col-sm-10">
      <table class="table table-striped border-1">
        <thead>
          <tr>
            <th class="middle"><h3 style="margin-top:0px; margin-bottom:0px; display:inline;">Bill TO </h3>
              <button type="button" class="btn btn-minier btn-primary pull-right" onclick="toggleAddressTemplate('B')">
                <i class="fa fa-plus"></i> Define New
              </button>
              </th>
          </tr>
          </thead>

        <tbody id="bt-table">
        <?php $bt_no = 0; ?>
        <?php if(! empty($billTo)) : ?>
          <?php foreach($billTo as $rs) : ?>
            <tr class="row-bt" id="bt-tr-<?php echo $bt_no; ?>">
              <td>
                <span id="bt-row-<?php echo $bt_no; ?>"><?php echo $rs->Address; ?></span>
                <?php if(! $is_exists) : ?>
                  <button type="button" class="btn btn-minier btn-danger pull-right margin-left-5" onclick="deleteBtAddress(<?php echo $bt_no; ?>)">ลบ</button>
                <?php endif; ?>
                <?php if(! $is_exists) : ?>
                  <button type="button" class="btn btn-minier btn-warning pull-right margin-left-5" onclick="editBtAddress(<?php echo $bt_no; ?>)">แก้ไข</button>
                <?php endif; ?>
                <?php if($is_exists) : ?>
                  <button type="button" class="btn btn-minier btn-info pull-right margin-left-5" onclick="viewBtAddress(<?php echo $bt_no; ?>)">แสดง</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php $bt_no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr><td></td></tr>
        <?php endif; ?>
        </tbody>
      </table>

      <table class="table table-striped border-1">
        <thead>
          <tr>
            <th class="middle"><h3 style="margin-top:0px; margin-bottom:0px; display:inline;">Ship TO </h3>
              <button type="button" class="btn btn-minier btn-primary pull-right" onclick="toggleAddressTemplate('S')">
                <i class="fa fa-plus"></i> Define New
              </button>
            </th>
          </tr>
          </thead>
        <tbody id="st-table">
          <?php $st_no = 0; ?>
          <?php if(! empty($shipTo)) : ?>
            <?php foreach($shipTo as $rs) : ?>
              <tr class="row-st" id="st-tr-<?php echo $bt_no; ?>">
                <td>
                  <span id="st-row-<?php echo $bt_no; ?>"><?php echo $rs->Address; ?></span>
                  <?php if(! $is_exists) : ?>
                    <button type="button" class="btn btn-minier btn-danger pull-right margin-left-5" onclick="deleteStAddress(<?php echo $st_no; ?>)">ลบ</button>
                  <?php endif; ?>
                  <?php if(! $is_exists) : ?>
                    <button type="button" class="btn btn-minier btn-warning pull-right margin-left-5" onclick="editStAddress(<?php echo $st_no; ?>)">แก้ไข</button>
                  <?php endif; ?>
                  <?php if($is_exists) : ?>
                    <button type="button" class="btn btn-minier btn-info pull-right margin-left-5" onclick="viewStAddress(<?php echo $st_no; ?>)">แสดง</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php $st_no++; ?>
            <?php endforeach; ?>
          <?php else : ?>
            <tr><td></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


  <input type="hidden" id="bt-no" value="<?php echo $bt_no; ?>">
  <input type="hidden" id="bt-data" value="<?php echo empty($billTo) ? "" : str_replace('"', '&quot;',json_encode($billTo)); ?>">
  <input type="hidden" id="bt-id" value=""> <!-- ใช้ระบุว่าแก้ไขรายการใด ถ้าไม่มีค่าหมายถึงเพิ่มใหม่ -->

  <input type="hidden" id="st-no" value="<?php echo $st_no; ?>">
  <input type="hidden" id="st-data" value="<?php echo empty($shipTo) ? "" : str_replace('"', '&quot;',json_encode($shipTo)); ?>">
  <input type="hidden" id="st-id" value=""> <!-- ใช้ระบุว่าแก้ไขรายการใด ถ้าไม่มีค่าหมายถึงเพิ่มใหม่ -->
  <input type="hidden" id="active-template" value="B">
</div><!--- end right column -->
