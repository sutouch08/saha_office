<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Print_barcode extends PS_Controller
{
	public $menu_code = 'PRINTBARC';
	public $menu_sub_group_code = '';
	public $menu_group_code = 'AD';
	public $title = 'PRINT BARCODE';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'print_barcode';
  }



  public function index()
  {
		if($this->input->post())
		{
			$ds = array();
			$this->load->helper('print');
			$this->load->library('excel');
			$file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
	  	$path = $this->config->item('upload_path');
	    $file	= 'uploadFile';
			$config = array(   // initial config for upload class
				"allowed_types" => "xlsx",
				"upload_path" => $path,
				"file_name"	=> "import_order",
				"max_size" => 5120,
				"overwrite" => TRUE
				);

				$this->load->library("upload", $config);

				if(! $this->upload->do_upload($file))
	      {
					echo $this->upload->display_errors();
				}
	      else
	      {
	        $info = $this->upload->data();
	        /// read file
					$excel = PHPExcel_IOFactory::load($info['full_path']);
					//get only the Cell Collection
	        $ds['data']	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);
					$ds['height'] = ($this->input->post('b_height') ? $this->input->post('b_height') : 15);
					$ds['width'] = ($this->input->post('b_width') ? $this->input->post('b_width') : NULL);
					$ds['font_size'] = ($this->input->post('font_size') ? $this->input->post('font_size') : 24);
				}

				$this->load->view('print_barcode', $ds);
		}
		else
		{
			$ds = array(
				'height' => 15,
				'width' => "",
				'font_size' => 24
			);

			$this->load->view('print_barcode', $ds);
		}
  }



}//--- end class


 ?>
