<?php
class Cashier_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	// public function get_trans_sales($sales_id=null,$args=array(), $where_not_in=array(), $advance=false){
	// 	$sel = "trans_sales.*,
	// 		users.username,
	// 		terminals.terminal_name";

	// 	if($advance == true)
	// 	{
	// 		$sel = "(trans_sales.delivery_time + trans_sales.prep_time) as overall_time, 
	// 		TIMESTAMPDIFF(SECOND,NOW(),CONCAT(trans_sales.order_delivery_date,' ',trans_sales.order_delivery_time))/60 as time_diff_minutes,".$sel;
	// 	}

	// 	$this->db->select($sel, FALSE);
	// 	$this->db->from('trans_sales');
	// 	$this->db->join('users','trans_sales.user_id = users.id');
	// 	$this->db->join('terminals','trans_sales.terminal_id = terminals.terminal_id');
		
	// 	if (!is_null($sales_id)){
	// 		if (is_array($sales_id))
	// 			$this->db->where_in('trans_sales.sales_id',$sales_id);
	// 		else
	// 			$this->db->where('trans_sales.sales_id',$sales_id);
	// 	}

	// 	if(!empty($args)){
	// 		foreach ($args as $col => $val) {
	// 			if(is_array($val)){
	// 				if(!isset($val['use'])){
	// 					$this->db->where_in($col,$val);
	// 				}
	// 				else{
	// 					$func = $val['use'];
	// 					if(isset($val['third']))
	// 						$this->db->$func($col,$val['val'],$val['third']);
	// 					else
	// 						$this->db->$func($col,$val['val']);
	// 				}
	// 			}
	// 			else
	// 				$this->db->where($col,$val);
	// 		}
	// 	}
		
	// 	if(!empty($where_not_in))
	// 	{
	// 		if(is_array($where_not_in))
	// 			$this->db->where_not_in('sales_id', $where_not_in);
	// 	}
	// 	$this->db->order_by('trans_sales.datetime desc');
	// 	$query = $this->db->get();
	// 	return $query->result();
	// }
	public function get_trans_sales($sales_id=null,$args=array(),$order='desc', $search_val=null, $search_by=null, $where = null,  $advance=false){
				
		$sel = "trans_sales.*,
			users.username,
			terminals.terminal_name,
			tables.name as table_name ";

		if($advance == true)
		{
			$sel = "(trans_sales.delivery_time + trans_sales.prep_time) as overall_time, TIMESTAMPDIFF(SECOND,NOW(),CONCAT(trans_sales.order_delivery_date,' ',trans_sales.order_delivery_time))/60 as time_diff_minutes, ".$sel;
		}

		$this->db->select($sel, FALSE);
			// trans_sales_payments.payment_type pay_type,
			// trans_sales_payments.amount pay_amount,
			// trans_sales_payments.reference pay_ref,
			// trans_sales_payments.card_type pay_card,
		$this->db->from('trans_sales');
		$this->db->join('users','trans_sales.user_id = users.id');
		$this->db->join('terminals','trans_sales.terminal_id = terminals.terminal_id');
		// $this->db->join('trans_sales_payments','trans_sales.sales_id = trans_sales_payments.sales_id','left');
		$this->db->join('tables','trans_sales.table_id = tables.tbl_id','left');
		$this->db->join('customer_address','trans_sales.address_id = customer_address.id','left');
		// $this->db->join('trans_sales_payments','trans_sales.sales_id = trans_sales_payments.sales_id','left');
		if($search_val != '' && $search_val != 'all')
		{
			if($search_by == 'name')
			{
				$this->db->join('customers','trans_sales.customer_id = customers.cust_id','left');
				$this->db->where("(TRIM(customers.fname) LIKE '%".$search_val."%' 
									OR TRIM(customers.lname) LIKE '%".$search_val."%' 
									OR TRIM(customers.mname) LIKE '%".$search_val."%' 
									OR CONCAT(TRIM(customers.fname), ' ' ,TRIM(customers.lname)) LIKE '%".$search_val."%' )");

			}else if($search_by == 'reference')
			{
				$this->db->where('trans_sales.trans_ref', $search_val); 
			}else if($search_by == 'number')
			{
				$this->db->join('customers','trans_sales.customer_id = customers.cust_id','left');
				$this->db->join('customer_nos','customers.cust_id = customer_nos.cust_id','left');
				$this->db->where('customer_nos.phone_no', $search_val); 
			}else if($search_by == 'code'){
				$this->db->where('trans_sales.delivery_code', $search_val); 
			}else if($search_by == 'address'){
				$this->db->where("(TRIM(customer_address.street_address) LIKE '%".$search_val."%' 
									OR TRIM(customer_address.city) LIKE '%".$search_val."%' 
									OR TRIM(customer_address.region) LIKE '%".$search_val."%' 
									OR TRIM(customer_address.landmark) LIKE '%".$search_val."%' 
									OR CONCAT(TRIM(customer_address.street_address), ' ' ,TRIM(customer_address.city), ' ' ,TRIM(customer_address.region), ' ' ,TRIM(customer_address.landmark)) LIKE '%".$search_val."%' )");
			}
		}
		if($where)
		{
			$this->db->where($where); 
		}	

		if (!is_null($sales_id)){
			if (is_array($sales_id))
				$this->db->where_in('trans_sales.sales_id',$sales_id);
			else
				$this->db->where('trans_sales.sales_id',$sales_id);
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
		$this->db->order_by('trans_sales.datetime',$order);
		// echo $this->db->last_query();
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales($items){
		// $this->db->set('datetime','NOW()',FALSE);
		$this->db->insert('trans_sales',$items);
		return $this->db->insert_id();
	}
	public function update_trans_sales($items,$sales_id){
		$this->db->set('trans_sales.update_date','NOW()',FALSE);
		$this->db->where('trans_sales.sales_id',$sales_id);
		$this->db->update('trans_sales',$items);
	}
	public function get_trans_sales_menus($sales_menu_id=null,$args=array()){
		$this->db->select('trans_sales_menus.*,menus.menu_code,menus.menu_name');
		$this->db->from('trans_sales_menus');
		$this->db->join('menus','trans_sales_menus.menu_id=menus.menu_id');
		if (!is_null($sales_menu_id)){
			if (is_array($sales_menu_id))
				$this->db->where_in('trans_sales_menus.sales_menu_id',$sales_menu_id);
			else
				$this->db->where('trans_sales_menus.sales_menu_id',$sales_menu_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_menus.line_id asc');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales_menus($items){
		$this->db->insert_batch('trans_sales_menus',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_menus($sales_id){
		$this->db->where('trans_sales_menus.sales_id', $sales_id);
		$this->db->delete('trans_sales_menus');
	}
	public function get_trans_sales_menu_modifiers($sales_mod_id=null,$args=array()){
		$this->db->select('trans_sales_menu_modifiers.*,modifiers.name as mod_name');
		$this->db->from('trans_sales_menu_modifiers');
		$this->db->join('modifiers','trans_sales_menu_modifiers.mod_id=modifiers.mod_id');
		if (!is_null($sales_mod_id)){
			if (is_array($sales_mod_id))
				$this->db->where_in('trans_sales_menu_modifiers.sales_mod_id',$sales_mod_id);
			else
				$this->db->where('trans_sales_menu_modifiers.sales_mod_id',$sales_mod_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_menu_modifiers.menu_id desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function get_trans_sales_discounts($sales_disc_id=null,$args=array()){
		$this->db->select('trans_sales_discounts.*');
		$this->db->from('trans_sales_discounts');
		if (!is_null($sales_disc_id)){
			if (is_array($sales_disc_id))
				$this->db->where_in('trans_sales_discounts.sales_disc_id',$sales_disc_id);
			else
				$this->db->where('trans_sales_discounts.sales_disc_id',$sales_disc_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_discounts.sales_disc_id desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales_menu_modifiers($items){
		$this->db->insert_batch('trans_sales_menu_modifiers',$items);
		return $this->db->insert_id();
	}
	public function add_trans_sales_discounts($items){
		$this->db->insert_batch('trans_sales_discounts',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_discounts($sales_id){
		$this->db->where('trans_sales_discounts.sales_id', $sales_id);
		$this->db->delete('trans_sales_discounts');
	}
	public function delete_trans_sales_menu_modifiers($sales_id){
		$this->db->where('trans_sales_menu_modifiers.sales_id', $sales_id);
		$this->db->delete('trans_sales_menu_modifiers');
	}
	public function get_trans_sales_charges($sales_charge_id=null,$args=array()){
		$this->db->select('trans_sales_charges.*');
		$this->db->from('trans_sales_charges');
		if (!is_null($sales_charge_id)){
			if (is_array($sales_charge_id))
				$this->db->where_in('trans_sales_charges.sales_charge_id',$sales_charge_id);
			else
				$this->db->where('trans_sales_charges.sales_charge_id',$sales_charge_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_charges.sales_charge_id desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales_charges($items){
		$this->db->insert_batch('trans_sales_charges',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_charges($sales_id){
		$this->db->where('trans_sales_charges.sales_id', $sales_id);
		$this->db->delete('trans_sales_charges');
	}
	public function get_trans_sales_payments($payment_id=null,$args=array()){
		$this->db->select('trans_sales_payments.*,users.username');
		$this->db->from('trans_sales_payments');
		$this->db->join('users','trans_sales_payments.user_id = users.id');
		if (!is_null($payment_id)){
			if (is_array($payment_id))
				$this->db->where_in('trans_sales_payments.payment_id',$payment_id);
			else
				$this->db->where('trans_sales_payments.payment_id',$payment_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_payments.payment_id desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales_payments($items){
		$this->db->set('datetime','NOW()',FALSE);
		$this->db->insert('trans_sales_payments',$items);
		return $this->db->insert_id();
	}
	public function check_trans_sales_complain($reasons, $sales_id){
		$this->db->like('reason', $reasons);
		$this->db->where('sales_id', $sales_id);
		$this->db->from('trans_sales_complaints');
		return $this->db->count_all_results();
	}
	public function get_trans_sales_complain($id){
		$this->db->select('trans_sales_complaints.*');
		$this->db->from('trans_sales_complaints');
		if (!is_null($id))
			$this->db->where('sales_id', $id);
		$query = $this->db->get();
		return $query->result();
	}
	public function delete_trans_sales_complain($sales_id){
		$this->db->where('trans_sales_complaints.sales_id', $sales_id);
		$this->db->delete('trans_sales_complaints');
	}
	public function add_trans_sales_complain($items){
		$this->db->set('datetime','NOW()',FALSE);
		$this->db->insert('trans_sales_complaints',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_payments($payment_id){
		$this->db->where('trans_sales_payments.payment_id', $payment_id);
		$this->db->delete('trans_sales_payments');
	}
	public function get_trans_sales_tax($sales_tax_id=null,$args=array()){
		$this->db->select('trans_sales_tax.*');
		$this->db->from('trans_sales_tax');
		if (!is_null($sales_tax_id)){
			if (is_array($sales_tax_id))
				$this->db->where_in('trans_sales_tax.sales_tax_id',$sales_tax_id);
			else
				$this->db->where('trans_sales_tax.sales_tax_id',$sales_tax_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_tax.sales_tax_id desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales_tax($items){
		$this->db->insert_batch('trans_sales_tax',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_tax($sales_id){
		$this->db->where('trans_sales_tax.sales_id', $sales_id);
		$this->db->delete('trans_sales_tax');
	}
	public function get_trans_sales_no_tax($sales_no_tax_id=null,$args=array()){
		$this->db->select('trans_sales_no_tax.*');
		$this->db->from('trans_sales_no_tax');
		if (!is_null($sales_no_tax_id)){
			if (is_array($sales_no_tax_id))
				$this->db->where_in('trans_sales_no_tax.sales_no_tax_id',$sales_no_tax_id);
			else
				$this->db->where('trans_sales_no_tax.sales_no_tax_id',$sales_no_tax_id);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales_no_tax.sales_no_tax_id desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_sales_no_tax($items){
		$this->db->insert_batch('trans_sales_no_tax',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_no_tax($sales_id){
		$this->db->where('trans_sales_no_tax.sales_id', $sales_id);
		$this->db->delete('trans_sales_no_tax');
	}
	public function get_tables($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('tables');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('tables.tbl_id',$id);
				}else{
					$this->db->where('tables.tbl_id',$id);
				}
			$this->db->order_by('tbl_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_occupied_tables($tbl_id=null){
		$this->db->trans_start();
			$this->db->select('trans_sales.table_id');
			$this->db->from('trans_sales');
			$this->db->join('tables','trans_sales.table_id = tables.tbl_id');
			if($tbl_id != null){
				if(is_array($tbl_id))
				{
					$this->db->where_in('table_id',$tbl_id);
				}else{
					$this->db->where('table_id',$tbl_id);
				}
			}
			$this->db->where('trans_sales.trans_ref is null','',false);
			$this->db->group_by('table_id');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_menu_promos($id=null){
		$this->db->trans_start();
			$this->db->select('promo_discount_items.*,promo_discounts.promo_code,promo_discounts.promo_name,promo_discounts.value,promo_discounts.absolute');
			$this->db->from('promo_discount_items');
			$this->db->join('promo_discounts','promo_discount_items.promo_id = promo_discounts.promo_id');
			if($id != null){
				if(is_array($id))
				{
					$this->db->where_in('promo_discount_items.item_id',$id);
				}else{
					$this->db->where('promo_discount_items.item_id',$id);
				}
			}
			$this->db->where('promo_discounts.inactive',0);
			$this->db->order_by('promo_discount_items.item_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_menu_promo_schedule($id=null,$day=null,$time=null){
		$this->db->trans_start();
			$this->db->select('promo_discount_schedule.*');
			$this->db->from('promo_discount_schedule, promo_discounts');
			if($id != null){
				if(is_array($id))
				{
					$this->db->where_in('promo_discount_schedule.promo_id',$id);
				}else{
					$this->db->where('promo_discount_schedule.promo_id',$id);
				}
			}
			if($day != null){
				if(is_array($day))
				{
					$this->db->where_in('promo_discount_schedule.day',$day);
				}else{
					$this->db->where('promo_discount_schedule.day',$day);
				}
			}
			if($time != null){
				$this->db->where('promo_discount_schedule.time_on <= TIME("'.$time.'")',null,false);
				$this->db->where('promo_discount_schedule.time_off >= TIME("'.$time.'")',null,false);
				$this->db->where('CURDATE() BETWEEN promo_discounts.date_from AND promo_discounts.date_to ');
			}
			
			$this->db->where('promo_discount_schedule.promo_id = promo_discounts.promo_id ');
			
			// if($time != null){
			// 	$this->db->where('promo_discount_schedule.time_on <= TIME("'.$time.'")',null,false);
			// 	$this->db->where('promo_discount_schedule.time_off >= TIME("'.$time.'")',null,false);
			// }
			
			$this->db->order_by('promo_discount_schedule.id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_read_details($type=X_READ,$date=null){
		$this->db->select('*');
		$this->db->from('read_details');
		$this->db->where('read_type',$type);
		$this->db->where('DATE(read_date)',$date);

		$query = $this->db->get();
		return $query->result();
	}
	public function get_latest_read_date($type){
		$this->db->select_max('read_date','maxi');
		$this->db->from('read_details');
		$this->db->where('read_type',$type);

		$query = $this->db->get();
		return $query->row();
	}
	public function get_user_shifts($args){
		$this->db->select('
			shifts.*,
			users.username,
			terminals.terminal_name,
			sum(shift_entries.amount) cash_float
			',false);
		$this->db->from('shifts');
		$this->db->join('users','shifts.user_id = users.id');
		$this->db->join('terminals','shifts.terminal_id = terminals.terminal_id');
		$this->db->join('shift_entries','shifts.shift_id = shift_entries.shift_id','left');
		if (!empty($args))
			$this->db->where($args);

		$this->db->group_by('shifts.shift_id');

		$query = $this->db->get();
		return $query->result();
	}
	public function add_read_details($items){
		$this->db->trans_start();
		$this->db->insert('read_details',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function get_cashout_header($cashout_id){
		$this->db->select(
			'cashout_entries.*,
			users.username,
			shifts.check_in,
			shifts.check_out,
			terminals.terminal_code,
			terminals.terminal_name');
		$this->db->from('cashout_entries');
		$this->db->join('shifts','cashout_entries.cashout_id = shifts.cashout_id');
		$this->db->join('users','shifts.user_id = users.id');
		$this->db->join('terminals','cashout_entries.terminal_id = terminals.terminal_id');
		$this->db->where('cashout_entries.cashout_id',$cashout_id);

		$query = $this->db->get();
		return $query->row();
	}
	public function get_cashout_details($cashout_id,$id=null){
		$this->db->select('*');
		$this->db->from('cashout_details');
		$this->db->where('cashout_id',$cashout_id);
		if (!is_null($id))
			$this->db->where('id',$id);

		$query = $this->db->get();
		return $query->result();
	}
	public function add_trans_payment_method($items){
		$this->db->set('datetime','NOW()',FALSE);
		$this->db->insert('trans_payment_method',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_payment_method($sales_id){
		$this->db->where('trans_payment_method.sales_id', $sales_id);
		$this->db->delete('trans_payment_method');
	}
	public function get_preparation_time($id){
		$this->db->select('SUM(menus.prep_time*trans_sales_menus.qty) as trans_prep_time_sq', FALSE);
		$this->db->from('trans_sales');
		$this->db->join('trans_sales_menus', 'trans_sales_menus.sales_id = trans_sales.sales_id');
		$this->db->join('menus', 'menus.menu_id = trans_sales_menus.menu_id');
		
		if($id!=null)
			$this->db->where('trans_sales.sales_id', $id);

		$this->db->group_by('trans_sales_menus.sales_id');
		$query = $this->db->get();
		return $query->row(); 
	}
	public function add_reference_address($items){
		$this->db->insert('reference_address',$items);
		return $this->db->insert_id();
	}
	public function get_mode_payment($id){
		$this->db->select('*');
		$this->db->from('trans_payment_method');
		if($id!=null)
			$this->db->where('trans_payment_method.sales_id', $id);
		$query = $this->db->get();
		return $query->result();
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
	public function get_min_purchase($id){
		$this->db->select('rd.min_purchase');
		$this->db->from('branch_details as bd');
		$this->db->join('res_details as rd', 'bd.res_id = rd.res_id');
		$this->db->where('bd.branch_id', $id);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
	public function get_city_search($search=""){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('loc_address');
			if($search != ""){
				$this->db->like('municipality', $search, ' both');
			}
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_reasons_void($all = true){
		$this->db->select('*');
		$this->db->from('reasons_void');

		if(!$all)
		{
			$this->db->where('reasons_void.inactive',0);
		}

		$query = $this->db->get();
		
		return $query->result();
	}
	public function get_reasons_complaint($all = true){
		$this->db->select('*');
		$this->db->from('reasons_complaint');
		if(!$all)
		{
			$this->db->where('reasons_complaint.inactive',0);
		}
		
		$query = $this->db->get();
		
		return $query->result();
	}

}