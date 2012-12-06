<?
include('../tavoli_zanda_session.php');$x=tavoli_zanda_session();

$_REQUEST['regb']=(int)$_REQUEST['regb'];if ($_REQUEST['regb']!==1) $_REQUEST['regb']=0;

if (isset($_REQUEST['map_x'])) {
	$x=((int)$_REQUEST['map_x'])-450;
	$y=((int)$_REQUEST['map_y'])-450;
	$z=(int)$_REQUEST['mapz'];if ($z<1) $z=1;
	$reg_x=round($x/$z*100+((int)$_REQUEST['ofs_x']));
	$reg_y=round($y/$z*100+((int)$_REQUEST['ofs_y']));
	$x=round($x/$z/400*8)*5000+((int)$_REQUEST['ofs_x']);
	$y=round($y/$z/400*8)*5000+((int)$_REQUEST['ofs_y']);
	$galaxis_szelessege=40000;
	if ($x<-$galaxis_szelessege) $x=-$galaxis_szelessege;
	if ($x>$galaxis_szelessege) $x=$galaxis_szelessege;
	if ($y<-$galaxis_szelessege) $y=-$galaxis_szelessege;
	if ($y>$galaxis_szelessege) $y=$galaxis_szelessege;
	header('Location: .?ofs_x='.$x.'&ofs_y='.$y.'&regb='.$_REQUEST['regb'].(($_REQUEST['reg']==1)?('&reg=1&reg_x='.$reg_x.'&reg_y='.$reg_y):'').($_REQUEST['all']?('&all='.$_REQUEST['all']):''));exit;
}

$zoom_array_x=array(null,0,-1,1,-1,1);
$zoom_array_y=array(null,0,-1,-1,1,1);
if (isset($_REQUEST['zoom'])) {
	$_REQUEST['zoom']=(int)$_REQUEST['zoom'];
	if ($_REQUEST['zoom']<1) $_REQUEST['zoom']=1;
	if ($_REQUEST['zoom']>5) $_REQUEST['zoom']=5;
}

include('../csatlak.php');//top10 szovi miatt

//if ($inaktiv_szerver) exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort <?=$szerver_prefix;?> <?=$lang[$lang_lang]['map/index.php']['szerver térkép'];?></title>
<link rel="stylesheet" type="text/css" href="stilus.css" />
<script type="text/javascript">
function set_reg_koord(x,y) {
<? if ($lang_lang=='hu') { ?>
	if (x<0) x='Ny '+(-x);else if (x>0) x='K '+x;
	if (y<0) y='É '+(-y);else if (y>0) y='D '+y;
<? } else { ?>
	if (x<0) x='W '+(-x);else if (x>0) x='E '+x;
	if (y<0) y='N '+(-y);else if (y>0) y='S '+y;
<? } ?>
	document.getElementById('reg_koord_terkep').value=y+', '+x;
	return false;
};
</script>
</head>
<body<?
if ($_REQUEST['reg']==1) echo ' onload="set_reg_koord('.((int)$_REQUEST['reg_x']).','.((int)$_REQUEST['reg_y']).')"';
?>>

<?
$zoom_url='';if (isset($_REQUEST['zoom'])) $zoom_url='zoom='.$_REQUEST['zoom'];

?>
<div id="kulso_resz_v2">

<? if ($_REQUEST['reg']==1) {
$reg_url='?regb='.$_REQUEST['regb'].'&reg=1'.($_REQUEST['all']?('&all='.$_REQUEST['all']):'');
$reg_url_amp='&amp;regb='.$_REQUEST['regb'].'&reg=1'.($_REQUEST['all']?('&all='.$_REQUEST['all']):'');
?>
<? } else {
$reg_url='?regb='.$_REQUEST['regb'].($_REQUEST['all']?('&all='.$_REQUEST['all']):'');
$reg_url_amp='&amp;regb='.$_REQUEST['regb'].($_REQUEST['all']?('&all='.$_REQUEST['all']):'');
?>
<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="../img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['map/index.php']['szerver térkép'];?></h1>
</div>
<p><?=$lang[$lang_lang]['map/index.php']['Válassz ezen nézetek közül'];?>: <a href=".<?=$reg_url?>"<? if (!isset($_REQUEST['zoom']) && !isset($_REQUEST['ofs_x'])) echo ' style="font-weight:bold"';?>><?=$lang[$lang_lang]['map/index.php']['teljes'];?></a>, <a href=".?zoom=1<?=$reg_url_amp?>"<? if ($_REQUEST['zoom']==1) echo ' style="font-weight:bold"';?>><?=$lang[$lang_lang]['map/index.php']['centrum'];?></a>, <a href=".?zoom=2<?=$reg_url_amp?>"<? if ($_REQUEST['zoom']==2) echo ' style="font-weight:bold"';?>><?=$lang[$lang_lang]['map/index.php']['ÉNy-i kvadráns'];?></a>, <a href=".?zoom=3<?=$reg_url_amp?>"<? if ($_REQUEST['zoom']==3) echo ' style="font-weight:bold"';?>><?=$lang[$lang_lang]['map/index.php']['ÉK-i kvadráns'];?></a>, <a href=".?zoom=4<?=$reg_url_amp?>"<? if ($_REQUEST['zoom']==4) echo ' style="font-weight:bold"';?>><?=$lang[$lang_lang]['map/index.php']['DNy-i kvadráns'];?></a>, <a href=".?zoom=5<?=$reg_url_amp?>"<? if ($_REQUEST['zoom']==5) echo ' style="font-weight:bold"';?>><?=$lang[$lang_lang]['map/index.php']['DK-i kvadráns'];?></a>, <?=$lang[$lang_lang]['map/index.php']['vagy részletesebb nagyításhoz kattints a térképre'];?>.</p>
<? } ?>


