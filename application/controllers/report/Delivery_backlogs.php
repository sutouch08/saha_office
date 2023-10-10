<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_backlogs extends PS_Controller
{
	public $menu_code = 'REDEBL';
	public $menu_group_code = 'RE';
	public $title = 'รายงาน งานยังไม่ได้จัดส่ง';
	public $filter;
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/delivery_backlogs';
		$this->load->model('report/delivery_report_model');
  }


	public function index()
	{
		$this->load->view('report/delivery/delivery_backlogs');
	}


	public function get_report()
	{
		$sc = TRUE;
		$data = array();

		$date_type = $this->input->post('dateType');
		$from_date = db_date($this->input->post('fromDate'));
		$to_date = db_date($this->input->post('toDate'));
		$doc_type = $this->input->post('docType');
		$all_cust = $this->input->post('allCust');
		$from_cust_code = $this->input->post('custFrom');
		$to_cust_code = $this->input->post('custTo');

		$no = 1;

		$filter = array(
			'date_type' => $date_type,
			'from_date' => from_date($from_date),
			'to_date' => to_date($to_date),
			'all_cust' => $all_cust,
			'from_cust_code' => $from_cust_code,
			'to_cust_code' => $to_cust_code
		);

		$delivery_state = array(
			'O' => 'Open',
			'R' => 'Released',
			'C' => 'Closed'
		);

		if($doc_type == 'all' OR $doc_type == 'IV')
		{
			$invoice = $this->delivery_report_model->get_iv_data($filter);

			if( ! empty($invoice))
			{
				foreach($invoice as $rs)
				{
					$de = $this->delivery_report_model->get_delivery_doc($rs->DocNum, "IV");
					$required_date = is_null($rs->U_Required_Delivery_Date) ? $rs->DocDate : $rs->U_Required_Delivery_Date;

					$today_date = date_create(date('Y-m-d'));
					$required_delivery_date = date_create($required_date);
					$diff = date_diff($today_date, $required_delivery_date);
					$days = $diff->format('%r%a') * -1;

					$arr = array(
						'no' => $no,
						'DocDate' => thai_date($rs->DocDate),
						'Required_date' => thai_date($required_date),
						'DocType' => 'IV',
						'DocNum' => $rs->DocNum,
						'Delivery_code' => empty($de) ? "ยังไม่ได้จัดสาย" : $de->delivery_code,
						'Delivery_state' => empty($de) ? "" : $delivery_state[$de->line_status],
						'Diff_date' => $days,
						'Urgency_text' => $rs->U_Delivery_Urgency,
						'CardCode' => $rs->CardCode,
						'CardName' => $rs->CardName,
						'DocTotal' => number($rs->DocTotal, 2),
						'Deliver_status' => $this->delivery_state($rs->U_Deliver_status),
						'Deliver_date' => empty($rs->U_Deliver_date) ? NULL : thai_date($rs->U_Deliver_date),
						'ShipTo' => $rs->Address2,
						'ZipCode' => $rs->ZipCode,
						'Route' => $this->get_route_name($rs->ZipCode, $rs->City),
						'Remark' => $rs->Comments,
						'RemarkInt' => $rs->U_Remark_Int,
						'Owner' => $rs->firstName.' '.$rs->lastName,
						'color' => $days > 0 ? 'red' : ''
					);

					array_push($data, $arr);
					$no++;
				}
			}
		}

		if($doc_type == 'all' OR $doc_type == 'DO')
		{
			$invoice = $this->delivery_report_model->get_do_data($filter);

			if( ! empty($invoice))
			{
				foreach($invoice as $rs)
				{
					$de = $this->delivery_report_model->get_delivery_doc($rs->DocNum, "DO");
					$required_date = is_null($rs->U_Required_Delivery_Date) ? $rs->DocDate : $rs->U_Required_Delivery_Date;

					$today_date = date_create(date('Y-m-d'));
					$required_delivery_date = date_create($required_date);
					$diff = date_diff($today_date, $required_delivery_date);
					$days = $diff->format('%r%a') * -1;

					$arr = array(
						'no' => $no,
						'DocDate' => thai_date($rs->DocDate),
						'Required_date' => thai_date($required_date),
						'DocType' => 'DO',
						'DocNum' => $rs->DocNum,
						'Delivery_code' => empty($de) ? "ยังไม่ได้จัดสาย" : $de->delivery_code,
						'Delivery_state' => empty($de) ? "" : $delivery_state[$de->line_status],
						'Diff_date' => $days,
						'Urgency_text' => $rs->U_Delivery_Urgency,
						'CardCode' => $rs->CardCode,
						'CardName' => $rs->CardName,
						'DocTotal' => number($rs->DocTotal, 2),
						'Deliver_status' => $this->delivery_state($rs->U_Deliver_status),
						'Deliver_date' => empty($rs->U_Deliver_date) ? NULL : thai_date($rs->U_Deliver_date),
						'ShipTo' => $rs->Address2,
						'ZipCode' => $rs->ZipCode,
						'Route' => $this->get_route_name($rs->ZipCode, $rs->City),
						'Remark' => $rs->Comments,
						'RemarkInt' => $rs->U_Remark_Int,
						'Owner' => $rs->firstName.' '.$rs->lastName,
						'color' => $days > 0 ? 'red' : ''
					);

					array_push($data, $arr);
					$no++;
				}
			}
		}

		echo json_encode($data);
	}

	public function do_export()
	{
		$date_type = $this->input->post('dateType');
		$from_date = db_date($this->input->post('fromDate'));
		$to_date = db_date($this->input->post('toDate'));
		$doc_type = $this->input->post('docType');
		$all_cust = $this->input->post('allCust');
		$from_cust_code = $this->input->post('custFrom');
		$to_cust_code = $this->input->post('custTo');
		$token = $this->input->post('token');

		$no = 1;

		$filter = array(
			'date_type' => $date_type,
			'from_date' => from_date($from_date),
			'to_date' => to_date($to_date),
			'all_cust' => $all_cust,
			'from_cust_code' => $from_cust_code,
			'to_cust_code' => $to_cust_code
		);

		$delivery_state = array(
			'O' => 'Open',
			'R' => 'Released',
			'C' => 'Closed'
		);

		$this->load->library('excel');

		$sheetName = 'งานยังไม่ได้จัดส่ง';
    $title = "รายงาน งานยังไม่ได้จัดส่ง ณ วันที่ ".date('d-m-Y H:i');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle($sheetName);
		$sheet = $this->excel->getActiveSheet();
		$sheet->getColumnDimension("A")->setAutoSize(true);
		$sheet->getColumnDimension("B")->setAutoSize(true);
		$sheet->getColumnDimension("C")->setAutoSize(true);
		$sheet->getColumnDimension("D")->setAutoSize(true);
		$sheet->getColumnDimension("E")->setAutoSize(true);
		$sheet->getColumnDimension("F")->setAutoSize(true);
		$sheet->getColumnDimension("G")->setAutoSize(true);
		$sheet->getColumnDimension("H")->setAutoSize(true);
		$sheet->getColumnDimension("I")->setAutoSize(true);
		$sheet->getColumnDimension("J")->setAutoSize(true);
		$sheet->getColumnDimension("K")->setAutoSize(true);
		$sheet->getColumnDimension("L")->setAutoSize(true);
		$sheet->getColumnDimension("M")->setAutoSize(true);
		$sheet->getColumnDimension("N")->setAutoSize(true);
		$sheet->getColumnDimension("O")->setAutoSize(true);
		$sheet->getColumnDimension("P")->setAutoSize(true);
		$sheet->getColumnDimension("Q")->setAutoSize(true);
		$sheet->getColumnDimension("R")->setAutoSize(true);
		$sheet->getColumnDimension("S")->setAutoSize(true);

		$row = 1;

		$sheet->setCellValue("A{$row}", $title);
		$sheet->mergeCells("A{$row}:S{$row}");
		$row++;

		$sheet->setCellValue("A{$row}", "วันที่เอกสาร");
		$sheet->setCellValue("B{$row}", "วันที่นัดจัดส่ง");
		$sheet->setCellValue("C{$row}", "ประเภทเอกสาร");
		$sheet->setCellValue("D{$row}", "เลขที่เอกสาร");
		$sheet->setCellValue("E{$row}", "ใบจัดสาย");
		$sheet->setCellValue("F{$row}", "สถานะใบจัดสาย");
		$sheet->setCellValue("G{$row}", "จำนวนวันค้างส่ง");
		$sheet->setCellValue("H{$row}", "ความเร่งด่วน");
		$sheet->setCellValue("I{$row}", "รหัสลูกค้า");
		$sheet->setCellValue("J{$row}", "ชื่อลูกค้า");
		$sheet->setCellValue("K{$row}", "มูลค่าบิล");
		$sheet->setCellValue("L{$row}", "สถานะการจัดส่ง");
		$sheet->setCellValue("M{$row}", "วันที่สถานะ");
		$sheet->setCellValue("N{$row}", "Ship To");
		$sheet->setCellValue("O{$row}", "Zip Code");
		$sheet->setCellValue("P{$row}", "เส้นทางการจัดส่งแนะนำ");
		$sheet->setCellValue("Q{$row}", "Remark");
		$sheet->setCellValue("R{$row}", "Remark Internal");
		$sheet->setCellValue("S{$row}", "ชื่อผู้ขาย");
		$row++;

		if($doc_type == 'all' OR $doc_type == 'IV')
		{
			$invoice = $this->delivery_report_model->get_iv_data($filter);

			if( ! empty($invoice))
			{
				foreach($invoice as $rs)
				{
					$de = $this->delivery_report_model->get_delivery_doc($rs->DocNum, "IV");
					$required_date = is_null($rs->U_Required_Delivery_Date) ? $rs->DocDate : $rs->U_Required_Delivery_Date;

					$today_date = date_create(date('Y-m-d'));
					$required_delivery_date = date_create($required_date);
					$diff = date_diff($today_date, $required_delivery_date);
					$days = $diff->format('%r%a') * -1;

					$sheet->setCellValue("A{$row}", thai_date($rs->DocDate));
					$sheet->setCellValue("B{$row}", thai_date($required_date));
					$sheet->setCellValue("C{$row}", "IV");
					$sheet->setCellValue("D{$row}", $rs->DocNum);
					$sheet->setCellValue("E{$row}", (empty($de) ? "ยังไม่ได้จัดสาย" : $de->delivery_code));
					$sheet->setCellValue("F{$row}", (empty($de) ? "" : $delivery_state[$de->line_status]));
					$sheet->setCellValue("G{$row}", $days);
					$sheet->setCellValue("H{$row}", $rs->U_Delivery_Urgency);
					$sheet->setCellValue("I{$row}", $rs->CardCode);
					$sheet->setCellValue("J{$row}", $rs->CardName);
					$sheet->setCellValue("K{$row}", $rs->DocTotal);
					$sheet->setCellValue("L{$row}", $this->delivery_state($rs->U_Deliver_status));
					$sheet->setCellValue("M{$row}", empty($rs->U_Deliver_date) ? NULL : thai_date($rs->U_Deliver_date));
					$sheet->setCellValue("N{$row}", $rs->Address2);
					$sheet->setCellValue("O{$row}", $rs->ZipCode);
					$sheet->setCellValue("P{$row}", $this->get_route_name($rs->ZipCode));
					$sheet->setCellValue("Q{$row}", $rs->Comments);
					$sheet->setCellValue("R{$row}", $rs->U_Remark_Int);
					$sheet->setCellValue("S{$row}", $rs->firstName.' '.$rs->lastName);

					if($days > 0)
					{
						$sheet->getStyle("G{$row}")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
					}

					$row++;
				}
			}
		}

		if($doc_type == 'all' OR $doc_type == 'DO')
		{
			$invoice = $this->delivery_report_model->get_do_data($filter);

			if( ! empty($invoice))
			{
				foreach($invoice as $rs)
				{
					$de = $this->delivery_report_model->get_delivery_doc($rs->DocNum, "DO");
					$required_date = is_null($rs->U_Required_Delivery_Date) ? $rs->DocDate : $rs->U_Required_Delivery_Date;

					$today_date = date_create(date('Y-m-d'));
					$required_delivery_date = date_create($required_date);
					$diff = date_diff($today_date, $required_delivery_date);
					$days = $diff->format('%r%a') * -1;

					$sheet->setCellValue("A{$row}", thai_date($rs->DocDate));
					$sheet->setCellValue("B{$row}", thai_date($required_date));
					$sheet->setCellValue("C{$row}", "DO");
					$sheet->setCellValue("D{$row}", $rs->DocNum);
					$sheet->setCellValue("E{$row}", (empty($de) ? "ยังไม่ได้จัดสาย" : $de->delivery_code));
					$sheet->setCellValue("F{$row}", (empty($de) ? "" : $delivery_state[$de->line_status]));
					$sheet->setCellValue("G{$row}", $days);
					$sheet->setCellValue("H{$row}", $rs->U_Delivery_Urgency);
					$sheet->setCellValue("I{$row}", $rs->CardCode);
					$sheet->setCellValue("J{$row}", $rs->CardName);
					$sheet->setCellValue("K{$row}", $rs->DocTotal);
					$sheet->setCellValue("L{$row}", $this->delivery_state($rs->U_Deliver_status));
					$sheet->setCellValue("M{$row}", empty($rs->U_Deliver_date) ? NULL : thai_date($rs->U_Deliver_date));
					$sheet->setCellValue("N{$row}", $rs->Address2);
					$sheet->setCellValue("O{$row}", $rs->ZipCode);
					$sheet->setCellValue("P{$row}", $this->get_route_name($rs->ZipCode, $rs->City));
					$sheet->setCellValue("Q{$row}", $rs->Comments);
					$sheet->setCellValue("R{$row}", $rs->U_Remark_Int);
					$sheet->setCellValue("S{$row}", $rs->firstName.' '.$rs->lastName);

					if($days > 0)
					{
						$sheet->getStyle("G{$row}")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
					}

					$row++;
				}
			}
		}

		setToken($token);
    $file_name = "รายงาน งานยังไม่ได้ส่ง.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
	}


	public function delivery_state($status = NULL)
	{
		/*
		1 = Loaded
		4 = สำเร็จ
		2 = ส่งบางส่วน
		3 = ไม่ได้ส่ง
		5 = ลูกค้าไม่รับของ
		6 = สินค้าผิด
		7 = เอกสารผิด
		*/

		$arr = array(
			'1' => 'Loaded',
			'4' => 'สำเร็จ',
			'2' => 'ส่งบางส่วน',
			'3' => 'ไม่ได้ส่ง',
			'5' => 'ลูกค้าไม่รับสินค้า',
			'6' => 'สินค้าผิด',
			'7' => 'เอกสารผิด'
		);

		return empty($status) ? NULL : $arr[$status];
	}


	public function get_route_name($zip_code, $city)
	{
		if( ! empty($zip_code))
		{
			$route = $this->delivery_report_model->get_route_by_zip_code_and_city($zip_code, $city);

			if(empty($route))
			{
				$route = $this->delivery_report_model->get_route_by_zip_code($zip_code);
			}

			if( ! empty($route))
			{
				$ds = "";
				$i = 1;
				foreach($route as $rs)
				{
					$ds .= $i === 1 ? $rs->name : ", {$rs->name}";
					$i++;
				}

				return $ds;
			}
		}

		return NULL;
	}

} //-- end class
?>
