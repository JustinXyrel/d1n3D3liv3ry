<?php
class Admin_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	public function get_user_roles($id=null,$exclude_admin=true){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('user_roles');
			if($id != null){
				$this->db->where('user_roles.id',$id);
			}
			if($exclude_admin)
				$this->db->where('user_roles.id !=', 1);
			$this->db->order_by('id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_user_roles($items){
		$this->db->insert('user_roles',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_user_roles($user,$id){
		$this->db->where('id', $id);
		$this->db->update('user_roles', $user);

		return $this->db->last_query();
	}
}
?>