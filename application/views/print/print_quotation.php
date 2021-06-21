<?php
$this->load->helper('print');
$total_row 	= 0;
$row_text = 45;
$all_row = count($details);

foreach($details as $rs)
{
	$model = mb_strlen($rs->Dscription);
	$spec  = mb_strlen($rs->ItemDetail);
	$newline = ceil(substr_count($rs->Dscription, "\n") * 0.5);
	$text_length = $spec > $model ? $spec : $model;

	$u_row = $text_length > $row_text ? ceil($text_length/$row_text) : 1;
	$u_row = $u_row > $newline ? $u_row : $newline;
	$total_row += $u_row;
}


$total_row 	= $total_row == 0 ? 1 : ($total_row < $all_row ? $all_row : $total_row);


$config = array(
	"logo_position" => "middle",
	"title_position" => "center",
	"row" => 10,
	"total_row" => $total_row,
	"font_size" => 11,
	"total_page" => ceil($total_row/10),
	"text_color" => "text-orange" //--- hilight text color class
);

$this->printer->config($config);

$page  = '';
$page .= $this->printer->doc_header();

$tax_rate = getConfig('SALE_VAT_RATE');

$logo_path = base_url()."images/company/company_logo.png";


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("#", "width:5mm; text-align:center; height:10mm;"),
          array("Item code", "width:30mm; text-align:center; height:10mm;"),
					array("Model", "width:40mm; text-align:center; height:10mm;"),
					array("Description", "width:40mm; text-align:center; height:10mm;"),
          array("จำนวน", "width:15mm; text-align:center; height:10mm;"),
					array("หน่วยละ", "width:20mm; text-align:center; height:10mm;"),
					array("ส่วนลด", "width:15mm; text-align:center; height:10mm;"),
          array("จำนวนเงิน", "width:25mm; text-align:center; font-size:11px; height:10mm;")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center; padding-left:3px; padding-right:3px; min-height:10mm;", //-- ลำดับ
            "text-align:left; padding-left:3px; padding-right:3px; min-height:10mm;",  //--- Item code
            "text-align:left; padding-left:3px; padding-right:3px; min-height:10mm; white-space:pre-wrap;", //--- Model
            "text-align:left; padding-left:3px; padding-right:3px; min-height:10mm; white-space:pre-wrap;", //--- Spec
            "text-align:right; padding-left:3px; padding-right:3px; min-height:10mm;", //--- จำนวน
						"text-align:right; padding-left:3px; padding-right:3px; min-height:10mm;", //---- หน่วยละ
            "text-align:right; padding-left:3px; padding-right:3px; min-height:10mm;", //--- ส่วนลด
						"text-align:right; padding-left:3px; padding-right:3px; min-height:10mm;" //--- จำนวนเงิน
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//

$footer = "<div style='width:190mm; height:30mm; margin:auto; border:solid 1px #333; padding:5px;'>";
//---- first box

$footer .="<div style='width:33%; height30mm; text-align:center; float:left; padding-left:10px; padding-right:10px;'>";
$footer .= '<table style="width:100%; margin-top:30px;">
							<tr>
								<td style="width:10%; vertical-align:bottom; font-size:12px;">ลงชื่อ</td>
								<td style="width:90%; border-bottom:solid 1px #333;">
							</tr>
							<tr>
								<td></td>
								<td style="text-align:center; padding:5px; font-size:12px;">ผู้สั่งซื้อ</td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:center; font-size:12px;">___/___/___</td>
							</tr>
						</table>';
$footer .="</div>";

$footer .="<div style='width:33%; height30mm; text-align:center; float:left; padding-left:10px; padding-right:10px;'>";
$footer .= '<table style="width:100%; margin-top:20px;">
							<tr>
								<td style="width:10%; vertical-align:bottom; font-size:12px;">ลงชื่อ</td>
								<td style="width:90%; border-bottom:solid 1px #333; padding:5px; font-size:12px;">'.(empty($empName) ? '&nbsp;' : $empName).'</td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:center; padding:5px; font-size:12px;">'.(empty($division_name) ? '&nbsp;' : $division_name).'</td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:center; font-size:12px;">___/___/___</td>
							</tr>
						</table>';
$footer .="</div>";

$footer .="<div style='width:33%; height30mm; text-align:center; float:left; padding-left:10px; padding-right:10px;'>";
$footer .= '<table style="width:100%; margin-top:30px;">
							<tr>
								<td style="width:10%; vertical-align:bottom; font-size:12px;">ลงชื่อ</td>
								<td style="width:90%; border-bottom:solid 1px #333; padding:5px;"></td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:center; padding:5px; font-size:12px;">ผู้อนุมัติ</td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:center; font-size:12px;">___/___/___</td>
							</tr>
						</table>';
$footer .="</div>";



$footer .="</div>";





$this->printer->footer = $footer;

