<?php
class Manager_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_trans_sales($sales_id=null,$args=array()){
		$this->db->select('trans_sales.*,users.username,terminals.terminal_name');
		$this->db->from('trans_sales');
		$this->db->join('users','trans_sales.user_id = users.id');
		$this->db->join('terminals','trans_sales.terminal_id = terminals.terminal_id');
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
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->order_by('trans_sales.datetime desc');
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
	public function add_trans_sales_menu_modifiers($items){
		$this->db->insert_batch('trans_sales_menu_modifiers',$items);
		return $this->db->insert_id();
	}
	public function delete_trans_sales_menu_modifiers($sales_id){
		$this->db->where('trans_sales_menu_modifiers.sales_id', $sales_id);
		$this->db->delete('trans_sales_menu_modifiers');
	}

	/////////////////////////////////JED///////////////////////////

	public function get_payment_type($date,$ptype){
		$this->db->select('*');
		$this->db->from('trans_sales_payments');
		$this->db->join('trans_sales','trans_sales.sales_id = trans_sales_payments.sales_id');
		$this->db->where('payment_type', $ptype);
		$this->db->where("DATE_FORMAT(trans_sales_payments.datetime,'%Y-%m-%d')",$date);
		$this->db->where("trans_sales.inactive",0);

		$query = $this->db->get();
		return $query->result();

	}

	public function get_payment_count($date,$ptype){
		$this->db->select('*');
		$this->db->from('trans_sales_payments');
		$this->db->join('trans_sales','trans_sales.sales_id = trans_sales_payments.sales_id');
		$this->db->where('payment_type', $ptype);
		$this->db->where("DATE_FORMAT(trans_sales_payments.datetime,'%Y-%m-%d')",$date);
		$this->db->where("trans_sales.inactive",0);
		$this->db->group_by('trans_sales_payments.sales_id');

		$query = $this->db->get();
		return $query->result();

	}

	public function get_summary_type($date,$stype){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('type', $stype);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);
		$this->db->where('type_id', 10);
		$this->db->where('inactive', 0);
		$this->db->where('trans_ref IS NOT NULL', null, false);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_summary_count($date,$stype){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('type', $stype);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);
		$this->db->where('type_id', 10);
		$this->db->where('inactive', 0);
		// $this->db->where('trans_ref is not', null);
		$this->db->where('trans_ref IS NOT NULL', null, false);
		// $this->db->group_by('sales_id');

		$query = $this->db->get();
		return $query->result();

	}
	public function get_terminal_total($date,$terminal){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('terminal_id', $terminal);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);
		$this->db->where('type_id', 10);
		$this->db->where('inactive', 0);
		$this->db->where('trans_ref IS NOT NULL', null, false);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_terminals(){
		$this->db->select('*');
		$this->db->from('terminals');
		$this->db->where('inactive', 0);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_open_order($date){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('inactive', 0);
		$this->db->where('trans_ref', null);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_open_order_total($date){
		$this->db->select_sum('total_amount');
		$this->db->from('trans_sales');
		$this->db->where('inactive', 0);
		$this->db->where('trans_ref', null);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_settled_order($date){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('inactive', 0);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);
		// $this->db->where('trans_ref', null);
		$this->db->where('trans_ref IS NOT NULL', null, false);
		$this->db->where('total_amount = total_paid', null, false);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_transactions($date){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('inactive', 0);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_subtotal($date){
		$this->db->select_sum('total_amount');
		$this->db->from('trans_sales');
		$this->db->where('inactive', 0);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_taxtotal($date){
		$this->db->select_sum('amount');
		$this->db->from('trans_sales_tax');
		$this->db->join('trans_sales','trans_sales.sales_id = trans_sales_tax.sales_id');
		$this->db->where('trans_sales.inactive', 0);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_disctotal($date){
		$this->db->select_sum('amount');
		$this->db->from('trans_sales_tax');
		$this->db->join('trans_sales','trans_sales.sales_id = trans_sales_tax.sales_id');
		$this->db->where('trans_sales.inactive', 0);
		$this->db->where('type_id', 10);
		$this->db->where("DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_voids($date){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where_in('type_id', array('10','11'));
		$this->db->where('inactive', 1);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_void_total($date){
		$this->db->select_sum('total_amount');
		$this->db->from('trans_sales');
		$this->db->where_in('type_id', array('10','11'));
		$this->db->where('inactive', 1);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}

	public function get_void_open($date){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('type_id', 10);
		$this->db->where('inactive', 1);
		$this->db->where('trans_ref', null);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_void_settled($date){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where('type_id', 11);
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);

		$query = $this->db->get();
		return $query->result();

	}
	public function get_all_sales_today($date,$terminal=null){
		$this->db->select('*');
		$this->db->from('trans_sales');
		$this->db->where("DATE_FORMAT(datetime,'%Y-%m-%d')",$date);
		if($terminal != null){
			$this->db->where('terminal_id', $terminal);
		}
		$this->db->where('trans_ref IS NOT NULL', null, false);
		$this->db->where('type_id', 10);

		$query = $this->db->get();
		return $query->result();

	}
	/////////////////////////////////////////////////////////////
	///////////////////End Jed/////////////////////////////////////
	public function get_manager_by_pin($pin)
	{
		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('pin = '.$pin.' AND (role = 1 OR role = 2)');

		$query = $this->db->get();
		return $query->row();
	}







	/* MANAGER REPORT FUNCTIONS */
	public function get_daily_sales($date) {
		$this->db->select('date(datetime) "date", sum(total_amount) "total"');
		$this->db->from('trans_sales');
		$this->db->where('date(datetime)',date('Y-m-d',$date));
		$this->db->where('type_id',SALES_TRANS);
		$this->db->where('inactive',0);
		$this->db->group_by('date(datetime)');

		$query = $this->db->get();
		return $query->result();
	}
	public function get_terminal_daily_sales($date,$terminal_id)
	{
		$this->db->select(
			'date(datetime) "date",
			terminals.terminal_code,
			terminals.terminal_name,
			sum(total_amount) "total",
			');
		$this->db->from('trans_sales');
		$this->db->join('terminals','trans_sales.terminal_id=terminals.terminal_id');
		$this->db->where('date(trans_sales.datetime)',date('Y-m-d',strtotime($date)));
		$this->db->where('trans_sales.type_id',SALES_TRANS);
		$this->db->where('trans_sales.inactive',0);
		$this->db->group_by('date(datetime),trans_sales.terminal_id');

		$query = $this->db->get();
		return $query->result();
	}
	public function get_cashier_daily_sales($date,$cashier_id=null)
	{
		$this->db->select(
			'DATE(datetime) "date",
			users.username,
			users.fname,
			users.mname,
			users.lname,
			SUM(total_amount) "total"');
		$this->db->from('trans_sales');
		$this->db->join('users','trans_sales.user_id=users.id');
		$this->db->where('DATE(datetime)',date('Y-m-d',strtotime($date)));
		$this->db->where('trans_sales.type_id',SALES_TRANS);
		$this->db->where('trans_sales.inactive',0);
		$this->db->group_by('DATE(datetime),trans_sales.user_id');

		$query = $this->db->get();
		return $query->result();
	}
	public function get_customer_daily_count($date)
	{
		$this->db->select('
				COUNT(*)- COUNT(customer_id) "registered",
				COUNT(customer_id) "unregistered"');
		$this->db->from('trans_sales');
		$this->db->where('type_id',SALES_TRANS);
		$this->db->where('inactive',0);

		$query = $this->db->get();
		return $query->row();
	}

}