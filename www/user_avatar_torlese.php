<?
include('csatlak.php');
include('ujkuki.php');

mysql_query('update userek set avatar_ext="" where id='.$uid);

kilep();
?>