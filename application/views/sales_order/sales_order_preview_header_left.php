<div class="col-sm-6 col-xs-12 padding-5">
  <table class="table">
    <tr>
      <td class="width-40 bg-green">Customer Code</td>
      <td class="width-60"><?php echo $header->CardCode; ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Customer Name</td>
      <td class="width-60"><?php echo $header->CardName; ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Contact Person</td>
      <td class="width-60"><?php echo $header->contact_person; ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">Customer Ref.</td>
      <td class="width-60"><?php echo $header->NumAtCard; ?></td>
    </tr>

    <tr>
      <td class="width-40 bg-green">Currency</td>
      <td class="width-60"><?php echo $header->DocCur; ?></td>
    </tr>

    <tr>
      <td class="width-40 bg-green">ฝ่าย</td>
      <td class="width-60"><?php echo $header->OcrCode .(!empty($header->department_name) ? ' : '.$header->department_name : ''); ?></td>
    </tr>
    <tr>
      <td class="width-40 bg-green">แผนก</td>
      <td class="width-60"><?php echo $header->OcrCode1 .(!empty($header->division_name) ? ' : '.$header->division_name : ''); ?></td>
    </tr>

    <tr>
      <td class="width-40 bg-green">Ship To :  (สาขาที่ <?php echo $header->ShipToCode; ?>)</td>
      <td class="width-60"><?php echo $header->Address2; ?></td>
    </tr>
  </table>
</div>
