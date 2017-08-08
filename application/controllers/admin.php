<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	private $datauser;

	public function __construct(){
		parent::__construct();
//cek session
		if($this->session->userdata('isLogin')== FALSE){
			redirect('login/login_form');	
		}
		//utk nge-load library
		$this->load->library('grocery_crud');

		//utk nge-load helper
		$this->load->helper(array('url','html','form'));

		//utk data user
		$this->datauser= $this->session->userdata('data_user');
	}

	//utk ngeload data belanjaan ke view
	public function _terminal_input($output=null){
		$output->pengguna = this->datauser;
		$this->load->view('terminal_form.php', $output);
	}

	//utk memanage data produk
	function product(){
		//membuat grocery baru
		$crud = new grochery_crud();
		//select tabel nya
		$crud->set_table('barang');
		//utk pilih kolom
		$crud->column('kode_barang','nama_barang','deskripsi','harga','stok','pembaharuan','status');

		//as nama kolom
		$crud->display_as('kode_barang','Kode');
		$crud->display_as('nama_barang','Nama');
		$crud->display_as('img','Gambar');
		$crud->display_as('deskripsi','Uraian');
		$crud->display_as('harga','Harga');
		$crud->display_as('status_harga','Status');

		//utk upload file dan direktorinya
		$crud->set_field_upload('img','assets/uploads/produk');
		$crud->set_subject('Produk');
		$output = $crud->render();
		$this->_terminal_input($output);
	}

	//memonitor list pesanan
	function pesananku(){
		$this->load->model('m_adm');
		$data['pengguna'] = $this->datauser;
		
		//data pesanan baru
		$data['baru']= $this->m_adm->pesananBaru();

		//data pesanan sedang diproses
		$data['dikirim']= $this->m_adm->pesananDikirim();

		//data pesanan dibatalkan
		$data['batal']= $this->m_adm->pesananBatal();

		//data pesanan telah selesai diproses
		$data['selesai']= $this->m_adm->pesananSelesai();
		$this->load->view('pesanan.php', $data);

	}

	//memonitor semua list pesanan pengguna
	function Allpesanan(){
		$this->load->model('m_adm');

		$data['pengguna'] = $this->datauser;
		$data['baru'] = $this->m_adm->AllpesananBaru();
		$data['dikirim'] = $this->m_adm->AllpesananDikirim;
		$data['selesai'] = $this->m_adm->AllpesananSelesai;
		$this->load->view('allpesananadmin.php', $data);
	}

	//memanage semua konfirmasi pesanan
	function Allkonfirmasi(){
		$this->load->model('m_adm');
		$data['pengguna'] = $this->datauser;
		
		//data pesanan baru
		$data['konfirm']= $this->m_adm->AlllistKonfirm();

		//data pesanan sedang diproses
		$data['proses']= $this->m_adm->AllpesananDikirim();

		//data pesanan dibatalkan
		$data['batal']= $this->m_adm->pesananBatal();

		$this->load->view('Allkonfirmasiadmin.php', $data);
	}

	function Proses($id){
		$this->load->model('m_adm');
		if ($this->input->post('submit')) {
			$this->m_adm->mproses($id);
			redirect('admin/Allpesanan');
		}
	}

	function Batalkan($id){
		$this->load->model('m_adm');
		if ($this->input->post('submit')) {
			$this->m_adm->batal($id);
			redirect('admin/AllpesananKu');
		}
	}

	function Balikin($id){
		$this->load->model('m_adm');
		if ($this->input->post('submit')) {
			$this->m_adm->balik($id);
			redirect('admin/AllpesananKu');
		}
	}

	function Batalkan($id){
		$this->load->model('m_adm');
		if ($this->input->post('submit')) {
			$this->m_adm->batal($id);
			redirect('admin/AllpesananKu');
		}
	}

	function listKonfirmasiPesanan(){
		$this->load->model('m_adm');
		$data['pengguna'] = $this->datauser;
		$data['bank']= $this->m_adm->listBank();
		$data['list']= $this->m_adm->pesananBaru();
		$data['konfirm']= $this->m_adm->listKonfirm();

		$this->load->view('konfirmasi_pesanan', $data);
	}

	function kontak(){
		if (this->input->post() && ($this->input->post('security_code') == $this->session->userdata('mycaptcha'))) {
			$nama = $this->input->post('nama');
			$email = $this->input->post('email');
			$hp = $this->input->post('hp');
			$subjek = $this->input->post('subjek');
			$isi = $this->input->post('isi');

			$insert = $this->db->insert('kontak', 
									array( 
											'nama'   => $nama,
											'email'  => $email,
											'nohp'   => $hp,
											'subjek' => $subjek,
											'isi'    => $isi 
										));
			$this->load->view('kontak_sukses.php', $data);
		}else{
			//utk CodeIgniter load container captcha helper
			$this->load->helper('captcha');

			$vals = array(
							'img_path'   => './captcha/',
							'img_url'    => base_url.'captcha/' 
							'img_width'  => '200',
							'img_height' => '30',
							'border'     => 0,
							'expiration' => 7200
						);
			//create captcha image
			$cap = create_captcha($vals);

			//store image html code in variable
			$data['image'] = $cap['image'];
			$data['pengguna'] = $this->datauser;

			//store the captcha wprd in a session
			$this->session->set_userdata('mycaptcha', $cap['word']);

			$this->load->view('kontak_form.php', $data);
		}
	}

	function Allkontakget(){
		$this->load->model('m_adm');
		$data['pengguna'] = $this->datauser;
		$data['allkontak']= $this->m_adm->getallkontak();
		$data['allkritik']= $this->m_adm->getallkritik();
		$data['allsaran']= $this->m_adm->getallsaran();

		$this->load->view('Allkontak.php', $data);
	}

	function Alluser(){
		$crud = new grochery_CRUD();
		$crud->set_table('user');
		$crud->columns('username','password');
		$crud->where('level','user');
		
		//menghilangkan field
		$crud->unset_fields('level','foto','nama','alamat','email','nohp','status');
		$crud->change_field_type('password','password');

		$output = $crud->render();
		$this->_terminal_input($output);

	}

	function Allbank(){
		$crud = new grochery_CRUD();
		$crud->set_table('bank');
		$crud->columns('ANREK','namaBank','noRekening');
		$crud->display_as('ANREK','A/N Rekening');
		$crud->display_as('namaBank','Nama Bank');
		$crud->display_as('noRekening','No.Rekening');
		$crud->where('level','user');

		$output = $crud->render();
		$this->_terminal_input($output);
	}

	function set_photo(){
		$crud = new grochery_CRUD();
		$crud->set_table('user');
		$crud->display_as('foto','Photo');
		$crud->where('id_user',$this->datauser->id_user);

		$output = $crud->render();
		$this->_terminal_input($output);

		//menghapus action
		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_print();
		$crud->unset_export();
		$crud->unset_read();
		$crud->unset_back_to_list();

		//menghilangkan field
		$crud->unset_fields('level','username','password','nama','email','nohp','alamat','create_user','status');
		$crud->set_field_upload('foto','assets/upload/img');

		//costumize pesan update
		$crud->set_lang_string('update_success_message','Success Update Photo.<div style="display:none">');

		$output = $crud->render();
		$this->_terminal_input($output);
	}

	function set_password(){
		$crud = new grochery_CRUD();
		$crud->set_table('user');
		$crud->where('id_user',$this->datauser->id_user);

		//menghapus action
		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_print();
		$crud->unset_export();
		$crud->unset_read();
		$crud->unset_back_to_list();

		//costumize pesan update
		$crud->set_lang_string('update_success_message','Success Update Password.<div style="display:none">');

		$output = $crud->render();
		$this->_terminal_input($output);
	}

	function set_identitas(){
		$crud = new grochery_CRUD();
		$crud->set_table('user');
		$crud->display_as('nohp','No.Handphone');
		$crud->where('id_user',$this->datauser->id_user);

		//menghapus action
		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_print();
		$crud->unset_export();
		$crud->unset_read();
		$crud->unset_back_to_list();

		//menghilangkan field
		$crud->unset_fields('foto','password','level','create_user','status');

		//costumize pesan update
		$crud->set_lang_string('update_success_message','Success Update Identitas.<div style="display:none">');

		$output = $crud->render();
		$this->_terminal_input($output);	
	}

}
?>
