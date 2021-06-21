<?php
class Item_model extends CI_Model
{
  public $price_list = 12;
  public $cost_list = 11;
  public $clear_price_list = 10;

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code, $priceList = NULL)
  {
    $priceList = empty($priceList) ? $this->$price_list : $priceList;

    $rs = $this->ms
    ->select('OITM.ItemCode AS code, OITM.ItemName AS name')
    ->select('OITM.SalUnitMsr AS uom, ITM1.Price AS price')
    ->select('OITM.VatGourpSa AS taxCode, OITM.UserText AS detail')
    ->select('OVTG.Rate AS taxRate')
    ->select('OITM.DfltWH AS dfWhsCode, OITM.WarrntTmpl')
    ->select('OITM.LastPurPrc AS last_price')
    ->from('OITM')
    ->join('ITM1', 'ITM1.ItemCode = OITM.ItemCode', 'left')
    ->join('OVTG', 'OVTG.Code = OITM.VatGourpSa', 'left')
    ->where('OITM.ItemCode', $code)
    ->where('ITM1.PriceList', $priceList)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_item_list($group = NULL)
  {
    $this->ms->select('ItemCode AS code, ItemName AS name, SalUnitMsr AS UoM');

    if(!empty($group) && is_array($group))
    {
      $this->ms->where_in('ItmsGrpCod', $group);
    }

    $this->ms->order_by('ItemCode', 'ASC');

    $rs = $this->ms->get('OITM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_items_by_range($pdFrom, $pdTo, $group = NULL)
  {
    $this->ms
    ->select('ItemCode AS code, ItemName AS name, SalUnitMsr AS UoM')
    ->where('ItemCode >=', $pdFrom)
    ->where('ItemCode <=', $pdTo);

    if(!empty($group) && is_array($group))
    {
      $this->ms->where_in('ItmsGrpCod', $group);
    }

    $this->ms->order_by('ItemCode', 'ASC');

    $rs = $this->ms->get('OITM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return  NULL;
  }



  public function get_item_group_list()
  {
    $rs = $this->ms
    ->select('ItmsGrpCod AS code, ItmsGrpNam AS name')
    ->order_by('ItmsGrpCod', 'ASC')
    ->get('OITB');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_average_cost($code)
	{
		$rs = $this->ms
    ->select('LstEvlPric AS cost')
    ->where('ItemCode', $code)
    ->get('OITM');

		if($rs->num_rows() === 1)
    {
      return $rs->row()->cost;
    }

    return NULL;
	}


  public function get_item_cost($code)
	{
		$rs = $this->ms
    ->select('Price AS cost')
    ->where('ItemCode', $code)
    ->where('PriceList', $this->cost_list)
    ->get('ITM1');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->cost;
    }

    return 0;
	}

} //---- End class

 ?>