$total_page  = $this->printer->total_page == 0 ? 1 : $this->printer->total_page;
$total_amount = 0;  //--- มูลค่ารวม(หลังหักส่วนลด)
$total_vat = 0;

$n = 1;
$index = 0;
while($total_page > 0 )
{
	$top = "";
	$top .= "<div style='width:190mm; margin:auto;'>";
	$top .= "<div class='text-center' style='padding-bottom:10px;'>";
	$top .= "<img src='{$logo_path}' class='company-logo' width='200px' />";
	$top .= "</div>";
	$top .= "<div class='text-center font-size-20'>";
	$top .= "ใบเสนอราคา (Sales Quotation)";
	$top .= "<span class='pull-right font-size-12'> Page {$this->printer->current_page} of {$this->printer->total_page} </span>";
	$top .= "</div>";
	$top .= "</div>";

	$top .= "<div style='width:190mm; height:140px; position:relative; margin:auto; border:solid 1px #333; padding-top:5px; border-radius:5px;'>";

	$top .= 	"<div style='width:60%; float:left; padding-left:10px; padding-right:10px;'>";
	$top .= 		"<table style='width:95%; border:none;'>";
	$top .= 			"<tr style='font-size:12px;'>";
	$top .=					"<td style='white-space:pre-wrap; vertical-align:text-top;'> เรียน &nbsp;:  &nbsp;{$doc->CardCode} &nbsp; {$doc->CardName}</td>";
	$top .= 			"</tr>";
	$top .= 			"<tr style='font-size:12px;'>";
	$top .=					"<td style='white-space:pre-wrap; vertical-align:text-top; padding-top:10px;'> ที่อยู  &nbsp;:  &nbsp;{$doc->Address}</td>";
	$top .= 			"</tr>";
	$top .= 		"</table>";
	$top .= 	"</div>";

	$top .= 	"<div style='width:40%; float:left; padding-left:10px; padding-right:10px;'>";
	$top .= 		"<table style='width:100%; border:none;'>";
	$top .= 			"<tbody style='line-height:25px;'>";
	$top .= 			"<tr style='font-size:12px;'>";
	$top .=					"<td style='width:50%; white-space:normal;'> วันที่ </td>";
	$top .=					"<td style='width:50%; white-space:normal;'>: ".thai_date($doc->DocDate, FALSE, '/')."</td>";
	$top .= 			"</tr>";
	$top .= 			"<tr style='font-size:12px;'>";
	$top .=					"<td style='width:50%; white-space:normal;'> เลขที่ </td>";
	$top .=					"<td style='width:50%; white-space:normal;'>: ".(empty($doc->DocNum) ? $doc->code : $doc->prefix).$doc->DocNum."</td>";
	$top .= 			"</tr>";
	$top .= 			"<tr style='font-size:12px;'>";
	$top .=					"<td style='width:50%; white-space:normal;'> กำหนดยืนราคา </td>";
	$top .=					"<td style='width:50%; white-space:normal;'>: ".thai_date($doc->DocDueDate, FALSE, '/')."</td>";
	$top .= 			"</tr>";
	$top .=				"</tbody>";
	$top .= 		"</table>";
	$top .= 	"</div>";

	$top .= 	"<div style='width:100%; position:absolute; bottom:5px; padding-left:10px; padding-right:10px;'>";
	$top .= 		"<table style='width:100%; border:none;'>";
	$top .= 			"<tr style='font-size:12px;'>";
	$top .=					"<td style='width:40%; white-space:normal;'><strong>โทร:</strong> ".(empty($customer) ? "-" : phone_display($customer->Phone1, $customer->Phone2, $customer->Cellular))." </td>";
	$top .=					"<td style='width:20%; white-space:normal;'><strong>Fax:</strong> ".(empty($customer) ? "-" : $customer->Fax)." </td>";
	$top .=					"<td style='width:40%; white-space:normal; text-align:right;'><strong>Email:</strong> ".(empty($customer) ? "-" : $customer->E_Mail)."</td>";
	$top .= 			"</tr>";
	$top .= 		"</table>";
	$top .= 	"</div>";
	$top .= "</div>";
	$top .= "<div style='width:190mm; margin:auto; line-height:5mm;'>";
	$top .=  "บริษัทฯ มีความยินดีเสนอราคาเพื่อให้ท่านพิจารณาตามรายละเอียดต่อไปนี้ :- ";
	$top .= "</div>";

  $page .= $this->printer->page_start();
  $page .= $top;

  $page .= $this->printer->content_start();
  $page .= $this->printer->table_start();
  $i = 0;
	$row = $this->printer->row;

	$last_row = FALSE;

  while($i < $row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {

			$model = mb_strlen($rs->Dscription);
			$spec  = mb_strlen($rs->ItemDetail);
			$newline = ceil(substr_count($rs->Dscription, "\n") * 0.5);
			$text_length = $spec > $model ? $spec : $model;
			$use_row = ceil($text_length/$row_text);
			$use_row = $use_row > $newline ? $use_row : $newline;
			if($use_row > 1)
			{
				//--- คำนวนบรรทัดที่ต้องใช้ต่อ 1 รายการ
				$use_row -= 1;
				$i += $use_row;
			}

      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
			if($rs->Type == 1)
			{
				$noo = "";
				if($n == 1)
				{
					$noo = $n;
					$n++;
				}

				$data = array($noo,"", $rs->LineText, "", "", "", "", "");

			}
			else
			{
				$data = array(
	        $n,
	        $rs->ItemCode,
					$rs->Dscription,
					$rs->ItemDetail,
					round($rs->Qty,2).' '.$rs->UomCode,
	        ($show_discount === TRUE ? number($rs->Price,2) : number($rs->SellPrice, 2)),
					($show_discount === TRUE ? round($rs->DiscPrcnt,2).' %' : ''),
	        number($rs->LineTotal, 2)
	      );

	      $total_amount   += $rs->LineTotal;
				$total_vat += $rs->LineTotal * ($rs->VatRate * 0.01);
				$n++;
			}
    }
    else
    {
      //$data = array("", "", "", "","", "", "", "");
			$data = array();
    }

    $page .= empty($data) ? "" : $this->printer->print_row($data, $last_row);

		$index++;

		//--- check next row
		$nextrow = isset($details[$index]) ? $details[$index] : FALSE;
		if(!empty($nextrow))
		{
			$model = mb_strlen($nextrow->Dscription);
			$spec  = mb_strlen($nextrow->ItemDetail);
			$newline = ceil(substr_count($nextrow->Dscription, "\n") * 0.5);
			$text_length = $spec > $model ? $spec : $model;
			$use_row = ceil($text_length/$row_text);
			$use_row = $use_row > $newline ? $use_row : $newline;
			$use_row += $i;
			if($row < $use_row)
			{
				$i = $use_row;
				$last_row = TRUE;
			}
			else
			{
				$i++;
			}
		}
		else
		{
			$i++;
		}

		$all_row--;
  }

  $page .= $this->printer->table_end();

  if($this->printer->current_page == $this->printer->total_page)
  {
		$amountBfVat = number($total_amount,2);
		$vatAmount = number($total_vat, 2);
		$amountAfterVat = $total_amount * (1 + ($tax_rate * 0.01));
		$netAmount = number($amountAfterVat, 2);
		$baht_text = baht_text($amountAfterVat);
		$remark = $doc->Comments;
  }
  else
  {
		$amountBfVat = "";
		$vatAmount = "";
		$amountAfterVat = "";
		$netAmount = "";
		$baht_text = "";
		$remark = "";
  }

  $subTotal = array();

  //--- ราคารวม
	$sub_price  = "<td rowspan='3' style='width:50%; vertical-align:top; border:solid 1px #333;'>หมายเหตุ :  {$doc->Comments}</td>";

  $sub_price .= "<td class='' style='width:30%; border:solid 1px #333; font-size:12px; padding:5px;'>รวมเป็นเงิน</td>";
  $sub_price .= "<td style='width:20%; border:solid 1px #333;' class='text-right'>{$amountBfVat}</td>";

  array_push($subTotal, array($sub_price));

  //--- ส่วนลดรวม
  $sub_disc  = "<td class='font-size-12' style='border:solid 1px #333;font-size:12px; padding:5px;'>จำนวนภาษีมูลค่าเพิ่ม {$tax_rate} %</td>";
  $sub_disc .= "<td class='text-right' style='border:solid 1px #333;font-size:12px; padding:5px;'>{$vatAmount}</td>";
  array_push($subTotal, array($sub_disc));


  //--- ยอดสุทธิ
  $sub_net  = "<td class='font-size-12' style='border:solid 1px #333;font-size:12px; padding:5px;'>รวมจำนวนเงินทั้งสิ้น</td>";
  $sub_net .= "<td class='text-right font-size-12' style='border:solid 1px #333; font-size:12px; padding:5px;'>{$netAmount}</td>";
  array_push($subTotal, array($sub_net));

	$amount_text  = "<td style='border:solid 1px #333; font-size:12px; padding:5px;'>เงื่อนไขการชำระเงิน : {$doc->Term}</td>";
	$amount_text .= "<td colspan='2' class='text-center' style='border:solid 1px #333; font-size:12px; padding:5px;'>{$baht_text}</td>";
	array_push($subTotal, array($amount_text));

  $page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->content_end();

	//$page .= $this->printer->print_remark($remark);
  $page .= $this->printer->footer;

  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>

 <style type="text/css" media="print">
 	@page{
 		margin:0;
 		size:A4 portrait;
 	}
  </style>
