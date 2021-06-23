
<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped" style="width:2400px;">
      <thead>
        <tr class="font-size-12">
          <th class="middle text-center bg-green" style="width:50px;">No.</th>
          <th class="middle text-center bg-green" style="width:50px;">Type</th>
          <th class="middle bg-green" style="width:150px;">Item No.</th>
          <th class="middle bg-green" style="width:200px;">Item Description.</th>
          <th class="middle bg-green" style="width:350px;">Item Detail</th>
          <th class="middle bg-green" style="width:200px;">Freetext</th>
          <th class="middle text-right bg-green" style="width:80px;">Quantity</th>
          <th class="middle text-center bg-green" style="width:60px;">Uom</th>
          <th class="middle text-right bg-green" style="width:150px;">มูลค่า/หน่วย ก่อน VAT</th>
          <th class="middle text-right bg-green" style="width:100px;">ส่วนลด</th>
          <th class="middle text-right bg-green" style="width:100px;">ส่วนลดตาม</th>
          <th class="middle text-center bg-green" style="width:100px;">Tax Code</th>
          <th class="middle text-right bg-green" style="width:150px;">มูลค่ารวม ก่อน VAT</th>
          <th class="middle text-center bg-green" style="width:150px;">Whs</th>
          <th class="middle text-center bg-green" style="width:100px;">In Stock</th>
          <th class="middle text-center bg-green" style="width:100px;">Committed</th>
          <th class="middle text-center bg-green" style="width:100px;">Ordered</th>
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
            <td colspan="15" style="white-space:pre-wrap;"><?php echo $ds->LineText; ?></td>
            <?php else : ?>
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo ($ds->Type == 1 ? 'Text' : '-'); ?></td>
            <td class="middle"><?php echo $ds->ItemCode; ?></td>
            <td class="middle"><?php echo $ds->Dscription; ?></td>
            <td class="middle"><?php echo $ds->ItemDetail; ?></td>
            <td class="middle"><?php echo $ds->FreeText; ?></td>
            <td class="middle text-right"><?php echo number(round($ds->Qty, 2)); ?></td>
            <td class="middle text-center"><?php echo $ds->UomCode; ?></td>
            <td class="middle text-right"><?php echo number($ds->Price, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->U_DISWEB, 2); ?></td>
            <td class="middle text-right"><?php echo number($ds->U_DISCEX, 2); ?></td>
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
              <td colspan="20">&nbsp;</td>
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
