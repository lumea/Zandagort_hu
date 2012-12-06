<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (!$ismert) kilep();

$_REQUEST['id']=(int)$_REQUEST['id'];

mysql_query('delete from csata_user where csata_id='.$_REQUEST['id'].' and user_id='.$uid);
kilep();
?>