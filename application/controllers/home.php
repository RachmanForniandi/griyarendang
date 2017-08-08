<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	private $datauser;

	public function __construct(){
		parent::__construct();

		//utk nge-load library
		$this->load->library(array('session'));

		//utk nge-load helper
		$this->load->helper('url');

		//utk nge-load model
		$this->load->model(array('m_login'));

		//utk data user
		$this->datauser= $this->session->userdata('data_user');
	}

	public function index(){
		//cek session
		if($this->session->userdata('isLogin')==FALSE){
			redirect('login/login_form');	
		}else{
			$user = $this->session->userdata('datauser');
			$data = array();
		//load data user
			$data['pengguna']= $user;
			$this->load->view('adm_home', $data);
		}
	}
}
