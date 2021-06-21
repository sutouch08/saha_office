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
          <tr><td></td></tr>
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
          <tr><td></td></tr>
        </tbody>
      </table>
    </div>
  </div>



  <input type="hidden" id="bt-no" value="0">
  <input type="hidden" id="bt-data" value="">
  <input type="hidden" id="bt-id" value=""> <!-- ใช้ระบุว่าแก้ไขรายการใด ถ้าไม่มีค่าหมายถึงเพิ่มใหม่ -->

  <input type="hidden" id="st-no" value="0">
  <input type="hidden" id="st-data" value="">
  <input type="hidden" id="st-id" value=""> <!-- ใช้ระบุว่าแก้ไขรายการใด ถ้าไม่มีค่าหมายถึงเพิ่มใหม่ -->
  <input type="hidden" id="active-template" value="B">
</div><!--- end right column -->
