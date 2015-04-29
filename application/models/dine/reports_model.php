<?php
class Reports_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_complaints_report_data($branch_id=null, $date_from=null, $date_to=null, $date=null){
		$this->db->select('branch_details.branch_id, branch_details.branch_code, trans_sales_complaints.reason as reason, COUNT(trans_sales_complaints.reason) as count_reason', FALSE);
		$this->db->from('trans_sales_complaints');
		$this->db->join('trans_sales', 'trans_sales_complaints.sales_id = trans_sales.sales_id');
		$this->db->join('branch_details', 'trans_sales.branch_id = branch_details.branch_id');
		if(!is_null($branch_id))	
			$this->db->where('branch_details.branch_id', $branch_id);
	
		if(!empty($date_from) && !empty($date_to))
			$this->db->where("DATE(trans_sales_complaints.datetime) BETWEEN REPLACE('". $date_from ."',' ','') AND REPLACE('". $date_to ."',' ','')");
		if(!empty($date))
			$this->db->where("DATE(trans_sales_complaints.datetime) = REPLACE('". $date ."',' ','')");
		
		$this->db->group_by(array("trans_sales_complaints.reason", "branch_details.branch_id")); 
		$query = $this->db->get();
		$result = $query->result();
	
		return $result;
	}

	//complete_time - process completed
	//	start_time - start process

	public function get_hit_rate_report_data($branch_id=null, $date_from=null, $date_to=null, $date=null, $args=array()){

		$this->db->select("TIMEDIFF( trans_sales.confirmed_time, trans_sales.datetime) as d_post_confirmed,
						  TIMEDIFF(trans_sales.start_time, trans_sales.confirmed_time) as d_confirmed_process,  
						  TIMEDIFF(trans_sales.start_time, trans_sales.complete_time) as d_process_complete,
						  TIMEDIFF(trans_sales.complete_time, trans_sales.delivered_time) as d_complete_delivered,
						  TIMEDIFF(trans_sales.delivered_time, trans_sales.dispatched_time) as d_dispatch_delivery,
						  TIME(trans_sales.delivered_time) as _delivered_time, TIME(trans_sales.datetime) as _agent_post, 
						  TIME(trans_sales.confirmed_time) as _confirmation_sent, TIME(trans_sales.complete_time) as _done_process_time, 
						  TIME(trans_sales.start_time) as _process_time, trans_sales.*, branch_details.branch_code,  CONCAT(customers.fname, ' ' ,customers.mname, ' ' , customers.lname) as cust_name", FALSE);						
		$this->db->from('trans_sales');
		$this->db->join('customers', 'customers.cust_id = trans_sales.customer_id');
		$this->db->join('branch_details', 'trans_sales.branch_id = branch_details.branch_id');
		if(!is_null($branch_id))		
			$this->db->where('branch_details.branch_id', $branch_id);
		if(!empty($date_from) && !empty($date_to))
			$this->db->where("DATE(trans_sales.datetime) BETWEEN REPLACE('". $date_from ."',' ','') AND REPLACE('". $date_to ."',' ','')");
		if(!empty($date))
			$this->db->where("DATE(trans_sales.datetime) = REPLACE('". $date ."',' ','')");
		
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
		
		// $this->db->group_by(array("trans_sales.reason", "branch_details.branch_id")); 
		$query = $this->db->get();
		$result = $query->result();
	
		return $result;
	
	}

	public function get_cancelled_report($branch_id=null, $date_from=null, $date_to=null, $date=null, $args=array()){
		$this->db->select('COUNT(trans_sales.reason) as count_reason, trans_sales.reason',false);
		$this->db->from('trans_sales');
		$this->db->join('branch_details', 'trans_sales.branch_id = branch_details.branch_id');
		
		if(!is_null($branch_id))	
			$this->db->where('branch_details.branch_id', $branch_id);
		if(!empty($date_from) && !empty($date_to))
			$this->db->where("DATE(trans_sales.datetime) BETWEEN REPLACE('". $date_from ."',' ','') AND REPLACE('". $date_to ."',' ','')");
		if(!empty($date))
			$this->db->where("DATE(trans_sales.datetime) = REPLACE('". $date ."',' ','')");
		
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
		
		$this->db->group_by(array("trans_sales.reason", "branch_details.branch_id")); 
		$query = $this->db->get();
		$result = $query->result();
		
		return $result;
	}

}