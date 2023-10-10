<?php
$this->load->helper('print');
$footer_address = FALSE; //--- แสดงที่อยู่ท้ายแผ่นหรือไม่
$row_per_page = 31; //--- จำนวนบรรทัด/หน้า
$total_row 	= 0;
$row_text = 44;
$all_row = count($details);

foreach($details as $rs)
{
	$Adr_length = mb_strlen($rs->Address);
	$name_length = mb_strlen($rs->CardName);
	$remark_length = empty($rs->remark) ? 0 : mb_strlen($rs->remark) * 2;
	//--- หาความยาวสูงสุดของ text
	$text_length = $Adr_length > $name_length ? $Adr_length : $name_length;
	$text_length = $remark_length > $text_length ? $remark_length : $text_length;
	$u_row = $text_length > $row_text ? ceil($text_length/$row_text) : 1;
	$total_row += $u_row;

	$rs->text_length = $text_length;
	$rs->use_row = $u_row;
	$rs->contact = ( ! empty($rs->contact) ? ( ! empty($rs->Phone) ? $rs->contact.'<br/>'.$rs->Phone : $rs->contact) :( ! empty($rs->Phone) ? $rs->Phone : ''));
}


$total_row 	= $total_row == 0 ? 1 : ($total_row < $all_row ? $all_row : $total_row);


$config = array(
	"logo_position" => "middle",
	"title_position" => "center",
	"page_width" => 210,
	"content_width" => 200,
	"row" => $row_per_page,
	"total_row" => $total_row,
	"font_size" => 11,
	"total_page" => ceil($total_row/$row_per_page),
	"text_color" => "text-orange" //--- hilight text color class
);

$this->printer->config($config);

$page  = '';
$page .= $this->printer->doc_header();

$logo_path = base_url()."images/company/company_logo.png";


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("No.", "width:5mm; text-align:center; padding:0px; font-family:calibri;"),
          array("ลูกค้า", "width:40mm; text-align:center; padding:0px; font-family:calibri;"),
					array("สถานที่จัดส่ง", "width:40mm; text-align:center;padding:0px; font-family:calibri;"),
          array("ผู้ติดต่อ-เบอร์โทร", "width:25mm; text-align:center; padding:0px; font-family:calibri;"),
					array("ประเภท", "width:12mm; text-align:center;padding:0px; font-family:calibri;"),
					array("เอกสาร", "width:20mm; text-align:center; padding:0px; font-family:calibri;"),
          array("ยอดเงิน", "width:15mm; text-align:center; padding:0px; font-family:calibri;"),
					array("เข้า", "width:10mm; text-align:center; padding:0px; font-family:calibri;"),
					array("ออก", "width:10mm; text-align:center; padding:0px; font-family:calibri;"),
					array("หมายเหตุ", "width:20mm; text-align:center; padding:0px; font-family:calibri;")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center; padding:1px; min-height:5mm; font-family:calibri; border-bottom:solid 1px #333;",
            "text-align:left; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
            "text-align:left; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
            "text-align:left; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
						"text-align:center; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
            "text-align:center; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
						"text-align:right; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
						"text-align:left; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
						"text-align:left; padding:1px; min-height:5mm; white-space:pre-wrap; font-family:calibri; border-bottom:solid 1px #333;",
						"text-align:left; padding:1px; min-height:5mm; overflow-x:hidden; font-family:calibri; border-bottom:solid 1px #333;"
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//

$footer = "<div style='width:200mm; height:30mm; margin:auto; border:none;'>";
//---- first box

$footer .="<div style='width:100%; height30mm;'>";
$footer .= '<table class="table" style="width:100%; margin-top:10px;">
							<tr>
								<td class="width-20 middle text-center" style="padding:1px; border:solid 1px #333; font-size:11px;">เจ้าหน้าที่จัดสายขนส่ง</td>
								<td class="width-20 middle text-center" style="padding:1px; border:solid 1px #333; font-size:11px;">เจ้าหน้าที่ลงข้อมูลจัดสาย</td>
								<td class="width-20 middle text-center" style="padding:1px; border:solid 1px #333; font-size:11px;">เจ้าหน้าที่ขนส่ง<br/>ตรวจเช็คสินค้าก่อนส่ง(ขาออก)</td>
								<td class="width-20 middle text-center" style="padding:1px; border:solid 1px #333; font-size:11px;">เจ้าหน้าที่ส่งสินค้า<br/>ตรวจเช็คสินค้าก่อนส่ง</td>
								<td class="width-20 middle text-center" style="padding:1px; border:solid 1px #333; font-size:11px;">เจ้าหน้าที่ตรวจเช็คงานจัดส่ง<br/>ขาเข้า</td>
								</tr>
							<tr>
								<td style="border:solid 1px #333; font-size:11px;"><div style="height:15mm; border-bottom:solid 1px #111;">&nbsp;</div></td>
								<td style="border:solid 1px #333; font-size:11px;"><div style="height:15mm; border-bottom:solid 1px #111;">&nbsp;</div></td>
								<td style="border:solid 1px #333; font-size:11px;"><div style="height:15mm; border-bottom:solid 1px #111;">&nbsp;</div></td>
								<td style="border:solid 1px #333; font-size:11px;"><div style="height:15mm; border-bottom:solid 1px #111;">&nbsp;</div></td>
								<td style="border:solid 1px #333; font-size:11px;"><div style="height:15mm; border-bottom:solid 1px #111;">&nbsp;</div></td>
							</tr>
							<tr>
								<td class="middle text-center" style="border:solid 1px #333; font-size:11px;">( แผนกจัดสาย / ขนส่ง )</td>
								<td class="middle text-center" style="border:solid 1px #333; font-size:11px;">( ธุรการ เอกสารขนส่ง)</td>
								<td class="middle text-center" style="border:solid 1px #333; font-size:11px;">( แผนกจัดสาย / ขนส่ง )</td>
								<td class="middle text-center" style="border:solid 1px #333; font-size:11px;">( เจ้าหน้าที่ส่งสินค้า )</td>
								<td class="middle text-center" style="border:solid 1px #333; font-size:11px;">( ธุรการ เอกสารขนส่ง )</td>
								</tr>
							<tr>
						</table>';
