<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['cron_id']=(int)$_REQUEST['cron_id'];

$cron_er=mysql_query('select *,coalesce(100*(1-timestampdiff(second,now(),mikor_aktualis)/timestampdiff(second,mikor_kiadva,mikor_aktualis)),-1) as keszenlet from cron_tabla where id='.$_REQUEST['cron_id'].' and feladat='.FELADAT_GYAR_EPIT);
$cron=mysql_fetch_array($cron_er);
if (!$cron) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen építkezés.']);

$er=mysql_query('select * from gyarak where id='.$cron['gyar_id']) or hiba(__FILE__,__LINE__,mysql_error());
$gyar=mysql_fetch_array($er);
if (!$gyar) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen épülettípus.']);

$er2=mysql_query('select * from bolygok where id='.$cron['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

if ($cron['keszenlet']>=0) $szazalek=100-$cron['keszenlet'];else $szazalek=100;
if ($szazalek>100) $szazalek=100;

//szep lassan, ami utana visszacancelezheto
$er4=mysql_query('select * from gyar_epitesi_ido where tipus='.$gyar['tipus'].' and szint='.$gyar['szint']);
$aux4=mysql_fetch_array($er4);
if ($adataim['karrier']==1 && $adataim['speci']==1) if (in_array($aux4['tipus'],$mernok_8_oras_gyarai)) $aux4['ido']=480;

$ido=6*$aux4['ido'];//6*aux=10%*60*aux
mysql_query('delete from cron_tabla where id='.$cron['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('insert into cron_tabla (mikor_aktualis,feladat,bolygo_id,gyar_id,aktiv,darab,mikor_kiadva,indulo_allapot,beepitett_szazalek) values("'.date('Y-m-d H:i:s',round(time()+(100-$szazalek)/100*$ido)).'",'.FELADAT_GYAR_LEROMBOL.','.$cron['bolygo_id'].','.$cron['gyar_id'].','.$cron['aktiv'].','.$cron['darab'].',"'.date('Y-m-d H:i:s',round(time()-$szazalek/100*$ido)).'",'.$cron['indulo_allapot'].','.((100-$szazalek>$cron['beepitett_szazalek'])?round(100-$szazalek):$cron['beepitett_szazalek']).')')or hiba(__FILE__,__LINE__,mysql_error());

//beepitett_szazalek mar nincs sehol hasznalva

bolygo_terulet_frissites($cron['bolygo_id']);

kilep();
?>