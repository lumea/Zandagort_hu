<?
include('../tavoli_zanda_session.php');$x=tavoli_zanda_session();
include('../csatlak.php');
//if ($inaktiv_szerver) exit;

$menu='';
switch($_REQUEST['menu']) {
	case 'xp':$menu='xp';break;
	case 'xp7':$menu='xp7';break;
}


$_REQUEST['k']=(int)$_REQUEST['k'];
if (isset($_REQUEST['sz'])) {
	$kivalasztott_szovi=mysql2row('select * from szovetsegek where id='.((int)$_REQUEST['sz']));
	if ($kivalasztott_szovi) {
	} else {
		$kivalasztott_szovi['id']=0;
	}
}
if (isset($_REQUEST['u'])) {
	$kivalasztott_user=mysql2row('select * from userek where id='.((int)$_REQUEST['u']));
	if ($kivalasztott_user['szovetseg']>0) {
		$kivalasztott_user_szovije=mysql2row('select * from szovetsegek where id='.((int)$kivalasztott_user['szovetseg']));
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort <?=$szerver_prefix;?> <?=$lang[$lang_lang]['top/index.php']['szerver toplista'];?></title>
<link rel="stylesheet" type="text/css" href="stilus.css" />
</head>
<body>
<div id="kulso_resz_v2">
<?
switch($menu) {
	case 'xp':?>
<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['top/index.php']['harci toplista'];?></h1>
</div>
<?
$er=mysql_query('select u.id,u.nev,u.tp,u.rang
,sz.nev as szovnev,sz.rovid_nev as szovrovnev
,if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"") as avatar_fajlnev
from userek u
left join szovetsegek sz on sz.id=u.szovetseg
where u.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and u.id not in ('.implode(',',$specko_userek_listaja).')
and (u.karrier!=3 or u.speci!=3)
and u.tp>0
order by u.tp desc,u.nev');
?>
<table>
<tr>
<th><?=$lang[$lang_lang]['top/index.php']['hely'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['szövetség'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['rang'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['TP'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"><img src="<?
if (strlen($aux['avatar_fajlnev'])) echo '../img/user_avatarok/'.$aux['avatar_fajlnev'];
else echo '../img/ikonok/fantom_avatar.gif';
?>" /></td>
<td><a href=".?u=<?=$aux['id'];?>"><?=$aux['nev'];?></a></td>
<td><?=$aux['szovrovnev'];?></td>
<td><?=$lang[$lang_lang]['top/index.php']['rang_'.$aux['rang']];?></td>
<td style="text-align: right"><?=number_format($aux['tp']/100,2,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
</tr>
<? } ?>
</table>
<br />
<?	break;
	case 'xp7':?>
<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['top/index.php']['heti harci toplista'];?></h1>
</div>
<?
$er=mysql_query('select u.id,u.nev,u.heti_tp,u.rang
,sz.nev as szovnev,sz.rovid_nev as szovrovnev
,if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"") as avatar_fajlnev
from userek u
left join szovetsegek sz on sz.id=u.szovetseg
where u.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and u.id not in ('.implode(',',$specko_userek_listaja).')
and (u.karrier!=3 or u.speci!=3)
and u.heti_tp>0
order by u.heti_tp desc,u.nev');
?>
<table>
<tr>
<th><?=$lang[$lang_lang]['top/index.php']['hely'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['szövetség'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['rang'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['heti TP'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"><img src="<?
if (strlen($aux['avatar_fajlnev'])) echo '../img/user_avatarok/'.$aux['avatar_fajlnev'];
else echo '../img/ikonok/fantom_avatar.gif';
?>" /></td>
<td><a href=".?u=<?=$aux['id'];?>"><?=$aux['nev'];?></a></td>
<td><?=$aux['szovrovnev'];?></td>
<td><?=$lang[$lang_lang]['top/index.php']['rang_'.$aux['rang']];?></td>
<td style="text-align: right"><?=number_format($aux['heti_tp']/100,2,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
</tr>
<? } ?>
</table>
<br />
<?	break;
	default:?>



<? if ($_REQUEST['k']>0) { ?>



<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['top/index.php']['szerver kohorsz toplista'];?></h1>
</div>

<h2><?
if ($_REQUEST['k']>1) echo '<a href=".?k='.($_REQUEST['k']-1).'">&larr;</a> ';
?><?=$lang[$lang_lang]['top/index.php']['. héten regisztrált játékosok prefix'];?><?=$_REQUEST['k']?><?=$lang[$lang_lang]['top/index.php']['. héten regisztrált játékosok'];?><?
$max_kohort=mysql2num('select max(floor(timestampdiff(day,"'.$szerver_indulasa.'",mikortol)/7)+1) from userek');
if ($_REQUEST['k']<$max_kohort) echo ' <a href=".?k='.($_REQUEST['k']+1).'">&rarr;</a>';
?></h2>
<table>
<?
$er=mysql_query('select u.nev,u.pontszam_exp_atlag,sz.nev as szovnev,sz.rovid_nev as szovrovnev,count(b.id) as db,u.id,if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"") as avatar_fajlnev
from userek u
left join szovetsegek sz on sz.id=u.szovetseg
left join bolygok b on b.tulaj=u.id
where u.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and u.id not in ('.implode(',',$specko_userek_listaja).')
and floor(timestampdiff(day,"'.$szerver_indulasa.'",u.mikortol)/7)+1='.$_REQUEST['k'].'
and (u.karrier!=3 or u.speci!=3)
group by u.id
order by u.pontszam_exp_atlag desc,db desc,u.nev,u.id');
?>
<tr>
<th><?=$lang[$lang_lang]['top/index.php']['hely'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['bolygók száma'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['szövetség'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"><img src="<?
if (strlen($aux['avatar_fajlnev'])) echo '../img/user_avatarok/'.$aux['avatar_fajlnev'];
else echo '../img/ikonok/fantom_avatar.gif';
?>" /></td>
<td><a href=".?u=<?=$aux['id'];?>"><?=$aux['nev'];?></a></td>
<td style="text-align: right"><?=number_format($aux['db'],0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
<td><?=$aux['szovrovnev'];?></td><td><?=$aux['szovnev'];?></td>
</tr>
<? } ?>
</table>
<br />

<p><?=$lang[$lang_lang]['top/index.php']['A toplista óránként frissül.'];?></p>



<? } else { ?>



<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['top/index.php']['szerver toplista'];?></h1>
</div>

<? if ($kivalasztott_user) { ?>

<p><br /></p>
<h2><?=$kivalasztott_user['nev']?></h2>
<? if ($kivalasztott_user['szovetseg']>0) { ?>
<h3>[<?=$kivalasztott_user_szovije['rovid_nev']?>] <?=$kivalasztott_user_szovije['nev']?></h3>
<? } else { ?>
<h3><? if ($lang_lang=='hu') echo 'Magányos farkas';else echo 'Lone wolf'; ?></h3>
<? } ?>
<p><img src="user_top.php?u=<?=$kivalasztott_user['id']?>" alt="" /></p>
<p><br /></p>

<? } elseif ($kivalasztott_szovi) { ?>

<h2><?
if ($kivalasztott_szovi['id']>0) {
	echo '['.$kivalasztott_szovi['rovid_nev'].'] '.$kivalasztott_szovi['nev'];
} else {
	if ($lang_lang=='hu') echo 'Magányos farkasok';else echo 'Lone wolves';
}
?></h2>
<table>
<?
$er=mysql_query('select u.nev,u.pontszam_exp_atlag,count(b.id) as db,u.id,if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"") as avatar_fajlnev,u.szovetseg
from userek u
left join szovetsegek sz on sz.id=u.szovetseg
left join bolygok b on b.tulaj=u.id
where u.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and u.id not in ('.implode(',',$specko_userek_listaja).')
and (u.karrier!=3 or u.speci!=3)
group by u.id
order by u.pontszam_exp_atlag desc,db desc,u.nev,u.id');
?>
<tr>
<th><?=$lang[$lang_lang]['top/index.php']['abszolút hely'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['szövin belüli hely'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['bolygók száma'];?></th>
</tr>
<? $i=0;$par=0;while($aux=mysql_fetch_array($er)) {$i++;
if ($aux['szovetseg']==$kivalasztott_szovi['id']) {$par++;
?>
<tr<? if ($par%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"><?=$par;?></td>
<td style="text-align: center"><img src="<?
if (strlen($aux['avatar_fajlnev'])) echo '../img/user_avatarok/'.$aux['avatar_fajlnev'];
else echo '../img/ikonok/fantom_avatar.gif';
?>" /></td>
<td><a href=".?u=<?=$aux['id'];?>"><?=$aux['nev'];?></a></td>
<td style="text-align: right"><?=number_format($aux['db'],0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
</tr>
<? } } ?>
</table>
<br />

<p><?=$lang[$lang_lang]['top/index.php']['A toplista óránként frissül.'];?></p>


<? } else { ?>

<h2><?=$lang[$lang_lang]['top/index.php']['Szövetségek'];?></h2>
<table>
<?
$er=mysql_query('select sz.nev,sz.rovid_nev,sum(u.pontszam_exp_atlag) as szov_pont,sz.tagletszam,sum(u.bolygo_szam) as bolygoszam,if(length(sz.minicimer_ext)>0,concat("p",sz.id,sz.minicimer_crc,".",sz.minicimer_ext),"") as minicimer_fajlnev,sz.id
from szovetsegek sz
left join (select u.szovetseg,u.pontszam_exp_atlag,count(1) as bolygo_szam,premium,premium_alap from userek u, bolygok b where b.tulaj=u.id group by u.id) u on u.szovetseg=sz.id
where sz.id not in ('.implode(',',$specko_szovetsegek_listaja).')
group by sz.id
order by szov_pont desc,sz.nev,sz.id');
?>
<tr>
<th><?=$lang[$lang_lang]['top/index.php']['hely'];?></th>
<th colspan="3"><?=$lang[$lang_lang]['top/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['bolygók száma'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['taglétszám'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['bolygók száma/fő'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"><img src="<?
if (strlen($aux['minicimer_fajlnev'])) echo '../img/minicimerek/'.$aux['minicimer_fajlnev'];
else echo '../img/ikonok/fantom_szovetseg.gif';
?>" /></td>
<td><a href=".?sz=<?=$aux['id'];?>"><?=$aux['rovid_nev'];?></a></td>
<td><a href=".?sz=<?=$aux['id'];?>"><?=$aux['nev'];?></a></td>
<td style="text-align: right"><?=number_format($aux['bolygoszam'],0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
<td style="text-align: right"><?=number_format($aux['tagletszam'],0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
<td style="text-align: right"><? if ($aux['tagletszam']) echo number_format($aux['bolygoszam']/$aux['tagletszam'],1,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
</tr>
<? } ?>
<?
$bolygok_szama=mysql2num('select count(1) from bolygok b, userek u where b.tulaj=u.id and u.szovetseg=0');
$userek_szama=mysql2num('select count(1) from userek where szovetseg=0');
$i++;
?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"></td>
<td colspan="2"><a href=".?sz=0">Magányos farkasok</a></td>
<td style="text-align: right"><?=number_format($bolygok_szama,0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
<td style="text-align: right"><?=number_format($userek_szama,0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
<td style="text-align: right"><? if ($userek_szama) echo number_format($bolygok_szama/$userek_szama,1,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
</tr>
</table>
<br />

<h2><?=$lang[$lang_lang]['top/index.php']['Játékosok'];?></h2>
<table>
<?
$er=mysql_query('select u.nev,u.pontszam_exp_atlag,sz.nev as szovnev,sz.rovid_nev as szovrovnev,count(b.id) as db,u.id,if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"") as avatar_fajlnev
from userek u
left join szovetsegek sz on sz.id=u.szovetseg
left join bolygok b on b.tulaj=u.id
where u.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and u.id not in ('.implode(',',$specko_userek_listaja).')
and (u.karrier!=3 or u.speci!=3)
group by u.id
order by u.pontszam_exp_atlag desc,db desc,u.nev,u.id');
?>
<tr>
<th><?=$lang[$lang_lang]['top/index.php']['hely'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['top/index.php']['bolygók száma'];?></th>
<th colspan="2"><?=$lang[$lang_lang]['top/index.php']['szövetség'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td style="text-align: center"><?=$i;?></td>
<td style="text-align: center"><img src="<?
if (strlen($aux['avatar_fajlnev'])) echo '../img/user_avatarok/'.$aux['avatar_fajlnev'];
else echo '../img/ikonok/fantom_avatar.gif';
?>" /></td>
<td><a href=".?u=<?=$aux['id'];?>"><?=$aux['nev'];?></a></td>
<td style="text-align: right"><?=number_format($aux['db'],0,$lang[$lang_lang]['top/index.php'][','],' ');?></td>
<td><?=$aux['szovrovnev'];?></td><td><?=$aux['szovnev'];?></td>
</tr>
<? } ?>
</table>
<br />

<p><?=$lang[$lang_lang]['top/index.php']['A toplista óránként frissül.'];?></p>

<? } ?>


<? } ?>



<?	break;
}
?>
</div>
</body>
</html>
<?
mysql_close($mysql_csatlakozas);
?>