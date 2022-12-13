<style>
  .tableFixHead {
    table-layout: fixed;
    min-width: 100%;
    width:2480px;
    margin-top:-1px;
    margin-left:-6px;
    margin-right:-6px;
    margin-bottom: 0;
    overflow-y: auto;
    height: 50px;
  }

  .tableFixHead thead th {
    position: sticky;
    top: -1px;
    background: #eee;
  }
</style>


<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5 table-responsive" style="border:solid 1px #dddddd; min-height:300px; max-height:321px; overflow:scroll;">
    <table class="table table-striped tableFixHead">
      <thead>
        <tr class="font-size-10">
          <th class="middle text-center" style="width:50px;">#</th>
          <th class="middle text-center" style="width:50px;">Type</th>
          <th class="middle" style="width:200px;">Item Code</th>
          <th class="middle" style="width:250px;">Item Description.</th>
          <th class="middle" style="width:200px;">Item Detail</th>
          <th class="middle" style="width:150px;">รหัสสมบูรณ์</th>
          <th class="middle text-right" style="width:100px;">Quantity</th>
          <th class="middle text-center" style="width:100px;">Uom</th>
          <th class="middle text-right" style="width:100px;">STD Price</th>
          <th class="middle text-right" style="width:100px;">Last Quote Price</th>
          <th class="middle text-right" style="width:100px;">Last Sell Price</th>
          <th class="middle text-right" style="width:100px;">Price</th>
          <th class="middle text-right" style="width:100px;">ส่วนต่างราคา(%)</th>
          <th class="middle text-right" style="width:100px;">ส่วนลด(%)</th>
          <th class="middle text-center" style="width:100px;">Tax Code</th>
          <th class="middle text-right" style="width:150px;">มูลค่ารวม (ก่อน vat)</th>
          <th class="middle text-center" style="width:150px;">Whs</th>
          <th class="middle text-center" style="width:100px;">In Stock</th>
          <th class="middle text-center" style="width:100px;">Commited</th>
          <th class="middle text-center" style="width:100px;">Ordered</th>
        </tr>
      </thead>

      <tbody id="details-template">
        <?php $no = 1; ?>
        <?php $rows = 5; ?>
        <?php if(!empty($details)) : ?>
        <?php   foreach($details as $ds) : ?>
          <tr>
            <?php if($ds->Type == 1) : ?>
            <td class="text-center no"><?php echo $no; ?></td>
            <td class="text-center"><?php echo ($ds->Type == 1 ? 'Text' : '-'); ?></td>
            <td colspan="17" style="white-space:pre-wrap;"><?php echo $ds->LineText; ?></td>
            <?php else : ?>
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo ($ds->Type == 1 ? 'Text' : '-'); ?></td>
            <td class="middle"><?php echo $ds->ItemCode; ?></td>
            <td class="middle"><?php echo $ds->Dscription; ?></td>
            <td class="middle"><?php echo $ds->ItemDetail; ?></td>
            <td class="middle"><?php echo $ds->FreeText; ?></td>
            <td class="middle text-right"><?php echo number(round($ds->Qty, 2)); ?></td>
            <td class="middle text-center"><?php echo $ds->UomName; ?></td>
            <td class="middle text-right"><?php echo number($ds->stdPrice, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->lastQuotePrice, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->lastSellPrice, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->Price, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->priceDiffPercent, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->U_DISWEB, 2); ?></td>
            <td class="middle text-center"><?php echo $ds->VatGroup; ?></td>
            <td class="middle text-right"><?php echo number($ds->LineTotal, 2); ?></td>
            <td class="middle text-center"><?php echo $ds->WhsCode; ?></td>
            <td class="middle text-center"><?php echo number($ds->OnHandQty,2); ?></td>
            <td class="middle text-center"><?php echo number($ds->IsCommited,2); ?></td>
            <td class="middle text-center"><?php echo number($ds->OnOrder, 2); ?></td>
            <?php endif; ?>
          </tr>
          <?php $no++; ?>
          <?php $rows--; ?>
        <?php   endforeach; ?>
        <?php if($rows > 0) : ?>
          <?php while($rows > 0) : ?>
            <tr>
              <td colspan="22">&nbsp;</td>
            </tr>
            <?php $rows--; ?>
          <?php endwhile; ?>
        <?php endif; ?>
        <?php endif; ?>

      </tbody>
      <tfoot>

      </tfoot>
    </table>
  </div>
</div>
<hr class="padding-5"/>
