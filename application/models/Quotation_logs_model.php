<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quotation_logs_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function add($action, $code)
  {
    $arr = array(
      'code' => $code,
      'user_id' => $this->user->id,
      'uname' => $this->user->uname,
      'emp_name' => $this->user->emp_name,
      'action' => $action
    );

    return $this->db->insert('quotation_logs', $arr);
  }


  public function get($code)
  {
    if(!empty($code))
    {
      $rs = $this->db->where('code', $code)->order_by('date_upd', 'ASC')->get('quotation_logs');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

} //--- End class

?>
