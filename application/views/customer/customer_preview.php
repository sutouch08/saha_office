<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Preview Business Partner</h3>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="preview-table">

              </div>
            </div>
            <div class="modal-footer" style="background-color:white;">
                <button type="button" class="btn btn-sm btn-success" onClick="sendToSAP()" ><i class="fa fa-send"></i> Send To SAP</button>
                <button type="button" class="btn btn-sm btn-default" onclick="closeModal('previewModal')">Close</button>
            </div>
        </div>
    </div>
  </div>
</div>

<script id="preview-template" type="text/x-handlebarsTemplate">
  <input type="hidden" id="web_order" value="{{U_WEBORDER}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">BP Code</td><td class="width-70">{{LeadCode}}</td></tr>
      <tr><td>BP Name</td><td>{{LeadName}}</td></tr>
      <tr><td>Currency</td><td>{{Currency}}</td></tr>
      <tr><td>Federal Tax ID</td><td>{{LicTradNum}}</td></tr>
      <tr><td>Owner</td><td>{{OwnerName}}</td></tr>
      <tr><td>Customer Level</td><td>{{Customer_level}}</td></tr>
      <tr><td>Phone 1</td><td>{{Phone1}}</td></tr>
      <tr><td>Phone 2</td><td>{{Phone2}}</td></tr>
    </tbody>
  </table>
</script>

<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Business Partner Temp Status</h3>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="temp-table">

              </div>
            </div>
        </div>
    </div>
  </div>
</div>

<script id="temp-template" type="text/x-handlebarsTemplate">
  <input type="hidden" id="U_WEBORDER" value="{{U_WEBORDER}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">Web Order</td><td class="width-70">{{U_WEBORDER}}</td></tr>
      <tr><td class="width-30">BP Code</td><td class="width-70">{{LeadCode}}</td></tr>
      <tr><td>BP Name</td><td>{{LeadName}}</td></tr>
      <tr><td>Date/Time To Temp</td><td>{{F_WebDate}}</td></tr>
      <tr><td>Date/Time To SAP</td><td>{{F_SapDate}}</td></tr>
      <tr><td>Status</td><td>{{F_Sap}}</td></tr>
      <tr><td>Message</td><td>{{Message}}</td></tr>
      <tr>
        <td colspan="2">
        {{#if del_btn}}
        <button type="button" class="btn btn-sm btn-danger" onClick="removeTemp()" ><i class="fa fa-trash"></i> Delete Temp</button>
        {{/if}}
        <button type="button" class="btn btn-sm btn-default" onclick="closeModal('tempModal')">Close</button>
        </td>
      </tr>
    </tbody>
  </table>
</script>
