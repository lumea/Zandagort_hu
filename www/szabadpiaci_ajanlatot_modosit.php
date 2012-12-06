<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$arfolyam=sanitint($_REQUEST['arfolyam']);

$er=mysql_query('select * from szabadpiaci_ajanlatok where id='.$_REQUEST['id']);
$ajanlat=mysql_fetch_array($er);
if (!$ajanlat['user_id']) kilep();
if ($ajanlat['user_id']!=$uid) kilep();

//arkorlatozas
if ($arfolyam<=0) kilep();
$regios_arak=mysql2row('select min(arfolyam),max(arfolyam) from tozsdei_arfolyamok where termek_id='.$ajanlat['termek_id']);
if ($arfolyam<$regios_arak[0]) kilep($lang[$lang_lang]['kisphpk']['A minimális ár a legalacsonyabb régiós ár.']);
if ($arfolyam>2*$regios_arak[1]) kilep($lang[$lang_lang]['kisphpk']['A maximális ár a legmagasabb régiós ár duplája.']);


$datum=date('Y-m-d H:i:s');

if ($ajanlat['vetel']) {/************************** VETEL ***********************************/
	mysql_query('lock tables userek write, szabadpiaci_ajanlatok write');
		$ajanlat_mostani_allapota=mysql2row('select * from szabadpiaci_ajanlatok where id='.$ajanlat['id']);
		if ($arfolyam>$ajanlat_mostani_allapota['arfolyam']) {//meg penzt kell beadni
			$mostani_vagyonod=mysql2num('select vagyon from userek where id='.$uid);
			$potlolagos_penz=$ajanlat_mostani_allapota['mennyiseg']*($arfolyam-$ajanlat_mostani_allapota['arfolyam']);
			if ($potlolagos_penz<=$mostani_vagyonod) {
				mysql_query('update userek set vagyon=vagyon-'.$potlolagos_penz.' where id='.$uid);
				mysql_query('update szabadpiaci_ajanlatok set arfolyam='.$arfolyam.',mikor="'.$datum.'" where id='.$ajanlat['id']);
			}
		} elseif ($arfolyam<$ajanlat_mostani_allapota['arfolyam']) {//penzt kapsz vissza
			mysql_query('update userek set vagyon=vagyon+'.($ajanlat_mostani_allapota['mennyiseg']*($ajanlat_mostani_allapota['arfolyam']-$arfolyam)).' where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('update szabadpiaci_ajanlatok set arfolyam='.$arfolyam.',mikor="'.$datum.'" where id='.$ajanlat['id']) or hiba(__FILE__,__LINE__,mysql_error());
		} else {//semmi valtozas
		}
	mysql_query('unlock tables');
} else {/************************** ELADAS ***********************************/
	mysql_query('update szabadpiaci_ajanlatok set arfolyam='.$arfolyam.',mikor="'.$datum.'" where id='.$ajanlat['id']);
}
szabadpiac_tisztit($ajanlat['termek_id']);

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>