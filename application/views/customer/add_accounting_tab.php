<div class="row">
  <div class="col-sm-8 col-xs-12">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Consolidating BP</label>
        <div class="col-sm-2 col-xs-12 padding-5">
          <input type="text" class="form-control input-sm" name="fatherCard" id="fatherCard" />
        </div>
        <div class="col-sm-4 col-xs-12 padding-5">
          <input type="text" class="form-control input-sm" name="fatherName" id="fatherName" disabled/>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-12 col-xs-12">
          <div class="radio">
            <label>
            <input type="radio" name="fatherType" class="ace" value="P" checked>
            <span class="lbl">  Payment Consolidation</span>
            </label>

            <label>
            <input type="radio" name="fatherType" class="ace" value="D">
            <span class="lbl">  Delivery Consolidation</span>
            </label>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-group">
        <label class="col-sm-4 col-xs-12">Accounts Receivable</label>
        <div class="col-sm-4 col-xs-12 padding-5">
          <input type="text" class="form-control input-sm" name="debPayAcct" id="debPayAcct" />
        </div>
        <div class="col-sm-4 padding-5">
          <input type="text" class="form-control input-sm" name="debPayName" id="debPayName" disabled />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 col-xs-12">Down Payment Clearing Account</label>
        <div class="col-sm-4 col-xs-12 padding-5">
          <input type="text" class="form-control input-sm" name="dpmClear" id="dpmClear" />
        </div>
        <div class="col-sm-4 padding-5">
          <input type="text" class="form-control input-sm" name="dpmName" id="dpmName" disabled />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 col-xs-12">Down Payment Inactive Account</label>
        <div class="col-sm-4 col-xs-12 padding-5">
          <input type="text" class="form-control input-sm" name="dpmInAct" id="dpmInAct" />
        </div>
        <div class="col-sm-4 padding-5">
          <input type="text" class="form-control input-sm" name="dpmInActName" id="dpmInActName" disabled />
        </div>
      </div>

      <div class="divider"></div>

      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Tax Status</label>
        <div class="col-sm-4 col-xs-12 padding-5">
          <select class="form-control input-sm" name="taxStatus" id="taxStatus">
            <option value="Y">Liable</option>
            <option value="N">Exempted</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Tax Group</label>
        <div class="col-sm-2 col-xs-4 padding-5">
          <input type="text" class="form-control input-sm" name="taxCode" id="taxCode" value="<?php echo $tax->code; ?>" />
        </div>
        <div class="col-sm-4 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm" name="taxName" id="taxName" value="<?php echo $tax->name; ?>" disabled/>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-12 col-xs-12">
          <div class="checkbox">
            <label>
            <input type="checkbox" name="deferTax" id="deferTax" class="ace" value="Y">
            <span class="lbl"> &nbsp;&nbsp;Deferred Tax</span>
            </label>
          </div>
        </div>
      </div>
    </div><!-- form -->
  </div><!--- end right column -->
</div>
