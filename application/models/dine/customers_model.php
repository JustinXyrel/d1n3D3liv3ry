<?php
class Customers_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_customer($id=null)
	{
		$this->db->select('*');
		$this->db->from('customers');
		// $this->db->join('categories','items.cat_id = categories.cat_id');
		// $this->db->join('subcategories','items.subcat_id = subcategories.sub_cat_id');
		// $this->db->join('item_types','items.type = item_types.id');
		// $this->db->join('suppliers','items.supplier_id = suppliers.supplier_id');
		if (!is_null($id)) {
			if (is_array($id))
				$this->db->where_in('customers.cust_id',$id);
			else
				$this->db->where('customers.cust_id',$id);
		}
		$this->db->order_by('customers.lname ASC');
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result();
	}
	public function get_customer_info($telno=null)
	{
		// SELECT * FROM customers WHERE '4560987' = replace(`phone`, '-', '');
		$sql = "SELECT * FROM `customers` WHERE '$telno' = replace(phone,'-','') ORDER BY customers.lname ASC";
		$query = $this->db->query($sql);
		// echo $this->db->last_query();
		return $query->result();
	}
	public function get_all_customer_count($telno=null){
		// $this->db->select('COUNT(*) as total_count');
		// $this->db->from('customers');
		// if(!empty($telno)){
			// // $this->db->where('customers.phone', $telno);
			// $this->db->where("'$telno' = replace(customers.phone, '-', '')");
		// }
		// $query = $this->db->get();
		
		$sql = "SELECT COUNT(*) as total_count FROM `customers` WHERE '$telno' = replace(phone,'-','')";
		$query = $this->db->query($sql);
		// // echo $this->db->last_query();
		// // $total=$this->db->count_all_results();
		return $query->result();
	}
	public function add_customer($items)
	{
		// $this->db->set('reg_date','NOW()',FALSE);
		$this->db->insert('customers',$items);
		return $this->db->insert_id();
	}
	public function update_customer($items,$id)
	{
		// $this->db->set('update_date','NOW()',FALSE);
		$this->db->where('cust_id',$id);
		$this->db->update('customers',$items);
	}
	public function search_customers($search=""){
		$this->db->trans_start();
			$this->db->select('cust.cust_id,cust.fname,cust.lname,cust.mname,cust.suffix,nos.phone_no, cust.is_vip');
			$this->db->from('customers as cust');
			$this->db->join('customer_nos  as nos','cust.cust_id = nos.cust_id');
			if($search != ""){
				$this->db->like('nos.phone_no', $search);
				$this->db->or_like('cust.fname', $search);
				$this->db->or_like('cust.mname', $search);
				$this->db->or_like('cust.lname', $search);
				$this->db->or_like('cust.suffix', $search);
			}
			$this->db->order_by('cust.fname,cust.lname');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_customer_address($id=null,$adtr=null)
	{
		$this->db->select('*');
		$this->db->from('customer_address');
		if (!is_null($id)) {
			if (is_array($id))
				$this->db->where_in('customer_address.cust_id',$id);
			else
				$this->db->where('customer_address.cust_id',$id);
		}
		if (!is_null($adtr)) {
			if (is_array($adtr))
				$this->db->where_in('customer_address.id',$adtr);
			else
				$this->db->where('customer_address.id',$adtr);
		}
		$this->db->order_by('customer_address.street_no ASC');
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result();
	}
	public function add_customer_address($items)
	{
		// $this->db->set('reg_date','NOW()',FALSE);
		$this->db->insert('customer_address',$items);
		return $this->db->insert_id();
	}
	public function update_customer_address($items,$adtr)
	{
		// $this->db->set('update_date','NOW()',FALSE);
		$this->db->where('id',$adtr);
		$this->db->update('customer_address',$items);
	}
	public function get_customer_address_search($id,$args=array()){
			
			$this->db->select('*');
			$this->db->from('customer_address');
			if(!empty($id)){
				$this->db->where('cust_id',$id);
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
					else{
						$this->db->where($col." LIKE '".$val."'", NULL, FALSE);
					}
				}
			}

			$query = $this->db->get();
			
			$result = $query->result();
			return $result[0];
	}
	public function get_phone_number($id=null, $cust_id=null){
		$this->db->select('*');
		$this->db->from('customer_nos');
		if($id!=null)
			$this->db->where('id', $id);
		if($cust_id!=null)
			$this->db->where('cust_id', $cust_id);
		$query = $this->db->get();
		return $query->result();
	}
	public function update_phone_number($items,$id, $args){
		$this->db->set('customer_nos.update_date','NOW()',FALSE);
		$this->db->where('customer_nos.cust_id',$id);
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
				else{
					$this->db->where($col,$val);
				}
			}
		}

		$this->db->update('customer_nos',$items);
	}
	public function add_phone_number($items)
	{
		$this->db->insert('customer_nos',$items);
		return $this->db->insert_id();
	}
	public function get_cust_info($id=null, $cust_id=null, $args=array()){
// 		$query = "SELECT customers.cust_id,customers.fname,customers.lname, customers.mname,customers.suffix, customer_nos.phone_no, customer_nos.default_no
// FROM customers, customer_nos
// WHERE customers.cust_id = $cust_id
// AND customer_nos.default_no = 1
// AND customers.cust_id = customer_nos.cust_id";

		$this->db->select('customers.cust_id,customers.fname,customers.lname, customers.mname,customers.suffix, customer_nos.phone_no, customer_nos.default_no');
		$this->db->from('customers');
		$this->db->join('customer_nos', 'customer_nos.cust_id = customers.cust_id');
		
		if($cust_id!=null)
			$this->db->where('customers.cust_id', $cust_id);
		if($id!=null)
			$this->db->where('customer_nos.id', $id);

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
				else{
					$this->db->where($col,$val);
				}
			}
		}

		$query = $this->db->get();

		return $query->result();
	}
}