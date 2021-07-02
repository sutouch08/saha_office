<div class="col-sm-6 col-xs-12 padding-5 last">
  <table class="table">
    <tr>
      <td class="width-40 bg-green"><?php echo (!empty($header->DocNum) ? "No." : "Series"); ?></td>
      <td class="width-60">
        <?php if(!empty($header->DocNum)) : ?>
        <?php echo $header->BeginStr . $header->DocNum; ?>
        <?php else : ?>
        <?php echo $header->series_name; ?>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Web Order</td>
      <td class="width-60"><?php echo $header->code; ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Payment Term</td>
      <td class="width-60"><?php echo $header->Term; ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Status</td>
      <td class="width-60">
        <?php $status = $header->Approved === 'A' ? 'Approved' :($header->Approved === 'R' ? 'Rejected' : 'Pending'); ?>
        <?php echo $status; ?>
      </td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Posting Date</td>
      <td class="width-60"><?php echo thai_date($header->DocDate, FALSE, '.'); ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Valid Until</td>
      <td class="width-60"><?php echo thai_date($header->DocDueDate, FALSE, '.'); ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Document Date</td>
      <td class="width-60"><?php echo thai_date($header->TextDate, FALSE, '.'); ?></td>
    </tr>
    <?php if($header->is_duplicate) : ?>
    <tr>
      <td class="width-40 bg-green">เลขที่ใบสั่งขายเดิม</td>
      <td class="width-60"><?php echo $header->U_ORIGINALSO; ?></td>
    </tr>
    <?php endif; ?>
    <?php if(!empty($header->U_SQNO)) : ?>
    <tr>
      <td class="width-40 bg-green">Quotation No</td>
      <td class="width-60"><?php echo $header->U_SQNO; ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td class="width-40 bg-green">Bill To</td>
      <td class="width-60"><?php echo $header->Address; ?></td>
    </tr>
  </table>
</div>
