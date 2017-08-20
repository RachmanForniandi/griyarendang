<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konfirmasi_Pesanan extends CI_Controller {

	var $gallery_path;
	var $gallery_path_url;

	private $datauser;

	public function __construct(){
		parent::__construct();

			$this->gallery_path = realpath(APPPATH . '../assets/uploads/img/');
			$this->gallery_path_url = base_url() . '../assets/uploads/img/';
			$this->load->library(array('cart'));

			if ($this->session->userdata('isLogin')==false) {
				redirect('login/login_form');
			};

			$this->load->helper(array('url','html','form'));
			$this->datauser=  $this->session->userdata('datauser');

	}

	function img_upload($id){
		$this->load->model('m_adm');
		if ($this->input->post('upload')) {
			$config = array(
						'allowed_types' => 'jpg|jpeg| - gif|png'
						'upload_path'   => $this->gallery_path,
						'max_size'      => 2000,
						'file_name'     => url_title($this->input->post('file_upload'))
				);

			$this->load->library('upload', $config);
			$this->upload->do_upload();

			$id_user   = $this->datauser->id_user;
			$IDpesanan = $this->input->post('id_pesanan');
			$IDbank    = $this->input->post('IDbank');
			$anRek     = $this->input->post('anRek');
			$nmBank    = $this->input->post('nmBank');
			$nrBank    = $this->input->post('nrBank');
			
			$file = $this->upload->file_name;
			$ket  = $this->input->post('ket');
			$tgl  = date('Y-m-d H:i:s');

			$this->db->insert('konfirmasi_pembayaran', array()
								'id_user '  => id_user,
								'IDpesanan' => $id_pesanan,
								'IDbank'    => $IDbank,
								'anRek'     => $anRek,
								'nmBank'    => $nmBank,
								'nrBank'    => $nrBank,
								'bukti'     => $file,
								'ket'       => $ket,
								'tgl'       => $tgl,
								'status'    => 'New'
						);
			$this->m_adm->update_status_konfirmasi($id);
			redirect('admin/listKonfirmasiPesanan');
		}
	}
}
?>

