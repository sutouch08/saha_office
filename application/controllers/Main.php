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

		$this->ms
    ->select('OITM.ItemCode, OITM.ItemName, OIBQ.WhsCode')
    ->select_sum('OIBQ.OnHandQty')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left');

    if(!empty($warehouse_code))
    {
      $this->ms->where('OIBQ.WhsCode', $warehouse_code);
    }

		$this->ms->where('OIBQ.OnHandQty >', 0);
		$this->ms->like('OIBQ.ItemCode', $txt);
		$this->ms->group_by('OITM.ItemCode, OITM.ItemName, OIBQ.WhsCode');
		$this->ms->order_by('OITM.ItemCode', 'ASC');
		$this->ms->order_by('OIBQ.WhsCode', 'ASC');

		$stock = $this->ms->get();

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
					'Qty' => round($rs->OnHandQty, 2),
					'Committed' => round($committed, 2),
					'Balance' => round($balance, 2)
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
