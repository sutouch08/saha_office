<?php
$this->load->helper('print');
$pageWidth = getConfig('PICK_LABEL_WIDTH', 80);
//$pageHeight = getConfig('PICK_LABEL_HEIGHT', 80);
$contentWidth = getConfig('PICK_LABEL_CONTENT_WIDTH', 75);
$currentPage = 1;
$totalPage = count($orders);
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

                  .table > tbody > tr > td {
                    border:0 !important;
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
        <?php if(!empty($orders)) : ?>
          <?php foreach($orders as $order) : ?>
        <?php $pageBreak = ($currentPage == $totalPage) ? "" : "page-break-after:always;"; ?>
        <!-- Page Start -->
    		<div class="page_layout" style="position:relative; width: <?php echo $pageWidth; ?>mm; padding-top:5mm; margin:auto; margin-bottom:10px; <?php echo $pageBreak; ?>">
          <div style="width:<?php echo $contentWidth; ?>mm; margin:auto; padding-bottom:10px;">
            <table class="table" style="margin-bottom:0px;">
              <tr>
                <td colspan="2" class="text-center" style="font-size:28px; font-weight:bold;">
                  <?php echo barcodeImage($order->DocNum.','.$order->OrderCode, 10, 60, 0); ?><br/>
                  <?php echo $order->prefix.'-'.$order->OrderCode; ?>
                </td>
              </tr>
              <tr>
                <td class="width-50 middle" style="font-size:18px;">
                  <?php echo $order->DocNum; ?>
                </td>
                <td class="width-50 middle text-right" style="font-size:14px;">Date : <?php echo date('d/m/y', strtotime($order->CreateDate)); ?></td>
              </tr>
              <tr>
                <td colspan="2" class="middle text-center" style="font-size:24px;">
                  <?php echo $order->CardName; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="font-size:18px;">
                  Ref: <?php echo $order->NumAtCard; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="font-size:14px;">
                  Remark: <?php echo $order->remark; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <span style="display:block;"><u>ที่อยู่จัดส่ง</u></span>
                  <?php echo $order->shipTo; ?>
                </td>
              </tr>
              <tr>
                <td class="middle" style="font-size:14px;">Item lines : <?php echo $order->ItemRows; ?></td>
                <td class="middle text-right" style="font-size:14px;">
                  เวลาพิมพ์ <?php echo date('d/m/y H:i'); ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" class="text-center" style="font-size:28px; font-weight:bold;">
                  <?php echo barcodeImage($order->DocNum, 10, 60, 0); ?><br/>
                  <?php echo $order->DocNum; ?>
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
