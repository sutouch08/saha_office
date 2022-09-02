<?php
class Quotation_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_temp_data($code) //-- web order
  {
    $rs = $this->mc
    ->select('U_WEBORDER, CardCode, CardName, F_WebDate, F_SapDate, F_Sap, Message')
    ->where('U_WEBORDER', $code)
    ->get('OQUT');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_status($code)
  {
    $rs = $this->mc->select('F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OQUT');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_non_sq_code($limit = 20)
  {
    $rs = $this->db->select('code, BeginStr')->where('DocNum IS NULL', NULL, FALSE)->limit($limit)->get('quotation');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms->select('DocNum')->where('U_WEBORDER', $code)->where('CANCELED', 'N')->get('OQUT');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('quotation');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('quotation_code', $code)->get('quotation_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_details($code)
  {
    return $this->db->where('quotation_code', $code)->delete('quotation_detail');
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('quotation', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('quotation_detail', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);

      return $this->db->update('quotation', $ds);
    }

    return FALSE;
  }

  //---- get sum of all line total (after discount)
  public function sum_line_total($code)
  {
    $rs = $this->db->select_sum('LineTotal')->where('quotation_code', $code)->get('quotation_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->LineTotal;
    }

    return 0;
  }



  function count_rows(array $ds = array())
  {

    if(!empty($ds['WebCode']))
    {
      $this->db->like('code', $ds['WebCode']);
    }

    if(!empty($ds['CardCode']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['CardCode']);
      $this->db->or_like('CardName', $ds['CardCode']);
      $this->db->group_end();
    }


    if(!empty($ds['SaleName']))
    {
      $sale_in = $this->get_sale_in($ds['SaleName']);

      $this->db->where_in('SlpCode', $sale_in);
    }

    if(!empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
    }


    if(!empty($ds['SoNo']))
    {
      $this->db->like('SoNo', $ds['SoNo']);
    }


    if(!empty($ds['CustRef']))
    {
      $this->db->like('NumAtCard', $ds['CustRef']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=',to_date($ds['toDate']));
    }

    if($ds['Approved'] !== 'all')
    {
      $this->db->where('Approved', $ds['Approved']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if($ds['SapStatus'] !== 'all')
    {
      $this->db->where('SapStatus', $ds['SapStatus']);
    }

    if(!$this->isAdmin && !$this->isSuperAdmin )
    {
      if($this->isLead)
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
      else
      {
        $this->db->where('user_id', $this->user->id);
      }
    }

    return $this->db->count_all_results('quotation');
  }





  function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if(!empty($ds['WebCode']))
    {
      $this->db->like('code', $ds['WebCode']);
    }

    if(!empty($ds['CardCode']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['CardCode']);
      $this->db->or_like('CardName', $ds['CardCode']);
      $this->db->group_end();
    }


    if(!empty($ds['SaleName']))
    {
      $sale_in = $this->get_sale_in($ds['SaleName']);

      $this->db->where_in('SlpCode', $sale_in);
    }

    if(!empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
    }

    if(!empty($ds['SoNo']))
    {
      $this->db->like('SoNo', $ds['SoNo']);
    }

    if(!empty($ds['CustRef']))
    {
      $this->db->like('NumAtCard', $ds['CustRef']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=',to_date($ds['toDate']));
    }

    if($ds['Approved'] !== 'all')
    {
      $this->db->where('Approved', $ds['Approved']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if($ds['SapStatus'] !== 'all')
    {
      $this->db->where('SapStatus', $ds['SapStatus']);
    }


    if(!$this->isAdmin && !$this->isSuperAdmin)
    {
      if($this->isLead === TRUE)
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
      else
      {
        $this->db->where('user_id', $this->user->id);
      }
    }


    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get('quotation');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_sale_in($txt)
  {
    $qr = "SELECT SlpCode FROM OSLP WHERE SlpName LIKE N'%{$this->ms->escape_str($txt)}%'";
    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      $arr = array();
      foreach($qs->result() as $rs)
      {
        $arr[] = $rs->SlpCode;
      }

      return $arr;
    }

    return array('abcdefghijklmnopqrstuvwxyz');
  }


  public function get_all_currency()
  {
    $rs = $this->ms->select('CurrCode AS code')->get('OCRN');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_open_project()
  {
    $rs = $this->ms
    ->select('PrjCode AS code, PrjName AS name')
    ->where('Active', 'Y')
    ->get('OPRJ');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_projectt_name($code)
  {
    $rs = $this->ms->select('PrjName AS name')->where('PrjCode', $code)->get('OPRJ');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_all_sale_type()
  {
    $rs = $this->ms
    ->select('OcrCode AS code, OcrName AS name')
    ->where('Active', 'Y')
    ->where('DimCode', 3)
    ->get('OOCR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_sale_type_name($code)
  {
    $rs = $this->ms->select('OcrName AS name')->where('DimCode', 3)->where('OcrCode', $code)->get('OOCR');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_department_name($code)
  {
    $rs = $this->ms->select('OcrName AS name')->where('DimCode', 1)->where('OcrCode', $code)->get('OOCR');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_division_name($code)
  {
    $rs = $this->ms->select('OcrName AS name')->where('DimCode', 2)->where('OcrCode', $code)->get('OOCR');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_series($month = NULL)
  {
    $month = empty($month) ? date('Y-m') : date('Y-m', strtotime($month));

    $rs = $this->ms
    ->select('Series AS code, SeriesName AS name, BeginStr AS prefix')
    ->where('ObjectCode', 23)
    ->where('Indicator', $month)
    ->order_by('Series', 'ASC')
    ->get('NNM1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;

    // $ds = new stdClass();
    // $ds->code = "880";
    // $ds->name = "SQ2111";
    // $ds->prefix = "SQ";
    //
    // return array($ds);
  }


  public function get_series_name($id)
  {
    $rs = $this->ms->select('SeriesName as name')->where('Series', $id)->get('NNM1');

    if($rs->num_rows() === 1)
    {
      return  $rs->row()->name;
    }

    return NULL;
  }


  public function get_prefix($series)
  {
    $rs = $this->ms->select('BeginStr AS prefix')->where('ObjectCode', 23)->where('Series', $series)->get('NNM1');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->prefix;
    }

    return NULL;
  }


  public function get_all_oum()
  {
    $rs = $this->ms
    ->select('UomCode AS code, UomName AS name')
    ->where('Locked', 'N')
    ->get('OUOM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_tax_code()
  {
    $rs = $this->ms
    ->select('Code AS code, Name AS name, Rate AS rate')
    ->where('Category', 'O')
    ->where('Inactive', 'N')
    ->get('OVTG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_whs()
  {
    $rs = $this->ms
    ->select('WhsCode AS code, WhsName AS name')
    ->get('OWHS');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('quotation');

    return $rs->row()->code;
  }


  public function get_max_line_disc($code)
  {
    $rs = $this->db->select_max('DiscPrcnt')->where('quotation_code', $code)->get('quotation_detail');

    if($rs->num_rows() === 1)
    {
      return floatval($rs->row()->DiscPrcnt);
    }

    return 0;
  }


  public function can_approve($uname, $sale_team, $disc)
  {
    $rs = $this->db
    ->where('uname', $uname)
    ->group_start()
    ->where('sale_team', $sale_team)
    ->or_where('sale_team', 'all')
    ->group_end()
    ->where('max_discount >=',$disc, FALSE)
    ->where('status', 1)
    ->count_all_results('quotation_approver');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_rule($sale_team, $discount)
  {
    $this->db->where('active', 1)->where('min_disc <=', $discount, FALSE);

    if(!empty($sale_team) && $sale_team !== 'all')
    {
      $this->db->where('sale_team', $sale_team);
    }

    $rs = $this->db->get('approve_rule');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add_sap_quotation(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OQUT', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function add_sap_quotation_row(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('QUT1', $ds);
    }

    return FALSE;
  }


  public function add_sap_quotation_text_row(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('QUT10', $ds);
    }

    return FALSE;
  }


  public function is_sap_exists_code($code)
  {
    $rs = $this->ms
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->count_all_results('OQUT');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_sap_exists_draft($code)
  {
    $rs = $this->ms
    ->where('ObjType', 23)
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->count_all_results('ODRF');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_sap_quotation($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->get('OQUT');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_quotation_draft($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('ObjType', 23)
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->get('ODRF');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_temp_quotations($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_WEBORDER', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OQUT');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function sap_exists_quotation_details($code)
  {
    $row = $this->mc->where('U_WEBORDER', $code)->count_all_results('QUT1');
    if($row > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function sap_exists_quotation_text_row($code)
  {
    $row = $this->mc->where('U_WEBORDER', $code)->count_all_results('QUT10');
    if($row > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_quotation_temp_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('QUT10');
    $this->mc->where('DocEntry', $docEntry)->delete('QUT1');
    $this->mc->where('DocEntry', $docEntry)->delete('OQUT');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }



  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_temp_exists_data($code)
  {
    $this->mc->trans_start();
    $this->mc->where('U_WEBORDER', $code)->delete('QUT10');
    $this->mc->where('U_WEBORDER', $code)->delete('QUT1');
    $this->mc->where('U_WEBORDER', $code)->delete('OQUT');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }




  public function getSyncList($limit = 100)
  {
    $syncDays = 30; //--- sync only last $syncdays
    $from_date = from_date(date('Y-m-d', strtotime("-{$syncDays} days")));
    $rs = $this->db
    ->select('code')
    ->where_in('Status', array(1, 3))
    ->where('date_add >', "{$from_date}")
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get('quotation');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function getSoSyncList($limit = 100)
  {
    $syncDays = 15; //--- sync only last $syncdays
    $from_date = from_date(date('Y-m-d', strtotime("-{$syncDays} days")));
    $rs = $this->db
    ->select('code, DocNum')
    ->where('DocNum IS NOT NULL', NULL, FALSE)
    ->where('SoNo IS NULL', NULL, FALSE)
    ->where('Status', 2)
    ->where('date_add >', "{$from_date}")
    ->group_start()
    ->where('SapStatus !=', 'E')
    ->or_where('SapStatus IS NULL', NULL, FALSE)
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get('quotation');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;

  }


  public function getSoNo($code)
  {
    $rs = $this->ms
    ->distinct()
    ->select('ORDR.DocNum')
    ->from('RDR1')
    ->join('ORDR', 'RDR1.DocEntry = ORDR.DocEntry')
    ->where('RDR1.BaseRef', $code)
    ->where('ORDR.CANCELED', 'N')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }



} //---- End class

 ?>
