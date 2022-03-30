<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_data extends PS_Controller
{
  public $title = 'Sync Logs';
	public $menu_code = 'SYNC_LOGS';
	public $menu_group_code = 'ADMIN';
	public $pm;
  public $limit = 100;
  public $date;

  public function __construct()
  {
    parent::__construct();

    $this->home = base_url().'sync_data';
    $this->load->model('sync_data_model');
    $this->date = date('Y-d-m H:i:s');
    $limit = getConfig('SYNC_LIMIT');
    $this->limit = $limit > 0 ? $limit : 100;
  }


  public function index()
  {
    if($this->isAdmin OR $this->isSuperAdmin)
    {

      $filter = array(
        'code' => get_filter('code', 'sync_code', ''),
        'docType' => get_filter('docType', 'docType', 'all'),
        'docNum' => get_filter('docNum', 'docNum', ''),
        'status' => get_filter('status', 'sync_status', 'all'),
        'from_date' => get_filter('from_date', 'sync_from_date', ''),
        'to_date' => get_filter('to_date', 'sync_to_date', '')
      );

      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_filter('set_rows', 'rows', 20);
  		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
  		if($perpage > 300)
  		{
  			$perpage = get_filter('rows', 'rows', 300);
  		}

  		$segment = 3; //-- url segment
  		$rows = $this->sync_data_model->count_rows($filter);

  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

  		$rs = $this->sync_data_model->get_list($filter, $perpage, $this->uri->segment($segment));

      $filter['data'] = $rs;

  		$this->pagination->initialize($init);
      $this->load->view('sync_data_view', $filter);
    }
    else
    {
      $this->deny_page();
    }
  }


  public function clear_logs($days = NULL)
  {
    $days = empty($days) ? getConfig('KEEP_SYNC_LOGS') : $days;

    if($this->sync_data_model->clear_logs($days))
    {
      $logs = array(
        'code' => 'Clear logs',
        'sync_code' => 'Logs',
        'DocNum' => NULL,
        'status' => 1
      );

      $this->sync_data_model->add_logs($logs);

      echo 'success';
    }
    else
    {
      $logs = array(
        'code' => 'Clear logs',
        'sync_code' => 'Logs',
        'DocNum' => NULL,
        'status' => 3,
        'message' => "Clear Sync Logs Failed"
      );

      $this->sync_data_model->add_logs($logs);

      echo "Clear Sync Logs Failed";
    }
  }


  public function clear_all_logs()
  {
    if($this->sync_data_model->clear_all_logs())
    {
      echo "success";
    }
    else
    {
      echo "Clear Sync Logs Failed";
    }
  }



  public function clear_sync_logs()
  {
    $days = getConfig('KEEP_SYNC_LOGS');

    $days = empty($days) ? 7 : $days;

    $this->sync_data_model->clear_logs($days);

    $logs = array(
      'code' => 'Clear logs',
      'sync_code' => 'Logs',
      'DocNum' => NULL,
      'status' => 1
    );

    $this->sync_data_model->add_logs($logs);
  }



  public function syncData()
  {
    $docType = trim($this->input->post('docType'));

    switch ($docType) {
      case 'SQ':
        $this->syncSQCode();
        break;
      case 'SO' :
        $this->syncSOCode();
        break;
      case 'TR' :
        $this->syncTRCode();
        break;
      case 'MV' :
        $this->syncMVCode();
        break;
      default:
        $this->syncSOCode();
        break;
    }

    echo "success";
  }



  public function syncSOCode()
  {
    $this->load->model('sales_order_model');

    $ds = $this->sales_order_model->getSyncList($this->limit);

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $temp = $this->sales_order_model->get_temp_status($rs->code);

        if(!empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $DocNum = $this->sales_order_model->get_sap_doc_num($rs->code);

            if(!empty($DocNum))
            {
              $arr = array(
                'DocNum' => $DocNum,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2,  //-- เข้า SAP แล้ว
                'SapStatus' => 'O',
                'Message' => NULL
              );

              $this->sales_order_model->update($rs->code, $arr);

              $logs = array(
                'code' => $rs->code,
                'sync_code' => 'SO',
                'DocNum' => $DocNum,
                'status' => 1
              );

              $this->sync_data_model->add_logs($logs);
            }
            else
            {
              $arr = array(
                'Status' => 3, //--- error
                'Message' => 'Mark as success in Temp But not found in SAP'
              );

              $this->sales_order_model->update($ds->code, $arr);
              
              $logs = array(
                'code' => $rs->code,
                'sync_code' => 'SO',
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
              'Status' => 3,
              'Message' => $temp->Message
            );

            $this->sales_order_model->update($rs->code, $arr);

            $logs = array(
              'code' => $rs->code,
              'sync_code' => 'SO',
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
              'sync_code' => 'SO',
              'DocNum' => NULL,
              'status' => 2,
              'message' => 'pending'
            );

            $this->sync_data_model->add_logs($logs);
          }
        }
        else
        {
          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'SO',
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
        'sync_code' => 'SO',
        'DocNum' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }

  }



  public function syncSQCode()
  {
    $this->load->model('quotation_model');

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
              'message' => 'pending'
            );

            $this->sync_data_model->add_logs($logs);
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
    //$this->load->model('quotation_model');

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



  public function syncTRCode()
  {
    $this->load->model('transfer_model');
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
                'message' => 'Mark as success in Temp But not found in SAP'
              );

              $this->sync_data_model->add_logs($logs);
            }
          }

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
              'message' => 'pending'
            );

            $this->sync_data_model->add_logs($logs);
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
    //$this->load->model('transfer_model');
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



  public function clear_filter()
  {
    $filter = array(
      'sync_code',
      'docType', 'docNum',
      'sync_status',
      'sync_from_date',
      'sync_to_date'
    );

    clear_filter($filter);

    echo 'done';
  }


} //--- end class

 ?>
