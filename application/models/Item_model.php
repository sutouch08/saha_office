<?php
class Item_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($ItemCode)
  {
    $rs = $this->ms
    ->select('OITM.ItemCode AS code, OITM.ItemName AS name, OITM.UgpEntry, OITM.IUoMEntry')
    ->select('OITM.VatGourpSa AS taxCode, OITM.UserText AS detail, OITM.ValidComm')
    ->select('OVTG.Rate AS taxRate')
    ->select('OITM.DfltWH AS dfWhsCode')
    ->from('OITM')
    ->join('OVTG', 'OVTG.Code = OITM.VatGourpSa', 'left')
    ->where('OITM.ItemCode', $ItemCode)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function price_list($ItemCode, $PriceList)
  {
    $rs = $this->ms->select('Price, UomEntry')->where('ItemCode', $ItemCode)->where('PriceList', $PriceList)->get('ITM1');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_special_price($ItemCode, $CardCode, $PriceList)
  {
    $rs = $this->ms
    ->select('ITM1.Price AS Price')
    ->select('OSPP.Price AS PriceAfDisc')
    ->select('OSPP.Discount')
    ->select('ITM1.UomEntry')
    ->from('OSPP')
    ->join('ITM1', 'OSPP.ItemCode = ITM1.ItemCode AND ITM1.PriceList = OSPP.ListNum', 'left')
    ->where('OSPP.ItemCode', $ItemCode)
    ->where('OSPP.CardCode', $CardCode)
    ->where('OSPP.ListNum', $PriceList)
    ->where('OSPP.Valid', 'Y')
    ->group_start()
    ->where('OSPP.ValidFrom <=', from_date())
    ->or_where('OSPP.ValidFrom IS NULL', NULL, FALSE)
    ->group_end()
    ->group_start()
    ->where('OSPP.ValidTo >=', to_date())
    ->or_where('OSPP.ValidTo IS NULL', NULL, FALSE)
    ->group_end()
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_uom_list($UgpEntry)
  {
    $rs = $this->ms
    ->select("UGP1.UomEntry, UGP1.BaseQty, OUOM.UomCode, OUOM.UomName")
    ->from('UGP1')
    ->join('OUOM', 'UGP1.UomEntry = OUOM.UomEntry', 'left')
    ->where('UGP1.UgpEntry', $UgpEntry)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_uom_list_by_item_code($ItemCode)
  {
    $rs = $this->ms
    ->select('UGP1.UomEntry, UGP1.BaseQty, OUOM.UomCode, OUOM.UomName')
    ->from('UGP1')
    ->join('OITM', 'OITM.UgpEntry = UGP1.UgpEntry', 'left')
    ->join('OUOM', 'UGP1.UomEntry = OUOM.UomEntry', 'left')
    ->where('OITM.ItemCode', $ItemCode)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_base_qty($itemCode, $UomEntry)
  {
    $rs = $this->ms
    ->select('BaseQty')
    ->from('OITM')
    ->join('UGP1', 'OITM.UgpEntry = UGP1.UgpEntry', 'left')
    ->where('OITM.ItemCode', $itemCode)
    ->where('UGP1.UomEntry', $UomEntry)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->BaseQty;
    }

    return 1;
  }



  public function get_uom_name($UomCode)
  {
    $qr = "SELECT UomName AS name FROM OUOM WHERE UomCode = N'{$UomCode}'";
    $rs = $this->ms->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_uom_id($UomCode)
  {
    $qr = "SELECT UomEntry FROM OUOM WHERE UomCode = N'{$UomCode}'";
    $rs = $this->ms->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->UomEntry;
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



  public function last_sell_price($itemCode, $cardCode, $uom)
  {
    if($uom !== NULL)
    {
      $qr = "SELECT TOP(1) Price
              FROM OINV A
              INNER JOIN INV1 B ON A.DocEntry = B.DocEntry
              WHERE B.ItemCode = '{$itemCode}'
              AND A.CardCode = '{$cardCode}'
              AND B.UomEntry = {$uom}
              ORDER BY A.DocDate DESC";
      $rs = $this->ms->query($qr);

      if($rs->num_rows() === 1)
      {
        return $rs->row()->Price;
      }
    }

    return 0;
  }


  public function getItemBarcode($ItemCode)
  {
    $rs = $this->ms->select('CodeBars AS barcode')->where('ItemCode', $ItemCode)->get('OITM');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->barcode;
    }

    return NULL;
  }


  public function getItemByBarcode($barcode)
  {
    $rs = $this->ms
    ->select('ItemCode')
    ->where('CodeBars', $barcode)
    ->or_where('ItemCode', $barcode)
    ->get('OITM');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->ItemCode;
    }

    return NULL;
  }


  public function get_barcode_uom($ItemCode, $UomEntry)
  {
    $rs = $this->ms
    ->select('BcdCode AS barcode')
    ->where('ItemCode', $ItemCode)
    ->where('UomEntry', $UomEntry)
    ->get('OBCD');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->barcode;
    }

    return NULL;
  }


  public function get_item_code_uom_by_barcode($barcode)
  {
    $rs = $this->ms
    ->select('ItemCode, UomEntry')
    ->where('BcdCode', $barcode)
    ->get('OBCD');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

} //---- End class

 ?>
