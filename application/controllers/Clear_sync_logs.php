<?php
class Clear_sync_logs extends CI_Controller
{
  public $ms;
  public $mc;
  public $keep_days = 7;

  public function __construct()
  {
    parent::__construct();
  }


  public function index()
  {
    $date = date('Y-m-d 00:00:00', strtotime("-{$this->keep_days} days"));
    $this->db->where('date_upd <', $date)->delete('sync_logs');
  }

} //--- end class

 ?>
