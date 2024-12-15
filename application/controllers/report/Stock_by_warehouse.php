<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_by_warehouse extends PS_Controller
{
	public $menu_code = 'RESTBLWH';
	public $menu_group_code = 'RE';
	public $title = 'รายงานสินค้าคงเหลือแยกตามคลัง(Grid)';
	public $filter;
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/stock_by_warehouse';
		$this->load->model('stock_model');
		$this->load->model('warehouse_model');
		$this->load->model('item_model');
  }


	public function index()
	{

    $whList = $this->warehouse_model->get_warehouse_list();
		$itemGroupList = $this->item_model->get_item_group_list();
		$ds = array(
			'whList' => $whList,
			'groupList' => $itemGroupList
		);

    $this->load->view('report/stock/stock_balance_by_warehouse', $ds);
	}


	public function get_report()
	{
		//print_r($this->input->get());
		$allProduct = $this->input->get('allProduct') == 1 ? TRUE : FALSE;
		$allWarehouse = $this->input->get('allWhouse') == 1 ? TRUE : FALSE;
		$allGroup = $this->input->get('allGroup') == 1 ? TRUE : FALSE;

		$pdFrom = trim($this->input->get('pdFrom'));
		$pdTo = trim($this->input->get('pdTo'));

		if(!empty($pdFrom) && !empty($pdTo))
		{
			$pdF = $pdFrom;
			$pdT = $pdTo;

			if($pdFrom > $pdTo)
			{
				$pdFrom = $pdT;
				$pdTo = $pdF;
			}
		}


		$itemGroup = $this->input->get('group');
		$warehouse = $this->input->get('warehouse');

		$hideItem = $this->input->get('hideItem') == 'Y' ? TRUE : FALSE;


		$header = array();
		$whList = array(); //--- array user for query stock
		if($allWarehouse)
		{
			$allWhs = $this->warehouse_model->get_warehouse_list();
			if(!empty($allWhs))
			{
				foreach($allWhs as $wh)
				{
					$arr = array(
						"whsCode" => $wh->code
					);

				//	$whList[] = $wh->code;
					array_push($header, $arr);
				}
			}
		}
		else
		{
			foreach($warehouse as $wh)
			{
				$arr = array(
					'whsCode' => $wh
				);

				$whList[] = $wh;

				array_push($header, $arr);
			}
		} //--- end all warehouse

		//print_r($header);

		if($allProduct)
		{
			$items = $this->item_model->get_item_list($itemGroup);
		}
		else
		{

			$items = $this->item_model->get_items_by_range($pdFrom, $pdTo, $itemGroup);
		}

		// print_r($items);
		$allTotal = 0;
		//---- stock data
		$data = array();

		//---- get_stock
		if(!empty($items))
		{
			$no = 1;
			foreach($items as $item)
			{
				$item_stock = $this->stock_model->get_stock_each_warehouse($item->code, $whList);
				$totalQty = 0;
				$ds = array();
				$stock = array();
				$row = array(
					"no" => $no,
					"pdCode" => $item->code,
					"pdName" => $item->name,
					"uom" => $item->UoM
				);

				if(!empty($item_stock))
				{
					foreach($item_stock as $rs)
					{
						$ds[$rs->WhsCode] = $rs->OnHandQty;
					}

					foreach($header as $val)
					{
						$qty = !isset($ds[$val['whsCode']]) ? "" : $ds[$val['whsCode']];
						$qty = $qty == 0 ? "" :round($qty, 2);
						$stock[] = array(
							'qty' => $qty
						);

						$totalQty += empty($qty) ? 0 : $qty;

					}

					$row["whQty"] = $stock;
					$row['whTotal'] = $totalQty;
				}
				else
				{
					foreach($header as $val)
					{
						$stock[] = array("qty" => "");
					}

					$row["whQty"] = $stock;
					$row['whTotal'] = $totalQty;
				}

				if(! $hideItem)
				{
					$data[] = $row;
					$no++;
				}
				else
				{
					if($totalQty > 0)
					{
						$data[] = $row;
						$no++;
					}
				}

			}

		} //--- end if empty items


		$dataset = array(
			'header' => $header,
			'data' => $data
		);

		echo json_encode($dataset);

	}//---- end get report






	public function do_export()
	{
		$allProduct = $this->input->post('allProduct') == 1 ? TRUE : FALSE;
		$allWarehouse = $this->input->post('allWhouse') == 1 ? TRUE : FALSE;
		$allGroup = $this->input->post('allGroup') == 1 ? TRUE : FALSE;

		$pdFrom = trim($this->input->post('pdFrom'));
		$pdTo = trim($this->input->post('pdTo'));

		if(!empty($pdFrom) && !empty($pdTo))
		{
			$pdF = $pdFrom;
			$pdT = $pdTo;

			if($pdFrom > $pdTo)
			{
				$pdFrom = $pdT;
				$pdTo = $pdF;
			}
		}


		$itemGroup = $this->input->post('group');
		$warehouse = $this->input->post('warehouse');

		$hideItem = $this->input->post('hideItem') == 'Y' ? TRUE : FALSE;

		$token = $this->input->post('token');

		$header = array();
		$whList = array(); //--- array user for query stock
		$wh_list = "";

		if($allWarehouse)
		{
			$allWhs = $this->warehouse_model->get_warehouse_list();
			if(!empty($allWhs))
			{
				foreach($allWhs as $wh)
				{
					$header[] = $wh->code;
				}
			}
		}
		else
		{
			$i = 1;
			foreach($warehouse as $wh)
			{
				$header[] = $wh;
				$whList[] = $wh;
				$wh_list .= $i == 1 ? $wh : ", {$wh}";
				$i++;
			}
		} //--- end all warehouse

		//---  Report title
    $report_title = 'รายงานสินค้าคงเหลือแยกตามคลัง';
    $wh_title     = 'คลัง :  '. ($allWarehouse ? 'All' : $wh_list);
    $pd_title     = 'สินค้า :  '. ($allProduct? 'All' : '('.$pdFrom.') - ('.$pdTo.')');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Inventory Stock Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->setCellValue('A2', $wh_title);
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);

		$row = 4;
	  $col = 0; //--- column is zero base;

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '#');
		$col++;
    $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Item Code');
		$col++;
    $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Item Name');
		$col++;
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'UoM');
		$col++;
    $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'WhTotal');
		$col++;

		foreach($header as $whName)
	  {
	    $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $whName);
	    $col++;
	  }

		$row++;

		if($allProduct)
		{
			$items = $this->item_model->get_item_list($itemGroup);
		}
		else
		{
			$items = $this->item_model->get_items_by_range($pdFrom, $pdTo, $itemGroup);
		}

		// print_r($items);
		$allTotal = 0;
		//---- stock data

		//---- get_stock
		if(!empty($items))
		{
			$no = 1;
			foreach($items as $item)
			{
				$item_stock = $this->stock_model->get_stock_each_warehouse($item->code, $whList);
				$totalQty = 0;
				$ds = array();
				$stock = array();

				if(!empty($item_stock))
				{
					foreach($item_stock as $rs)
					{
						$ds[$rs->WhsCode] = $rs->OnHandQty;
					}

					foreach($header as $val)
					{
						$qty = !isset($ds[$val]) ? "" : $ds[$val];
						$qty = $qty == 0 ? "" :round($qty, 2);
						$stock[] = $qty;

						$totalQty += empty($qty) ? 0 : $qty;
					}
				}
				else
				{
					foreach($header as $val)
					{
						$stock[] = "";
					}
				}

				if(!$hideItem OR $totalQty > 0)
				{
					$col = 0;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $no);
					$col++;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item->code);
					$col++;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item->name);
					$col++;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item->UoM);
					$col++;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $totalQty);
					$col++;

					foreach($stock as $qty)
					{
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $qty);
						$col++;
					}

					$row++;
					$no++;
				}
			}

		} //--- end if empty items

		setToken($token);
		$file_name = "Inventory in Warehouse Report.xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
		header('Content-Disposition: attachment;filename="'.$file_name.'"', true);
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		$writer->save('php://output');



	}//---- end get report

} //--- end classs


 ?>
