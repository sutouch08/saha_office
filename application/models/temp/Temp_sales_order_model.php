<?php
class Temp_sales_order_model extends CI_Model
{
  private $tb = "ORDR";
  private $td = "RDR1";

  public function __construct()
  {
    parent::__construct();
  }

	public function get($docEntry)
	{
		$rs = $this->mc->where('DocEntry', $docEntry)->get($this->tb);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->mc->like('U_WEBORDER', $ds['code']);
    }

    if(!empty($ds['supplier']))
    {
      $this->mc->group_start();
      $this->mc->like('CardCode', $ds['supplier']);
      $this->mc->or_like('CardName', $ds['supplier']);
      $this->mc->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->mc->where('DocDate >=', from_date($ds['from_date']));
      $this->mc->where('DocDate <=', to_date($ds['to_date']));
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] === 'Y')
      {
        $this->mc->where('F_Sap', 'Y');
      }
      else if($ds['status'] === 'N')
      {
        $this->mc->where('F_Sap IS NULL', NULL, FALSE);
      }
      else if($ds['status'] === 'E')
      {
        $this->mc->where('F_Sap', 'N');
      }
    }

    return $this->mc->count_all_results($this->tb);
  }



  public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
  {
    $this->mc
    ->select('DocEntry, U_WEBORDER, DocDate, TaxDate, CardCode, CardName')
    ->select('F_Web, F_WebDate')
    ->select('F_Sap, F_SapDate')
    ->select('Message');

    if(!empty($ds['code']))
    {
      $this->mc->like('U_WEBORDER', $ds['code']);
    }

    if(!empty($ds['supplier']))
    {
      $this->mc->group_start();
      $this->mc->like('CardCode', $ds['supplier']);
      $this->mc->or_like('CardName', $ds['supplier']);
      $this->mc->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->mc->where('DocDate >=', from_date($ds['from_date']));
      $this->mc->where('DocDate <=', to_date($ds['to_date']));
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] === 'Y')
      {
        $this->mc->where('F_Sap', 'Y');
      }
      else if($ds['status'] === 'N')
      {
        $this->mc->where('F_Sap IS NULL', NULL, FALSE);
      }
      else if($ds['status'] === 'E')
      {
        $this->mc->where('F_Sap', 'N');
      }
    }

    $this->mc->order_by('DocDate', 'DESC')->order_by('U_WEBORDER', 'DESC');

    if(!empty($perpage))
    {
      $this->mc->limit($perpage, $offset);
    }

    $rs = $this->mc->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_detail($docEntry)
  {
    $rs = $this->mc
    ->select('U_WEBORDER, LineNum, ItemCode, Dscription, Quantity, UomCode')
    ->select('Price, PriceBefDi, PriceAfVAT, DiscPrcnt, VatSum, LineTotal, Currency, Rate')
    ->select('VatGroup, VatPrcnt, GTotal, WhsCode, SlpCode, Text, FreeTxt')
    ->select('OcrCode, OcrCode2, OwnerCode, U_DISWEB, U_DISCEX, U_SO_LSALEPRICE')
    ->where('DocEntry', $docEntry)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function removeTemp($docEntry)
	{
    $sc = TRUE;
		$this->mc->trans_begin();

    if(! $this->mc->where('DocEntry', $docEntry)->delete($this->td))
    {
      $sc = FALSE;
    }
    else
    {
      if( ! $this->mc->where('DocEntry', $docEntry)->delete($this->tb))
      {
        $sc = FALSE;
      }
    }


		if($sc === TRUE)
		{
			$this->mc->trans_commit();
		}
		else
		{
			$this->mc->trans_rollback();
		}

		return $sc;
	}


  public function setStatus($docEntry, $status = 'Y')
  {
    return $this->mc->set('F_Sap', 'Y')->where('DocEntry', $docEntry)->update($this->tb);
  }


} //--- end model

?>
