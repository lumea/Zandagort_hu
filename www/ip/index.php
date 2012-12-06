<?
//exit;
include('../tavoli_zanda_session.php');$x=tavoli_zanda_session();
include('../csatlak.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort <?=$szerver_prefix;?> <?=$lang[$lang_lang]['ip/index.php']['szerver IP-lista'];?></title>
<link rel="stylesheet" type="text/css" href="stilus.css" />
</head>
<body>
<div id="kulso_resz_v2">

<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['ip/index.php']['szerver IP-lista'];?></h1>
</div>

<p><?=$lang[$lang_lang]['ip/index.php']['Akik az utóbbi időben többször egy IP-ről játszottak.'];?></p>
<table>
<?
$er=mysql_query('select u1.nev,u2.nev,sz1.nev,sz2.nev,floor(log(m1.pont)/log(10)*2)-6 as csillagok_szama
from '.$database_mmog_nemlog.'.multi_matrix m1
inner join '.$database_mmog_nemlog.'.multi_matrix m2 on m2.ki=m1.kivel and m2.kivel=m1.ki
inner join userek u1 on m1.ki=u1.id
inner join userek u2 on m1.kivel=u2.id
left join szovetsegek sz1 on sz1.id=u1.szovetseg
left join szovetsegek sz2 on sz2.id=u2.szovetseg
where m1.pont>1000 and m1.pont>2*m1.minusz_pont
and m2.pont>1000 and m2.pont>2*m2.minusz_pont
and u1.id not in ('.implode(',',$specko_userek_listaja).')
and u2.id not in ('.implode(',',$specko_userek_listaja).')
and u1.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).')
and u2.szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).')
order by u1.nev,u1.id,u2.nev,u2.id');
?>
<tr>
<th><?=$lang[$lang_lang]['ip/index.php']['egyik'];?></th>
<th><?=$lang[$lang_lang]['ip/index.php']['másik'];?></th>
<th><?=$lang[$lang_lang]['ip/index.php']['mennyire gyakran'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<td><b><?=$aux[0];?></b><?=(strlen($aux[2])>0)?(' ('.$aux[2].')'):''?></td>
<td><b><?=$aux[1];?></b><?=(strlen($aux[3])>0)?(' ('.$aux[3].')'):''?></td>
<td><?
for($cs=0;$cs<$aux['csillagok_szama'];$cs++) echo '<img src="asterisk_yellow.png" alt="" />';
?></td>
</tr>
<? } ?>
</table>

<br />


</div>
</body>
</html>
<?
mysql_close($mysql_csatlakozas);
?>