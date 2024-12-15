<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_order_backlogs extends PS_Controller
{
	public $menu_code = 'RESOBL';
	public $menu_group_code = 'RE';
	public $title = 'รายงานออเดอร์ค้างส่ง';
	public $filter;
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales_order_backlogs';
		$this->load->model('report/sales_order_report_model');
		$this->load->model('pick_model');
		$this->load->model('stock_model');
		$this->load->model('item_model');
  }


	public function index()
	{
		$this->load->view('report/sales_order/sales_order_backlogs');
	}


	public function get_report()
	{
		$sc = TRUE;
		$no = 1;
		$ds = array();
		$filter = json_decode($this->input->post('filter'));

		if( ! empty($filter))
		{
			$orders = $this->sales_order_report_model->get_orders($filter);

			if( ! empty($orders))
			{
				foreach($orders as $od)
				{
					$details = $this->sales_order_report_model->getOpenRows($od->DocEntry, $filter->item_code);

					if( ! empty($details))
					{
						foreach($details as $rs)
						{
							$PrevRelease = $this->pick_model->get_prev_release_qty($rs->DocEntry, $rs->LineNum);
							$baseQty = $this->item_model->get_base_qty($rs->ItemCode, $rs->UomEntry);
							$invQty = $rs->Quantity * $baseQty;
							$AvailableQty = ($invQty - $PrevRelease) > 0 ? $invQty - $PrevRelease : 0;
							$onhand = $this->stock_model->get_onhand_stock($rs->ItemCode);
							$commit = $this->pick_model->get_committed_stock($rs->ItemCode);
							$OnHand = $onhand - $commit;

							$PrevRelease = $PrevRelease > 0 ? $PrevRelease/$baseQty : 0;
							$AvailableQty = $AvailableQty > 0 ? $AvailableQty/$baseQty : 0;
							$OnHand = $OnHand > 0 ? $OnHand/$baseQty : 0;

							$arr = array(
								'no' => $no,
								'DocNum' => $od->DocNum,
								'DocDate' => thai_date($od->DocDate),
								'DocDueDate' => thai_date($od->DocDueDate),
								'OrderCode' => $od->DocNum,
								'CardCode' => $od->CardCode,
								'CardName' => $od->CardName,
								'ItemCode' => $rs->ItemCode,
								'Dscription' => $rs->Dscription,
								'Price' => number($rs->Price, 2),
								'Qty' => number($rs->Quantity, 2),
								'OpenQty' => number($rs->OpenQty, 2),
								'Released' => number($PrevRelease, 2),
								'OnHand' => number($OnHand, 2),
								'Available' => number($AvailableQty, 2),
								'unitMsr' => $rs->unitMsr,
								'color' => ($AvailableQty <= 0 OR $AvailableQty > $OnHand) ? 'red' : ''
							);

							array_push($ds, $arr);
							$no++;
						}
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : fileter";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}

	public function do_export()
	{
		$date_type = $this->input->post('dateType');
		$from_date = db_date($this->input->post('fromDate'));
		$to_date = db_date($this->input->post('toDate'));
		$doc_type = $this->input->post('docType');
		$so_code = trim($this->input->post('soCode'));
		$customer = trim($this->input->post('customer'));
		$itemCode = trim($this->input->post('itemCode'));
		$token = $this->input->post('token');

		$no = 1;

		$arr = array(
			'date_type' => $date_type,
			'from_date' => $from_date,
			'to_date' => $to_date,
			'customer' => $customer,
			'so_code' => $so_code,
			'item_code' => $itemCode
		);

		$filter = (object) $arr;

		$this->load->library('excel');

		$sheetName = 'ออเดอร์ค้างส่ง';
    $title = "รายงาน ออเดอร์ค้างส่ง ณ วันที่ ".date('d-m-Y H:i');

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

		$row = 1;

		$sheet->setCellValue("A{$row}", $title);
		$sheet->mergeCells("A{$row}:S{$row}");
		$row++;

		$sheet->setCellValue("A{$row}", "#");
		$sheet->setCellValue("B{$row}", "Doc Date");
		$sheet->setCellValue("C{$row}", "Due Date");
		$sheet->setCellValue("D{$row}", "Order No.");
		$sheet->setCellValue("E{$row}", "Item Code");
		$sheet->setCellValue("F{$row}", "Description");
		$sheet->setCellValue("G{$row}", "Uom");
		$sheet->setCellValue("H{$row}", "Price");
		$sheet->setCellValue("I{$row}", "Ordered");
		$sheet->setCellValue("J{$row}", "Open");
		$sheet->setCellValue("K{$row}", "Released");
		$sheet->setCellValue("L{$row}", "Balance");
		$sheet->setCellValue("M{$row}", "Available");
		$sheet->setCellValue("N{$row}", "Customer Code");
		$sheet->setCellValue("O{$row}", "Customer Name");

		$row++;

		$orders = $this->sales_order_report_model->get_orders($filter);

		if( ! empty($orders))
		{
			foreach($orders as $od)
			{
				$details = $this->sales_order_report_model->getOpenRows($od->DocEntry, $filter->item_code);

				if( ! empty($details))
				{
					foreach($details as $rs)
					{
						$PrevRelease = $this->pick_model->get_prev_release_qty($rs->DocEntry, $rs->LineNum);
						$baseQty = $this->item_model->get_base_qty($rs->ItemCode, $rs->UomEntry);
						$invQty = $rs->Quantity * $baseQty;
						$AvailableQty = ($invQty - $PrevRelease) > 0 ? $invQty - $PrevRelease : 0;
						$onhand = $this->stock_model->get_onhand_stock($rs->ItemCode);
						$commit = $this->pick_model->get_committed_stock($rs->ItemCode);
						$OnHand = $onhand - $commit;

						$PrevRelease = $PrevRelease > 0 ? $PrevRelease/$baseQty : 0;
						$AvailableQty = $AvailableQty > 0 ? $AvailableQty/$baseQty : 0;
						$OnHand = $OnHand > 0 ? $OnHand/$baseQty : 0;

						$sheet->setCellValue("A{$row}", $no);
						$sheet->setCellValue("B{$row}", thai_date($od->DocDate));
						$sheet->setCellValue("C{$row}", thai_date($od->DocDueDate));
						$sheet->setCellValue("D{$row}", $od->DocNum);
						$sheet->setCellValue("E{$row}", $rs->ItemCode);
						$sheet->setCellValue("F{$row}", $rs->Dscription);
						$sheet->setCellValue("G{$row}", $rs->unitMsr);
						$sheet->setCellValue("H{$row}", $rs->Price);
						$sheet->setCellValue("I{$row}", $rs->Quantity);
						$sheet->setCellValue("J{$row}", $rs->OpenQty);
						$sheet->setCellValue("K{$row}", $PrevRelease);
						$sheet->setCellValue("L{$row}", $OnHand);
						$sheet->setCellValue("M{$row}", $AvailableQty);
						$sheet->setCellValue("N{$row}", $od->CardCode);
						$sheet->setCellValue("O{$row}", $od->CardName);

						$no++;
						$row++;
					}
				}
			}
		}

		setToken($token);
    $file_name = "รายงานออเดอร์ค้างส่ง".date('YmdHi').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"', true);
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
	}
} //-- end class
?>
