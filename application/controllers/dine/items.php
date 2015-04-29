<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Items extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('dine/items_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/items_helper');
	}
	public function index()
	{
		$data = $this->syter->spawn('items');

		$items = $this->items_model->get_item();
		$data['code'] = items_display($items);

		$this->load->view('page',$data);
	}
	public function get_subcategories($cat_id = null)
	{
		$results = $this->site_model->get_custom_val('subcategories',
			array('sub_cat_id,name,code'),
			(is_null($cat_id) ? null : 'cat_id'),
			(is_null($cat_id) ? null : $cat_id),
			true);
		$echo_array = array();
		foreach ($results as $val) {
			$echo_array[$val->sub_cat_id] = "[ ".$val->code." ] ".$val->name;
		}
		echo json_encode($echo_array);
	}
	public function setup($item_id = null)
	{
		$data = $this->syter->spawn();

		if (is_null($item_id))
			$data['page_title'] = fa('fa-cutlery fa-fw')." Add new item";
		else {
			$item = $this->items_model->get_item($item_id);
			$item = $item[0];
			if (!empty($item->code)) {
				$data['page_title'] = fa('fa-cutlery fa-fw')." ".iSetObj($item,'name');
				if (!empty($item->update_date))
					$data['page_subtitle'] = "Last updated ".$item->update_date;

			} else {
				header('Location:'.base_url().'items/setup');
			}
		}

		$data['code'] = items_form_container($item_id);
		$data['load_js'] = "dine/items.php";
		$data['use_js'] = "itemFormContainerJs";

		$this->load->view('page',$data);
	}
	public function setup_load($item_id = null)
	{
		$details = array();
		if (!is_null($item_id))
			$item = $this->items_model->get_item($item_id);
		if (!empty($item))
			$details = $item[0];

		$data['code'] = items_details_form($details,$item_id);
		$data['load_js'] = "dine/items.php";
		$data['use_js'] = "itemDetailsJs";
		$this->load->view('load',$data);
	}
	public function item_details_db()
	{
		// if (!$this->input->post())
			// header("Location:".base_url()."items");

		$items = array(
			'barcode' => $this->input->post('barcode'),
			'code' => $this->input->post('code'),
			'name' => $this->input->post('name'),
			'desc' => $this->input->post('desc'),
			'cat_id' => $this->input->post('cat_id'),
			'subcat_id' => $this->input->post('subcat_id'),
			'supplier_id' => $this->input->post('supplier_id'),
			'uom' => $this->input->post('uom'),
			'cost' => $this->input->post('cost'),
			'type' => $this->input->post('type'),
			'no_per_pack' => $this->input->post('no_per_pack'),
			'no_per_case' => $this->input->post('no_per_case'),
			'reorder_qty' => $this->input->post('reorder_qty'),
			'max_qty' => $this->input->post('max_qty'),
			'inactive' => (int)$this->input->post('inactive'),
		);

		if ($this->input->post('item_id')) {
			$id = $this->input->post('item_id');
			$this->items_model->update_item($items,$id);
			$msg = "Updated item: ".$items['name'];
		} else {
			$id = $this->items_model->add_item($items);
			$msg = "Added new item: ".$items['name'];
		}

		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function inventory()
	{
		$data = $this->syter->spawn('items');

		$query = $this->items_model->get_curr_item_inv_and_locs();
		$records = $query->result_array();

		$loc_fields = array();
		if (!empty($records)) {
			$xx = $records[0];
			foreach ($xx as $k => $v) {
				if (strpos($k, "!!Loc-") === false)
					continue;

				$loc_fields[$k] = str_replace("!!Loc-", "", $k);
			}
		}

		$data['code'] = item_inventory_and_location_container($records, $loc_fields);
		$data['page_title'] = "Item Inventory";
		$data['page_subtitle'] = "Current item count and location";
		$this->load->view('page',$data);
	}
}