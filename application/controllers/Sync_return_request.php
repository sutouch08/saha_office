<?php
class Sync_return_request extends CI_Controller
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
    $this->load->model('return_request_model');

    $limit = getConfig('SYNC_LIMIT');
    $this->limit = $limit > 0 ? $limit : 100;
  }


  public function index()
  {
    $ds = $this->return_request_model->getSyncList($this->limit);

    if( ! empty($ds))
    {
      foreach($ds as $rs)
      {
        $temp = $this->return_request_model->get_temp_status($rs->code);

        if( ! empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $DocNum = $this->return_request_model->get_sap_doc_num($rs->code);

            if( ! empty($DocNum))
            {
              $arr = array(
                'DocNum' => $DocNum,
                'tempStatus' => 'S'
              );

              $this->return_request_model->update($rs->code, $arr);

              $logs = array(
                'code' => $rs->code,
                'sync_code' => 'RQ',
                'DocNum' => $DocNum,
                'status' => 1
              );

              $this->sync_data_model->add_logs($logs);
            }
            else
            {
              $arr = array(
                'DocNum' => NULL,
                'tempStatus' => 'N'
              );

              $this->return_request_model->update($rs->code, $arr);

              $logs = array(
                'code' => $rs->code,
                'sync_code' => 'RQ',
                'DocNum' => NULL,
                'status' => 3,
                'message' => 'Mark as success in Temp But not found in SAP'
              );

              $this->sync_data_model->add_logs($logs);
            }
          }

          if($temp->F_Sap === 'N')
          {
            $arr = array(
              'DocNum' => NULL,
              'tempStatus' => 'F'
            );

            $this->return_request_model->update($rs->code, $arr);

            $logs = array(
              'code' => $rs->code,
              'sync_code' => 'RQ',
              'DocNum' => NULL,
              'status' => 3,
              'message' => $temp->Message
            );

            $this->sync_data_model->add_logs($logs);
          }


          if($temp->F_Sap === NULL)
          {
            $logs = array(
              'code' => $rs->code,
              'sync_code' => 'RQ',
              'DocNum' => NULL,
              'status' => 2,
              'message' => 'pending'
            );

            $this->sync_data_model->add_logs($logs);
          }
        }
        else
        {
          $arr = array(
            'DocNum' => NULL,
            'tempStatus' => 'N'
          );

          $this->return_request_model->update($rs->code, $arr);

          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'RQ',
            'DocNum' => NULL,
            'status' => 3,
            'message' => 'Order not in temp'
          );

          $this->sync_data_model->add_logs($logs);
        }
      }
    }
    else
    {
      $arr = array(
        'code' => 'Sync',
        'sync_code' => 'RQ',
        'DocNum' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }
  }

} //--- end class

 ?>
