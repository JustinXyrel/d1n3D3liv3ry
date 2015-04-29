<?php
class Settings_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function add_promo_details($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->set('update_date', 'NOW()', FALSE);
		$this->db->insert('promo_discounts',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_promo_details($items,$id){
		$this->db->set('update_date', 'NOW()', FALSE);
		$this->db->where('promo_id', $id);
		$this->db->update('promo_discounts', $items);
	}
	public function add_promo_discount_schedules($items){
		$this->db->insert('promo_discount_schedule',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function get_promo_discount_schedules($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('promo_discount_schedule');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('promo_discount_schedule.promo_id',$id);
				}else{
					$this->db->where('promo_discount_schedule.promo_id',$id);
				}
			$this->db->order_by('promo_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function validate_discount_schedules($promo_id=null,$day=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('promo_discount_schedule');
			if($promo_id != null)
				$this->db->where('promo_discount_schedule.promo_id',$promo_id);
			if($day != null)
				$this->db->where('promo_discount_schedule.day',$day);
				
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_promo_discounts($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('promo_discounts');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('promo_discounts.promo_id',$id);
				}else{
					$this->db->where('promo_discounts.promo_id',$id);
				}
			$this->db->order_by('promo_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_promo_discount_items($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('promo_discount_items');
			$this->db->join('menus', 'menus.menu_id = promo_discount_items.item_id');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('promo_discount_items.promo_id',$id);
				}else{
					$this->db->where('promo_discount_items.promo_id',$id);
				}
			$this->db->order_by('promo_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function validate_promo_discount_items($promo_id=null,$item=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('promo_discount_items');
			if($promo_id != null)
				$this->db->where('promo_discount_items.promo_id',$promo_id);
			if($item != null)
				$this->db->where('promo_discount_items.item_id',$item);
			
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_promo_item($items){
		$this->db->insert('promo_discount_items',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function get_uom($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('uom');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('uom.code',$id);
				}else{
					$this->db->where('uom.code',$id);
				}
			$this->db->order_by('code desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_uom($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('uom',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_uom($items,$id){
		$this->db->where('code', $id);
		$this->db->update('uom', $items);
	}
	//-----------Categories-----start-----allyn
	public function get_category($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('categories');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('categories.cat_id',$id);
				}else{
					$this->db->where('categories.cat_id',$id);
				}
			$this->db->order_by('cat_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_category($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('categories',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_category($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('cat_id', $id);
		$this->db->update('categories', $items);
	}
	//-----------Categories-----end-----allyn
	//-----------Sub Categories-----start-----allyn
	public function get_subcategory($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('subcategories');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('subcategories.sub_cat_id',$id);
				}else{
					$this->db->where('subcategories.sub_cat_id',$id);
				}
			$this->db->order_by('sub_cat_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_subcategory($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('subcategories',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_subcategory($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('sub_cat_id', $id);
		$this->db->update('subcategories', $items);
	}
	//-----------Sub Categories-----end-----allyn
	//-----------Suppliers-----start-----allyn
	public function get_supplier($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('suppliers');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('suppliers.supplier_id',$id);
				}else{
					$this->db->where('suppliers.supplier_id',$id);
				}
			$this->db->order_by('supplier_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_supplier($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('suppliers',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_supplier($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('supplier_id', $id);
		$this->db->update('suppliers', $items);
	}
	//-----------Suppliers-----end-----allyn
	//-----------Tax Rates-----start-----allyn
	public function get_tax_rates($id=null,$inactive=0){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('tax_rates');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('tax_rates.tax_id',$id);
				}else{
					$this->db->where('tax_rates.tax_id',$id);
				}
			$this->db->where('tax_rates.inactive',$inactive);
			$this->db->order_by('tax_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_tax_rates($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('tax_rates',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_tax_rates($items,$id){
		$this->db->where('tax_id', $id);
		$this->db->update('tax_rates', $items);
	}
	//-----------Tax Rates-----end-----allyn
	public function get_table_layout($id=null){
		$this->db->trans_start();
			$this->db->select('image');
			$this->db->from('branch_details');
			$this->db->where('branch_details.branch_id',$id);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function update_table_layout($items,$id){
		$this->db->where('branch_id', $id);
		$this->db->update('branch_details', $items);
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
	public function add_tables($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('tables',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_tables($items,$id){
		$this->db->where('tbl_id', $id);
		$this->db->update('tables', $items);
	}
	public function delete_tables($id){
		$this->db->where('tbl_id', $id);
		$this->db->delete('tables');
	}
	public function delete_all_tables(){
		// $this->db->where('tbl_id', $id);
		$this->db->empty_table('tables');
	}
	//-----------Terminals-----start-----allyn
	public function get_terminal($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('terminals');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('terminals.terminal_id',$id);
				}else{
					$this->db->where('terminals.terminal_id',$id);
				}
			$this->db->order_by('terminal_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_terminal($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('terminals',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_terminal($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('terminal_id', $id);
		$this->db->update('terminals', $items);
	}
	//-----------Terminals-----end-----allyn
	//-----------Currencies-----start-----allyn
	public function get_currency($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('currencies');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('currencies.id',$id);
				}else{
					$this->db->where('currencies.id',$id);
				}
			$this->db->order_by('id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_currency($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('currencies',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_currency($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('id', $id);
		$this->db->update('currencies', $items);
	}
	//-----------Currencies-----end-----allyn
	//-----------References-----start-----allyn
	public function get_references(){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('trans_types');
			// $this->db->where('trans_types.type_id',$id);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function update_references($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('type_id', $id);
		$this->db->update('trans_types', $items);
	}
	//-----------References-----end-----allyn
	//-----------Locations-----start-----allyn
	public function get_location($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('locations');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('locations.loc_id',$id);
				}else{
					$this->db->where('locations.loc_id',$id);
				}
			$this->db->order_by('loc_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_location($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('locations',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_location($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('loc_id', $id);
		$this->db->update('locations', $items);
	}
	//-----------Locations-----end-----allyn
	// ------------- Receipt Discounts ------------- //
	public function get_receipt_discounts($id = null)
	{
		$this->db->from('receipt_discounts');
		if (!is_null($id)) {
			if (is_array($id))
				$this->db->where_in('disc_id',$id);
			else
				$this->db->where('disc_id',$id);
		}
		$query = $this->db->get();
		return $query->result();
	}
	public function add_receipt_discount($items)
	{
		$this->db->trans_start();
		$this->db->insert('receipt_discounts',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function update_receipt_discount($items,$id)
	{
		$this->db->trans_start();
		$this->db->where('disc_id',$id);
		$this->db->update('receipt_discounts',$id);
		$this->db->trans_complete();
	}
	// --------- End of Receipt Discounts ---------- //

	//////////////////////jed start
	public function delete_promo_item($ref)
	{
		$this->db->where('id', $ref);
		$this->db->delete('promo_discount_items');
	}
	public function delete_promo_schedule($ref)
	{
		$this->db->where('id', $ref);
		$this->db->delete('promo_discount_schedule');
	}

	////////////////////////jed end
	public function get_charges($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('charges');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('charges.charge_id',$id);
				}else{
					$this->db->where('charges.charge_id',$id);
				}
			$this->db->order_by('charge_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_charges($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('charges',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_charges($items,$id){
		$this->db->where('charge_id', $id);
		$this->db->update('charges', $items);
	}
	public function get_denominations($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('denominations');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('denominations.id',$id);
				}else{
					$this->db->where('denominations.id',$id);
				}
			$this->db->order_by('value desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_denominations($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('denominations',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_denominations($items,$id){
		$this->db->where('id', $id);
		$this->db->update('denominations', $items);
	}
	//-----------deno---jed
	public function get_denomination($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('denominations');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('id',$id);
				}else{
					$this->db->where('id',$id);
				}
			$this->db->order_by('id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_denomination($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('denominations',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_denomination($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('id', $id);
		$this->db->update('denominations', $items);
	}
	public function get_res_details($id=null, $res_id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('res_details');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('res_details.id',$id);
				}else{
					$this->db->where('res_details.id',$id);
				}
			if($res_id != null)
				if(is_array($res_id))
				{
					$this->db->where_in('res_details.res_id',$res_id);
				}else{
					$this->db->where('res_details.res_id',$res_id);
				}
			$this->db->order_by('res_details.res_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

}
?>