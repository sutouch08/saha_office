<?php
$this->load->helper('print');
$pageWidth = getConfig('PACK_LABEL_WIDTH', 80);
$pageHeight = getConfig('PACK_LABEL_HEIGHT', 80);
$contentWidth = getConfig('PACK_LABEL_CONTENT_WIDTH', 75);
$currentPage = 1;
$totalPage = count($boxes);
?>
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<link rel="icon" href="<?php echo base_url(); ?>assets/images/icons/favicon.ico" type="image/x-icon" />
  	<title><?php echo $this->title; ?></title>
  	<link href="<?php echo base_url(); ?>assets/fonts/fontawesome-5/css/all.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/template.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/print.css" rel="stylesheet" />
  	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
  	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
  	<style>
                  .page_layout{
                    border: solid 3px #aaa;
                    border-radius:0px;
                  }

                  .content-table > tbody > tr {
                    height:5mm;
                  }

                  .content-table > tbody > tr:last-child {
                    height: auto;
                  }

                  .table > tbody > tr > td {
                    border: solid 1px #000;
                  }

                  @media print{
                    .page_layout{ border: none; }
                  }
                </style>
  	</head>
  	<body>

      <div style="width:100%">
        <?php if(!empty($boxes)) : ?>
          <?php foreach($boxes as $box) : ?>
        <?php $pageBreak = ($currentPage == $totalPage) ? "" : "page-break-after:always;"; ?>
        <!-- Page Start -->
    		<div class="page_layout" style="position:relative; width: <?php echo $pageWidth; ?>mm; padding-top:1mm; height:<?php echo $pageHeight; ?>mm; margin:auto; margin-bottom:10px; <?php echo $pageBreak; ?>">
          <div style="width:<?php echo $contentWidth; ?>mm; margin:auto; padding-bottom:10px;">
            <table class="table" style="margin-bottom:0px;">
              <tr>
                <td colspan="2" class="text-center" style="font-size:45px; font-weight:bold;">
                  <?php echo barcodeImage($order->BeginStr.'-'.$order->DocNum, 15, ($contentWidth-10), 0); ?><br/>
                  <?php echo $order->BeginStr.'-'.$order->DocNum; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="font-size:24px; font-weight:bold;">
                  <span style="font-size:10px; display:block;"><?php echo $order->CardCode; ?></span>
                  <?php echo $order->CardName; ?>
                </td>
              </tr>
              <tr>
                <td style="width:70%; font-size:24px; font-weight:bold; white-space: nowrap; overflow:hidden;">
                  <span style="font-size:10px; display:block;">Cust Ref.</span>
                  <input class="width-100" style="border:0px;" value="<?php echo $order->NumAtCard; ?>" readonly>
                </td>
                <td style="width:30; text-align:center; vertical-align:middle; font-size:14px; font-weight:bold;">
                  <?php echo $doc->pickCode; ?>
                </td>
              </tr>
              <tr>
                <td rowspan="2" style="padding:1px;">
                  <textarea style="width:100%; border:0px; font-size:14px; overflow:hidden;" rows="5" readonly>Ship To.<?php echo trim($order->Address2); ?></textarea>
                </td>
                <td>
                  <span style="font-size:14px;font-weight:bold;"><?php echo $doc->code; ?></span>
                </td>
              </tr>
              <tr>
                <td class="middle text-center" style="padding:0px; font-size:50px; font-weight:bold;">
                  <?php echo $box->box_no; ?>/<?php echo $last_box_no; ?>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <?php $currentPage++; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      </div>
    </body>
  </html>

<script>
  $(document).ready(function () {
    window.print();
});
</script>
