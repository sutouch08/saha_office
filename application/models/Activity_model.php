<?php
class Activity_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('activity');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get('activity');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('activity', $ds);
    }

    return FALSE;
  }


  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('activity', $ds);
    }

    return FALSE;
  }



  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('activity');
  }

  public function count_rows(array $ds = array())
  {
    if(!empty($ds['WebCode']))
    {
      $this->db->like('code', $ds['WebCode']);
    }

    if(!empty($ds['Activity']) && $ds['Activity'] !== 'all')
    {
      $this->db->where('action', $ds['Activity']);
    }

    if(!empty($ds['Type']) && $ds['Type'] !== 'all')
    {
      $this->db->where('CntctType', $ds['Type']);
    }

    if(!empty($ds['Subject']) && $ds['Subject'] !== 'all')
    {
      $this->db->where('CntctSbjct', $ds['Subject']);
    }

    if(!empty($ds['AssignedTo']))
    {
      $this->db->group_start();
      $this->db->like('UserName', $ds['AssignedTo']);
      $this->db->or_like('EmpName', $ds['AssignedTo']);
      $this->db->group_end();
    }

    if(!empty($ds['Customer']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['Customer']);
      $this->db->or_like('CardName', $ds['Customer']);
      $this->db->group_end();
    }

    if(!empty($ds['StartDate']) && !empty($ds['EndDate']))
    {
      $this->db->where('Recontact >=', from_date($ds['StartDate']));
      $this->db->where('endDate <=', to_date($ds['EndDate']));
    }

    if(!empty($ds['Project']) && $ds['Project'] !== 'all')
    {
      $this->db->where('FIPROJECT', $ds['Project']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if(! $this->isAdmin)
    {
      if(! $this->isLead)
      {
        $this->db->group_start();
        $this->db->where('user_id', $this->user->id);
        $this->db->or_where('AttendEmpl', $this->user->emp_id);
        $this->db->group_end();
      }
      else
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
    }

    return $this->db->count_all_results('activity');
  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
  {

    if(!empty($ds['WebCode']))
    {
      $this->db->like('code', $ds['WebCode']);
    }

    if(!empty($ds['Activity']) && $ds['Activity'] !== 'all')
    {
      $this->db->where('action', $ds['Activity']);
    }

    if(!empty($ds['Type']) && $ds['Type'] !== 'all')
    {
      $this->db->where('CntctType', $ds['Type']);
    }

    if(!empty($ds['Subject']) && $ds['Subject'] !== 'all')
    {
      $this->db->where('CntctSbjct', $ds['Subject']);
    }

    if(!empty($ds['AssignedTo']))
    {
      $this->db->group_start();
      $this->db->like('UserName', $ds['AssignedTo']);
      $this->db->or_like('EmpName', $ds['AssignedTo']);
      $this->db->group_end();
    }

    if(!empty($ds['Customer']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['Customer']);
      $this->db->or_like('CardName', $ds['Customer']);
      $this->db->group_end();
    }

    if(!empty($ds['StartDate']) && !empty($ds['EndDate']))
    {
      $this->db->where('Recontact >=', from_date($ds['StartDate']));
      $this->db->where('endDate <=', to_date($ds['EndDate']));
    }

    if(!empty($ds['Project']) && $ds['Project'] !== 'all')
    {
      $this->db->where('FIPROJECT', $ds['Project']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if(! $this->isAdmin)
    {
      if(! $this->isLead)
      {
        $this->db->group_start();
        $this->db->where('user_id', $this->user->id);
        $this->db->or_where('AttendEmpl', $this->user->emp_id);
        $this->db->group_end();
      }
      else
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
    }

    $this->db->order_by('code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get('activity');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_user_in($txt)
  {
    $sc = "";
    $qr = "SELECT USERID FROM OUSR ";
    $qr .= "WHERE USER_CODE LIKE N'%{$this->ms->escape_str($txt)}%' ";
    $qr .= "OR U_NAME LIKE N'%{$this->ms->escape_str($txt)}%' ";

    $qs = $this->ms->query($qr);
    if($qs->num_rows() > 0)
    {
      $i = 1;
      foreach($qs->result() as $rs)
      {
        $sc .= $i === 1 ? $rs->USERID : ", {$rs->USERID}";
        $i++;
      }
    }
    else
    {
      $sc = "123456789";
    }

    return $sc;
  }



  public function get_emp_in($txt)
  {
    $sc = "";
    $qr = "SELECT empID FROM OHEM ";
    $qr .= "WHERE firstName LIKE N'%{$this->ms->escape_str($txt)}%' ";
    $qr .= "OR lastName LIKE N'%{$this->ms->escape_str($txt)}%' ";

    $qs = $this->ms->query($qr);
    if($qs->num_rows() > 0)
    {
      $i = 1;
      foreach($qs->result() as $rs)
      {
        $sc .= $i === 1 ? $rs->empID : ", {$rs->empID}";
        $i++;
      }
    }
    else
    {
      $sc = "123456789";
    }

    return $sc;
  }




  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('activity');

    return $rs->row()->code;
  }



  public function get_subject($type = NULL)
  {
    $this->ms->select('Code AS code, Name AS name')->where('Active', 'Y');

    if($type !== NULL)
    {
      $this->ms->where('Type', $type);
    }

    $rs = $this->ms->order_by('Code', 'ASC')->get('OCLS');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_subject_by_type($type_code)
  {
    $rs = $this->ms
    ->select('Code AS code, Name AS name')
    ->where('Active', 'Y')
    ->where('Type', $type_code)
    ->order_by('Code', 'ASC')
    ->get('OCLS');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_subject_type($code)
  {
    $rs = $this->ms
    ->select('Type')
    ->where('Code', $code)
    ->get('OCLS');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Type;
    }

    return NULL;
  }

  public function get_type()
  {
    $rs = $this->ms
    ->select('Code AS code, Name AS name')
    ->where('Active', 'Y')
    ->order_by('Code', 'ASC')
    ->get('OCLT');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_contact_by_card_code($code)
  {
    $rs = $this->ms
    ->select('CntctCode AS code, Name AS name')
    ->where('CardCode', $code)
    ->get('OCPR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_card_code($code)
  {
    $rs = $this->ms->select('CardCode')->where('CntctCode', $code)->get('OCPR');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->CardCode;
    }

    return NULL;
  }


  public function get_project()
  {
    $rs = $this->ms
    ->select('PrjCode AS code, PrjName AS name')
    ->where('Active', 'Y')
    ->order_by('PrjName', 'ASC')
    ->get('OPRJ');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_project_name($code)
  {
    $rs = $this->ms->select('PrjName AS name')->where('PrjCode', $code)->get('OPRJ');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }

  public function get_location()
  {
    $rs = $this->ms->select('Code AS code, Name AS name')->get('OCLO');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_user()
  {
    $rs = $this->ms
    ->select('USERID AS code, U_NAME AS name')
    ->where('Locked', 'N')
    ->order_by('U_NAME', 'ASC')
    ->get('OUSR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_sap_exists_code($code)
  {
    $row = $this->ms->where('U_WEBORDER', $code)->count_all_results('OCLG');

    if($row > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_temp_exists_data($code)
  {
    $row = $this->mc->where('U_WEBORDER', $code)->count_all_results('OCLG');

    if($row > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function drop_temp_exists_data($code)
  {
    return $this->mc->where('U_WEBORDER', $code)->delete('OCLG');
  }


  public function add_temp(array $ds = array())
  {
    return $this->mc->insert('OCLG', $ds);
  }


  public function get_temp_data($code)
  {
    $rs = $this->mc->where('U_WEBORDER', $code)->get('OCLG');
    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_status($code)
  {
    $rs = $this->mc->select('F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OCLG');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


} //---- End class

 ?>
