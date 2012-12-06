<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$ismert) kilep();

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['kp']=(int)$_REQUEST['kp'];

if ($_REQUEST['kp']<=0) kilep($lang[$lang_lang]['kisphpk']['Legalább 1 KP-t fel kell használnod.']);

$er2=mysql_query('select u.id,u.pontszam_exp_atlag,u.tulaj_szov from bolygok b, userek u where b.id='.$_REQUEST['bolygo_id'].' and b.tulaj=u.id');
$bolygo=mysql_fetch_array($er2);
if (!$bolygo) kilep();
if ($bolygo['id']!=$uid) {
	if ($adataim['karrier']==1 && $adataim['speci']==1) {//mernok
		if ($bolygo['pontszam_exp_atlag']>=$adataim['pontszam_exp_atlag']) kilep($lang[$lang_lang]['kisphpk']['Csak nálad kisebb pontszámú játékosnak fejlesztheted a bolygóját.']);
	} else {//mindenki mas
		kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);
	}
}

$er3=mysql_query('select kp from userek where id='.$uid);
$aux3=mysql_fetch_array($er3);
if ($_REQUEST['kp']>$aux3[0]) $_REQUEST['kp']=$aux3[0];

if ($_REQUEST['kp']<=0) kilep($lang[$lang_lang]['kisphpk']['Nincs KP-d a fejlesztéshez.']);


$effektiv_kp=$_REQUEST['kp'];
if ($adataim['karrier']==1) if ($adataim['speci']==1) $effektiv_kp=5*$effektiv_kp;//mernok

mysql_query('update bolygok set
raforditott_kornyezet_kp=raforditott_kornyezet_kp+'.$effektiv_kp.',
terraformaltsag=round(10000+20000*sqrt(raforditott_kornyezet_kp/terulet)),
terulet_beepitett_effektiv=round(terulet_beepitett/terraformaltsag*10000),
terulet_szabad=greatest(terulet-terulet_beepitett_effektiv,0)
where id='.$_REQUEST['bolygo_id']);
mysql_query('update userek set kp=if(kp>'.$_REQUEST['kp'].',kp-'.$_REQUEST['kp'].',0) where id='.$uid);


if ($bolygo['id']!=$uid) {
	mysql_select_db($database_mmog_nemlog);
	mysql_query('insert into kp_transzfer_log (user_id_1,tulaj_szov_1,user_id_2,tulaj_szov_2,mennyiseg,mikor) values('.$uid.','.$adataim['tulaj_szov'].','.$bolygo['id'].','.$bolygo['tulaj_szov'].','.$_REQUEST['kp'].',"'.date('Y-m-d H:i:s').'")');
	mysql_select_db($database_mmog);
}

kilep();
?>