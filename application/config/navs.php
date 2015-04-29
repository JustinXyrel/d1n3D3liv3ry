<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//////////////////////////////////////////////////
/// SIDE BAR LINKS                            ///
////////////////////////////////////////////////

$nav = array();
// $nav['trans_order'] = array('title'=>'<i class="fa fa-list-alt"></i> <span>Orders</span>','path'=>'trans_order','exclude'=>0);	
$nav['trans_order'] = array('title'=>'<i class="fa fa-list-alt"></i> <span>Orders</span>', 'path'=>'agents','exclude'=>0);	
	// $reportsView['hit_rate'] = array('title'=>'Hit Report','path'=>'reports/hit_rate_report','exclude'=>0);
	// $reportsView['complaint'] = array('title'=>'Complaint Report','path'=>'reports/complaint_report','exclude'=>0);
	// // $reportsView['hold'] = array('title'=>'Hold Report','path'=>'reports/hold_report','exclude'=>0);
	// $reportsView['cancelled'] = array('title'=>'Cancelled Report','path'=>'reports/cancelled_report','exclude'=>0);
$nav['reports'] = array('title'=>'<i class="fa fa-th-list"></i> <span>Reports</span>','path'=>'reports','exclude'=>0);

// $nav['cashier'] = array('title'=>'<i class="fa fa-desktop"></i> <span>Cashier</span>','path'=>'cashier','exclude'=>0);
// $nav['customers'] = array('title'=>'<i class="fa fa-user"></i> <span>Customers</span>','path'=>'customers','exclude'=>0);
// $nav['gift_cards'] = array('title'=>'<i class="fa fa-gift"></i> <span>Gift Cards</span>','path'=>'gift_cards','exclude'=>0);
//$nav['charges'] = array('title'=>'<i class="fa fa-tag"></i> <span>Extra Charges</span>','path'=>'charges','exclude'=>0);
// 	$trans['receiving'] = array('title'=>'Receiving','path'=>'receiving','exclude'=>0);
// 	$trans['adjustment'] = array('title'=>'Adjustment','path'=>'adjustment','exclude'=>0);
// $nav['trans'] = array('title'=>'<i class="fa fa-random"></i> <span>Transactions</span>','path'=>$trans,'exclude'=>0);
// 	$items['list'] = array('title'=>'List','path'=>'items','exclude'=>0);
// 	$items['item_inv'] = array('title'=>'Inventory','path'=>'items/inventory','exclude'=>0);
// $nav['items'] = array('title'=>'<i class="fa fa-shopping-cart"></i> <span>Items</span>','path'=>$items,'exclude'=>0);
	// $resSettings['types'] = array('title'=>'Restaurants','path'=>'restaurant/','exclude'=>0);
// $nav['restaurant'] = array('title'=>'<i class="fa fa-cutlery"></i> <span>Restaurants</span>','path'=>'restaurants','exclude'=>0);
// 	$menus['menulist'] = array('title'=>'List','path'=>'menu','exclude'=>0);
// 	$menus['menucat'] = array('title'=>'Categories','path'=>'menu/categories','exclude'=>0);
// 	$menus['menusched'] = array('title'=>'Schedules','path'=>'menu/schedules','exclude'=>0);
// $nav['menu'] = array('title'=>'<i class="fa fa-cutlery"></i> <span>Menu</span>','path'=>$menus,'exclude'=>0);
// 	$mods['modslist'] = array('title'=>'List','path'=>'mods','exclude'=>0);
// 	$mods['modgrps'] = array('title'=>'Groups','path'=>'mods/groups','exclude'=>0);
// $nav['mods'] = array('title'=>'<i class="fa fa-tags"></i> <span>Modifiers</span>','path'=>$mods,'exclude'=>0);
	//$dtr['schedules'] = array('title'=>'Schedules','path'=>'dtr/dtr_schedules','exclude'=>0);
	// $dtr['shifts'] = array('title'=>'Shifts','path'=>'dtr/dtr_shifts','exclude'=>0);
	// $dtr['scheduler'] = array('title'=>'Scheduler','path'=>'dtr/scheduler','exclude'=>0);
