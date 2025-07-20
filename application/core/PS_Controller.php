<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $ugroup;
  public $uid;
  public $user;
  public $isSuperAdmin = FALSE;
  public $isAdmin = FALSE;
  public $isSalesAdmin = FALSE;
  public $isLead = FALSE;
  public $isSale = FALSE;
  public $sale_in = NULL;
  public $home;
  public $ms;
  public $mc;
	public $error;

  public function __construct()
  {
    parent::__construct();
    //--- check is user has logged in ?
    _check_login();

    $this->uid = get_cookie('uid');
    $this->user = $this->user_model->get_user_by_uid($this->uid);
    //--- get permission for user
    $this->ugroup = $this->user->ugroup;

    $this->isSuperAdmin = $this->ugroup === 'superAdmin' ? TRUE : FALSE;
    $this->isAdmin = $this->ugroup === 'admin' ? TRUE : FALSE;
    $this->isSalesAdmin = $this->ugroup === 'salesAdmin' ? TRUE : FALSE;
    $this->isLead = $this->ugroup === 'lead' ? TRUE : FALSE;
    $this->isSale = $this->ugroup === 'sale' ? TRUE : FALSE;

    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database


    $this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1 && $this->isSuperAdmin === FALSE)
    {
      redirect(base_url().'maintenance');
    }
  }



  public function response($sc = TRUE)
	{
		echo $sc === TRUE ? 'success' : $this->error;
	}

  public function _response($sc = TRUE)
  {
    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function deny_page()
  {
    return $this->load->view('deny_page');
  }

  public function expired_page()
  {
    return $this->load->view('expired_page');
  }


  public function error_page()
  {
    return $this->load->view('page_error');
  }


  public function page_error()
  {
    return $this->load->view('page_error');
  }
}

?>