$footer .= "<div style='width:100%; height:20mm; font-size:10px;'>";
$footer .=   "<p style='margin-bottom:2px;'>*หมายเหตุ* (ปรับจุดละ 100 บาท)</p>";
$footer .=   "<p style='padding-left:15px; margin-bottom:2px;'>1.ผู้ที่รับงานไปแล้วจะต้องตรวจสอบความเรียบร้อยก่อนจัดส่งและกรณีรับสินค้าคืนให้ถูกต้อง.</p>";
$footer .=   "<p style='padding-left:15px;'>2.ต้องลงเวลาขาเข้าและขาออก.</p>";
$footer .=   "<p class='text-right' style='padding-left:15px;'>พิมพ์วันที่ &nbsp;&nbsp; ".date('d/m/Y').' &nbsp; &nbsp;'.date('H:i:s')."</p>";
$footer .= "</div>";
$footer .= "</div>";
$footer .= "</div>";


$this->printer->footer = $footer;

$total_page  = $this->printer->total_page == 0 ? 1 : $this->printer->total_page;
$total_amount_product = 0;
$total_amount_doc = 0;

$n = 1;
$index = 0;
while($total_page > 0 )
{
	$top = "";
	$top .= "<div style='width:200mm; margin:auto;'>";
	$top .= "<div class='text-left' style='padding-top:10px; padding-bottom:10px;'>";
	$top .= "<table class='width-100'>
						<tr>
							<td style='width:33.33%;'>&nbsp;</td>
							<td style='width:33.33%; text-align:center;'><strong>ใบคุมการจัดส่ง(พนังานส่งสินค้า)</strong></td>
							<td style='font-size:11px; text-align:right;'>Page {$this->printer->current_page}/{$this->printer->total_page}</td>
						</tr>
						<tr>
							<td style='font-size:12px;'>เลขที่เอกสาร : {$doc->code} <br/>พนักงานขับรถ : {$doc->driver_name}</td>
							<td style='font-size:12px; vertical-align:bottom;'>พนักงานติดรถ : {$empName}</td>
							<td style='font-size:12px; text-align:right; vertical-align:bottom;'>วันที่ : ".thai_date($doc->DocDate, FALSE, '/')."</td>
						</tr>
						</table>";
	$top .= "";
	$top .= "</div>";
	$top .= "<div class='text-center font-size-14'>";
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
			$text_length = $rs->text_length;
			$use_row = $rs->use_row;

			if($use_row > 1)
			{
				//--- คำนวนบรรทัดที่ต้องใช้ต่อ 1 รายการ
				$use_row -= 1;
				$i += $use_row;
			}

			$data = array(
				$n,
				$rs->CardName,
				$rs->Address,
				$rs->contact,
				$rs->type == 'P' ? 'สินค้า' : ($rs->type == 'D' ? 'เอกสาร' : ($rs->type == 'R' ? 'รับเช็ค' : 'อื่นๆ')),
				$rs->DocType.'-'.$rs->DocNum,
				number($rs->DocTotal, 2),
				"",
				"",
				$rs->remark
			);

			$total_amount_product += ($rs->type == 'P' ? $rs->DocTotal : 0);
			$total_amount_doc += ($rs->type == 'D' ? $rs->DocTotal : 0);
			$n++;
    }
    else
    {
			$data = array("","", "", "", "", "", "", "", "", "");
    }

    $page .= empty($data) ? "" : $this->printer->print_row($data, $last_row);

		$index++;

		//--- check next row
		$nextrow = isset($details[$index]) ? $details[$index] : FALSE;

		if(!empty($nextrow))
		{
			$use_row = $nextrow->use_row;
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


  $subTotal = array();

  //--- ราคารวม
	$page .= "<tr>";
	$page .= "<td colspan='10' style='font-size:14px; text-align:center'>";
	$page .= "ยอดส่งสินค้า &nbsp;&nbsp;&nbsp; ".number($total_amount_product, 2)." &nbsp;&nbsp;&nbsp;";
	$page .= "ยอดส่งเอกสาร &nbsp;&nbsp;&nbsp; ".number($total_amount_doc, 2)."</td>";
	$page .= "</tr>";
	$page .= $this->printer->table_end();
  $page .= $this->printer->content_end();
  $page .= $this->printer->footer;

	$page .= $this->printer->page_end($footer_address);

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
