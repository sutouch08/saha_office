<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
	public $menu_code = 'GRPO';
	public $menu_group_code = 'IC';
	public $title = 'Goods Receipt PO';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'receive_po';
		$this->load->model('receive_po_model');
		$this->load->model('vendor_model');
		$this->load->model('item_model');
		$this->load->helper('currency');
		$this->load->helper('warehouse');
		$this->load->helper('receive_po');
  }



  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'gr_code', ''),
			'vendor' => get_filter('vendor', 'gr_vendor', ''),
			'po_code' => get_filter('po_code', 'gr_po_code', ''),
			'invoice' => get_filter('invoice', 'gr_invoice', ''),
			'warehouse' => get_filter('warehouse', 'gr_warehouse', 'all'),
			'user' => get_filter('user', 'gr_user', 'all'),
			'status' => get_filter('Status', 'gr_Status', 'all'),
			'from_date' => get_filter('from_date', 'gr_from_date', ''),
			'to_date' => get_filter('to_date', 'gr_to_date', '')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_rows();
			$segment = 3;
			$rows = $this->receive_po_model->count_rows($filter);
			$filter['data'] = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));
			$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$this->pagination->initialize($init);
			$this->load->view('receive_po/receive_po_list', $filter);
		}
  }


	public function add_new()
	{
		$this->load->view('receive_po/receive_po_add');
	}


	public function add()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			$date_add = db_date($ds->date_add);

			$code = $this->get_new_code($date_add);

			$arr = array(
				'code' => $code,
				'date_add' => $date_add,
				'vendor_code' => $ds->vendor_code,
				'vendor_name' => $ds->vendor_name,
				'invoice_code' => get_null($ds->invoice),
				'warehouse_code' => $ds->warehouse_code,
				'user' => $this->user->uname
			);

			if( ! $this->receive_po_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "Failed to create new document";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'code' => $sc === TRUE ? $code : NULL
		);

		echo json_encode($arr);
	}


	public function edit($code)
	{
		$doc = $this->receive_po_model->get($code);

		if( ! empty($doc))
		{
			$ds = array(
				'doc' => $doc,
				'details' => $this->receive_po_model->get_details($code)
			);

			$this->load->view('receive_po/receive_po_edit', $ds);
		}
		else
		{
			$this->page_error();
		}
	}


	public function get_po_detail()
  {
    $sc = TRUE;
    $ds = array();

    $po_code = $this->input->get('po_code');

    $po = $this->receive_po_model->get_po($po_code);

    if( ! empty($po))
    {
      $ro = getConfig('RECEIVE_OVER_PO');

      $rate = ($ro * 0.01);

      $details = $this->receive_po_model->get_po_details($po_code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
  				if($rs->OpenQty > 0)
  				{
            $dif = $rs->Quantity - $rs->OpenQty;
            $onOrder = $this->receive_po_model->get_on_order_qty($rs->ItemCode, $rs->DocEntry, $rs->LineNum);

            $qty = $rs->OpenQty - $onOrder;
  	        $arr = array(
  	          'no' => $no,
              'uid' => $rs->DocEntry."-".$rs->LineNum,
              'product_code' => $rs->ItemCode,
              'product_name' => $rs->Dscription.' '.$rs->Text,
              'baseCode' => $po_code,
              'baseEntry' => $rs->DocEntry,
              'baseLine' => $rs->LineNum,
              'vatCode' => $rs->VatGroup,
              'vatRate' => $rs->VatPrcnt,
              'unitCode' => $rs->unitMsr,
              'unitMsr' => $rs->unitMsr,
              'NumPerMsr' => $rs->NumPerMsr,
              'unitMsr2' => $rs->unitMsr2,
              'NumPerMsr2' => $rs->NumPerMsr2,
              'UomEntry' => $rs->UomEntry,
              'UomEntry2' => $rs->UomEntry2,
              'UomCode' => $rs->UomCode,
              'UomCode2' => $rs->UomCode2,
  	          'PriceBefDi' => round($rs->PriceBefDi, 3),
              'PriceBefDiLabel' => number($rs->PriceBefDi, 3),
              'DiscPrcnt' => round($rs->DiscPrcnt, 2),
              'Price' => round($rs->Price, 3),
              'PriceAfDiscLabel' => number($rs->Price, 3),
              'onOrder' => $onOrder,
              'qty' => $qty,
  	          'qtyLabel' => number($qty, 2),
              'backlogs' => $rs->OpenQty,
  	          'limit' => ($rs->Quantity + ($rs->Quantity * $rate)) - $dif,
  	          'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE
  	        );

  	        array_push($ds, $arr);
  	        $no++;
  				}
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบใบสั่งซื้อ";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'DocNum' => $sc === TRUE ? $po->DocNum : NULL,
      'DocCur' => $sc === TRUE ? $po->DocCur : NULL,
      'DocRate' => $sc === TRUE ? $po->DocRate : NULL,
      'CardCode' => $sc === TRUE ? $po->CardCode : NULL,
      'CardName' => $sc === TRUE ? $po->CardName : NULL,
      'DiscPrcnt' => $sc === TRUE ? $po->DiscPrcnt : NULL,
      'details' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }

	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_GRPO');
    $run_digit = getConfig('RUN_DIGIT_GRPO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_model->get_max_code($pre);

    if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  public function clear_filter()
	{

		$filter = array(
			'gr_code',
			'gr_vendor',
			'gr_po_code',
			'gr_invoice',
			'gr_warehouse',
			'gr_user',
			'gr_status',
			'gr_from_date',
			'gr_to_date'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>
