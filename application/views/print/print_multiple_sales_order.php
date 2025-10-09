<?php
$this->load->helper('print');
$page  = '';
$page .= $this->printer->doc_header();
$codes = [];

if( ! empty($data))
{
	foreach($data as $ds)
	{
		// echo "<pre>";
		// print_r($ds);
		// echo "</pre>";
		$this->printer->current_page = 1;
		$codes[] = $ds->doc->code;
		$footer_address = FALSE; //--- แสดงที่อยู่ท้ายแผ่นหรือไม่
		$row_per_page = 22; //--- จำนวนบรรทัด/หน้า
		$total_row 	= 0;
		$row_text = 50;
		$all_row = count($ds->details);

		foreach($ds->details as $rs)
		{
			$model = mb_strlen($rs->Dscription);
			$newline = ceil(substr_count($rs->Dscription, "\n") * 0.5);
			$text_length = $model;
			$u_row = $text_length > $row_text ? ceil($text_length/$row_text) : 1;
			$u_row = $u_row > $newline ? $u_row : $newline;
			$total_row += $u_row;
		}

		$total_row 	= $total_row == 0 ? 1 : ($total_row < $all_row ? $all_row : $total_row);

		$config = array(
			"logo_position" => "middle",
			"title_position" => "center",
			"row" => $row_per_page,
			"total_row" => $total_row,
			"font_size" => 11,
			"total_page" => ceil($total_row/$row_per_page),
			"text_color" => "text-orange" //--- hilight text color class
		);

		$this->printer->config($config);
		$tax_rate = getConfig('SALE_VAT_RATE');
		$logo_path = base_url()."images/company/company_logo.png";

		$thead	= array(
			array("ลำดับที่<br/>No.", "width:10mm; text-align:center; padding:0px; font-family:calibri;"),
			array("รหัสสินค้า<br/>Code", "width:20mm; text-align:center; padding:0px; font-family:calibri;"),
			array("รายละเอียด<br/>Description", "width:60mm; text-align:center;padding:0px; font-family:calibri;"),
			array("Zone", "width:15mm; text-align:center; padding:0px; font-family:calibri; vertical-align:middle;"),
			array("จำนวน<br/>Quantity", "width:18mm; text-align:center; padding:0px; font-family:calibri;"),
			array("ราคา/หน่วย<br/>Unit Price", "width:18mm; text-align:center;padding:0px; font-family:calibri;"),
			array("ส่วนลด<br/>Disc(%)", "width:12mm; text-align:center; padding:0px; font-family:calibri;"),
			array("จำนวนเงิน<br/>Amount", "width:25mm; text-align:center; padding:0px; font-family:calibri;"),
			array("InStock", "width:12mm; text-align:center; padding:0px; font-family:calibri; vertical-align:middle;")
		);
		$this->printer->add_subheader($thead);

		$pattern = array(
			"text-align:center; padding:3px; min-height:5mm; font-family:calibri;", //-- ลำดับ
			"text-align:center; padding:3px; min-height:5mm; font-family:calibri;",  //--- Item code
			"text-align:left; padding:3px; min-height:5mm; white-space:pre-wrap; font-family:calibri;", //--- Model
			"text-align:center; padding:3px; min-height:5mm; font-family:calibri;", //--- Zone
			"text-align:center; padding:3px; min-height:5mm; font-family:calibri;", //--- จำนวน
			"text-align:right; padding:3px; min-height:5mm; font-family:calibri;", //---- หน่วยละ
			"text-align:center; padding:3px; min-height:5mm; font-family:calibri;", //--- ส่วนลด
			"text-align:right; padding:3px; min-height:5mm; font-family:calibri;", //--- จำนวนเงิน
			"text-align:center; padding:3px; min-height:5mm; font-family:calibri;" //--- InStock
		);
		$this->printer->set_pattern($pattern);

		$footer = "<div style='width:190mm; height:30mm; margin:auto; border:none;'>";
		$footer .="<table style='width:100%;'>";
		$footer .= "<tr><td style='width:33%; border: solid 1px #000; padding:5px;'>";
		$footer .= '<table style="width:100%;">
									<tr><td class="text-center" style="font-size:12px;">ผู้จัดทำ</td></tr>
									<tr><td><br/><br/></td></tr>
									<tr>
										<td class="text-center" style="font-size:10px;">.......................................................................</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td style="text-align:center; font-size:12px;">วันที่/Date ........../........../..........</td>
									</tr>
								</table>';
		$footer .="</td>";
		$footer .="<td style='width:34%; border:solid 1px #000; padding:5px; vertical-align:text-top;'>";
		$footer .= '<table style="width:100%;">
									<tr><td class="text-center" style="font-size:12px;">QC</td></tr>
									<tr><td><br/><br/></td></tr>
									<tr>
										<td class="text-center" style="font-size:10px;">.......................................................................</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
								</table>';
		$footer .="</td>";
		$footer .="<td style='width:33%; border:solid 1px #000; padding:5px; vertical-align:text-top;'>";
		$footer .= '<table style="width:100%;">
									<tr><td class="text-center" style="font-size:12px;">จำนวนชิ้น</td></tr>
									<tr><td><br/><br/></td></tr>
									<tr>
										<td class="text-center" style="font-size:10px;">.......................................................................</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
								</table>';
		$footer .="</td>";
		$footer .= "</tr></table>";
		$footer .= "<div style='width:100%; height:5mm; text-align:right; float:left; padding-top:10px; padding-left:10px; padding-right:10px; font-size:10px;'>";
		$footer .= date('d/m/Y').' &nbsp; &nbsp;'.date('H:i:s');
		$footer .="</div>";
		$footer .="</div>";

		$this->printer->footer = $footer;

		$total_page  = $this->printer->total_page == 0 ? 1 : $this->printer->total_page;
		$total_price = 0;
		$total_amount = 0;  //--- มูลค่ารวม(หลังหักส่วนลด)
		$total_discount = 0;
		$total_vat = 0;

		$n = 1;
		$index = 0;
		while($total_page > 0 )
		{
			$top = "";
			$top .= "<div style='width:190mm; margin:auto;'>";
			$top .= "<div class='text-left' style='padding-top:20px; padding-bottom:0px;'>";
			$top .= "<table class='width-100'>
								<tr>
									<td rowspan='4' style='width:25%;'>
										<img src='{$logo_path}' class='company-logo' width='170px' />
									</td>
									<td><strong>บริษัท สหออฟฟิศ จำกัด (สำนักงานใหญ่)</strong> <span class='pull-right' style='margin-right:70px;'>SAHA OFFICE CO.,LTD.</td>
								</tr>
								<tr><td style='font-family:calibri; font-size:12px;'>186 ม.21 ซ.เพชรงาม ถ.พุทธรักษา-แพรกษา ต.บางพลีใหญ่ อ.บางพลี จ.สมุทรปราการ 10540</td></tr>
								<tr><td style='font-family:calibri; font-size:11px;'>186 M.21 SOI PETCH NGAM, PUTHARAKSA-PRAEKSA RD., T.BANGPHLI YAI, A.BANGPHLI, SAMUTPRAKARN 10540</td></tr>
								<tr><td style='font-family:calibri; font-size:12px;'>Tel: 02-115-6888 Fax: 02-101-4948 เลขประจำตัวผู้เสียภาษีอากร 0105535080569</td></tr>
								</table>";
			$top .= "";
			$top .= "</div>";
			$top .= "<div class='text-center font-size-14'>";
			$top .= "<span class='bold'>ใบสั่งขาย (Sales Order)</span>";
			$top .= "<span class='font-size-11' style='position:absolute; top:20px; right:20px;'> Page {$this->printer->current_page} of {$this->printer->total_page} </span>";
			$top .= "<span class='pull-right font-size-11' style='position:absolute; right:20px;'> วันที่กำหนดส่ง : ".thai_date($ds->doc->DocDueDate, FALSE, '/')."</span>";
			$top .= "</div>";
			$top .= "</div>";

			$top .= "<div style='width:190mm; height:140px; position:relative; margin:auto; border-top:solid 2px #333; padding-top:5px; border-radius:0px;'>";

			$top .= 	"<div style='width:65%; float:left; padding-left:10px; padding-right:15px;'>";
			$top .= 		"<table style='border:none;'>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .= 				"<td style='width:50px; vertical-align:text-top;'>ชื่อลูกค้า</td>";
			$top .=					"<td style='white-space:pre-wrap; vertical-align:text-top;'>:{$ds->doc->CardCode} &nbsp; {$ds->doc->CardName} <span style='display:inline-block;'>(สาขา {$ds->doc->PayToCode})</span></td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .= 				"<td style='vertical-align:text-top;'>ที่อยู่ </td>";
			$top .=					"<td style='white-space:pre-wrap; vertical-align:text-top; padding-top:10px;'>:{$ds->doc->Address} &nbsp;&nbsp; ";
			$top .= 					(empty($ds->customer) ? "" : (empty($ds->customer->LicTradNum) ? "" : "<span style='display:inline-block;'>TAX ID : ".$ds->customer->LicTradNum."</span>"));
			$top .= 				"</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .= 				"<td style='vertical-align:text-top; padding-top:10px;'>โทรศัพท์ </td>";
			$top .= 				"<td>";
			$top .= 					"<div class='width-60' style='float:left; padding-top:10px;'>: &nbsp; ";
			$top .= 						(empty($ds->customer) ? "-" : phone_display($ds->customer->Phone1, $ds->customer->Phone2, $ds->customer->Cellular));
			$top .= 					"</div>";
			$top .= 					"<div class='width-40' style='float:left; padding-top:10px;'>แฟ็กซ์ &nbsp;: &nbsp; ";
			$top .= 						(empty($ds->customer) ? "-" : $ds->customer->Fax);
			$top .= 					"</div>";
			$top .= 				"</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .= 				"<td style='vertical-align:text-top; padding-top:10px;'>ผู้ติดต่อ </td>";
			$top .= 				"<td>";
			$top .= 					"<div class='width-60' style='float:left; padding-top:10px;'>: &nbsp; ";
			$top .= 						(empty($ds->shipTo)) ? "-" : $ds->shipTo->U_Contract;
			$top .= 					"</div>";
			$top .= 					"<div class='width-40' style='float:left; padding-top:10px;'>โทร &nbsp;: &nbsp; ";
			$top .= 						(empty($ds->shipTo) ? "-" : $ds->shipTo->U_Tel);
			$top .= 					"</div>";
			$top .= 				"</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .= 				"<td style='vertical-align:text-top;'>หมายเหตุ </td>";
			$top .= 				"<td style='white-space:pre-wrap; vertical-align:text-top; padding-top:10px;'>: &nbsp;{$ds->doc->Address2} : {$ds->doc->Comments}</td>";
			$top .= 			"</tr>";
			$top .= 		"</table>";
			$top .= 	"</div>";

			$top .= 	"<div style='width:35%; float:left; padding-left:10px; padding-right:10px;'>";
			$top .= 		"<table style='table-layout:fixed; width:100%; border:none;'>";
			$top .= 			"<tbody style='line-height:20px;'>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .=					"<td style='width:40%; white-space:normal;'>เลขที่ใบสั่งขาย</td>";
			$top .=					"<td style='width:60%; white-space:normal;'>: ".$ds->doc->BeginStr."-".$ds->doc->DocNum."</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .=					"<td style='white-space:normal;'>วันที่</td>";
			$top .=					"<td style='white-space:normal;'>: ".thai_date($ds->doc->DocDate, FALSE, '/')."</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .=					"<td style='white-space:normal;'>เลขที่อ้างอิง</td>";
			$top .=					"<td style='white-space:normal;'>: {$ds->doc->reference}</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .=					"<td style='white-space:normal;'>การชำระเงิน </td>";
			$top .=					"<td style=' white-space:normal;'>: {$ds->doc->Term}</td>";
			$top .= 			"</tr>";
			$top .= 			"<tr style='font-size:11px;'>";
			$top .=					"<td style='vertical-align:text-top;'>พนักงานขาย </td>";
			$top .=					"<td style='white-space:nowrap; overflow:hidden;'>: ";
			$top .= 					(empty($ds->sale) ? "" : $ds->sale->SlpName);
			$top .= 					(empty($ds->sale) ? "" : (empty($ds->sale->Telephone) ? "" : "<br/> &nbsp; ".$ds->sale->Telephone));
			$top .= 					(empty($ds->sale) ? "" : (empty($ds->sale->Mobil) ? "" : "<br/> &nbsp; ".$ds->sale->Mobil));
			$top .= 				"</td>";
			$top .= 			"</tr>";

			if(!empty($ds->sale->Email))
			{
				$top .= "<tr style='font-size:11px;'><td colspan='2' align='right'>{$ds->sale->Email}</td></tr>";
			}

			$top .= 			"<tr style='font-size:11px;'>";
			$top .=					"<td style='vertical-align:text-top;'>ผู้เปิด </td>";
			$top .=					"<td style='white-space:nowrap; overflow:hidden;'>: ";
			$top .= 					$ds->doc->OwnerName;
			$top .= 				"</td>";
			$top .= 			"</tr>";

			$top .=				"</tbody>";
			$top .= 		"</table>";
			$top .= 	"</div>";
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
		    $rs = isset($ds->details[$index]) ? $ds->details[$index] : FALSE;

		    if( ! empty($rs) )
		    {

					$model = mb_strlen($rs->Dscription);
					// $spec  = mb_strlen($rs->ItemDetail);
					$newline = ceil(substr_count($rs->Dscription, "\n") * 0.5);
					$text_length = $model;
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

						$data = array($noo,"", $rs->LineText, "", "", "", "");

					}
					else
					{
						$data = array(
			        $n,
			        $rs->ItemCode,
							$rs->Dscription,
							$rs->zone_code,
							round($rs->Qty,2)." ".$rs->UomName,
			        ($ds->show_discount === TRUE ? number($rs->Price,2) : number($rs->SellPrice, 2)),
							($ds->show_discount === TRUE ? ($rs->DiscPrcnt > 0 ? number(round($rs->DiscPrcnt,2), 2): '0.00') : '0.00'),
			        number($rs->LineTotal, 2),
							round($rs->InStock, 2)
			      );

						$row_price = ($rs->Price * $rs->Qty);
						$total_price += $row_price;
						$total_discount += $row_price - $rs->LineTotal;
			      $total_amount   += $rs->LineTotal;
						$total_vat += $rs->LineTotal * ($rs->VatRate * 0.01);
						$n++;
					}
		    }
		    else
		    {
					$data = array("","", "", "", "", "", "", "", "");
		    }

		    $page .= empty($data) ? "" : $this->printer->print_row($data, $last_row);

				$index++;

				//--- check next row
				$nextrow = isset($ds->details[$index]) ? $ds->details[$index] : FALSE;
				if(!empty($nextrow))
				{
					$model = mb_strlen($nextrow->Dscription);
					$newline = ceil(substr_count($nextrow->Dscription, "\n") * 0.5);
					$text_length = $model;
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

			if($this->printer->current_page == $this->printer->total_page)
		  {
				$amountBfDisc = number($total_price, 2);
				$disAmount = number($total_discount, 2);
				$amountBfVat = number($total_amount,2);
				$vatAmount = number($total_vat, 2);
				$amountAfterVat = $total_amount * (1 + ($tax_rate * 0.01));
				$netAmount = number($amountAfterVat, 2);
				$baht_text = baht_text($amountAfterVat);
				$remark = $ds->doc->Comments;
		  }
		  else
		  {
				$amountBfDisc = "";
				$disAmount = "";
				$amountBfVat = "";
				$vatAmount = "";
				$amountAfterVat = "";
				$netAmount = "";
				$baht_text = "";
				$remark = "";
		  }

			$subTotal = array();
			$page .= "<tr>";
			$page .= "<td rowspan='3' colspan='4' style='font-size:11px; vertical-align:top; padding:5px; border-width:1px 1px 0px 1px; border-style:solid; border-color:#000;'>{$baht_text}</td>";
		  $page .= "<td colspan='3' style='border-top:solid 1px #000; font-size:11px; padding:2px;'>รวมเป็นเงิน</td>";
		  $page .= "<td colspan='2' style='font-size:11px; border-width:1px 1px 0px 1px; border-style:solid; border-color:#000; padding:2px;' class='text-right'>{$amountBfDisc}</td>";
			$page .= "</tr>";

			$page .= "<tr>";
		  $page .= "<td colspan='3' style='font-size:11px; padding:2px; border:none;'><u>หัก</u>ส่วนลด</td>";
		  $page .= "<td colspan='2' style='font-size:11px; border-width:0px 1px 0px 1px; border-style:solid; border-color:#000; padding:2px;' class='text-right'>{$disAmount}</td>";
			$page .= "</tr>";

			$page .= "<tr>";
		  $page .= "<td colspan='3' style='font-size:11px; padding:2px; border:none;'>จำนวนเงินหลังหักส่งนลด</td>";
		  $page .= "<td colspan='2' style='font-size:11px; border-width:0px 1px 0px 1px; border-style:solid; border-color:#000; padding:2px;' class='text-right'>{$amountBfVat}</td>";
			$page .= "</tr>";

			$page .= "<tr>";
			$page .= "<td rowspan='2' colspan='4' style='font-size:11px; vertical-align:top; padding:5px; border-width:0px 1px 0px 1px; border-style:solid; border-color:#000;'>";
			$page .= "ราคานี้อาจมีการเปลี่ยนแปลงโดยมิต้องแจ้งให้ทราบล่วงหน้า<br/>(Subjected to change without prior notice)";
			$page .= "</td>";
		  $page .= "<td colspan='3' style='border:none; font-size:11px; padding:2px;'>จำนวนภาษีมูลค่าเพิ่ม &nbsp; 7.00%</td>";
		  $page .= "<td colspan='2' style='font-size:11px; border-width:0px 1px 0px 1px; border-style:solid; border-color:#000; padding:2px;' class='text-right'>{$vatAmount}</td>";
			$page .= "</tr>";

			$page .= "<tr>";
		  $page .= "<td colspan='3' style='font-size:11px; padding:2px; border:none;'>จำนวนเงินรวมทั้งสิ้น</td>";
		  $page .= "<td colspan='2' style='font-size:11px; border-width:0px 1px 0px 1px; border-style:solid; border-color:#000; padding:2px;' class='text-right'>{$netAmount}</td>";
			$page .= "</tr>";

			$page .= $this->printer->table_end();
			$page .= $this->printer->content_end();
			$page .= $this->printer->footer;
			$page .= $this->printer->page_end($footer_address);
			$total_page --;
			$this->printer->current_page++;
		}
	} //-- end foreach data
} //--- empty data

$page .= $this->printer->doc_footer();

echo $page;
 ?>

 <style type="text/css" media="print">
 	@page{
 		margin:0;
 		size:A4 portrait;
 	}
  </style>

	<script>
		window.onafterprint = function() {
			let codes = '<?php echo json_encode($codes); ?>';
			$.ajax({
				url:'<?php echo $this->home; ?>/add_multiple_print_logs',
				type:'POST',
				cache:false,
				data:{
					'codes' : codes
				},
				success:function(rs) {
					console.log(rs);
				},
				error:function(rs) {
					console.log(rs);
				}
			})
		};
	</script>
