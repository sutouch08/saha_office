<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('warehouse');

	}


	public function index()
	{		
		$this->load->view('main_view');
	}


	public function get_sell_items_stock()
  {
		$this->load->model('stock_model');
		$sc = array();

		$txt = trim($this->input->post('search_text'));
		$warehouse_code = trim($this->input->post('warehouse_code'));

		$qr  = "SELECT I.ItemCode, I.ItemName, Q.WhsCode, Q.OnHandQty, B.BinCode ";
		$qr .= "FROM OIBQ AS Q ";
		$qr .= "LEFT JOIN OITM AS I ON Q.ItemCode = I.ItemCode ";
		$qr .= "LEFT JOIN OBIN AS B ON Q.BinAbs = B.AbsEntry "; 
		$qr .= "WHERE Q.BinAbs > 0 ";

		if( ! empty($warehouse_code))
		{
			$qr .= "AND Q.WhsCode = '{$warehouse_code}' ";			
		}

		$qr .= "AND (I.ItemCode LIKE N'%{$txt}%' OR I.ItemName LIKE N'%{$txt}%') ";
		$qr .= "ORDER BY I.ItemCode ASC, Q.WhsCode ASC";		

		$stock = $this->ms->query($qr);

		if(!empty($stock->num_rows() > 0))
		{
			$no = 1;

			foreach($stock->result() as $rs)
			{
				$committed = $this->stock_model->get_committed_stock($rs->ItemCode, $rs->WhsCode);
				$balance = $rs->OnHandQty - $committed;

				$arr = array(
					'no' => $no,
					'ItemCode' => $rs->ItemCode,
					'ItemName' => $rs->ItemName,
					'WhsCode' => $rs->WhsCode,
					'BinCode' => $rs->BinCode,
					'Qty' => ac_format($rs->OnHandQty, 2),
					'Committed' => ac_format($committed, 2),
					'Balance' => ac_format($balance, 2)
				);

				array_push($sc, $arr);
				$no++;
			}
		}
		else
		{
			array_push($sc, array('nodata' => 'nodata'));
		}

		echo json_encode($sc);
  }
} //--- end class
