<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//////////////////////////////////////////////////
/// Include your css or style sheets          ///
////////////////////////////////////////////////
$styleSheets = array();
$styleSheets[] = "css/bootstrap.min.css";
$styleSheets[] = "css/font-awesome.min.css";
$styleSheets[] = "css/ionicons.min.css";

$styleSheets[] = "css/datatables/dataTables.bootstrap.css";

$styleSheets[] = "css/fileUpload/jquery.fileupload.css";

$styleSheets[] = "css/timepicker/bootstrap-timepicker.css";

$styleSheets[] = "js/plugins/perfect-scrollbar/perfect-scrollbar.css";

$styleSheets[] = "css/AdminLTE.css";
$styleSheets[] = "css/page.css";
// $styleSheets[] = "css/skin-red.css";
$styleSheets[] = "css/skin-gray.css";

$config['incCss'] = $styleSheets;

////////////////////////////////////////////////
/// Include your js files                   ///
//////////////////////////////////////////////
$jsFiles = array();
$jsFiles[] = "js/jquery.min.js";
$jsFiles[] = "js/bootstrap.min.js";

$jsFiles[] = "js/plugins/datatables/jquery.dataTables.js";
$jsFiles[] = "js/plugins/datatables/dataTables.bootstrap.js";

$jsFiles[] = "js/plugins/input-mask/jquery.inputmask.js";
$jsFiles[] = "js/plugins/input-mask/jquery.inputmask.date.extensions.js";
$jsFiles[] = "js/plugins/input-mask/jquery.inputmask.extensions.js";

$jsFiles[] = "js/plugins/noty/packaged/jquery.noty.packaged.min.js";
$jsFiles[] = "js/AdminLTE/app.js";
$jsFiles[] = "js/plugins/sparkline/jquery.sparkline.min.js";
$jsFiles[] = "js/plugins/morris/morris.min.js";
$jsFiles[] = "js/bootbox.js";
$jsFiles[] = "js/jquery.number.js";

$jsFiles[] = "js/plugins/perfect-scrollbar/jquery.mousewheel.js";
$jsFiles[] = "js/plugins/perfect-scrollbar/perfect-scrollbar.js";

$jsFiles[] = "js/plugins/timepicker/bootstrap-timepicker.js";

$jsFiles[] = "js/initial.js";
$jsFiles[] = "js/helper.js";
$config['incJs'] = $jsFiles;
