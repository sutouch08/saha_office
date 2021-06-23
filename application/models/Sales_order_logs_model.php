<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_order_logs_model extends CI_Model
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

    return $this->db->insert('sales_order_logs', $arr);
  }


  public function get($code)
  {
    if(!empty($code))
    {
      $rs = $this->db->where('code', $code)->order_by('date_upd', 'ASC')->get('sales_order_logs');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

} //--- End class

?>
