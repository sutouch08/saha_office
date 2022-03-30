<div class="row">
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที่</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" value="" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" value="" placeholder="To" readonly />
		</div>
	</div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">ลูกค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" id="customer" value="" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่ออเดอร์</label>
    <input type="number" class="form-control input-sm" id="soCode" placeholder="ค้นหาเลขที่ออเดอร์" autofocus/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-so" onclick="get_order_list()">แสดงรายการ</button>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear" onclick="clear_so_filter()">Clear filter</button>
  </div>

  <div class="col-lg-2 col-lg-offset-3 col-md-1-harf col-md-offset-1 col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-danger btn-block" id="btn-delete-row" onclick="deleteRows()">Delete Rows</button>
  </div>
</div>
<hr class="padding-5 margin-top-10 margin-bottom-10">




<div class="modal fade" id="ErrorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom:0px;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Seach Results</h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <div class="row">
          <div class="col-sm-12 col-xs-12">
            <h3 id="err-text" class="text-center"></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="ordersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:90%; margin:auto; margin-top:30px;">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom:0px;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Seach Results</h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" id="order-table" style="overflow:auto; max-height:600px;">

          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="addAll()">เพิ่มเข้า Pick List</button>
          <button type="button" class="btn btn-sm btn-info" onClick="viewSoDetails()">เลือกรายการสินค้า</button>
      </div>
    </div>
  </div>
</div>


<script id="order-template" type="text/x-handlebarsTemplate">
  <table class="table table-striped border-1" style="margin-bottom:5px; min-width:1000px;">
    <tbody>
      <tr>
      <td class="middle text-center" style="width:25px;">
        <label>
          <input type="checkbox" class="ace" id="check-order-all" onchange="checkAllOrder()">
          <span class="lbl"></span>
        </label>
      </td>
      <td style="width:90px;">SO No.</td>
      <td style="width:100px;">รหัสลูกค้า</td>
      <td style="min-width:250px;">ชื่อลูกค้า</td>
      <td style="width:100px;">วันที่</td>
      <td style="min-width:250px;">Ship to</td>
      <td style="">Remark</td>
      </tr>
      {{#each this}}
        <tr>
          <td class="middle text-center">
            <label>
              <input type="checkbox" class="ace check-order" id="check-order-{{DocEntry}}" value="{{DocEntry}}" />
              <span class="lbl"></span>
            </label>
          </td>
          <td class="middle">{{DocNum}}</td>
          <td class="middle">{{CardCode}}</td>
          <td class="middle">{{CardName}}</td>
          <td class="middle">{{DocDate}}</td>
          <td class="middle">{{ShipTo}}</td>
          <td class="middle">{{remark}}</td>
      {{/each}}
    </tbody>
  </table>
</script>


<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:90%; margin:auto; margin-top:30px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-title"
                  style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">เลือกรายการสินค้า</h4>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" id="details-table" style="overflow:auto; max-height:600px;">

              </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-primary" onclick="addToList()">เพิ่มเข้า Pick List</button>
        </div>
    </div>
  </div>
</div>

<script id="details-template" type="text/x-handlebarsTemplate">
  <table class="table table-striped border-1" style="margin-bottom:5px; min-width:800px;">
    <tbody>
      <tr>
        <td class="middle text-center" style="width:50px;">
          <label>
            <input type="checkbox" class="ace" id="check-detail-all" onchange="checkDetailAll()">
            <span class="lbl"></span>
          </label>
        </td>
        <td class="middle text-center" style="width:100px;">Order No.</td>
        <td class="middle" style="width:150px;">ItemCode</td>
        <td class="middle" style="width:250px;">Description</td>
        <td class="middle text-center" style="width:150;">UOM</td>
        <td class="middle text-right" style="width:150px;">Ordered</td>
        <td class="middle text-right" style="width:150px;">Open</td>
        <td class="middle text-right" style="width:150px;">Released</td>
        <td class="middle text-right" style="width:150px;">Balace</td>
        <td class="middle text-right" style="width:150px;">Available</td>
        <td class="middle" style="width:250px;">Customer</td>
      </tr>

      {{#each this}}
  			<tr class="{{red}}">
          <td class="middle text-center">
            {{#if disabled}}

            {{else}}
            <label>
              <input type="checkbox"
                class="ace check-detail"
                id="check-detail-{{rowNum}}"
                data-docentry="{{OrderEntry}}"
                data-linenum="{{OrderLine}}"
                value="{{rowNum}}" />
              <span class="lbl"></span>
            </label>
            {{/if}}
          </td>
          <td class="middle text-center">{{OrderCode}}</td>
          <td class="middle">{{ItemCode}}</td>
          <td class="middle">{{ItemName}}</td>
          <td class="middle text-center">{{unitMsr}}</td>
          <td class="middle text-right">{{OrderQty}}</td>
          <td class="middle text-right">{{OpenQty}}</td>
          <td class="middle text-right">{{PrevRelease}}</td>
          <td class="middle text-right">{{AvailableQty}}</td>
          <td class="middle text-right">{{OnHand}}</td>
          <td class="middle">{{CardName}}</td>
  			</tr>
      {{/each}}
    </tbody>
  </table>
</script>
