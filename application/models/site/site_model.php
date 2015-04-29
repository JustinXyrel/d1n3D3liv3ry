<?php
class Site_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();

	}
	public function get_user_details($id=null,$username=null,$password=null,$pin=null){
		$this->db->trans_start();
			$this->db->select('users.*, user_roles.role as user_role,user_roles.access as access, user_roles.id as user_role_id');
			$this->db->from('users');
			$this->db->join('user_roles','users.role = user_roles.id','LEFT');
			if($id != null){
				$this->db->where('users.id',$id);
			}
			if($pin != null){
				$this->db->where('users.pin',$pin);
			}
			if($username != null && $password != null){
				$this->db->where('users.username',$username);
				$this->db->where('users.password',md5($password));
			}
			$this->db->where('users.inactive',0);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();


		if(count($result) > 0){
			if(count($result) == 1){
				return $result[0];
			}
			else{
				return $result;
			}
		}
		else{
			return array();
		}
	}

	public function get_db_now($format='php',$dateOnly=false){
		if($dateOnly)
			$query = $this->db->query("SELECT DATE(now()) as today");
		else
			$query = $this->db->query("SELECT now() as today");
		$result = $query->result();
		foreach($result as $val){
			$now = $val->today;
		}

		if($format=='php')
			return date('m/d/Y H:i:s',strtotime($now));
		else
			return $now;
	}

	public function update_language($items,$id){
		$this->db->trans_start();
		$this->db->where('id',$id);
		$this->db->update('users',$items);
		$this->db->trans_complete();
		return $this->db->last_query();
	}

	public function get_company_profile(){
		$this->db->trans_start();
			$this->db->select('company_profile.*');
			$this->db->from('company_profile');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result[0];
	}

	public function get_custom_val($tbl,$col,$where=null,$val=null,$returnAll=false){
		if(is_array($col)){
			$colTxt = "";
			foreach ($col as $col_txt) {
				$colTxt .= $col_txt.",";
			}
			$colTxt = substr($colTxt,0,-1);
			$this->db->select($tbl.".".$colTxt);
		}
		else{
			$this->db->select($tbl.".".$col);
		}
		$this->db->from($tbl);

		if($where != '' || $val != '')
			$this->db->where($tbl.".".$where,$val);
		
		$query = $this->db->get();
		$result = $query->result();
		if($returnAll){
			return $result;
		}
		else{
			if(count($result) > 0){
				if(count($result) == 1)
					return $result[0];
				else
					return $result;
			}
			else
				return "";
		}
	}

	public function update_profile($user,$id){
		$this->db->where('id', $id);
		$this->db->update('users', $user);

		return $this->db->last_query();
	}
}
?>