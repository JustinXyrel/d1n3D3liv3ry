<?php

class Trans_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function get_customer_search($search=""){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('customers');
			if($search != ""){
				$this->db->like('phone', $search); 
			}
			$this->db->order_by('fname');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_menu_modifier_items($id){
		$this->db->trans_start();
			$this->db->select('m.*');
			$this->db->from('modifiers m');
			$this->db->join('modifier_group_details mgd', 'mgd.mod_id = m.mod_id');
			$this->db->join('modifier_groups mg', 'mg.mod_group_id = mgd.mod_group_id');
			$this->db->where('mgd.mod_group_id', $id);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_branches_list($id=null){
		$this->db->trans_start();
			$this->db->select("*,CONCAT(address, ' ', base_location) as addr", false);
			$this->db->from('branch_details');
			if($id!=null)
				$this->db->where('branch_id', $id);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_branch_search($search=""){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('branch_details');
			if($search != ""){
				$this->db->like('branch_name', $search, ' both');
			}
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_street_list($id=null)
	{
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('branch_zones');
			$this->db->join('branch_details','branch_details.branch_id = branch_zones.branch_id', 'inner');
			if($id!=null)
				$this->db->where('id', $id);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;

	}
	
	
}
