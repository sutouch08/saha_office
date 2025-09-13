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
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>U_WEBORDER</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->U_WEBORDER; ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
		<label>DocDate</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->DocDate; ?>" readonly />
	</div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
		<label>DocDueDate</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->DocDueDate; ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
		<label>TaxDate</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->TaxDate; ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2-harf col-xs-4 padding-5">
		<label>CardCode</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->CardCode; ?>" readonly/>
	</div>
	<div class="col-lg-5 col-md-4-harf col-sm-7 col-xs-8 padding-5">
		<label>CardName</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->CardName; ?>" readonly />
	</div>
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>NumAtCard</label>
    <input type="text" class="width-100 text-center r" value="<?php echo $doc->NumAtCard; ?>" placeholder="invoice code" readonly/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>PayToCode</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->PayToCode; ?>" readonly />
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ShipToCode</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->ShipToCode; ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>Currency</label>
    <input type="text" class="width-100 text-center r" value="<?php echo $doc->DocCur; ?>" readonly/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->DocRate; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>DiscPrcnt</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->DiscPrcnt; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>DiscSum</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->DiscSum; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>DocTotal</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->DocTotal; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>VatSum</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->VatSum; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
		<label>Series</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->Series; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>SlpCode</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->SlpCode; ?>"  readonly/>
	</div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label>OwnerCode</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->OwnerCode; ?>"  readonly/>
	</div>
	<div class="col-lg-11 col-md-10 col-sm-9 col-xs-12 padding-5">
		<label>Comments</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->Comments; ?>" readonly/>
	</div>
  <div class="divider"></div>
</div>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:2240px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">LineNum</th>
          <th class="fix-width-100">ItemCode</th>
          <th class="fix-width-200">Description</th>
          <th class="fix-width-100">UomCode</th>
          <th class="fix-width-100 text-right">Qty</th>
          <th class="fix-width-100 text-right">PriceBefDi</th>
          <th class="fix-width-100 text-right">Price</th>
          <th class="fix-width-100 text-right">DiscPrcnt</th>
          <th class="fix-width-100 text-right">PriceAfVAT</th>
          <th class="fix-width-100 text-right">LineTotal</th>
          <th class="fix-width-100 text-right">VatSum</th>
          <th class="fix-width-100 text-right">GTotal</th>
          <th class="fix-width-100 text-center">Currency</th>
          <th class="fix-width-100 text-center">Rate</th>
          <th class="fix-width-100 text-center">VatGroup</th>
          <th class="fix-width-100 text-center">VatPrcnt</th>
          <th class="fix-width-100 text-center">WhsCode</th>
          <th class="fix-width-100 text-center">SlpCode</th>
          <th class="fix-width-200">Text</th>
          <th class="min-width-200">FreeTxt</th>
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
              <td class="middle"><?php echo $rs->UomCode; ?></td>
              <td class="middle text-right"><?php echo $rs->Quantity; ?></td>
              <td class="middle text-right"><?php echo $rs->PriceBefDi; ?></td>
              <td class="middle text-right"><?php echo $rs->Price; ?></td>
              <td class="middle text-right"><?php echo $rs->DiscPrcnt; ?></td>
              <td class="middle text-right"><?php echo $rs->PriceAfVAT; ?></td>
              <td class="middle text-right"><?php echo $rs->LineTotal; ?></td>
              <td class="middle text-right"><?php echo $rs->VatSum; ?></td>
              <td class="middle text-right"><?php echo $rs->GTotal; ?></td>
              <td class="middle text-center"><?php echo $rs->Currency; ?></td>
              <td class="middle text-center"><?php echo $rs->Rate; ?></td>
              <td class="middle text-center"><?php echo $rs->VatGroup; ?></td>
              <td class="middle text-center"><?php echo $rs->VatPrcnt; ?></td>
              <td class="middle text-center"><?php echo $rs->WhsCode; ?></td>
              <td class="middle text-center"><?php echo $rs->SlpCode; ?></td>
              <td class="middle"><?php echo $rs->Text; ?></td>
              <td class="middle"><?php echo $rs->FreeTxt; ?></td>
            </tr>
						<?php $totalQty += $rs->Quantity; ?>
            <?php $totalAmount += $rs->LineTotal; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
