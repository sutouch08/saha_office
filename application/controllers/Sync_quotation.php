<?php
class Sync_quotation extends CI_Controller
{
  public $ms;
  public $mc;
  public $limit = 50;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->load->model('sync_data_model');
  }


  public function index()
  {
    $list = $this->getSyncList();
    $count = 0;
    $update = 0;

    if(!empty($list))
    {
      foreach($list as $ds)
      {
        $count++;
        $temp = $this->get_temp_status($ds->code);
        if(!empty($temp))
        {

          if($temp->F_Sap === 'Y')
          {
            $DocNum = $this->get_sap_doc_num($ds->code);

            if(!empty($DocNum))
            {
              $arr = array(
                'DocNum' => $DocNum,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2,  //-- เข้า SAP แล้ว
                'SapStatus' => 'O',
                'Message' => NULL
              );

              $this->update($ds->code, $arr);
              $update++;
            }
            // else
            // {
            //   $draft_code = $this->get_sap_draft_code($ds->code);
            //   if(!empty($draft_code))
            //   {
            //     $arr = array(
            //       'sap_date' => $temp->F_SapDate,
            //       'Status' => 4,  //-- เข้า Darft ใน SAP แล้ว
            //       'Message' => NULL
            //     );
            //
            //     $this->update($ds->code, $arr);
            //     $update++;
            //   }
            // }
          }
          else
          {
            if($temp->F_Sap === 'N')
            {
              $arr = array(
                'Status' => 3, //--- error
                'Message' => $temp->Message
              );

              $this->update($ds->code, $arr);
            }
          }
        } //--- end temp
      } //--- endforeach
    }


    //---- add logs
    $logs = array(
      'sync_item' => 'SQ',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

    $this->syncSoNo();
  }


  private function getSyncList()
  {
    $rs = $this->db
    ->select('code')
    ->where_in('Status', array(1, 3, 4))
    ->order_by('code', 'ASC')
    ->limit($this->limit)
    ->get('quotation');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function get_temp_status($code)
  {
    $rs = $this->mc->select('F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OQUT');
    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  private function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_WEBORDER', $code)
    ->get('OQUT');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }

  private function get_sap_draft_code($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('ObjType', 23)
    ->where('U_WEBORDER', $code)
    ->get('ODRF');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  private function update($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update('quotation', $ds);
  }



  public function syncSoNo()
  {
    $list = $this->getSoSyncList();

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $SoNo = $this->getSoNo($rs->DocNum);
        if(!empty($SoNo))
        {
          $arr = array(
            'SoNo' => $SoNo
          );

          $this->update($rs->code, $arr);
        }
      }
    }
  }


  private function getSoSyncList()
  {
    $rs = $this->db
    ->select('code, DocNum')
    ->where('DocNum IS NOT NULL', NULL, FALSE)
    ->where('SoNo IS NULL', NULL, FALSE)
    ->where('Status', 2)
    ->group_start()
    ->where('SapStatus !=', 'E')
    ->or_where('SapStatus IS NULL', NULL, FALSE)
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit($this->limit)
    ->get('quotation');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
    
  }


  private function getSoNo($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('Ref1', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocNum', 'DESC')
    ->get('ORDR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }



} //--- end class

 ?>
