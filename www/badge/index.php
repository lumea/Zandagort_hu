<?
//exit;
include('../tavoli_zanda_session.php');$x=tavoli_zanda_session();
include('../csatlak.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort <?=$szerver_prefix;?> <?=$lang[$lang_lang]['badge/index.php']['plecsnik'];?></title>
<link rel="stylesheet" type="text/css" href="stilus.css" />
</head>
<body>
<div id="kulso_resz_v2">

<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['badge/index.php']['plecsnik'];?></h1>
</div>

<table>
<?
$er=mysql_query('select b.cim,b.alcim,b.leiras_hu,b.leiras_en
,coalesce(count(ub.user_id),0) as darab
,coalesce(sum(ub.szin=1),0) as arany
,coalesce(sum(ub.szin=2),0) as ezust
,coalesce(sum(ub.szin=3),0) as bronz
from badgek b
left join user_badge ub on b.id=ub.badge_id
where b.id!=1
group by b.id');
?>
<tr>
<th><?=$lang[$lang_lang]['badge/index.php']['név'];?></th>
<th><?=$lang[$lang_lang]['badge/index.php']['leírás'];?></th>
<th><?=$lang[$lang_lang]['badge/index.php']['arany'];?></th>
<th><?=$lang[$lang_lang]['badge/index.php']['ezüst'];?></th>
<th><?=$lang[$lang_lang]['badge/index.php']['bronz'];?></th>
</tr>
<? $i=0;while($aux=mysql_fetch_array($er)) {$i++; ?>
<tr<? if ($i%2) echo ' class="pt"';?>>
<th style="text-align:left"><?=$aux['cim'];?>-<?=$aux['alcim'];?></th>
<td><?=$aux['leiras_'.$lang_lang];?></td>
<td style="text-align:right"><?=$aux['arany'];?></td>
<td style="text-align:right"><?=$aux['ezust'];?></td>
<td style="text-align:right"><?=$aux['bronz'];?></td>
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