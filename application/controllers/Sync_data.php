<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_data extends CI_Controller
{
  public $title = 'Sync Data';
	public $menu_code = '';
	public $menu_group_code = '';
	public $pm;
  public $limit = 100;
  public $date;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->load->model('sync_data_model');
    $this->date = date('Y-d-m H:i:s');
  }


  public function index()
  {
    $this->load->view('sync_data_view');
  }


  public function clear_logs($days = 7)
  {
    $this->load->view('clear_old_logs_view', array('days' => $days));
  }


  public function clear_old_logs($days)
  {
    $sc = $this->sync_data_model->clear_old_logs($days);

    if($sc)
    {
      echo 'done';
    }
    else
    {
      echo 'error';
    }
  }



  public function syncQuotationCode()
  {
    $this->load->model('quotation_model');
    $ds = $this->quotation_model->get_non_sq_code($this->limit);
    $count = 0;
    $update = 0;
    $message = 'done';
    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $temp = $this->quotation_model->get_temp_status($rs->code);
        if(!empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $sap = $this->quotation_model->get_sap_doc_num($rs->code);

            if(!empty($sap))
            {
              $arr = array(
                'DocNum' => $sap->DocNum,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2, //--- เข้า SAP แล้ว
                'Message' => NULL
              );

              $this->quotation_model->update($rs->code, $arr);
              $update++;
            }
          }
          else
          {
            if($temp->F_Sap === 'N')
            {
              $arr = array(
                'Status' => 3,
                'Message' => $temp->Message
              );

              $this->quotation_model->update($rs->code, $arr);
            }
          }
        }
      }
    }
    else
    {
      $message = 'not found';
    }

    $logs = array(
      'sync_item' => 'SQ',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

    echo $count.' | '.$update.' | '.$message;
  }



  public function syncCustomer()
  {
    $this->load->model('customers_model');
    $ds = $this->customers_model->get_non_sap_code($this->limit);
    $count = 0;
    $update = 0;
    $message = 'done';
    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $temp = $this->customers_model->get_temp_status($rs->code);
        if(!empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $sap = $this->customers_model->get_sap_card_code($rs->code);

            if(!empty($sap))
            {
              $arr = array(
                'CardCode' => $sap->CardCode,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2, //--- เข้า SAP แล้ว
                'Message' => NULL
              );

              $this->customers_model->update($rs->code, $arr);
              $update++;
            }
          }
          else
          {
            if($temp->F_Sap === 'N')
            {
              $arr = array(
                'Status' => 3,
                'Message' => $temp->Message
              );

              $this->customers_model->update($rs->code, $arr);
            }
          }
        }
      }
    }
    else
    {
      $message = 'not found';
    }

    $logs = array(
      'sync_item' => 'BP',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

    echo $count.' | '.$update.' | '.$message;
  }



} //--- end class

 ?>