<form action="." method="get">
<? if (((int)$_REQUEST['all'])>0) { ?>
<input type="hidden" name="all" value="<?=((int)$_REQUEST['all'])?>" />
<? } ?>
<input type="hidden" name="mapz" value="<?
if (isset($_REQUEST['ofs_x'])) echo '8';
else {
	if (!isset($_REQUEST['zoom'])) echo '1';
	else echo '2';
}
?>" />
<input type="hidden" name="ofs_x" value="<?
if (!isset($_REQUEST['zoom'])) echo ((int)$_REQUEST['ofs_x']);
else echo 20000*$zoom_array_x[$_REQUEST['zoom']];
?>" />
<input type="hidden" name="ofs_y" value="<?
if (!isset($_REQUEST['zoom'])) echo ((int)$_REQUEST['ofs_y']);
else echo 20000*$zoom_array_y[$_REQUEST['zoom']];
?>" />
<input type="hidden" name="regb" value="<?=$_REQUEST['regb']?>" />


<? if ($_REQUEST['reg']==1) { ?>
<ol style="padding-top: 10px">
<li><?=$lang[$lang_lang]['map/index.php']['kattints oda, ahova kéred a bolygódat'];?> (<?=$lang[$lang_lang]['map/index.php']['nagyításnál az X-szel jelölt bolygók közül választhatsz'];?>)</li>
<li><?=$lang[$lang_lang]['map/index.php']['a térkép bezoomol, ekkor lehetőséged van pontosítani'];?> (<a href=".?reg=1&reg_x=<?=$_REQUEST['reg_x']?>&reg_y=<?=$_REQUEST['reg_y']?>"><?=$lang[$lang_lang]['map/index.php']['kizoomolás'];?></a>)</li>
<li><?=$lang[$lang_lang]['map/index.php']['ha megvan a végleges hely, nyomd meg az "Ide kérem!" gombot'];?></li>
</ol>
<p style="text-align: center"><?=$lang[$lang_lang]['map/index.php']['Kiválasztott koordináta'];?>: <input type="text" class="szoveg" id="reg_koord_terkep" /> <input type="button" class="gomb" value="<?=$lang[$lang_lang]['map/index.php']['Ide kérem!'];?>" onclick="window.opener.document.getElementById('reg_koord').value=document.getElementById('reg_koord_terkep').value;window.close();return false" /></p>
<input type="hidden" name="reg" value="1" />
<? } ?>


<p style="text-align: center"><input type="image" name="map" src="map.php?<?=http_build_query($_GET,'','&amp;');?>" /></p>

