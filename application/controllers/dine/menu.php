<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
    }
	public function index(){
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
        $data = $this->syter->spawn('menu');
        $menus = $this->menu_model->get_menus();
        $data['code'] = menuListPage($menus);
        $this->load->view('page',$data);
    }
    public function form($menu_id=null){
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
        $data = $this->syter->spawn('menu');
        $data['code'] = menuFormPage($menu_id);
        $data['add_css'] = 'js/plugins/typeaheadmap/typeaheadmap.css';
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/menu.php';
        $data['use_js'] = 'menuFormJs';
        $this->load->view('page',$data);
    }
    public function details_load($menu_id=null){
        $this->load->helper('dine/menu_helper');
        $this->load->model('dine/menu_model');
        $menu=array();
        if($menu_id != null){
            $menus = $this->menu_model->get_menus($menu_id);
            $menu=$menus[0];
        }
        $data['code'] = menuDetailsLoad($menu,$menu_id);
        $data['load_js'] = 'dine/menu.php';
        $data['use_js'] = 'detailsLoadJs';
        $this->load->view('load',$data);
    }
    public function details_db(){
        $this->load->model('dine/menu_model');
        $items = array(
            "menu_code"=>$this->input->post('menu_code'),
            "menu_cat_id"=>$this->input->post('menu_cat_id'),
            "menu_barcode"=>$this->input->post('menu_barcode'),
            "menu_sched_id"=>$this->input->post('menu_sched_id'),
            "menu_name"=>$this->input->post('menu_name'),
            "cost"=>$this->input->post('cost'),
            "no_tax"=>(int)$this->input->post('no_tax'),
            "inactive"=>(int)$this->input->post('inactive')
        );

        if($this->input->post('form_menu_id')){
            $this->menu_model->update_menus($items,$this->input->post('form_menu_id'));
            $id = $this->input->post('form_menu_id');
            $act = 'update';
            $msg = 'Updated Menu '.$this->input->post('menu_name');
        }else{
            $id = $this->menu_model->add_menus($items);
            $act = 'add';
            $msg = 'Added  new Menu '.$this->input->post('menu_name');
        }

        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('menu_name'),"act"=>$act,'msg'=>$msg));
    }
    public function recipe_load($menu_id=null)
    {
        $det = $this->menu_model->get_recipe_items($menu_id);
        $data['code'] = menuRecipeLoad($menu_id,null,$det);
        $data['load_js'] = 'dine/menu.php';
        $data['use_js'] = 'recipeLoadJs';
        $this->load->view('load',$data);
    }
    public function recipe_search_item()
    {
        $search = $this->input->post('search');
        $results = $this->menu_model->search_items($search);
        $items = array();
        if(count($results) > 0 ){
            foreach ($results as $res) {
                $items[] = array('key'=>$res->code." ".$res->name,'value'=>$res->item_id);
            }
        }
        echo json_encode($items);
    }
    public function recipe_item_details($item_id=null)
    {
        $this->load->model('dine/items_model');
        $items = $this->items_model->get_item($item_id);
        $item = $items[0];
        $det['cost'] = $item->cost;
        $det['uom'] = $item->uom;
        echo json_encode($det);
    }
    public function recipe_details_db()
    {
        $items = array(
            'menu_id' => $this->input->post('menu-id-hid'),
            'item_id' => $this->input->post('item-id-hid'),
            'uom' => $this->input->post('item-uom-hid'),
            'qty' => $this->input->post('qty'),
            'cost' => $this->input->post('item-cost')
        );

        $recipe_det = $this->menu_model->get_recipe_items($items['menu_id'],$items['item_id']);
        if (count($recipe_det) > 0) {
            $det = $recipe_det[0];
            $this->menu_model->update_recipe_item($items,$items['menu_id'],$items['item_id']);
            $id = $det->recipe_id;
            $item_name = $det->item_name;
            $act = "update";
            $msg = "Updated item: ".$item_name;
        } else {
            $this->load->model('dine/items_model');
            $detx = $this->items_model->get_item($items['item_id']);
            $detx = $detx[0];

            $item_name = $detx->name;
            $id = $this->menu_model->add_recipe_item($items);
            $act = "add";
            $msg = "Add new item: ".$this->input->post('item-search');
        }

        $this->make->sRow(array('id'=>'row-'.$id));
            $this->make->td($item_name);
            $this->make->td($items['uom']);
            $this->make->td($items['qty']);
            $this->make->td($items['cost']);
            $this->make->td($items['qty'] * $items['cost']);
            $a = $this->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$id,'ref'=>$id,'class'=>'del-item','return'=>true));
            $this->make->td($a);
        $this->make->eRow();
        $row = $this->make->code();

        echo json_encode(array('id'=>$id,'row'=>$row,'msg'=>$msg,'act'=>$act));
    }
    public function override_price_total($asJson=true,$updateDB=true){
        $this->load->model('resto/menu_model');
        $total = $this->input->post('total');
        $menu_id = $this->input->post('menu_id');
        $a = $total;
        $b = str_replace( ',', '', $a );

        if( is_numeric( $b ) ) {
            $a = $b;
        }
        $this->menu_model->update_menus(array('cost'=>$a),$menu_id);
    }
    public function get_recipe_total()
    {
        $menu_id = $this->input->post('menu_id');
        $recipe_det = $this->menu_model->get_recipe_items($menu_id);
        $total = 0;
        foreach ($recipe_det as $val) {
            $total += ($val->item_cost * $val->qty);
        }
        echo json_encode(array('total'=>num($total)));
    }
    public function remove_recipe_item()
    {
        $recipe_id = $this->input->post('recipe_id');
        $this->menu_model->remove_recipe_item($recipe_id);
        $json['msg'] = "Recipe Item Deleted.";
        echo json_encode($json);
    }
    /**********     Menu Modifier Groups   **********/
    public function modifier_load($menu_id=null)
    {
        $det = $this->menu_model->get_menu_modifiers($menu_id);
        $data['code'] = menuModifierLoad($menu_id,$det);
        $data['load_js'] = 'dine/menu.php';
        $data['use_js'] = 'menuModifierJs';
        $this->load->view('load',$data);
    }
    public function modifier_search_item()
    {
        $search = $this->input->post('search');
        $results = $this->menu_model->search_modifier_groups($search);
        $items = array();
        if(count($results) > 0 ){
            foreach ($results as $res) {
                $items[] = array('key'=>$res->mod_group_id." ".$res->name,'value'=>$res->mod_group_id);
            }
        }
        echo json_encode($items);
    }
    public function menu_modifier_db()
    {
        if (!$this->input->post())
            header('Location:'.base_url().'menu');

        $items = array(
            'menu_id' => $this->input->post('menu-id-hid'),
            'mod_group_id' => $this->input->post('mod-group-id-hid'),
        );

        $det = $this->menu_model->get_menu_modifiers($items['menu_id'],$items['mod_group_id']);

        if (count($det) == 0) {
            $id = $this->menu_model->add_menu_modifier($items);

            $mod_group = $this->menu_model->get_modifier_groups(array('mod_group_id'=>$items['mod_group_id']));
            $mod_group = $mod_group[0];

            $this->make->sRow(array('id'=>'row-'.$id));
                $this->make->td(fa('fa-asterisk')." ".$mod_group->name);
                $a = $this->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$id,'ref'=>$id,'class'=>'del-item','return'=>true));
                $this->make->td($a);
            $this->make->eRow();

            $row = $this->make->code();

            echo json_encode(array('result'=>'success','msg'=>'Modifier group has been added','row'=>$row));
        } else
            echo json_encode(array('result'=>'error','msg'=>'Menu already has modifier group'));

    }
    public function remove_menu_modifier()
    {
        $id = $this->input->post('id');
        $this->menu_model->remove_menu_modifier($id);
        $json['msg'] = 'Removed modifier group';
        echo json_encode($json);
    }
    /*******   End of  Menu Modifier Groups   *******/
    public function categories(){
        $this->load->model('dine/menu_model');
        $this->load->helper('site/site_forms_helper');
        $menu_categories = $this->menu_model->get_menu_categories();
        $data = $this->syter->spawn('menu');
        $data['page_subtitle'] = "Categories";
        $data['code'] = site_list_form("menu/categories_form","categories_form","Categories",$menu_categories,'menu_cat_name',"menu_cat_id");
        $data['add_js'] = 'js/site_list_forms.js';
        $this->load->view('page',$data);
    }
    public function categories_form($ref=null){
        $this->load->helper('dine/menu_helper');
        $this->load->model('dine/menu_model');
        $cat = array();
        if($ref != null){
            $cats = $this->menu_model->get_menu_categories($ref);
            $cat = $cats[0];
        }
        $this->data['code'] = makeMenuCategoriesForm($cat);
        $this->load->view('load',$this->data);
    }
    public function categories_form_db(){
        $this->load->model('dine/menu_model');
        $items = array();
        $items = array(
            "menu_cat_name"=>$this->input->post('menu_cat_name'),
            "menu_sched_id"=>$this->input->post('menu_sched_id'),
            "menu_cat_order_no"=>$this->input->post('menu_cat_order_no'),
            "inactive"=>(int)$this->input->post('inactive')
        );
        if($this->input->post('menu_cat_id')){
            $this->menu_model->update_menu_categories($items,$this->input->post('menu_cat_id'));
            $id = $this->input->post('menu_cat_id');
            $act = 'update';
            $msg = 'Updated Menu Category . '.$this->input->post('menu_cat_name');
        }else{
            $id = $this->menu_model->add_menu_categories($items);
            $act = 'add';
            $msg = 'Added  new Menu Category '.$this->input->post('menu_cat_name');
        }
        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('menu_cat_name'),"act"=>$act,'msg'=>$msg));
    }
    public function schedules(){
        $this->load->model('dine/menu_model');
        $this->load->helper('site/site_forms_helper');
        $menu_schedules = $this->menu_model->get_menu_schedules();
        $data = $this->syter->spawn('menu');
        $data['page_subtitle'] = "Schedules";
        $data['code'] = site_list_form("menu/schedules_form","schedules_form","Schedules",$menu_schedules,'desc',"menu_sched_id");

        $data['add_js'] = 'js/site_list_forms.js';

        $this->load->view('page',$data);
    }
    public function schedules_form($ref=null){
        $this->load->helper('dine/menu_helper');
        $this->load->model('dine/menu_model');
        $sch = array();
        // if($ref == null)    $ref = $this->input->post('menu_sched_id');
        if($ref != null){
            $schs = $this->menu_model->get_menu_schedules($ref);
            // echo 'REF :: '.$ref;
            $sch = $schs[0];
        }
        $dets = $this->menu_model->get_menu_schedule_details($ref);

        $data['code'] = makeMenuSchedulesForm($sch,$dets);
        $data['load_js'] = 'dine/menu.php';
        $data['use_js'] = 'scheduleJs';
        $this->load->view('load',$data);
    }
    public function menu_sched_db(){
        $this->load->model('dine/menu_model');
        $items = array();
        $items = array("desc"=>$this->input->post('desc'),
                        "inactive"=>(int)$this->input->post('inactive')
            );
        $id = $this->input->post('menu_sched_id');
        $add = "add";
        if($id != ''){
            $this->menu_model->update_menu_schedules($items,$id);
            $add = "upd";
        }else{
            $id = $this->menu_model->add_menu_schedules($items);
        }

        echo json_encode(array("id"=>$id,"act"=>$add,"desc"=>$this->input->post('desc')));
    }
    public function menu_sched_details_db(){
        $this->load->model('dine/menu_model');
        $items = array();
        $items = array("day"=>$this->input->post('day'),
                        "time_on"=>date('H:i:s',strtotime($this->input->post('time_on'))),
                        "time_off"=>date('H:i:s',strtotime($this->input->post('time_off'))),
                        "menu_sched_id"=>$this->input->post('sched_id')
                        );
        // $id = $this->input->post('sched_id');
        $day = $this->input->post('day');

        $count = $this->menu_model->validate_menu_schedule_details($this->input->post('sched_id'),$day);
        if($count == 0){
            // if($id != '')    $this->menu_model->update_menu_schedule_details($items,$id);
            // else             $this->menu_model->add_menu_schedule_details($items);
            $id = $this->menu_model->add_menu_schedule_details($items);
            // echo json_encode(array("msg"=>'success'));
            echo json_encode(array("msg"=>'Successfully Added',"id"=>$this->input->post('sched_id')));
        }else{
            echo json_encode(array("msg"=>'error',"id"=>$this->input->post('sched_id')));
            // echo json_encode(array("msg"=>$count));
            // echo json_encode(array("msg"=>$this->db->last_query()));
        }
    }
    public function remove_schedule_promo_details(){
        $id = $this->input->post('pr_sched_id');
        $this->menu_model->delete_menu_schedule_details($id);
        echo json_encode(array("msg"=>'Successfully Deleted'));
    }
}