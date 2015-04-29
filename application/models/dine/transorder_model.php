<?php

class Transorder_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function get_transaction_search($search="", $args=array()){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('trans_sales');
			if($search != ""){
				$this->db->like('trans_ref', $search); 
			}
			if(!empty($args)){
				foreach ($args as $col => $val) {
					if(is_array($val)){
						if(!isset($val['use'])){
							$this->db->where_in($col,$val);
						}
						else{
							$func = $val['use'];
							if(isset($val['third']))
								$this->db->$func($col,$val['val'],$val['third']);
							else
								$this->db->$func($col,$val['val']);
						}
					}
					else
						$this->db->where($col,$val);
				}
			}
			$this->db->order_by('trans_ref');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_transactions($id, $args=array(), $where_not_in=null, $select=null){
		$this->db->trans_start();

			if(!empty($select))
				$this->db->select($select);
			else
				$this->db->select('users.username, branch_details.branch_name as branch_name, trans_sales.reason tr_reason, trans_sales.inactive tr_inactive, trans_sales.*, customers.*');
			$this->db->from('trans_sales');
			$this->db->join('customers', 'customers.cust_id = trans_sales.customer_id');
			$this->db->join('branch_details', 'branch_details.branch_id = trans_sales.branch_id');
			$this->db->join('users','trans_sales.user_id = users.id');
			$this->db->where('trans_sales.type', 'delivery');

			if($id!=null)
				$this->db->where('trans_sales.sales_id', $id);

			if(!empty($where_not_in))
			{
				if(is_array($where_not_in))
					$this->db->where_not_in('sales_id', $where_not_in);
			}

			if(!empty($args)){
				foreach ($args as $col => $val) {
					if(is_array($val)){
						if(!isset($val['use'])){
							$this->db->where_in($col,$val);
						}
						else{
							$func = $val['use'];
							if(isset($val['third']))
								$this->db->$func($col,$val['val'],$val['third']);
							else
								$this->db->$func($col,$val['val']);
						}
					}
					else
						$this->db->where($col,$val);
				}
			}

			$this->db->order_by('trans_sales.datetime ASC');
			$query = $this->db->get();
				$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function update_transaction($items, $id)
	{
		$this->db->where('sales_id',$id);
		$this->db->update('trans_sales',$items);

		return $this->db->affected_rows();
	}

	public function get_delivery_code($code=null)
	{
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('trans_sales');
			$this->db->where('delivery_code', $code); 
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function confrim_delivery_code($items,$id)
	{
		$this->db->where('delivery_code',$id);
		$this->db->update('trans_sales',$items);

		return $this->db->affected_rows();
	}
	public function count_warning_status(){
		$query = $this->db->query("SELECT br.branch_id as branch_id, COUNT(*) as count
									FROM trans_sales ts, branch_details br
									WHERE ts.branch_id = br.branch_id
									AND ts.inactive = 0
									AND ts.delivered != 1
									AND ts.void_ref IS NULL
									AND (((ts.complete_time IS NULL) AND ((TIMESTAMPDIFF(SECOND ,ts.start_time, NOW())/60) > ts.prep_time))
									OR ((TIMESTAMPDIFF(SECOND ,ts.start_time, ts.complete_time)/60) > ts.prep_time))
									GROUP BY ts.branch_id");
		$result = $query->result_array();

		return $result;
	}

}
