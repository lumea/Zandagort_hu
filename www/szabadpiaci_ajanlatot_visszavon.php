<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$er=mysql_query('select * from szabadpiaci_ajanlatok where id='.$_REQUEST['id']);
$ajanlat=mysql_fetch_array($er);
if (!$ajanlat['user_id']) kilep();
if ($ajanlat['user_id']!=$uid) kilep();

if ($ajanlat['vetel']) {/************************** VETEL ***********************************/
	mysql_query('lock tables userek write, szabadpiaci_ajanlatok write');
		mysql_query('update userek set vagyon=vagyon+'.($ajanlat['mennyiseg']*$ajanlat['arfolyam']).' where id='.$uid);
		mysql_query('delete from szabadpiaci_ajanlatok where id='.$ajanlat['id']);
	mysql_query('unlock tables');
} else {/************************** ELADAS ***********************************/
	mysql_query('lock tables bolygo_eroforras write, userek write, szabadpiaci_ajanlatok write');
		if ($ajanlat['termek_id']<150) {//nyersi
			mysql_query('update bolygo_eroforras set db=db+'.$ajanlat['mennyiseg'].' where bolygo_id='.$ajanlat['bolygo_id'].' and eroforras_id='.$ajanlat['termek_id']);
		} else {//KP
			mysql_query('update userek set megoszthato_kp=megoszthato_kp+'.$ajanlat['mennyiseg'].' where id='.$uid);
		}
		mysql_query('delete from szabadpiaci_ajanlatok where id='.$ajanlat['id']);
	mysql_query('unlock tables');
}

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>