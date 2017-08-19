<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {
	private $datauser;

	function __construct(){
		parent::__construct();

		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('login/login_form');
		}
			$this->load->library('form_validation');
			$this->load->model('cart_model');
			$this->load->library(array('cart'));
			$this->load->database();
			$this->load->library(array('url','form'));
			$this->datauser=  $this->session->userdata('datauser');

	}

	function index(){
		$data['produk'] = $this->cart_model->tampil_produk();
		$data['pengguna'] = $this->datauser;
		$this->load->view('home_cart', $data);
	}

	function tambah(){
		$id = $this->input->post('kode_barang');
		$qty = $this->input->post('banyak');

		$this->db->where('kode_barang', $id);
		$query =$this->db->get('barang', 1);

		if ($query->num_rows >0) {
			foreach ($query->result() as $row) {
				$data = array('id' => $id,  
							  'qty' => $qty,
							  'price' => $row->harga,
							  'id' => $row->nama_barang
							  );
				$this->cart->insert($data);
			}
		}
	}

	function show_cart(){
		$data['pengguna'] = $this->datauser;
		$this->load->view('list_cart',$data)
	}

	function total_cart(){
		$data['total'] = $this->cart->total_items();
		$this->load->view('total',$data);
	}

	function pesanSekarang(){
		$this->form_validation->set_rules('IDpesanan[]', 'kode_pesanan', 'required|trim|xss_clean');
		$this->form_validation->set_rules('IDuser[]', 'iduser', 'required|trim|xss_clean');
		$this->form_validation->set_rules('qty[]', 'qty', 'required|trim|xss_clean');
		$this->form_validation->set_rules('produk[]', 'produk', 'required|trim|xss_clean');
		$this->form_validation->set_rules('harga_satuan[]', 'hrg_satuan', 'required|trim|xss_clean');

		if ($this->form_validation->run() == FALSE) {
			echo validation_errors(); //tampilkan bila ada error
		}else{
			$kp = $this->input->post('IDpesanan');
			$tgl = date('Y-m-d H-i-s');
			$result = array();
			foreach ($kp as $key => $value) {
				 $result[] = array("kode_pesanan" => $_POST['IDpesanan'][$key],
				 				   "iduser"       => $_POST['IDuser'][$key],
				 				   "qty"          => $_POST['qty'][$key],
				 				   "produk"       => $_POST['produk'][$key],
				 				   "hrg_satuan"   => $_POST['harga_satuan'][$key],
				 				   "tgl"		  => $tgl,
				 				   "status"       => 'Baru'
				 				    );
			}
			$res = $this->db->insert_batch('pesanan', $result);
			if ($res) {
				echo "barang sudah dipesan";
				redirect('admin/pesananKu');
			}else{
				echo "Gagal di input";
			}
		}
	}
}
?>

