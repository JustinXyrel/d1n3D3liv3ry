<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "site";
$route['404_override'] = '';


$route['login'] = "site/login";

# DINE #
	$route['settings'] = "dine/settings";
	$route['settings/(:any)'] = "dine/settings/$1";
	$route['items'] = "dine/items";
	$route['items/(:any)'] = "dine/items/$1";
	$route['customers'] = "dine/customers";
	$route['customers/(:any)'] = "dine/customers/$1";
	$route['setup'] = "dine/setup";
	$route['setup/(:any)'] = "dine/setup/$1";
	$route['menu'] = "dine/menu";
	$route['menu/(:any)'] = "dine/menu/$1";
	$route['mods'] = "dine/mods";
	$route['mods/(:any)'] = "dine/mods/$1";
	$route['receiving'] = "dine/receiving";
	$route['receiving/(:any)'] = "dine/receiving/$1";
	$route['adjustment'] = "dine/adjustment";
	$route['adjustment/(:any)'] = "dine/adjustment/$1";
	$route['cashier'] = "dine/cashier";
	$route['cashier/(:any)'] = "dine/cashier/$1";
	$route['manager'] = "dine/manager";
	$route['manager/(:any)'] = "dine/manager/$1";	$route['dtr'] = "dine/dtr";
	$route['dtr/(:any)'] = "dine/dtr/$1";
	$route['clock'] = "dine/clock";
	$route['clock/(:any)'] = "dine/clock/$1";
	$route['gift_cards'] = "dine/gift_cards";
	$route['gift_cards/(:any)'] = "dine/gift_cards/$1";
	$route['drawer'] = "dine/drawer";
	$route['drawer/(:any)'] = "dine/drawer/$1";
	$route['charges'] = "dine/charges";
	$route['charges/(:any)'] = "dine/charges/$1";

# RESTO #
	$route['restaurants'] = "resto/restaurants";
	$route['restaurants/(:any)'] = "resto/restaurants/$1";
	$route['managements'] = "resto/managements";
	$route['managements/(:any)'] = "resto/managements/$1";
	// $route['menu'] = "resto/menu";
	// $route['menu/(:any)'] = "resto/menu/$1";
	$route['branches'] = "resto/branches";
	$route['branches/(:any)'] = "resto/branches/$1";

# APP #
	$route['ourMenu'] = "app/ourmenu";
	$route['ourMenu/(:any)'] = "app/ourmenu/$1";
	$route['order'] = "app/order";
	$route['order/(:any)'] = "app/order/$1";

# USER #
	$route['user'] = "core/user";
	$route['user/(:any)'] = "core/user/$1";

# ADMIN #
	$route['admin'] = "core/admin";
	$route['admin/(:any)'] = "core/admin/$1";

# WAGON #
	$route['wagon'] = "core/wagon";
	$route['wagon/(:any)'] = "core/wagon/$1";	

# DELIVERY #
	$route['trans'] = "dine/trans";
	$route['trans/(:any)'] = "dine/trans/$1";
	$route['trans_order'] = "dine/trans_order";
	$route['trans_order/(:any)'] = "dine/trans_order/$1";

# AGENTS DELIVERY #
	$route['agents'] = "dine/agent";
	$route['agents/(:any)'] = "dine/agent/$1";
	
# AGENTS REPORTS
	$route['reports'] = "dine/reports";
	$route['reports/(:any)'] = "dine/reports/$1";
/* End of file routes.php */
/* Location: ./application/config/routes.php */