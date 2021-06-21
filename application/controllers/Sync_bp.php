<?php
class Sync_bp extends CI_Controller
{
  public $ms;
  public $mc;
  public $limit = 100;

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
            $CardCode = $this->get_sap_card_code($ds->code);
            if(!empty($CardCode))
            {
              $arr = array(
                'CardCode' => $CardCode,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2,  //-- เข้า SAP แล้ว
                'Message' => NULL
              );

              $this->update($ds->code, $arr);
              $update++;
            }
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
      'sync_item' => 'BP',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);
  }


  private function getSyncList()
  {
    $rs = $this->db
    ->select('code')
    ->where_in('Status', array(1,3))
    ->order_by('code', 'DESC')
    ->limit($this->limit)
    ->get('customer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function get_temp_status($code)
  {
    $rs = $this->mc->select('F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OCRD');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  private function get_sap_card_code($code)
  {
    $rs = $this->ms
    ->select('CardCode')
    ->where('U_WEBORDER', $code)
    ->get('OCRD');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->CardCode;
    }

    return NULL;
  }

  private function update($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update('customer', $ds);
  }




} //--- end class

 ?>
