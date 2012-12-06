<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

mysql_query('delete from aktivitas_megosztas where ki='.$uid.' and kivel='.((int)$_REQUEST['id'])) or hiba(__FILE__,__LINE__,mysql_error());
kilep();
?>