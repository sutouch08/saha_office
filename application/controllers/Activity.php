<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends PS_Controller
{
	public $menu_code = 'ACTIVT';
	public $menu_group_code = 'CR';
	public $title = 'Activity';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'activity';
		$this->load->model('activity_model');
		$this->load->model('customers_model');
		$this->load->helper('activity');
  }



  public function index()
  {

		$filter = array(
			'WebCode' => get_filter('WebCode', 'acWebCode', ''),
			'Activity' => get_filter('Activity', 'acActivity', 'all'),
			'Type' => get_filter('Type', 'acType', 'all'),
			'Subject' => get_filter('Subject', 'acSubject', 'all'),
			'AssignedTo' => get_filter('AssignedTo','acAssignedTo', ''),
			'Customer' => get_filter('Customer', 'acCustomer', ''),
			'StartDate' => get_filter('StartDate', 'acStartDate', ''),
			'EndDate' => get_filter('EndDate', 'acEndDate', ''),
			'Project' => get_filter('Project', 'acProject', 'all'),
			'Status' => get_filter('Status', 'acStatus', 'all'),
			'order_by' => get_filter('order_by', 'acorder_by', 'code'),
			'sort_by' => get_filter('sort_by', 'acsort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->activity_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->activity_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('activity/activity_list', $filter);
  }


	public function add_new()
	{
		$this->title = "New Activity";

		$this->load->view('activity/activity_add');
	}



	public function add()
	{
		$sc = TRUE;

    $data = json_decode(file_get_contents("php://input"));

		if(!empty($data))
		{
			$code = $this->get_new_code();

			$arr = array(
				'code' => $code,
				'Action' => $data->Action,
				'CntctType' => $data->CntctType,
				'TypeName' => $data->TypeName,
				'CntctSbjct' => $data->CntctSbjct,
				'SubjectName' => $data->SubjectName,
				'attendType' => $data->attendType,
				'AttendEmpl' => get_null($data->AttendEmpl),
				'UserName' => get_null($data->UserName),
				'AttendUser' => get_null($data->AttendUser),
				'EmpName' => get_null($data->EmpName),
				'CardCode' => get_null($data->CardCode),
				'CardName' => get_null($data->CardName),
				'CntctCode' => get_null($data->CntctCode),
				'ContactPer' => get_null($data->ContactPer),
				'Tel' => get_null($data->Tel),
				'Details' => get_null($data->Details),
				'Notes' => get_null($data->Notes),
				'Recontact' => db_date($data->Recontact),
				'BeginTime' => $data->BeginTime,
				'endDate' => db_date($data->endDate),
				'ENDTime' => $data->ENDTime,
				'Duration' => $data->Duration,
				'Priority' => $data->Priority,
				'Location' => $data->Location,
				'FIPROJECT' => get_null($data->FIPROJECT),
				'Stage' => get_null($data->Stage),
				'DocType' => $data->DocType,
				'DocNum' => get_null($data->DocNum),
				'user_id' => $this->user->id,
				'uname' => $this->user->uname,
				'sale_team' => $this->user->sale_team
			);

			if(! $this->activity_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "Insert Failed";
			}

			if($sc === TRUE)
			{
				$this->doExport($code);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

		$this->response($sc);
	}



	public function edit($code)
	{
		$data = $this->activity_model->get($code);

		if(!empty($data))
		{
			$data->project_name = !empty($data->FIPROJECT) ? $this->activity_model->get_project_name($data->FIPROJECT) : NULL;

			$this->load->view('activity/activity_edit', $data);
		}
		else
		{
			$this->load->view('page_error');
		}
	}



	public function update()
	{
		$sc = TRUE;

    $data = json_decode(file_get_contents("php://input"));

		if(!empty($data))
		{
			$arr = array(
				'Action' => $data->Action,
				'CntctType' => $data->CntctType,
				'TypeName' => $data->TypeName,
				'CntctSbjct' => $data->CntctSbjct,
				'SubjectName' => $data->SubjectName,
				'attendType' => $data->attendType,
				'AttendEmpl' => get_null($data->AttendEmpl),
				'UserName' => get_null($data->UserName),
				'AttendUser' => get_null($data->AttendUser),
				'EmpName' => get_null($data->EmpName),
				'CardCode' => get_null($data->CardCode),
				'CardName' => get_null($data->CardName),
				'ContactPer' => get_null($data->ContactPer),
				'Tel' => get_null($data->Tel),
				'Details' => get_null($data->Details),
				'Notes' => get_null($data->Notes),
				'Recontact' => db_date($data->Recontact),
				'BeginTime' => $data->BeginTime,
				'endDate' => db_date($data->endDate),
				'ENDTime' => $data->ENDTime,
				'Duration' => $data->Duration,
				'Priority' => $data->Priority,
				'Location' => $data->Location,
				'FIPROJECT' => get_null($data->FIPROJECT),
				'Stage' => get_null($data->Stage),
				'DocType' => $data->DocType,
				'DocNum' => get_null($data->DocNum),
				'user_id' => $this->user->id,
				'uname' => $this->user->uname,
				'sale_team' => $this->user->sale_team
			);

			if(! $this->activity_model->update($data->code, $arr))
			{
				$sc = FALSE;
				$this->error = "Update Failed";
			}

			if($sc === TRUE)
			{
				$this->doExport($data->code);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

		$this->response($sc);
	}


	public function delete()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if(!empty($code))
    {
      $ds = $this->activity_model->get($code);
      if(!empty($ds))
      {
        if($ds->Status != 2)
        {
          if(! $this->activity_model->is_sap_exists_code($code))
          {
            //--- remove temp data
            if(! $this->activity_model->drop_temp_exists_data($code))
            {
              $sc = FALSE;
              $this->error = "Failed : Delete Temp data not successful";
            }
            else
            {
              if(! $this->activity_model->delete($code))
              {
                $sc = FALSE;
                $this->error = "Failed : Delete Activity data failed";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid Activity Status : Document already in SAP";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Activity Status (2) : Document already in SAP";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Web Code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter : code";
    }

    $this->response($sc);
  }


	public function doExport($code)
	{
		$sc = TRUE;

		$exists = $this->activity_model->is_sap_exists_code($code);

		if(! $exists)
		{
			//--- get activity data
			$rs = $this->activity_model->get($code);

			if(!empty($rs))
			{
				//--- check temp
				$temp = $this->activity_model->is_temp_exists_data($rs->code);

				if($temp)
				{
					//---- drop temp data if exists
					$this->activity_model->drop_temp_exists_data($rs->code);
				}

				//--- export data
				$arr = array(
					'U_WEBORDER' => $rs->code,
					'Action' => $rs->Action,
					'CntctType' => $rs->CntctType,
					'CntctSbjct' => $rs->CntctSbjct,
					'AttendEmpl' => $rs->AttendEmpl,
					'AttendUser' => $rs->AttendUser,
					'CardCode' => $rs->CardCode,
					'CntctCode' => $rs->CntctCode,
					'ContactPer' => $rs->ContactPer,
					'Tel' => $rs->Tel,
					'Details' => $rs->Details,
					'Notes' => $rs->Notes,
					'Recontact' => $rs->Recontact,
					'BeginTime' => $rs->BeginTime,
					'endDate' => $rs->endDate,
					'ENDTime' => $rs->ENDTime,
					'Duration' => $rs->Duration,
					'Priority' => $rs->Priority,
					'Location' => $rs->Location,
					'FIPROJECT' => $rs->FIPROJECT,
					'DocType' => $rs->DocType,
					'DocNum' => $rs->DocNum,
					'F_Web' => 'A',
					'F_WebDate' => sap_date(now(), TRUE)
				);

				if(! $this->activity_model->add_temp($arr))
				{
					$sc = FALSE;
					$this->error = "Insert Failed";
				}


				if($sc === TRUE)
				{
					//--- set status to pending
					$status = array(
						'Status' => 1, //---- Pending
						'temp_date' => now(),
						'sap_date' => NULL,
						'Message' => NULL
					);

					$this->activity_model->update($rs->code, $status);
				}
			} //-- end if empty $rs;

		} //--- end if exists

		return $sc;
	}



	public function get_temp_data()
  {
    $code = $this->input->get('code'); //--- U_WEBORDER

    $data = $this->activity_model->get_temp_data($code);

    if(!empty($data))
    {
      $arr = array(
        'U_WEBORDER' => $data->U_WEBORDER,
        'CardCode' => $data->CardCode,
        'F_WebDate' => thai_date($data->F_WebDate, TRUE),
        'F_SapDate' => empty($data->F_SapDate) ? '-' : thai_date($data->F_SapDate, TRUE),
        'F_Sap' => $data->F_Sap === 'Y' ? 'Success' : ($data->F_Sap === 'N' ? 'Failed' : 'Pending'),
        'Message' => empty($data->Message) ? '' : $data->Message,
				'del_btn' => $data->F_Sap === 'Y' ? '' : 'ok'
      );

      echo json_encode($arr);
    }
    else
    {
      echo 'No data found';
    }
  }





	public function remove_temp()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $temp = $this->activity_model->get_temp_status($code);

    if(! empty($temp))
    {
			if($temp->F_Sap === 'Y')
			{
				$sc = FALSE;
	      $this->error = "Delete Failed : Temp Data already in SAP";
			}
			else
			{
				if(! $this->activity_model->drop_temp_exists_data($code))
	      {
	        $sc = FALSE;
	        $this->error = "Delete Failed : Delete Temp Failed";
	      }
	      {
	        $arr = array(
	          'Status' => 0,
	          'temp_date' => NULL,
	          'sap_date' => NULL,
	          'Message' => NULL
	        );

	        $this->activity_model->update($code, $arr);
	      }
			}
    }


    $this->response($sc);
  }



	public function get_preview_data()
  {
    $code = $this->input->get('code');

    if(!empty($code))
    {
      $rs = $this->activity_model->get($code);

      if(!empty($rs))
      {
        $arr = array(
          'U_WEBORDER' => $rs->code,
					'ClgCode' => $rs->ClgCode,
					'Action' => action_name($rs->Action), //--- activity_helper
					'Type' => $rs->TypeName,
					'Subject' => $rs->SubjectName,
					'AssignedTo' => ($rs->attendType == 'U' ? $rs->UserName : $rs->EmpName),
          'CardCode' => $rs->CardCode,
          'CardName' => $rs->CardName,
          'Contact' => $rs->ContactPer,
          'Tel' => $rs->Tel,
					'Details' => $rs->Details,
					'Notes' => $rs->Notes,
					'StartTime' => $rs->Recontact.' '.get_time_from_int($rs->BeginTime),
					'EndTime' => $rs->endDate.' '.get_time_from_int($rs->ENDTime)
        );

        echo json_encode($arr);
      }
      else
      {
        echo "Invalid Web Code";
      }
    }
    else
    {
      echo "Missing required parameter: Web Code";
    }
  }



	public function get_project()
	{
		$txt = $_REQUEST['term'];
		$sc = array();

    $qr = "SELECT PrjCode AS code, PrjName AS name ";
    $qr .= "FROM OPRJ ";

		if($txt != '*')
		{
			$qr .= "WHERE PrjCode LIKE N'%{$this->ms->escape_str($txt)}%' ";
	    $qr .= "OR PrjName LIKE N'%{$this->ms->escape_str($txt)}%' ";
		}

    $qr .= "ORDER BY PrjName ASC ";
    $qr .= "OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = str_replace('|', '', $rd->code).' | '.str_replace('|', '', $rd->name);
      }
    }

    echo json_encode($sc);
	}




	public function get_document()
	{
		$type = array(
			'23' => 'OQUT',
			'17' => 'ORDR',
			'1470000113' => 'OPRQ'
		);

		$searchText = trim($this->input->get('searchText'));
		$objType = trim($this->input->get('objectType'));
		$cardCode = trim($this->input->get('cardCode'));

		if($objType >= 23)
		{
			$qr  = "SELECT DocNum, DocDate, CardName, Comments ";
			$qr .= "FROM {$type[$objType]} ";
			$qr .= "WHERE DocNum != '' ";


			if(!empty($cardCode))
			{
				$qr .= "AND CardCode = '{$cardCode}' ";
			}
			else
			{
				$sale_in = $this->user_model->get_sale_in();

				if(!empty($sale_in))
				{
					$qr .= "AND SlpCode IN({$sale_in}) ";
				}
				else
				{
					$qr .= "AND SlpCode = {$this->user->sale_id} ";
				}
			}

			if(!empty($searchText))
			{
				$qr .= "AND (DocNum LIKE N'%{$this->ms->escape_str($searchText)}%' ";
				$qr .= "OR CardCode LIKE N'%{$this->ms->escape_str($searchText)}%' ";
				$qr .= "OR CardName LIKE N'%{$this->ms->escape_str($searchText)}%' ";
				$qr .= "OR Comments LIKE N'%{$this->ms->escape_str($searchText)}%') ";
			}

			$qr .= "ORDER BY DocDate DESC ";

			if(empty($searchText))
			{
				$qr .= "OFFSET 0 ROWS FETCH NEXT 100 ROWS ONLY";
			}

			$qs = $this->ms->query($qr);

			if($qs->num_rows() > 0)
			{
				$ds = array();
				$no = 1;
				foreach($qs->result() as $rs)
				{

					$arr = array(
						'no' => $no,
						'DocNum' => $rs->DocNum,
						'DocDate' => thai_date($rs->DocDate, FALSE, '.'),
						'CardName' => $rs->CardName,
						'Details' => $rs->Comments
					);

					$no++;
					array_push($ds, $arr);
				}

				echo json_encode($ds);
			}
			else
			{
				echo "not found";
			}
		}
		else
		{
			echo "not found";
		}
	}


	public function sendToSAP()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));

		if(!empty($code))
		{
			if(!$this->doExport($code))
			{
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required Parameter : code";
		}

		$this->response($sc);
	}




	public function get_subject_list()
	{
		$ds = array();

		$typeCode = $this->input->get('typeCode');
		if(!empty($typeCode))
		{
			$qs = $this->activity_model->get_subject_by_type($typeCode);

			if(!empty($qs))
			{
				foreach($qs as $rs)
				{
					$arr = array(
						'code' => $rs->code,
						'name' => $rs->name
					);

					array_push($ds, $arr);
				}
			}
			else
			{
				$arr = array(
					'nodata' => 'nodata'
				);

				array_push($ds, $arr);
			}
		}

		echo json_encode($ds);
	}


	public function get_contact_list()
	{
		$ds = array();

		$CardCode = trim($this->input->get('CardCode'));

		if(!empty($CardCode))
		{
			$qs = $this->activity_model->get_contact_by_card_code($CardCode);

			if(!empty($qs))
			{
				foreach($qs as $rs)
				{
					$arr = array('code' => $rs->code, 'name' => $rs->name);
					array_push($ds, $arr);
				}
			}
			else
			{
				$arr = array(
					'nodata' => 'nodata'
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
		else
		{
			echo "Missing Required Parameter : CardCode";
		}
	}



	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('Y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ACTIVITY');
    $run_digit = getConfig('RUN_DIGIT_ACTIVITY');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->activity_model->get_max_code($pre);
    if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  public function clear_filter()
	{
		$filter = array(
			'acWebCode',
			'acActivity',
			'acType',
			'acSubject',
			'acAssignedTo',
			'acCustomer',
			'acStartDate',
			'acEndDate',
			'acProject',
			'acStatus',
			'acorder_by',
			'acsort_by'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
