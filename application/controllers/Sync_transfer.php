<?php
class Sync_transfer extends CI_Controller
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
    $this->load->model('transfer_model');
    $limit = getConfig('SYNC_LIMIT');
    $this->limit = $limit > 0 ? $limit : 100;
  }


  public function index()
  {
    $list = $this->transfer_model->getSyncList($this->limit);

    if(!empty($list))
    {
      foreach($list as $ds)
      {
        $temp = $this->transfer_model->get_temp_status($ds->code);
        if(!empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $DocNum = $this->transfer_model->get_sap_doc_num($ds->code);

            if(!empty($DocNum))
            {
              $arr = array(
                'DocNum' => $DocNum,
                'SapDate' => $temp->F_SapDate,
                'Status' => 'Y',  //-- เข้า SAP แล้ว
                'message' => NULL
              );

              $this->transfer_model->update_by_code($ds->code, $arr);
              $this->close_pick_rows($ds->code);

              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'TR',
                'DocNum' => $DocNum,
                'status' => 1
              );

              $this->sync_data_model->add_logs($logs);
            }
            else
            {
              $arr = array(
                'Status' => 'F', //--- error
                'message' => "Mark as success in Temp But not found in SAP"
              );

              $this->transfer_model->update_by_code($ds->code, $arr);

              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'TR',
                'DocNum' => NULL,
                'status' => 3,
                'message' => "Mark as success in Temp But not found in SAP"
              );

              $this->sync_data_model->add_logs($logs);
            }
          }
          else
          {
            if($temp->F_Sap === 'N')
            {
              $arr = array(
                'Status' => 'F', //--- error
                'message' => $temp->Message
              );

              $this->transfer_model->update_by_code($ds->code, $arr);

              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'TR',
                'DocNum' => NULL,
                'status' => 3,
                'message' => $temp->Message
              );

              $this->sync_data_model->add_logs($logs);
            }

            if($temp->F_Sap === NULL)
            {
              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'TR',
                'DocNum' => NULL,
                'status' => 2,
                'message' => "Pending"
              );

              $this->sync_data_model->add_logs($logs);
            }
          }
        }
        else
        {
          $logs = array(
            'code' => $ds->code,
            'sync_code' => 'TR',
            'DocNum' => NULL,
            'status' => 3,
            'message' => 'Document not in temp'
          );

          $this->sync_data_model->add_logs($logs);

        } //--- end temp
      } //--- endforeach
    }
    else
    {
      $arr = array(
        'code' => 'Sync',
        'sync_code' => 'TR',
        'DocNum' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }

  }



  private function close_pick_rows($code)
  {
    $details = $this->transfer_model->get_details_by_code($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $this->db
        ->set('LineStatus', 'C')
        ->where('AbsEntry', $rs->pick_list_id)
        ->where('OrderCode', $rs->orderCode)
        ->where('ItemCode', $rs->ItemCode)
        ->update('pick_row');
      }
    }
  }




} //--- end class

 ?>
