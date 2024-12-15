<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Picked_details extends PS_Controller
{
	public $menu_code = 'REPLDE';
	public $menu_group_code = 'RE';
	public $title = 'รายงานการจัดสินค้า';
	public $filter;
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/picked_details';
		$this->load->model('report/pick_report_model');
  }


	public function index()
	{
  	$ds = array(
			'users' =>$this->user_model->get_all()
		);

		$this->load->view('report/pick/picked_details', $ds);
	}


	public function get_report()
	{
		ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
		set_time_limit(600);

		$sc = TRUE;
		$ds = array();

		if($this->input->get())
		{
			$allUser = $this->input->get('allUser') == 1 ? TRUE : FALSE;
			$users = $this->input->get('users');
			$selectDate = $this->input->get('selectDate');
			$fromDate = $this->input->get('fromDate');
			$toDate = $this->input->get('toDate');

			$data = $this->pick_report_model->get_data($allUser, $users, $selectDate, $fromDate, $toDate);

			if( ! empty($data))
			{
				$no = 1;
				foreach($data as $rs)
				{
					$arr = array(
						'no' => $no,
						'DocNum' => $rs->DocNum,
						'OrderCode' => $rs->OrderCode,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'ReleaseQty' => number($rs->BaseRelQty),
						'PickQty' => number($rs->BasePickQty),
						'unitMsr' => $rs->unitMsr2,
						'DocDate' => thai_date($rs->CreateDate, FALSE),
						'StartPick' => thai_date($rs->StartPick, FALSE),
						'FinishPick' => thai_date($rs->FinishPick, FALSE),
						'OrderDate' => thai_date($rs->OrderDate, FALSE),
						'uname' => $rs->uname
					);

					array_push($ds, $arr);
					$no++;
				}

				$dataset = array(
					'status' => 'success',
					'data' => $ds
				);
			}
			else
			{
				$dataset = array(
					'status' => 'nodata'
				);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		echo $sc === TRUE ? json_encode($dataset) : $this->error;

	}//---- end get report




	public function do_export()
	{
		ini_set('memory_limit','1024M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
		set_time_limit(600);

		$allUser = $this->input->post('allUser') == 1 ? TRUE : FALSE;
		$users = $this->input->post('users');
		$selectDate = $this->input->post('selectDate');
		$fromDate = $this->input->post('fromDate');
		$toDate = $this->input->post('toDate');
		$token = $this->input->post('token');

		$data = $this->pick_report_model->get_data($allUser, $users, $selectDate, $fromDate, $toDate);

		$user_list = "ทั้งหมด";

		if( ! $allUser)
		{
			if( ! empty($users))
			{
				$unames = $this->pick_report_model->get_users_in($users);

				if( ! empty($unames))
				{
					$user_list = "";

					$i = 1;
					foreach($unames as $u)
					{
						$user_list .= $i === 1 ? $u->uname : ", {$u->uname}";
						$i++;
					}
				}
			}
		}

		//---  Report title
		$report_title = 'รายงานรายละเอียดการจัดสินค้า';
		$user_title = "User : {$user_list}";
		$date_title = 'กรองตาม :  '. ($selectDate == 'SO' ? 'SO Date' :($selectDate == 'DocDate' ? 'Pick List Date' : 'Finish Date')) . "วันที่ : {$fromDate} ถึง {$toDate}";


		//--- load excel library
		$this->load->library('excel');

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Pick details report');

		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

		//--- set report title header
		$this->excel->getActiveSheet()->setCellValue('A1', $report_title);
		$this->excel->getActiveSheet()->setCellValue('A2', $user_title);
		$this->excel->getActiveSheet()->setCellValue('A3', $date_title);

		$row = 4;

		$this->excel->getActiveSheet()->setCellValue("A{$row}", "#");
		$this->excel->getActiveSheet()->setCellValue("B{$row}", "PL No.");
		$this->excel->getActiveSheet()->setCellValue("C{$row}", "SO No.");
		$this->excel->getActiveSheet()->setCellValue("D{$row}", "Item Code");
		$this->excel->getActiveSheet()->setCellValue("E{$row}", "Item Name");
		$this->excel->getActiveSheet()->setCellValue("F{$row}", "Release Qty");
		$this->excel->getActiveSheet()->setCellValue("G{$row}", "Pick Qty");
		$this->excel->getActiveSheet()->setCellValue("H{$row}", "Uom");
		$this->excel->getActiveSheet()->setCellValue("I{$row}", "Posting Date");
		$this->excel->getActiveSheet()->setCellValue("J{$row}", "PL Date");
		$this->excel->getActiveSheet()->setCellValue("K{$row}", "Start Pick");
		$this->excel->getActiveSheet()->setCellValue("L{$row}", "Finish Pick");
		$this->excel->getActiveSheet()->setCellValue("M{$row}", "User");

		$row++;

		if(! empty($data))
		{
			$no = 1;
			foreach($data as $rs)
			{
				$this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
				$this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->DocNum);
				$this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->OrderCode);
				$this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->ItemCode);
				$this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->ItemName);
				$this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->BaseRelQty);
				$this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->BasePickQty);
				$this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->unitMsr2);
				$this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->OrderDate);
				$this->excel->getActiveSheet()->setCellValue("J{$row}", $rs->CreateDate);
				$this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->StartPick);
				$this->excel->getActiveSheet()->setCellValue("L{$row}", $rs->FinishPick);
				$this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->uname);
				$no++;
				$row++;
			}

			$this->excel->getActiveSheet()->getStyle('A4:M'.$row)->getAlignment()->setHorizontal('center');
			$this->excel->getActiveSheet()->getStyle('D4:E'.$row)->getAlignment()->setHorizontal('left');
		}

		setToken($token);
		$file_name = "Pick Details Report.xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
		header('Content-Disposition: attachment;filename="'.$file_name.'"', true);
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		$writer->save('php://output');
	}

} //--- end classs


 ?>
