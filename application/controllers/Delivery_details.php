<?php
class Delivery_details extends PS_Controller
{
	public $menu_code = 'DELDETAIL';
	public $menu_group_code = 'TR';
	public $title = 'รายละเอียดการจัดส่ง';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'delivery_details';
		$this->load->model('delivery_details_model');
		$this->load->model('delivery_model');
    $this->load->helper('transport');
  }



  public function index()
  {
		$filter = array(
			'delivery_code' => get_filter('delivery_code', 'delivery_code', ''),
      'driver_id' => get_filter('driver_id', 'driver_id', 'all'),
      'vehicle_id' => get_filter('vehicle_id', 'vehicle_id', 'all'),
      'route_id' => get_filter('route_id', 'route_id', 'all'),
			'CardCode' => get_filter('CardCode', 'CardCode', ''),
      'CardName' => get_filter('CardName', 'CardName', ''),
      'contact' => get_filter('contact', 'contact', ''),
      'type' => get_filter('type', 'type', 'all'),
      'DocType' => get_filter('DocType', 'DocType', 'all'),
      'DocNum' => get_filter('DocNum', 'DocNum', ''),
      'result_status' => get_filter('result_status', 'result_status', 'all'),
      'line_status' => get_filter('line_status', 'line_status', 'all'),
      'release_from' => get_filter('release_from', 'release_from', ''),
      'release_to' => get_filter('release_to', 'release_to', ''),
      'finish_from' => get_filter('finish_from', 'finish_from', ''),
      'finish_to' => get_filter('finish_to', 'finish_to', ''),
			'ship_from_date' => get_filter('ship_from_date', 'ship_from_date', ''),
			'ship_to_date' => get_filter('ship_to_date', 'ship_to_date', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
      'uname' => get_filter('uname', 'uname', '')
		);

				//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->delivery_details_model->count_rows($filter);
    $rs = $this->delivery_details_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    $filter['data'] = $rs;

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
    $this->pagination->initialize($init);

    $this->load->view('delivery_details/delivery_details_list', $filter);
  }


	public function shipTypeName($shipType)
	{
		return $shipType == 'P' ? 'ส่งสินค้า' :($shipType == 'D' ? 'ส่งเอกสาร' : 'อื่นๆ');
	}

	public function resultStatusName($status)
	{
		$name = 'Loaded';

		switch($status)
		{
			case '1' : $name = 'Loaded'; break;
			case '2' : $name = 'ส่งบางส่วน'; break;
			case '3' : $name = 'ไม่ได้ส่ง'; break;
			case '4' : $name = 'สำเร็จ'; break;
			case '5' : $name = 'ลูกค้าไม่รับของ'; break;
			case '6' : $name = 'สินค้าผิด'; break;
			case '7' : $name = 'เอกสารผิด'; break;
			case '8' : $name = 'ติดต่อลูกค้าไม่ได้-ไม่ได้เข้าส่ง'; break;
			default : $name = 'Loaded'; break;
		}

		return $name;
	}


	public function lineStatusName($status)
	{
		$name = "Open";

		switch($status)
		{
			case 'O' : $name = "Open"; break;
			case 'R' : $name = "Released"; break;
			case 'C' : $name = "Closed"; break;
			case 'D' : $name = "Canceled"; break;
			default : $name = "Open";
		}

		return $name;
	}


	public function export_filter()
	{
		ini_set('memory_limit','2048M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
		$token = $this->input->post('token');
    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Transport report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', 'Document Code');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Customer Code');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Customer Name');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Contact');
    $this->excel->getActiveSheet()->setCellValue('E1', 'Phone');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Ship Type');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Doc Type');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Doc Num');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Doc Total');
		$this->excel->getActiveSheet()->setCellValue('J1', 'Posting Date');
    $this->excel->getActiveSheet()->setCellValue('K1', 'Shipping Status');
    $this->excel->getActiveSheet()->setCellValue('L1', 'Document Status');
    $this->excel->getActiveSheet()->setCellValue('M1', 'Document Date');
    $this->excel->getActiveSheet()->setCellValue('N1', 'Shipment Date');
    $this->excel->getActiveSheet()->setCellValue('O1', 'Release Date');
    $this->excel->getActiveSheet()->setCellValue('P1', 'Shipping Result Date');
    $this->excel->getActiveSheet()->setCellValue('Q1', 'Ship To Code');
    $this->excel->getActiveSheet()->setCellValue('R1', 'Street');
    $this->excel->getActiveSheet()->setCellValue('S1', 'Block');
    $this->excel->getActiveSheet()->setCellValue('T1', 'City');
    $this->excel->getActiveSheet()->setCellValue('U1', 'County');
    $this->excel->getActiveSheet()->setCellValue('V1', 'Country');
		$this->excel->getActiveSheet()->setCellValue('W1', 'Zip Code');
		$this->excel->getActiveSheet()->setCellValue('X1', 'Remark');
		$this->excel->getActiveSheet()->setCellValue('Y1', 'Vehicle');
		$this->excel->getActiveSheet()->setCellValue('Z1', 'Route');
		$this->excel->getActiveSheet()->setCellValue('AA1', 'Dificulty');
		$this->excel->getActiveSheet()->setCellValue('AB1', 'Driver');
		$this->excel->getActiveSheet()->setCellValue('AC1', 'Asst.1');
		$this->excel->getActiveSheet()->setCellValue('AD1', 'Asst.2');

    $row = 2;


		$filter = array(
			'delivery_code' => get_filter('xCode', 'delivery_code', ''),
      'driver_id' => get_filter('xDriver', 'driver_id', 'all'),
      'vehicle_id' => get_filter('xVehicle', 'vehicle_id', 'all'),
      'route_id' => get_filter('xRoute', 'route_id', 'all'),
			'CardCode' => get_filter('xCardCode', 'CardCode', ''),
      'CardName' => get_filter('xCardName', 'CardName', ''),
      'contact' => get_filter('xContact', 'contact', ''),
      'type' => get_filter('xShipType', 'type', 'all'),
      'DocType' => get_filter('xDocType', 'DocType', 'all'),
      'DocNum' => get_filter('xDocNum', 'DocNum', ''),
      'result_status' => get_filter('xResultStatus', 'result_status', 'all'),
      'line_status' => get_filter('xLineStatus', 'line_status', 'all'),
      'release_from' => get_filter('xReleaseFrom', 'release_from', ''),
      'release_to' => get_filter('xReleaseTo', 'release_to', ''),
      'finish_from' => get_filter('xCloseFrom', 'finish_from', ''),
      'finish_to' => get_filter('xCloseTo', 'finish_to', ''),
			'ship_from_date' => get_filter('xShipFrom', 'ship_from_date', ''),
			'ship_to_date' => get_filter('xShipTo', 'ship_to_date', ''),
      'from_date' => get_filter('xFromDate', 'from_date', ''),
      'to_date' => get_filter('xToDate', 'to_date', ''),
      'uname' => get_filter('xUname', 'uname', '')
		);
	
		$ds = $this->delivery_details_model->getExportList($filter);

		if( ! empty($ds))
		{
			$empList = array();
			$leter = array('AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM');

			foreach($ds as $rs)
      {
				$this->excel->getActiveSheet()->setCellValue('A'.$row, $rs->delivery_code);
				$this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->CardCode);
				$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->CardName);
				$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->contact);
				$this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->Phone);
				$this->excel->getActiveSheet()->setCellValue('F'.$row, $this->shipTypeName($rs->type));
				$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->DocType);
				$this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->DocNum);
				$this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->DocTotal);
				$this->excel->getActiveSheet()->setCellValue('J'.$row, $rs->DocDate);
				$this->excel->getActiveSheet()->setCellValue('K'.$row, $this->resultStatusName($rs->result_status));
				$this->excel->getActiveSheet()->setCellValue('L'.$row, $this->lineStatusName($rs->line_status));
				$this->excel->getActiveSheet()->setCellValue('M'.$row, $rs->DocumentDate);
				$this->excel->getActiveSheet()->setCellValue('N'.$row, $rs->ShipDate);
				$this->excel->getActiveSheet()->setCellValue('O'.$row, $rs->release_date);
				$this->excel->getActiveSheet()->setCellValue('P'.$row, $rs->finish_date);
				$this->excel->getActiveSheet()->setCellValue('Q'.$row, $rs->ShipToCode);
				$this->excel->getActiveSheet()->setCellValue('R'.$row, $rs->Street);
				$this->excel->getActiveSheet()->setCellValue('S'.$row, $rs->Block);
				$this->excel->getActiveSheet()->setCellValue('T'.$row, $rs->City);
				$this->excel->getActiveSheet()->setCellValue('U'.$row, $rs->County);
				$this->excel->getActiveSheet()->setCellValue('V'.$row, $rs->Country);
				$this->excel->getActiveSheet()->setCellValue('W'.$row, $rs->ZipCode);
				$this->excel->getActiveSheet()->setCellValue('X'.$row, $rs->remark);
				$this->excel->getActiveSheet()->setCellValue('Y'.$row, $rs->vehicle_name);
				$this->excel->getActiveSheet()->setCellValue('Z'.$row, $rs->route_name);
				$this->excel->getActiveSheet()->setCellValue('AA'.$row, $rs->level);
				$this->excel->getActiveSheet()->setCellValue('AB'.$row, $rs->driver_name);

				if(! isset($empList[$rs->delivery_id]))
				{
					$emp = $this->delivery_model->get_delivery_employee('E', $rs->delivery_code);

					if( ! empty($emp))
					{
						foreach($emp as $rd)
						{
							$empList[$rs->delivery_id][] = $rd->emp_name;
						}
					}
				}

				if( ! empty($empList[$rs->delivery_id]))
				{
					$i = 0;
					foreach($empList[$rs->delivery_id] as $name)
					{
						$this->excel->getActiveSheet()->setCellValue($leter[$i].$row, $name);
						$i++;
					}
				}

        $row++;
      }
		}

    setToken($token);

    $file_name = "Transport report.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
	}


  public function clear_filter()
  {
    $filter = array(
			'delivery_code',
      'driver_id',
      'vehicle_id',
      'route_id',
			'CardCode',
      'CardName',
      'contact',
      'type',
      'DocType',
      'DocNum',
      'result_status',
      'line_status',
      'release_from',
      'release_to',
      'finish_from',
      'finish_to',
			'ship_from_date',
			'ship_to_date',
      'from_date',
      'to_date',
      'uname',
		);

    return clear_filter($filter);
  }



} //--- end class
?>
