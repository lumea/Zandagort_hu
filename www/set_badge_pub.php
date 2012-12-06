<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$ismert) kilep();

$_REQUEST['mit']=(int)$_REQUEST['mit'];
$_REQUEST['mire']=(int)$_REQUEST['mire'];
if ($_REQUEST['mire']<0) $_REQUEST['mire']=0;
if ($_REQUEST['mire']>2) $_REQUEST['mire']=2;

mysql_query('update user_badge set publikus='.$_REQUEST['mire'].' where user_id='.$uid.' and badge_id='.$_REQUEST['mit']);

kilep();
?>