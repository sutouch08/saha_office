<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends PS_Controller{
	public $menu_code = 'SETTING';
	public $menu_group_code = 'AD'; //--- System security
	public $title = 'Setting';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'setting';
		$this->load->model('config_model');
  }



  public function index()
  {
		if($this->isAdmin OR $this->isSuperAdmin)
		{

			$groups = array('Company', 'Document', 'SAP', 'LABEL');

	    $ds = array();
	    foreach($groups as $rs)
	    {
	       $group = $this->config_model->get_config_by_group($rs);

	       if(!empty($group))
	       {
	         foreach($group as $rd)
	         {
	           $ds[$rd->code] = $this->config_model->get($rd->code);
	         }
	       }
	    }

	    $this->load->view('setting/configs', $ds);

		}
		else
		{
			$this->deny_page();
		}

  }



	public function update_config()
  {
    $sc = TRUE;

    if($this->input->post())
    {
      $this->error = "Cannot update : ";
      $configs = $this->input->post();
      foreach($configs as $name => $value)
      {
        if(! $this->config_model->update($name, $value))
        {
          $sc = FALSE;
          $this->error .= "{$name}, ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Form content not found";
    }

  	$this->response($sc);
  }




}//--- end class


 ?>
