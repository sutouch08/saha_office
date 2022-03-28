<?php
class Maintenance extends CI_Controller
{
  public $user;
  public $isSuperAdmin = FALSE;
  public $isAdmin = FALSE;
  public $uid;

  public function __construct()
  {
    parent::__construct();

    $this->uid = get_cookie('uid');
    $this->user = $this->user_model->get_user_by_uid($this->uid);
    //--- get permission for user
    $this->ugroup = $this->user->ugroup;

    $this->isSuperAdmin = $this->ugroup === 'superAdmin' ? TRUE : FALSE;
    $this->isAdmin = $this->ugroup === 'admin' ? TRUE : FALSE;

    $this->load->model('config_model');
  }


  public function index()
  {
    if(getConfig('CLOSE_SYSTEM') == 0 && $this->isSuperAdmin === FALSE)
    {
      redirect(base_url());
    }

    $this->load->view('maintenance');
  }

  public function open_system()
  {
    if($this->isSuperAdmin OR $this->isAdmin)
    {
      $rs = $this->config_model->update('CLOSE_SYSTEM', 0);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function check_open_system()
  {
    $rs = $this->config_model->get('CLOSE_SYSTEM');
    echo $rs == 1 ? 'close' : 'open';
  }


}


 ?>