<p><?=$lang[$lang_lang]['map/index.php']['Jelmagyarázat'];?>:</p>
<ul>
<li><?=$lang[$lang_lang]['map/index.php']['a bolygó osztályok színei'];?>: <span style="font-weight:bold;color:rgb(83,154,148)">A</span>, <span style="font-weight:bold;color:rgb(236,164,62)">B</span>, <span style="font-weight:bold;color:rgb(196,199,110)">C</span>, <span style="font-weight:bold;color:rgb(70,97,56)">D</span>, <span style="font-weight:bold;color:rgb(225,234,241)">E</span></li>
<li><?=$lang[$lang_lang]['map/index.php']['a már foglalt bolygók halványabban vannak jelölve'];?></li>
<li><?=$lang[$lang_lang]['map/index.php']['a bolygók mérete:<br />
normál bolygó (2M): kis pötty,<br />
nagybolygó (4-10M): nagy pötty (erős zoom-nál kis pötty és a mérettel arányos kör)'];?></li>
<li><?=$lang[$lang_lang]['map/index.php']['a legnagyobb zoom-nál X-szel vannak jelölve azok a (2M-es) bolygók, amikre lehet regisztrálni (a többit csak foglalni lehet)'];?></li>
<?
$regb_nelkuli_get=array();
foreach($_GET as $k=>$v) if ($k!='regb') $regb_nelkuli_get[$k]=$v;
$jelenlegi_url_reb_nelkul=http_build_query($regb_nelkuli_get);
if (strlen($jelenlegi_url_reb_nelkul)>0) $jelenlegi_url_reb_nelkul='?'.$jelenlegi_url_reb_nelkul;else $jelenlegi_url_reb_nelkul='.';
?>
<li><? if ($_REQUEST['regb']) { ?><?=$lang[$lang_lang]['map/index.php']['csak a regisztrálható bolygók látszódnak'];?> (<a href="<?=$jelenlegi_url_reb_nelkul;?>"><?=$lang[$lang_lang]['map/index.php']['látszódjon az összes'];?></a>)<? } else { ?><?=$lang[$lang_lang]['map/index.php']['az összes bolygó látszódik'];?> (<a href="<?=$jelenlegi_url_reb_nelkul.($jelenlegi_url_reb_nelkul=='.'?'?':'&').'regb=1';?>"><?=$lang[$lang_lang]['map/index.php']['csak a regisztrálhatók látszódjanak'];?></a>)<? } ?></li>
<?
$all_nelkuli_get=array();
foreach($_GET as $k=>$v) if ($k!='all') $all_nelkuli_get[$k]=$v;
$jelenlegi_url_all_nelkul=http_build_query($all_nelkuli_get);
if (strlen($jelenlegi_url_all_nelkul)>0) $jelenlegi_url_all_nelkul='?'.$jelenlegi_url_all_nelkul;else $jelenlegi_url_all_nelkul='.';
?>
<li><?=$lang[$lang_lang]['map/index.php']['régiók színezése'];?>: <? if ($_REQUEST['all']==2) { ?><?=$lang[$lang_lang]['map/index.php']['bekapcsolva'];?> (<a href="<?=$jelenlegi_url_all_nelkul;?>"><?=$lang[$lang_lang]['map/index.php']['ki!'];?></a>)<? } else { ?><?=$lang[$lang_lang]['map/index.php']['kikapcsolva'];?> (<a href="<?=$jelenlegi_url_all_nelkul.($jelenlegi_url_all_nelkul=='.'?'?':'&').'all=2';?>"><?=$lang[$lang_lang]['map/index.php']['be!'];?></a>)<? } ?></li>
</ul>

<?
$all_nelkuli_get=array();
foreach($_GET as $k=>$v) if ($k!='all') $all_nelkuli_get[$k]=$v;
$jelenlegi_url_all_nelkul=http_build_query($all_nelkuli_get);
if (strlen($jelenlegi_url_all_nelkul)>0) $jelenlegi_url_all_nelkul='?'.$jelenlegi_url_all_nelkul;else $jelenlegi_url_all_nelkul='.';
?>
<p><?=$lang[$lang_lang]['map/index.php']['Top 10 szövetség'];?> (<?
if (((int)$_REQUEST['all'])==1) {
?><a href="<?=$jelenlegi_url_all_nelkul;?>"><?=$lang[$lang_lang]['map/index.php']['ne látszódjon'];?></a><?
} else {
?><a href="<?=$jelenlegi_url_all_nelkul.($jelenlegi_url_all_nelkul=='.'?'?':'&').'all=1';?>"><?=$lang[$lang_lang]['map/index.php']['látszódjon'];?></a><?
}
?>):</p>
<ol>
<?
$szovi_rgb[0]='255,20,40';
$szovi_rgb[1]='247,91,51';
$szovi_rgb[2]='255,180,0';
$szovi_rgb[3]='255,255,0';
$szovi_rgb[4]='0,255,0';
$szovi_rgb[5]='0,255,200';
$szovi_rgb[6]='20,160,255';
$szovi_rgb[7]='40,80,255';
$szovi_rgb[8]='160,30,255';
$szovi_rgb[9]='255,64,128';
//$er=mysql_query('select sz.id,sz.nev,count(1) as darab from bolygok b, szovetsegek sz where b.tulaj_szov=sz.id and sz.id not in ('.implode(',',$specko_szovetsegek_listaja).') group by sz.id order by count(1) desc,sz.id limit 10');
$er=mysql_query('select sz.id,sz.nev,sum(u.bolygo_szam) as darab
from szovetsegek sz, (
select u.szovetseg,u.pontszam_exp_atlag,count(1) as bolygo_szam,premium,premium_alap from userek u, bolygok b where b.letezik=1 and b.tulaj=u.id group by u.id
) u
where u.szovetseg=sz.id and sz.id not in ('.implode(',',$specko_szovetsegek_listaja).')
group by sz.id
order by sum(u.pontszam_exp_atlag) desc,sz.nev limit 10');
$i=0;while($aux=mysql_fetch_array($er)) {echo '<li style="color:rgb('.$szovi_rgb[$i].')">'.$aux['nev'].' ('.$aux['darab'].' '.$lang[$lang_lang]['map/index.php']['bolygó'.($aux['darab']>1?'k':'')].')</li>';$i++;}
?>
</ol>

</form>

<br /><br />

</div>
</body>
</html>