<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $doc->U_WEBORDER; ?></h4>
  </div>
</div>
<hr class="margin-bottom-15"/>
<div class="row">
  <?php $status = $doc->F_Sap == 'Y' ? 'Success' : ($doc->F_Sap == 'N' ? 'Failed' : 'Pending'); ?>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Status</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $status; ?>" readonly/>
	</div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>Temp date</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->F_WebDate, TRUE); ?>" readonly/>
	</div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>Sap date</label>
		<input type="text" class="width-100 text-center r" value="<?php echo empty($doc->F_Sap) ? "" : thai_date($doc->F_SapDate, TRUE); ?>" readonly/>
	</div>

  <div class="col-lg-8 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
		<label>Message</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->Message; ?>" readonly/>
	</div>
  <div class="divider"></div>
</div>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>U_WEBORDER</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->U_WEBORDER; ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Doc date</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->DocDate); ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->TaxDate); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>Vendor code</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->CardCode; ?>" readonly/>
	</div>
	<div class="col-lg-5-harf col-md-5-harf col-sm-5 col-xs-8 padding-5">
		<label>Vendor name</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->CardName; ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-3 padding-5">
		<label>NumAtCard</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->NumAtCard; ?>" placeholder="invoice code" readonly/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>Currency</label>
    <input type="text" class="width-100 text-center r" value="<?php echo $doc->DocCur; ?>" readonly/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->DocRate; ?>"  readonly/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Whs</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->ToWhsCode; ?>" readonly/>
	</div>
	<div class="col-lg-8-harf col-md-6-harf col-sm-5-harf col-xs-12 padding-5">
		<label>Comments</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->Comments; ?>" readonly/>
	</div>
  <div class="divider"></div>
</div>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:700px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">LineNum</th>
          <th class="fix-width-100">ItemCode</th>
          <th class="fix-width-200">Description</th>
          <th class="fix-width-100">PO No.</th>
          <th class="fix-width-100">Uom</th>
          <th class="fix-width-100">Bin Loc.</th>
          <th class="fix-width-80 text-right">Price</th>
          <th class="fix-width-80 text-right">Disc.</th>
          <th class="fix-width-80 text-right">Qty</th>
          <th class="fix-width-100 text-right">LinstTotal</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
					<?php $totalQty = 0; ?>
          <?php $totalAmount = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-11">
              <td class="middle text-center"><?php echo $rs->LineNum; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?></td>
              <td class="middle"><?php echo $rs->Dscription; ?></td>
              <td class="middle"><?php echo $rs->po_code; ?></td>
              <td class="middle"><?php echo $rs->unitMsr; ?></td>
              <td class="middle"><?php echo $rs->BinCode; ?></td>
              <td class="middle text-right"><?php echo number($rs->PriceAfVAT,2); ?></td>
              <td class="middle text-right"><?php echo number($rs->DiscPrcnt,2); ?> %</td>
              <td class="middle text-right"><?php echo number($rs->Quantity,2); ?></td>
              <td class="middle text-right"><?php echo number($rs->LineTotal,2); ?></td>
            </tr>
						<?php $totalQty += $rs->Quantity; ?>
            <?php $totalAmount += $rs->LineTotal; ?>
          <?php endforeach; ?>
					<tr>
						<td colspan="8" class="text-right">Total</td>
            <td class="text-right"><?php echo number($totalQty, 2); ?></td>
						<td class="text-right"><?php echo number($totalAmount, 2); ?></td>
					</tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
