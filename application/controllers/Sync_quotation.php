<?php
class Sync_quotation extends CI_Controller
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
    $this->load->model('quotation_model');

    $limit = getConfig('SYNC_LIMIT');
    $this->limit = $limit > 0 ? $limit : 100;
  }


  public function index()
  {
    $list = $this->quotation_model->getSyncList($this->limit);

    if(!empty($list))
    {
      foreach($list as $ds)
      {

        $temp = $this->quotation_model->get_temp_status($ds->code);
        if(!empty($temp))
        {

          if($temp->F_Sap === 'Y')
          {
            $DocNum = $this->quotation_model->get_sap_doc_num($ds->code);

            if(!empty($DocNum))
            {
              $arr = array(
                'DocNum' => $DocNum,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2,  //-- เข้า SAP แล้ว
                'SapStatus' => 'O',
                'Message' => NULL
              );

              $this->quotation_model->update($ds->code, $arr);

              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'SQ',
                'DocNum' => $DocNum,
                'status' => 1
              );

              $this->sync_data_model->add_logs($logs);
            }
            else
            {
              $arr = array(
                'Status' => 3, //--- error
                'Message' => "Mark as success in Temp But not found in SAP"
              );

              $this->quotation_model->update($ds->code, $arr);
              
              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'SQ',
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
                'Status' => 3, //--- error
                'Message' => $temp->Message
              );

              $this->quotation_model->update($ds->code, $arr);

              $logs = array(
                'code' => $ds->code,
                'sync_code' => 'SQ',
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
                'sync_code' => 'SQ',
                'DocNum' => NULL,
                'status' => 2,
                'message' => "pending"
              );

              $this->sync_data_model->add_logs($logs);
            }
          }
        }
        else
        {
          $logs = array(
            'code' => $ds->code,
            'sync_code' => 'SQ',
            'DocNum' => NULL,
            'status' => 3,
            'message' => 'Order not in temp'
          );

          $this->sync_data_model->add_logs($logs);
        } //--- end temp
      } //--- endforeach
    }
    else
    {
      $arr = array(
        'code' => 'Sync',
        'sync_code' => 'SQ',
        'DocNum' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }

    $this->syncSoNo();
  }



  public function syncSoNo()
  {
    $list = $this->quotation_model->getSoSyncList($this->limit);

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $SoNo = $this->quotation_model->getSoNo($rs->DocNum);
        if(!empty($SoNo))
        {
          $arr = array(
            'SoNo' => $SoNo
          );

          $this->quotation_model->update($rs->code, $arr);
        }
      }
    }
  }

} //--- end class

 ?>
