<?php
class Sync_data_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add_logs(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('sync_logs', $ds);
    }
    return FALSE;
  }


  public function clear_old_logs($days = 7)
  {

    $date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

    return $this->db->where('date_upd <', $date)->delete('sync_logs');
  }

}//--- end class

?>
