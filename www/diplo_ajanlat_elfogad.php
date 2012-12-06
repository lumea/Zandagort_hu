<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']>0) {
	if ($jogaim[7]==0) kilep($lang[$lang_lang]['kisphpk']['Nincs diplomáciai jogod.']);
	$ki_vagy_id=$adataim['szovetseg'];
} else $ki_vagy_id=-$uid;

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from diplomacia_ajanlatok where id='.$_REQUEST['id']);
$ajanlat=mysql_fetch_array($er);
if (!$ajanlat) kilep();
if ($ajanlat['kinek']!=$ki_vagy_id) kilep($lang[$lang_lang]['kisphpk']['Ezt az ajánlatot másnak küldték.']);

if ($ki_vagy_id>0) {
	$er=mysql_query('select nev from szovetsegek where id='.$adataim['szovetseg']);
	$aux=mysql_fetch_array($er);$nev=$aux[0];
} else $nev=$adataim['nev'];

//van-e haboru
$egybol_mehet=true;
$statusz=mysql2row('select * from diplomacia_statuszok where (ki='.$ajanlat['ki'].' and kivel='.$ajanlat['kinek'].') or (ki='.$ajanlat['kinek'].' and kivel='.$ajanlat['ki'].') and mi='.DIPLO_HADI);
if ($statusz) {
	$egybol_mehet=false;
	$ajanlat_tevo=mysql2row('select * from userek where id='.$ajanlat['diplo']);
	if ($ajanlat_tevo) if ($ajanlat_tevo['karrier']==4) if ($ajanlat_tevo['speci']==1) $egybol_mehet=true;//bekebiro
}

if ($egybol_mehet) {
	//beke azonnal; vagy nem is bekekotes van, ami szinten azonnal megy
	mysql_query('delete from diplomacia_statuszok where ki='.$ajanlat['ki'].' and kivel='.$ajanlat['kinek']);
	mysql_query('delete from diplomacia_statuszok where ki='.$ajanlat['kinek'].' and kivel='.$ajanlat['ki']);
	if ($ajanlat['mit']>0) {
		mysql_query('insert into diplomacia_statuszok (ki,kivel,mi,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,diplo_1,diplo_2,nyilvanos)
		values('.$ajanlat['ki'].','.$ajanlat['kinek'].','.$ajanlat['mit'].','.$ajanlat['szoveg_id'].','.$ajanlat['ki'].',"'.sanitstr($ajanlat['szoveg_reszlet']).'",'.$ajanlat['felbontasi_ido'].','.$ajanlat['diplo'].','.$uid.','.$ajanlat['nyilvanos'].'),
		('.$ajanlat['kinek'].','.$ajanlat['ki'].','.$ajanlat['mit'].','.$ajanlat['szoveg_id'].','.$ajanlat['ki'].',"'.sanitstr($ajanlat['szoveg_reszlet']).'",'.$ajanlat['felbontasi_ido'].','.$ajanlat['diplo'].','.$uid.','.$ajanlat['nyilvanos'].')');
	}
	if ($ajanlat['ki']>0) diplouzenet($ajanlat['ki'],'Ajánlat elfogadása',"$nev elfogadta szövetséged ".$diplo_ajanlatok[$ajanlat['mit']]." ajánlatát.",'Offer accepted',"$nev accepted the offer of ".$diplo_ajanlatok_en[$ajanlat['mit']]." of your alliance.");
	else diplouzenet($ajanlat['ki'],'Ajánlat elfogadása',"$nev elfogadta ".$diplo_ajanlatok[$ajanlat['mit']]." ajánlatodat.",'Offer accepted',"$nev accepted your offer of ".$diplo_ajanlatok_en[$ajanlat['mit']].".");
	mysql_query('delete from diplomacia_ajanlatok where id='.$ajanlat['id']);
} else {
	//beke 48 ora mulva
	$lejarat_str=' 48 óra múlva lép életbe.';
	$lejarat_str_en=' It takes effect in 48 hours.';
	$lejarat_mikor=date('Y-m-d H:i:s',time()+3600*48);
	mysql_query('update diplomacia_statuszok set felbontas_alatt=3,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$statusz['ki'].' and kivel='.$statusz['kivel'].' and mi='.DIPLO_HADI);
	mysql_query('update diplomacia_statuszok set felbontas_alatt=3,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$statusz['kivel'].' and kivel='.$statusz['ki'].' and mi='.DIPLO_HADI);
	//
	if ($ajanlat['mit']>0) {
		mysql_query('insert into diplomacia_leendo_statuszok (miota,ki,kivel,mi,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,diplo_1,diplo_2,nyilvanos)
		values("'.$lejarat_mikor.'",'.$ajanlat['ki'].','.$ajanlat['kinek'].','.$ajanlat['mit'].','.$ajanlat['szoveg_id'].','.$ajanlat['ki'].',"'.sanitstr($ajanlat['szoveg_reszlet']).'",'.$ajanlat['felbontasi_ido'].','.$ajanlat['diplo'].','.$uid.','.$ajanlat['nyilvanos'].'),
		("'.$lejarat_mikor.'",'.$ajanlat['kinek'].','.$ajanlat['ki'].','.$ajanlat['mit'].','.$ajanlat['szoveg_id'].','.$ajanlat['ki'].',"'.sanitstr($ajanlat['szoveg_reszlet']).'",'.$ajanlat['felbontasi_ido'].','.$ajanlat['diplo'].','.$uid.','.$ajanlat['nyilvanos'].')');
	}
	if ($ajanlat['ki']>0) diplouzenet($ajanlat['ki'],'Ajánlat elfogadása',"$nev elfogadta szövetséged ".$diplo_ajanlatok[$ajanlat['mit']]." ajánlatát.$lejarat_str",'Offer accepted',"$nev accepted the offer of ".$diplo_ajanlatok_en[$ajanlat['mit']]." of your alliance.$lejarat_str_en");
	else diplouzenet($ajanlat['ki'],'Ajánlat elfogadása',"$nev elfogadta ".$diplo_ajanlatok[$ajanlat['mit']]." ajánlatodat.$lejarat_str",'Offer accepted',"$nev accepted your offer of ".$diplo_ajanlatok_en[$ajanlat['mit']].".$lejarat_str_en");
	mysql_query('delete from diplomacia_ajanlatok where id='.$ajanlat['id']);
	//
}

kilep();
?>