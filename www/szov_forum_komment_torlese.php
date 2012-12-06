<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_9']) kilep($lang[$lang_lang]['kisphpk']['Nincs moderálási jogod.']);

$_REQUEST['komment_id']=(int)$_REQUEST['komment_id'];

$er=mysql_query('select * from szov_forum_kommentek where id='.$_REQUEST['komment_id']) or hiba(__FILE__,__LINE__,mysql_error());
$komment=mysql_fetch_array($er);
if (!$ismert) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen komment.']);
$er=mysql_query('select * from szov_forum_temak where id='.$komment['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
$tema=mysql_fetch_array($er);
if (!$tema || $tema['szov_id']!=$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen komment.']);
if ($tema['belso'] && ($jogaim[1]==0)) kilep();

mysql_query('delete from szov_forum_kommentek where id='.$_REQUEST['komment_id']) or hiba(__FILE__,__LINE__,mysql_error());


$er=mysql_query('select * from szov_forum_kommentek where tema_id='.$komment['tema_id'].' order by id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) {
	mysql_query('update szov_forum_temak set uccso_user='.$aux['ki'].',uccso_datum="'.$aux['mikor'].'",uccso_komment='.$aux['id'].',kommentek_szama=if(kommentek_szama>0,kommentek_szama-1,0) where id='.$komment['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('insert into szov_forum_tema_olv (tema_id,user_id,uccso_komment) values('.$komment['tema_id'].','.$uid.','.$aux['id'].') on duplicate key update uccso_komment='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
} else {//nincs tobb komment -> tema torlese
	mysql_query('delete from szov_forum_temak where id='.$komment['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('delete from szov_forum_tema_olv where tema_id='.$komment['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
	kilep('***');
}

kilep();
?>