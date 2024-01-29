<?php
class Mdata extends CI_Model{

	function __construct()
    {
        parent::__construct();
    }

	public function get_profile($id_user){
		exit;
		$sql = "SELECT * FROM tb_user where id='$id_user'  order by id desc";
		$query = $this->db->query($sql);
		return $query->result();
		$this->db->close();
	}

	function check_data_cetak_abc($id){
		$this->abcdb = $this->load->database('abcdb', TRUE);
		$sql = "select id from datacetak where id_dummy='$id'";
		$query = $this->abcdb->query($sql);
		if($query->num_rows() > 0){
			return 1;
		}else{
			return 2;
		}
	}

	function check_abc_pemasukan($id){
		$this->abcdb = $this->load->database('abcdb', TRUE);
		$sql = "select id from pemasukkan where id_dummy='$id'";
		$query = $this->abcdb->query($sql);
		if($query->num_rows() > 0){
			return 1;
		}else{
			return 2;
		}
	}
	
    
	

	
}
