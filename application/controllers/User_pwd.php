<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_pwd extends PS_Controller
{
  public $title = 'Change Password';
	public $menu_code = 'change password';
	public $menu_group_code = 'SC';
  public $error;


	public function __construct()
	{
		parent::__construct();
    $this->home = base_url().'user_pwd';
	}


	public function index()
	{
    redirect($this->home.'/change');
	}


  public function change()
	{
    $uid = get_cookie('uid');

    $user = $this->user_model->get_user_by_uid($uid);

    if(!empty($user))
    {
      $ds['data'] = $user;
      $this->load->view('users/change_pwd', $ds);
    }
    else
    {
      $this->error_page();
    }

	}


  public function verify_password()
  {
    $sc = TRUE;
    $uid = trim($this->input->post('uid'));
    $pwd = trim($this->input->post('pwd'));
    $user = $this->user_model->get_user_by_uid($uid);
    if(!empty($user))
    {
      if(!password_verify($pwd, $user->pwd))
      {
        $sc = FALSE;
        $this->error = "Wrong Password";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid uid";
    }

    $this->response($sc);
  }



  public function change_password()
	{
    $sc = TRUE;

		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			if(!$this->user_model->change_password($id, $pwd))
      {
        $sc = FALSE;
        $this->error = "Change password not successfull, please try again";
      }
		}
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter : user_id";
    }

		$this->response($sc);
	}




  public function change_skey()
  {
    $sc = TRUE;
    $uid = trim($this->input->post('uid'));
    $user = $this->user_model->get_user_by_uid($uid);
    if(!empty($user))
    {
      $skey = trim($this->input->post('skey'));
      $skey = md5($skey);
      $is_exists = $this->user_model->is_skey_exists($skey, $uid);
      if($is_exists)
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถใช้รหัสนี้ได้กรุณากำหนดรหัสอื่น";
      }
      else
      {
        $arr = array('skey' => $skey);
        if(! $this->user_model->update_user($user->id, $arr))
        {
          $sc = FALSE;
          $this->error = "เปลี่ยนรหัสลับไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบ user หรือ user ไม่ถูกต้อง";
    }

    echo $sc === TRUE ? 'success' : $this->error;

  }

}
 ?>
