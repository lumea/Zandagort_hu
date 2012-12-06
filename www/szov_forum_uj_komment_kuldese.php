<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['regi_tema_id']=(int)$_REQUEST['regi_tema_id'];
$_REQUEST['uj_tema']=sanitstr($_REQUEST['uj_tema']);
$_REQUEST['uj_komment']=sanitstr_html($_REQUEST['uj_komment']);
$_REQUEST['uj_tema_belso']=(int)$_REQUEST['uj_tema_belso'];if ($jogaim[1]==0) $_REQUEST['uj_tema_belso']=0;
$_REQUEST['uj_tema_vendeg']=(int)$_REQUEST['uj_tema_vendeg'];if ($jogaim[6]==0) $_REQUEST['uj_tema_vendeg']=0;

if (strlen($_REQUEST['uj_komment'])==0) kilep($lang[$lang_lang]['kisphpk']['Írjál is valamit!']);

if ($_REQUEST['regi_tema_id']==0) {//uj tema (csak szovitag lehet)
	if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);
	if ($jogaim[8]==0) kilep($lang[$lang_lang]['kisphpk']['Nincs témanyitási jogod.']);
	if (strlen($_REQUEST['uj_tema'])==0) kilep($lang[$lang_lang]['kisphpk']['Adj címet a témának!']);
	mysql_query('insert into szov_forum_temak (szov_id,cim,nyito_user,belso,vendeg) values('.$adataim['szovetseg'].',"'.$_REQUEST['uj_tema'].'",'.$uid.','.$_REQUEST['uj_tema_belso'].','.$_REQUEST['uj_tema_vendeg'].')');
	$er=mysql_query('select last_insert_id() from szov_forum_temak');
	$aux=mysql_fetch_array($er);
	$_REQUEST['regi_tema_id']=$aux[0];
}




$tema=mysql2row('select * from szov_forum_temak where id='.$_REQUEST['regi_tema_id']);
$latod=false;$vendeg=false;
if ($tema) {
	if ($tema['szov_id']==$adataim['szovetseg'] and (!$tema['belso'] or $jogaim[1])) $latod=true;
	if (!$latod) if ($tema['vendeg']) {
		$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$tema['szov_id'].' and user_id='.$uid);
		if ($aux) {$latod=true;$vendeg=true;}
	}
}

if (!$latod) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen téma.']);

//if (!$tema || $tema['szov_id']!=$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen téma.']);
//if ($tema['belso'] && ($jogaim[1]==0)) kilep();

$datum=date('Y-m-d H:i:s');
mysql_query('insert into szov_forum_kommentek (tema_id,ki,mit) values('.$tema['id'].','.$uid.',"'.$_REQUEST['uj_komment'].'")');
$er=mysql_query('select last_insert_id() from szov_forum_kommentek');
$aux=mysql_fetch_array($er);
mysql_query('update szov_forum_temak set uccso_user='.$uid.',uccso_datum="'.$datum.'",uccso_komment='.$aux[0].',kommentek_szama=kommentek_szama+1 where id='.$tema['id']);
mysql_query('insert into szov_forum_tema_olv (tema_id,user_id,uccso_komment) values('.$tema['id'].','.$uid.','.$aux[0].') on duplicate key update uccso_komment='.$aux[0]);

mysql_query('update userek set kommakt_szoviforum=kommakt_szoviforum+1 where id='.$uid);

kilep('***'.$tema['id']);
?>