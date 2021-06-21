<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <h3 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Preview Activity</h3>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="preview-table">

              </div>
            </div>
            <div class="modal-footer" style="background-color:white;">
              <div class="row">
                <div class="col-sm-3 col-xs-6 padding-5">
                  <button type="button" class="btn btn-sm btn-success btn-block" onClick="sendToSAP()" ><i class="fa fa-send"></i> Send To SAP</button>
                </div>
                <div class="col-sm-3 col-xs-6 padding-5">
                  <button type="button" class="btn btn-sm btn-default btn-block" onclick="closeModal('previewModal')">Close</button>
                </div>
              </div>


            </div>
        </div>
    </div>
  </div>
</div>

<script id="preview-template" type="text/x-handlebarsTemplate">
  <input type="hidden" id="code" value="{{U_WEBORDER}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr>
        <td>Web order</td>
        <td>{{U_WEBORDER}}</td>
      </tr>
      <tr>
        <td>Activity</td>
        <td>{{Action}}</td>
      </tr>
      <tr>
        <td>Type</td>
        <td>{{Type}}</td>
      </tr>
      <tr>
        <td>Subject</td>
        <td>{{Subject}}</td>
      </tr>
      <tr>
        <td>Assigned To</td>
        <td>{{AssignedTo}}</td>
      </tr>
      <tr>
        <td>BP</td>
        <td>{{CardCode}} : {{CardName}}</td>
      </tr>
      <tr>
        <td>Start Time</td>
        <td>{{StartTime}}</td>
      </tr>
      <tr>
        <td>End Time</td>
        <td>{{EndTime}}</td>
      </tr>
      <tr>
        <td>Remarks</td>
        <td>{{Details}}</td>
      </tr>
      <tr>
        <td>Content</td>
        <td>{{Notes}}</td>
      </tr>

    </tbody>
  </table>
</script>

<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
              <h3 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Activity Temp Status</h3>
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
  <input type="hidden" id="web_code" value="{{U_WEBORDER}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">Web Order</td><td class="width-70">{{U_WEBORDER}}</td></tr>
      <tr><td class="width-30">BP Code</td><td class="width-70">{{CardCode}}</td></tr>
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
