<?
include('../tavoli_zanda_session.php');$x=tavoli_zanda_session();
include('../csatlak.php');
$ismert=0;$uid=false;
if (!empty($_COOKIE['uid'])) {
	$suti_uid=(int)(substr($_COOKIE['uid'],32));
	$suti_session_so=substr($_COOKIE['uid'],0,32);
	$r=mysql_query('select * from userek where id='.$suti_uid.' and kitiltva=0 and inaktiv=0') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($r);
	if ($aux['session_so']==$suti_session_so) {
		if (time()<strtotime($aux['session_ervenyesseg'])) {
			$adataim=$aux;
			$uid=$adataim['id'];
			$ismert=1;
		}
	}
}
//if ($inaktiv_szerver) exit;

$lathatod=false;
$_REQUEST['c']=(int)$_REQUEST['c'];
if ($_REQUEST['c']<=-1000) {
	$szoba=mysql2row('select * from cset_szobak where id='.(-$_REQUEST['c']));
	if (!$szoba) kilep();
	if ($szoba['hivatalos']) $lathatod=true;
}

if (!$lathatod) {
	$csat_mapping=array(0,-1,$adataim['szovetseg'],-500,-500,-500);
	if ($adataim['szovetseg']<=0) $csat_mapping[2]=-500;//nemletezo csetszoba
	$r=mysql_query('select cssz.id
from cset_szobak cssz
left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id
where cssz.tulaj='.$uid.' or csszu.user_id='.$uid.'
group by cssz.id limit 3');
	$cs=0;while($aux=mysql_fetch_array($r)) {
		$cs++;
		$csat_mapping[2+$cs]=-$aux[0];
	}
	$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$_REQUEST['c'].' and user_id='.$uid);
	if ($aux) $csat_mapping[]=$_REQUEST['c'];
	if (!in_array($_REQUEST['c'],$csat_mapping)) kilep();
}

if (isset($_REQUEST['d'])) {
	$datum=$_REQUEST['d'];
} else {
	$aux=mysql2row('select min(left(mikor,10)),max(left(mikor,10)) from '.$database_mmog_nemlog.'.cset_hozzaszolasok_hist where szov_id='.$_REQUEST['c']);
	$datum='';
	$min_datum=$aux[0];
	$max_datum=$aux[1];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<? if ($datum=='') { ?>
<title><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver prefix'];?><?=$szerver_prefix;?><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver'];?> chat log</title>
<? } else { ?>
<title><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver prefix'];?><?=$szerver_prefix;?><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver'];?> <?=$datum?> chat log</title>
<? } ?>
<meta name="description" content="<?=$lang[$lang_lang]['index.php']['Egy sci-fi témájú massively multiplayer online stratégiai játék.'];?>" />
<meta name="keywords" content="<?=$lang[$lang_lang]['index.php']['játék játékok game games online stratégiai űr sci-fi'];?>" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
</head>
<body>
<? if ($datum=='') { ?>
<h1><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver prefix'];?><?=$szerver_prefix;?><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver'];?> chat log</h1>
<h2><?
?></h2>
<pre>
<?
$er=mysql_query('select distinct left(mikor,10)
from '.$database_mmog_nemlog.'.cset_hozzaszolasok_hist
where szov_id='.$_REQUEST['c'].'
and mikor between "'.$min_datum.' 00:00:00" and "'.$max_datum.' 23:59:59"');
while($aux=mysql_fetch_array($er)) {
	echo '<a href=".?c='.$_REQUEST['c'].'&d='.$aux[0].'">'.$aux[0]."</a>\n";
}
?>
</pre>
<? } else { ?>
<h1><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver prefix'];?><?=$szerver_prefix;?><?=$lang[$lang_lang]['index.php']['Zandagort online stratégiai játék - sN szerver'];?> <?=$datum?> chat log</h1>
<pre>
<?
//sz.nev -> sz.rovid_nev
$er=mysql_query('select right(cs.mikor,8) as mikor,if(u.id is null,"<i>'.($lang_lang=='hu'?'törölt játékos':'deleted').'</i>",concat(u.nev," (",coalesce(sz.rovid_nev,"'.($lang_lang=='hu'?'magányos farkas':'lone wolf').'"),")")) as ki,cs.mit
from '.$database_mmog_nemlog.'.cset_hozzaszolasok_hist cs
left join userek u on cs.ki=u.id
left join szovetsegek sz on u.szovetseg=sz.id
where cs.szov_id='.$_REQUEST['c'].'
and cs.mikor between "'.sanitstr($datum).' 00:00:00" and "'.sanitstr($datum).' 23:59:59"
order by cs.id');
//where cs.szov_id='.$_REQUEST['c'].' and left(cs.mikor,10)="'.sanitstr($datum).'"
while($aux=mysql_fetch_array($er)) {
	echo $aux['mikor'].' <b>'.$aux['ki'].'</b> '.$aux['mit']."\n";
}
?>
</pre>
<? } ?>
</body>
</html>
<?
mysql_close($mysql_csatlakozas);
?>