<?php
$this->load->helper('print');
$pageWidth = getConfig('PALLET_LABEL_WIDTH', 80);
$pageHeight = getConfig('PALLET_LABEL_HEIGHT', 80);
$contentWidth = getConfig('PALLET_LABEL_CONTENT_WIDTH', 75);
$fontSize = getConfig('PALLET_LABEL_FONT_SIZE', 24);
$currentPage = 1;
$totalPage = count($pallets);
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
        <?php if(!empty($pallets)) : ?>
          <?php foreach($pallets as $pallet) : ?>
        <?php $pageBreak = ""; //($currentPage == $totalPage) ? "" : "page-break-after:always;"; ?>
        <!-- Page Start -->
    		<div class="page_layout" style="position:relative; width: <?php echo $pageWidth; ?>mm; padding-top:5mm; height:<?php echo $pageHeight; ?>mm; margin:auto; margin-bottom:10px; <?php echo $pageBreak; ?>">
          <div style="width:<?php echo $contentWidth; ?>mm; margin:auto; padding-bottom:10px;">
            <table class="table" style="margin-bottom:0px;">
              <tr>
                <td class="text-center" style="font-size:<?php echo $fontSize; ?>px; border:0px; padding:0px;">
                  <?php echo barcodeImage($pallet->code, 20, getConfig('PALLET_LABEL_CONTENT_WIDTH'), 0); ?><br/>
                  Pallet No : <?php echo $pallet->code; ?>
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
//   $(document).ready(function () {
//     window.print();
// });
</script>
