<?
include('csatlak.php');
$nem_szamit_aktivitasnak=1;include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');


$_REQUEST['szov']=(int)$_REQUEST['szov'];
if ($_REQUEST['szov']>0) {//vendeg szovi?
	$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$_REQUEST['szov'].' and user_id='.$uid);
	if ($aux) {
		$adataim['szovetseg']=$_REQUEST['szov'];
	}
}


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

if ($csat_mapping[2]==-500) if ($adataim['uccso_cset_id']!=0) {mysql_query('update userek set uccso_cset_id=0 where id='.$uid);$adataim['uccso_cset_id']=0;}
if ($csat_mapping[3]==-500) if ($adataim['uccso_priv_cset_id_1']!=0) {mysql_query('update userek set uccso_priv_cset_id_1=0 where id='.$uid);$adataim['uccso_priv_cset_id_1']=0;}
if ($csat_mapping[4]==-500) if ($adataim['uccso_priv_cset_id_2']!=0) {mysql_query('update userek set uccso_priv_cset_id_2=0 where id='.$uid);$adataim['uccso_priv_cset_id_2']=0;}
if ($csat_mapping[5]==-500) if ($adataim['uccso_priv_cset_id_3']!=0) {mysql_query('update userek set uccso_priv_cset_id_3=0 where id='.$uid);$adataim['uccso_priv_cset_id_3']=0;}


$_REQUEST['akt']=(int)$_REQUEST['akt'];

$uccsok=explode(',',$_REQUEST['uccsok']);
for($csat=0;$csat<6;$csat++) {
	$uccsok[$csat]=(int)$uccsok[$csat];
	if ($_REQUEST['elso']==1) {
		switch($csat) {
			case 0:$uccsok[$csat]=$adataim['uccso_nagy_cset_id'];break;
			case 1:$uccsok[$csat]=$adataim['uccso_angol_cset_id'];break;
			case 2:$uccsok[$csat]=$adataim['uccso_cset_id'];break;
			case 3:$uccsok[$csat]=$adataim['uccso_priv_cset_id_1'];break;
			case 4:$uccsok[$csat]=$adataim['uccso_priv_cset_id_2'];break;
			case 5:$uccsok[$csat]=$adataim['uccso_priv_cset_id_3'];break;
		}
	}
	$szurok[$csat]='csh.szov_id='.$csat_mapping[$csat].' and csh.id>'.$uccsok[$csat].' ';
	$szurok_regi[$csat]='csh.szov_id='.$csat_mapping[$csat].' and csh.id<='.$uccsok[$csat].' ';
}

/*
uccso_nagy_cset_id
uccso_angol_cset_id
uccso_cset_id
uccso_priv_cset_id_1
uccso_priv_cset_id_2
uccso_priv_cset_id_3
*/

