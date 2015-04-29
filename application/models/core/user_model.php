<?php
class User_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_users($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('users');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('users.id',$id);
				}else{
					$this->db->where('users.id',$id);
				}
			$this->db->order_by('id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_users($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('users',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_users($user,$id){
		$this->db->where('id', $id);
		$this->db->update('users', $user);

		return $this->db->last_query();
	}
}
?>