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

$_REQUEST['kinek']=sanitstr($_REQUEST['kinek']);
if (strlen($_REQUEST['kinek'])==0) kilep($lang[$lang_lang]['kisphpk']['Írd be, hogy melyik szövetségnek vagy magányos játékosnak küldöd.']);
$er=mysql_query('select * from userek where szovetseg=0 and nev="'.$_REQUEST['kinek'].'"');
$kinek=mysql_fetch_array($er);$kinek_id=-$kinek['id'];
if (!$kinek) {
	$er=mysql_query('select * from szovetsegek where id!='.$adataim['szovetseg'].' and nev="'.$_REQUEST['kinek'].'"');
	$kinek=mysql_fetch_array($er);$kinek_id=$kinek['id'];
	if (!$kinek) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen szövetség vagy magányos játékos.']);
}

$_REQUEST['mirol']=(int)$_REQUEST['mirol'];
$meddig=mb_strpos($_REQUEST['szoveg'],"\n",0,'utf-8');
if ($meddig===false) $szoveg_reszlet=sanitstr(mb_substr($_REQUEST['szoveg'],0,100,'utf-8'));
else $szoveg_reszlet=sanitstr(mb_substr($_REQUEST['szoveg'],0,min($meddig,100),'utf-8'));
$megjegyzes=trim($_REQUEST['szoveg']);if (strlen($megjegyzes)) $megjegyzes="\n---\n$megjegyzes";
$_REQUEST['szoveg']=sanitstr($_REQUEST['szoveg']);

$_REQUEST['lejarat']=(int)$_REQUEST['lejarat'];
if ($_REQUEST['lejarat']<0) $_REQUEST['lejarat']=0;
if ($_REQUEST['lejarat']>48) $_REQUEST['lejarat']=48;
if ($_REQUEST['lejarat']>12) {
	if ($adataim['karrier']!=4) kilep($lang[$lang_lang]['kisphpk']['12 óránál hosszabb ajánlatot csak diplomata karrierrel rendelkező játékos küldhet.']);
}
$lejarat_str='';if ($_REQUEST['lejarat']>0) $lejarat_str=' '.$_REQUEST['lejarat'].' óra felbontási idővel';
$lejarat_str_en='';if ($_REQUEST['lejarat']>0) $lejarat_str_en=' with a break-up time of '.$_REQUEST['lejarat'].' hours';

if ($ki_vagy_id>0) {
	$er=mysql_query('select nev from szovetsegek where id='.$adataim['szovetseg']);
	$aux=mysql_fetch_array($er);$nev=$aux[0];
} else $nev=$adataim['nev'];
$ki_id=$ki_vagy_id;


$nyilvanos=0;
if ($_REQUEST['lejarat']>12) {
	$nyilvanos=1;
	if ($adataim['karrier']==4) if ($adataim['speci']==2) $nyilvanos=0;
}

switch($_REQUEST['mirol']) {
	case 0://tuzszunet
		$er=mysql_query('select * from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id.' and mi=1');
		$aux=mysql_fetch_array($er);
		if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nem álltok háborúban egymással, nincs szükség tűzszünetre.']);
		//
		mysql_query('insert into diplomacia_szovegek (szoveg) values("'.$_REQUEST['szoveg'].'")');
		$er=mysql_query('select last_insert_id() from diplomacia_szovegek');
		$aux=mysql_fetch_array($er);
		mysql_query('insert into diplomacia_ajanlatok (ki,kinek,mit,szoveg_id,szoveg_reszlet,diplo,nyilvanos) values('.$ki_id.','.$kinek_id.',0,'.$aux[0].',"'.$szoveg_reszlet.'",'.$uid.',0)');
		if ($kinek_id>0) diplouzenet($kinek_id,'Tűzszüneti ajánlat',"$nev tűzszüntetet kötne a szövetségeddel.$megjegyzes",'Offer of cease-fire',"$nev would like to contract a cease-fire with your alliance.$megjegyzes");
		else diplouzenet($kinek_id,'Tűzszüneti ajánlat',"$nev tűzszüntetet kötne veled.$megjegyzes",'Offer of cease-fire',"$nev would like to contract a cease-fire with you.$megjegyzes");
		kilep();
	break;
	case 2://testver
		mysql_query('insert into diplomacia_szovegek (szoveg) values("'.$_REQUEST['szoveg'].'")');
		$er=mysql_query('select last_insert_id() from diplomacia_szovegek');
		$aux=mysql_fetch_array($er);
		mysql_query('insert into diplomacia_ajanlatok (ki,kinek,mit,szoveg_id,szoveg_reszlet,felbontasi_ido,diplo,nyilvanos) values('.$ki_id.','.$kinek_id.',2,'.$aux[0].',"'.$szoveg_reszlet.'",'.$_REQUEST['lejarat'].','.$uid.','.$nyilvanos.')');
		if ($kinek_id>0) diplouzenet($kinek_id,'Testvérszövetség ajánlat',"$nev testvérszövetséget kötne a szövetségeddel$lejarat_str.$megjegyzes",'Offer of brotherhood',"$nev would like to contract a brotherhood with your alliance$lejarat_str_en.$megjegyzes");
		else diplouzenet($kinek_id,'Testvérszövetség ajánlat',"$nev testvérszövetséget kötne veled$lejarat_str.$megjegyzes",'Offer of brotherhood',"$nev would like to contract a brotherhood with you$lejarat_str_en.$megjegyzes");
		kilep();
	break;
	case 3://mnt
		mysql_query('insert into diplomacia_szovegek (szoveg) values("'.$_REQUEST['szoveg'].'")');
		$er=mysql_query('select last_insert_id() from diplomacia_szovegek');
		$aux=mysql_fetch_array($er);
		mysql_query('insert into diplomacia_ajanlatok (ki,kinek,mit,szoveg_id,szoveg_reszlet,felbontasi_ido,diplo,nyilvanos) values('.$ki_id.','.$kinek_id.',3,'.$aux[0].',"'.$szoveg_reszlet.'",'.$_REQUEST['lejarat'].','.$uid.','.$nyilvanos.')');
		if ($kinek_id>0) diplouzenet($kinek_id,'Megnemtámadási ajánlat',"$nev megnemtámadási egyezményt kötne a szövetségeddel$lejarat_str.$megjegyzes",'Offer of non-aggression',"$nev would like to contract a non-aggression pact with your alliance$lejarat_str_en.$megjegyzes");
		else diplouzenet($kinek_id,'Megnemtámadási ajánlat',"$nev megnemtámadási egyezményt kötne veled$lejarat_str.$megjegyzes",'Offer of non-aggression',"$nev would like to contract a non-aggression pact with you$lejarat_str_en.$megjegyzes");
		kilep();
	break;
}

kilep('Nincs ilyen szerződéstípus.');
?>