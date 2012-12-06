<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (!$ismert) kilep();

$_REQUEST['cimzettek']=trim($_REQUEST['cimzettek']);
$_REQUEST['targy']=sanitstr_html_special($_REQUEST['targy']);
$_REQUEST['uzenet']=sanitstr_html_special($_REQUEST['uzenet']);

if ($_REQUEST['targy']=='') $_REQUEST['targy']='-';

$cimzettek=explode(',',$_REQUEST['cimzettek']);$cimzett_idk=array();
for($i=0;$i<count($cimzettek);$i++) {
	$cimzettek[$i]=sanitstr($cimzettek[$i]);
	$er=mysql_query('select * from userek where nev="'.$cimzettek[$i].'"') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux) {
		$cimzett_idk[]=$aux['id'];
	} else {
		$er=mysql_query('select * from szovetsegek where nev="'.$cimzettek[$i].'"') or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);
		if ($aux) {
			if ($adataim['szovetseg']>0) {
				if ($adataim['szovetseg']==$aux['id']) $cimzett_idk[]=-$aux['id'];//sajat szovi
				/*else {//idegen szovi
					$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
					$aux2=mysql_fetch_array($res2);
					if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
					//if ($adataim['tisztseg']=-1 || $tiszt_jog['jog_7']) $cimzett_idk[]=-$aux['id'];//van diplomata jogod
					if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_7']) {} else $cimzett_idk[]=-$aux['id'];//van diplomata jogod
				}*/
			}//szovin kivulrol nem levet szovinek irni
		}
	}
}
$cimzett_idk=array_unique($cimzett_idk);

if (count($cimzett_idk)==0) kilep($lang[$lang_lang]['kisphpk']['Ismeretlen, hiányzó vagy nem engedélyezett címzett.']);

$datum=date('Y-m-d H:i:s');

$cimzettek_listaja_string=',';
for($i=0;$i<count($cimzett_idk);$i++) {
	if ($cimzett_idk[$i]>0) $cimzettek_listaja_string.=$cimzett_idk[$i].',';
	elseif ($cimzett_idk[$i]<0) $cimzettek_listaja_string.='s'.(-$cimzett_idk[$i]).',';
}

if (!in_array($uid,$cimzett_idk)) if (!in_array(-$adataim['szovetseg'],$cimzett_idk)) {
	mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,olvasott,mappa,cimzettek) values('.$uid.','.$uid.',"'.$datum.'","'.$_REQUEST['targy'].'","'.$_REQUEST['uzenet'].'",1,"'.(($adataim['nyelv']=='hu')?'Kimenő':'Outgoing').'","'.$cimzettek_listaja_string.'")') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from levelek') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	for($i=0;$i<count($cimzett_idk);$i++) {
		if ($cimzett_idk[$i]>0) mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$cimzett_idk[$i].')') or hiba(__FILE__,__LINE__,mysql_error());
		elseif ($cimzett_idk[$i]<0) mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_SZOVETSEG.','.(-$cimzett_idk[$i]).')') or hiba(__FILE__,__LINE__,mysql_error());
	}
}

for($j=0;$j<count($cimzett_idk);$j++) {
	if ($cimzett_idk[$j]>0) {
		$er_cimzett=mysql_query('select nyelv from userek where id='.$cimzett_idk[$j]) or hiba(__FILE__,__LINE__,mysql_error());
		$aux_cimzett=mysql_fetch_array($er_cimzett);
		mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek,mappa) values('.$uid.','.$cimzett_idk[$j].',"'.$datum.'","'.$_REQUEST['targy'].'","'.$_REQUEST['uzenet'].'","'.$cimzettek_listaja_string.'","'.(($aux_cimzett['nyelv']=='hu')?'Bejövő':'Incoming').'")') or hiba(__FILE__,__LINE__,mysql_error());
		$er=mysql_query('select last_insert_id() from levelek') or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);
		for($i=0;$i<count($cimzett_idk);$i++) if ($cimzett_idk[$i]>0) mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$cimzett_idk[$i].')') or hiba(__FILE__,__LINE__,mysql_error());
	} elseif ($cimzett_idk[$j]<0) {
		$er_sz=mysql_query('select id,nyelv from userek where szovetseg='.(-$cimzett_idk[$j])) or hiba(__FILE__,__LINE__,mysql_error());
		while($aux_sz=mysql_fetch_array($er_sz)) {
			mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek,mappa) values('.$uid.','.$aux_sz[0].',"'.$datum.'","'.$_REQUEST['targy'].'","'.$_REQUEST['uzenet'].'","'.$cimzettek_listaja_string.'","'.(($aux_sz['nyelv']=='hu')?'Bejövő':'Incoming').'")') or hiba(__FILE__,__LINE__,mysql_error());
			$er=mysql_query('select last_insert_id() from levelek') or hiba(__FILE__,__LINE__,mysql_error());
			$aux=mysql_fetch_array($er);
			for($i=0;$i<count($cimzett_idk);$i++) {
				if ($cimzett_idk[$i]>0) mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$cimzett_idk[$i].')') or hiba(__FILE__,__LINE__,mysql_error());
				elseif ($cimzett_idk[$i]<0) mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_SZOVETSEG.','.(-$cimzett_idk[$i]).')') or hiba(__FILE__,__LINE__,mysql_error());
			}
		}
	}
}

mysql_query('update userek set kommakt_level=kommakt_level+1 where id='.$uid);

kilep();
?>