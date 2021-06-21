<div class="col-sm-6 col-xs-12">
  <div class="row">
    <div class="col-sm-10">
      <table class="table table-striped border-1">
        <thead>
          <tr>
            <th class="middle"><h3 style="margin-top:0px; margin-bottom:0px; display:inline;">Contact Person </h3>
              <?php if(!$is_exists) : ?>
              <button type="button" class="btn btn-minier btn-primary pull-right" onclick="clearContactForm()">
                <i class="fa fa-plus"></i> Define New
              </button>
              <?php endif; ?>
            </th>
          </tr>
          </thead>
        <tbody id="ct-table">
          <?php $ct_no = 0; ?>
          <?php if(! empty($contact)) : ?>
            <?php foreach($contact as $rs) : ?>
              <tr class="row-ct" id="ct-tr-<?php echo $ct_no; ?>">
                <td>
                  <span id="ct-row-<?php echo $ct_no; ?>"><?php echo $rs->Name; ?></span>
                  <?php if(! $is_exists) : ?>
                    <button type="button" class="btn btn-minier btn-danger pull-right margin-left-5" onclick="deleteContactPerson(<?php echo $ct_no; ?>)">ลบ</button>
                  <?php endif; ?>
                  <?php if(! $is_exists) : ?>
                    <button type="button" class="btn btn-minier btn-warning pull-right margin-left-5" onclick="editContactPerson(<?php echo $ct_no; ?>)">แก้ไข</button>
                  <?php endif; ?>
                  <?php if($is_exists) : ?>
                    <button type="button" class="btn btn-minier btn-info pull-right margin-left-5" onclick="viewContactPerson(<?php echo $ct_no; ?>)">แสดง</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php $ct_no++; ?>
            <?php endforeach; ?>
          <?php else : ?>
            <tr><td></td></tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>



  <input type="hidden" id="ct-no" value="<?php echo $ct_no; ?>">
  <input type="hidden" id="ct-data" value="<?php echo empty($contact) ? "" : str_replace('"', '&quot;',json_encode($contact)); ?>">
  <input type="hidden" id="ct-id" value=""> <!-- ใช้ระบุว่าแก้ไขรายการใด ถ้าไม่มีค่าหมายถึงเพิ่มใหม่ -->
</div><!--- end right column -->