?>
/*{"h":[<?
for($csat=0;$csat<6;$csat++) {
if ($csat>0) echo ',';
echo '[';

$kell_e=true;
if ($csat==0) if ($user_beallitasok['chat_hu']==0) $kell_e=false;
if ($csat==1) if ($user_beallitasok['chat_en']==0) $kell_e=false;

if ($kell_e) {
$er=mysql_query('select csh.id,csh.ki,csh.mikor,csh.mit,if(csh.szov_id>0,u.nev,if(u.szovetseg>0,concat(u.nev," (",sz.rovid_nev,")"),concat(u.nev," ['.$lang[$lang_lang]['kisphpk']['magányos farkas'].']"))) as nev,coalesce(u.id,0) as user_id
from cset_hozzaszolasok csh
left join userek u on u.id=csh.ki
left join szovetsegek sz on sz.id=u.szovetseg
where '.$szurok[$csat].'order by csh.id');

$i=0;while($hozza=mysql_fetch_array($er)) {
$uccsok[$csat]=$hozza['id'];
if ($i) echo ',';$i++;
?>[<?=$hozza['id'];?>,<?=$hozza['ki'];?>,<?
if ($hozza['ki']) {
	echo json_encode($hozza['nev']);
} else echo '"?"';
?>,<?=json_encode($hozza['mit']);?>,"<?=substr($hozza['mikor'],11);?>",<?=json_encode($hozza['user_id']);?>]<?
}
}

echo ']';
}
?>],"rh":[<?
for($csat=0;$csat<6;$csat++) {
if ($csat>0) echo ',';
echo '[';

if ($_REQUEST['elso']==1) {
$er=mysql_query('select csh.id,csh.ki,csh.mikor,csh.mit,if(csh.szov_id>0,u.nev,if(u.szovetseg>0,concat(u.nev," (",sz.rovid_nev,")"),concat(u.nev," ['.$lang[$lang_lang]['kisphpk']['magányos farkas'].']"))) as nev,coalesce(u.id,0) as user_id
from cset_hozzaszolasok csh
left join userek u on u.id=csh.ki
left join szovetsegek sz on sz.id=u.szovetseg
where '.$szurok_regi[$csat].'order by csh.id');

$i=0;while($hozza=mysql_fetch_array($er)) {
if ($i) echo ',';$i++;
?>[<?=$hozza['id'];?>,<?=$hozza['ki'];?>,<?
if ($hozza['ki']) {
	echo json_encode($hozza['nev']);
} else echo '"?"';
?>,<?=json_encode($hozza['mit']);?>,"<?=substr($hozza['mikor'],11);?>",<?=json_encode($hozza['user_id']);?>]<?
}
}

echo ']';
}
?>],"uccso":[<?

switch($_REQUEST['akt']) {
	case 1:mysql_query('update userek set uccso_nagy_cset_id='.$uccsok[0].' where id='.$uid);break;
	case 2:mysql_query('update userek set uccso_angol_cset_id='.$uccsok[1].' where id='.$uid);break;
	case 3:mysql_query('update userek set uccso_cset_id='.$uccsok[2].' where id='.$uid);break;
	case 4:mysql_query('update userek set uccso_priv_cset_id_1='.$uccsok[3].' where id='.$uid);break;
	case 5:mysql_query('update userek set uccso_priv_cset_id_2='.$uccsok[4].' where id='.$uid);break;
	case 6:mysql_query('update userek set uccso_priv_cset_id_3='.$uccsok[5].' where id='.$uid);break;
}

for($csat=0;$csat<6;$csat++) {
	if ($csat>0) echo ',';
	echo $uccsok[$csat];
}
?>],"online":[<?
// 15 perc = online statusz hossza
for($csat=0;$csat<6;$csat++) {
	if ($csat>0) echo ',';
	switch($csat) {
		case 0:echo '0';break;
		case 1:echo '0';break;
		case 2:echo mysql2jsonmatrix('select nev,timestampdiff(minute,uccso_akt,now()),id from userek where timestampdiff(minute,uccso_akt,now())<15 and szovetseg='.$csat_mapping[$csat].' order by nev');break;
		case 3:
			if ($csat_mapping[3]!=-500)	echo mysql2jsonmatrix('select u.nev,timestampdiff(minute,u.uccso_akt,now()),u.id,u.szovetseg,sz.rovid_nev from cset_szobak cssz left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id left join userek u on u.id=cssz.tulaj or u.id=csszu.user_id left join szovetsegek sz on sz.id=u.szovetseg where cssz.id='.(-$csat_mapping[3]).' group by u.id order by u.nev');
			else echo '0';
		break;
		case 4:
			if ($csat_mapping[4]!=-500)	echo mysql2jsonmatrix('select u.nev,timestampdiff(minute,u.uccso_akt,now()),u.id,u.szovetseg,sz.rovid_nev from cset_szobak cssz left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id left join userek u on u.id=cssz.tulaj or u.id=csszu.user_id left join szovetsegek sz on sz.id=u.szovetseg where cssz.id='.(-$csat_mapping[4]).' group by u.id order by u.nev');
			else echo '0';
		break;
		case 5:
			if ($csat_mapping[5]!=-500)	echo mysql2jsonmatrix('select u.nev,timestampdiff(minute,u.uccso_akt,now()),u.id,u.szovetseg,sz.rovid_nev from cset_szobak cssz left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id left join userek u on u.id=cssz.tulaj or u.id=csszu.user_id left join szovetsegek sz on sz.id=u.szovetseg where cssz.id='.(-$csat_mapping[5]).' group by u.id order by u.nev');
			else echo '0';
		break;
	}
}
?>],"lat":[<?
echo $user_beallitasok['chat_hu'].','.$user_beallitasok['chat_en'];
echo ',1';
if ($csat_mapping[3]!=-500) echo ',1';else echo ',0';
if ($csat_mapping[4]!=-500) echo ',1';else echo ',0';
if ($csat_mapping[5]!=-500) echo ',1';else echo ',0';
?>],"szobak":[<?
$szoba_lista='';
if ($csat_mapping[3]!=-500) $szoba_lista.=','.(-$csat_mapping[3]);
if ($csat_mapping[4]!=-500) $szoba_lista.=','.(-$csat_mapping[4]);
if ($csat_mapping[5]!=-500) $szoba_lista.=','.(-$csat_mapping[5]);
if ($szoba_lista=='') {
} else {
	$szoba_infok[500]['hivatalos']=0;
	$szoba_infok[500]['tulaj_nev']='';
	$szoba_lista=substr($szoba_lista,1);
	$r=mysql_query('select cssz.*,u.nev as tulaj_nev from cset_szobak cssz, userek u where u.id=cssz.tulaj and cssz.id in ('.$szoba_lista.')');
	while($aux=mysql_fetch_array($r)) $szoba_infok[$aux['id']]=$aux;
	echo '['.$csat_mapping[3].','.$szoba_infok[-$csat_mapping[3]]['hivatalos'].',"'.addslashes($szoba_infok[-$csat_mapping[3]]['tulaj_nev']).'"]';
	echo ',['.$csat_mapping[4].','.$szoba_infok[-$csat_mapping[4]]['hivatalos'].',"'.addslashes($szoba_infok[-$csat_mapping[4]]['tulaj_nev']).'"]';
	echo ',['.$csat_mapping[5].','.$szoba_infok[-$csat_mapping[5]]['hivatalos'].',"'.addslashes($szoba_infok[-$csat_mapping[5]]['tulaj_nev']).'"]';
}
?>],"szoba_idk":[<?
echo implode(',',$csat_mapping);
?>]}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>