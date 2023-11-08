<?php
class Mdata extends CI_Model{

	function __construct()
    {
        parent::__construct();
    }

	public function get_profile($id_user){
		$sql = "SELECT * FROM tb_user where id='$id_user'  order by id desc";
		$query = $this->db->query($sql);
		return $query->result();
		$this->db->close();
	}
	
    
	

	
}
