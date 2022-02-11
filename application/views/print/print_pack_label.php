<?php
$this->load->helper('print');
$pageWidth = getConfig('PRINT_LABEL_WIDTH');
$pageHeight = getConfig('PRINT_LABEL_HEIGHT');
$contentWidth = getConfig('PRINT_LABEL_CONTENT_WIDTH');
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
                    border: solid 1px #aaa;
                    border-radius:0px;
                  }

                  .content-table > tbody > tr {
                    height:5mm;
                  }
                  .content-table > tbody > tr:last-child {
                    height: auto;
                  }

                  @media print{
                    .page_layout{ border: none; }
                  }
                </style>
  	</head>
  	<body>
    	<div class="hidden-print" style="margin-top:10px; padding-bottom:10px; padding-right:5mm; width:200mm; margin-left:auto; margin-right:auto; text-align:right">
    	   <button class="btn btn-primary" onclick="print()"><i class="fa fa-print"></i>&nbspพิมพ์</button>
    	</div>
      <div style="width:100%">
        <?php if(!empty($boxes)) : ?>
          <?php foreach($boxes as $box) : ?>
        <?php $pageBreak = ($currentPage == $totalPage) ? "" : "page-break-after:always;"; ?>
        <!-- Page Start -->
    		<div class="page_layout" style="position:relative; width: <?php echo $pageWidth; ?>mm; padding-top:5mm; height:<?php echo $pageHeight; ?>mm; margin:auto; margin-bottom:10px; <?php echo $pageBreak; ?>">
          <div style="width:<?php echo $contentWidth; ?>mm; margin:auto; padding-bottom:10px;">
            <table class="table table-bordered" style="margin-bottom:0px;">
              <tr>
                <td rowspan="2" class="width-70 text-center" style="font-size:24px;">
                  <?php echo barcodeImage($order->BeginStr.'-'.$order->DocNum, 10, 50, 0); ?>
                  <?php echo $order->BeginStr.'-'.$order->DocNum; ?>
                </td>
                <td class="width-30 text-center">กล่องที่</td>
              </tr>
              <tr>
                <td class="middle text-center" style="font-size:30px; font-weight:bold;">
                  <?php echo $box->box_no; ?>/<?php echo $last_box_no; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                      <span style="font-size:9px; display:block;">Pick No.</span>
                      <span style="font-size:18px;"><?php echo $doc->pickCode; ?></span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-left:solid 1px #ccc;">
                      <span style="font-size:9px; display:block;">Pack No.</span>
                      <span style="font-size:18px;"><?php echo $doc->code; ?></span>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="font-size:18px;">
                  <span style="font-size:9px; display:block;">Customer.</span>
                  <?php echo $order->CardCode.' : '.$order->CardName; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="font-size:18px;">
                  Ref : <?php echo $order->NumAtCard; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <span style="font-size:9px; display:block;">Ship To.</span>
                  <?php echo $order->Address2; ?>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <?php $currentPage++; ?>
        <?php endforeach; ?>
      <?php endif; //-- end if(!empty($boxes))?>
      </div>
    </body>
  </html>

<script>
  $(document).ready(function () {
    window.print();
});
</script>