// $nav['dtr'] = array('title'=>'<i class="fa fa-clock-o"></i> <span>DTR</span>','path'=>$dtr,'exclude'=>0);
// 	$generalSettings['gcategories'] = array('title'=>'Categories','path'=>'settings/categories','exclude'=>0);
// 	$generalSettings['gsubcategories'] = array('title'=>'Sub Categories','path'=>'settings/subcategories','exclude'=>0);
// 	$generalSettings['guom'] = array('title'=>'UOM','path'=>'settings/uom','exclude'=>0);
// 	$generalSettings['promos'] = array('title'=>'Promos','path'=>'settings/promos','exclude'=>0);
// 	$generalSettings['discounts'] = array('title'=>'Discounts','path'=>'settings/discounts','exclude'=>0);
// 	$generalSettings['gsuppliers'] = array('title'=>'Suppliers','path'=>'settings/suppliers','exclude'=>0);
// 	$generalSettings['gcustomers'] = array('title'=>'Customers','path'=>'settings/customers','exclude'=>0);
// 	$generalSettings['gtaxrates'] = array('title'=>'Tax Rates','path'=>'settings/tax_rates','exclude'=>0);
// 	$generalSettings['grecdiscs'] = array('title'=>'Receipt Discounts','path'=>'settings/receipt_discounts','exclude'=>0);
// 	$generalSettings['gterminals'] = array('title'=>'Terminals','path'=>'settings/terminals','exclude'=>0);
// 	$generalSettings['gcurrencies'] = array('title'=>'Currencies','path'=>'settings/currencies','exclude'=>0);
// 	$generalSettings['greferences'] = array('title'=>'References','path'=>'settings/references','exclude'=>0);
// 	$generalSettings['glocations'] = array('title'=>'Locations','path'=>'settings/locations','exclude'=>0);
// 	$generalSettings['tblmng'] = array('title'=>'Seating Management','path'=>'settings/seat_management','exclude'=>0);
// 	$generalSettings['denomination'] = array('title'=>'Denominations','path'=>'settings/denomination','exclude'=>0);
// $nav['general_settings'] = array('title'=>'<i class="fa fa-cogs"></i> <span>General Settings</span>','path'=>$generalSettings,'exclude'=>0);
// $nav['setup'] = array('title'=>'<i class="fa fa-cog"></i> <span>Setup</span>','path'=>'setup/details','exclude'=>0);
///ADMIN CONTROL////////////////////////////////
	$controlSettings['user'] = array('title'=>'Users','path'=>'user','exclude'=>0);
	$controlSettings['roles'] = array('title'=>'Roles','path'=>'admin/roles','exclude'=>0);
$nav['control'] = array('title'=>'<i class="fa fa-gear"></i> <span>Admin Control</span>','path'=>$controlSettings,'exclude'=>0);
// $nav['messages'] = array('title'=>'<i class="fa fa-envelope-o"></i> <span>Messages</span>','path'=>'messages','exclude'=>1);
// $nav['messages'] = array('title'=>'<i class="fa fa-envelope-o"></i> <span>Messages</span>','path'=>'messages','exclude'=>1);
// $nav['preferences'] = array('title'=>'<i class="fa fa-wrench"></i> <span>Preferences</span>','path'=>'preference','exclude'=>1);
// $nav['profile'] = array('title'=>'<i class="fa fa-folder-o"></i> <span>Profile</span>','path'=>'profile','exclude'=>1);
///LOGOUT///////////////////////////////////////
$nav['logout'] = array('title'=>'<i class="fa fa-sign-out"></i> <span>Logout</span>','path'=>'site/go_logout','exclude'=>1);
$config['sideNav'] = $nav;
