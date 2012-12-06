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

if ($ki_vagy_id>0) {
	$er=mysql_query('select nev from szovetsegek where id='.$adataim['szovetseg']);
	$aux=mysql_fetch_array($er);$nev=$aux[0];
} else $nev=$adataim['nev'];
$ki_id=$ki_vagy_id;
switch($_REQUEST['mirol']) {
	case 1://haduzenet
		$er=mysql_query('select * from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id.' and (mi='.DIPLO_TESTVER.' or mi='.DIPLO_MNT.') and felbontasi_ido>0');
		$aux=mysql_fetch_array($er);
		if (!$aux) {//instant haduzenet
			mysql_query('delete from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id);
			mysql_query('delete from diplomacia_statuszok where ki='.$kinek_id.' and kivel='.$ki_id);
			mysql_query('insert into diplomacia_szovegek (szoveg) values("'.$_REQUEST['szoveg'].'")');
			$er=mysql_query('select last_insert_id() from diplomacia_szovegek');
			$aux=mysql_fetch_array($er);
			mysql_query('insert into diplomacia_statuszok (ki,kivel,mi,szoveg_id,kezdemenyezo,szoveg_reszlet,diplo_1,diplo_2,nyilvanos,felbontasi_ido)
			values('.$ki_id.','.$kinek_id.',1,'.$aux[0].','.$ki_id.',"'.$szoveg_reszlet.'",'.$uid.',0,0,48),
			('.$kinek_id.','.$ki_id.',1,'.$aux[0].','.$ki_id.',"'.$szoveg_reszlet.'",'.$uid.',0,0,48)');
			if ($kinek_id>0) diplouzenet($kinek_id,'Hadüzenet',"$nev hadat üzent a szövetségednek.$megjegyzes",'Declaration of war',"$nev declared war against your alliance.$megjegyzes");
			else diplouzenet($kinek_id,'Hadüzenet',"$nev hadat üzent neked.$megjegyzes",'Declaration of war',"$nev declared war against you.$megjegyzes");
		} else {//felbontas utani haduzenet
			$lejarat_str=' A hadüzenet '.$aux['felbontasi_ido'].' óra múlva lép életbe.';
			$lejarat_str_en=' The declaration of war takes effect in '.$aux['felbontasi_ido'].' hours.';
			$lejarat_mikor=date('Y-m-d H:i:s',time()+3600*$aux['felbontasi_ido']);
			mysql_query('update diplomacia_statuszok set felbontas_alatt=2,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$ki_id.' and kivel='.$kinek_id.' and (mi='.DIPLO_TESTVER.' or mi='.DIPLO_MNT.')');
			mysql_query('update diplomacia_statuszok set felbontas_alatt=2,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$kinek_id.' and kivel='.$ki_id.' and (mi='.DIPLO_TESTVER.' or mi='.DIPLO_MNT.')');
			mysql_query('insert into diplomacia_szovegek (szoveg) values("'.$_REQUEST['szoveg'].'")');
			$er=mysql_query('select last_insert_id() from diplomacia_szovegek');
			$aux=mysql_fetch_array($er);
			mysql_query('insert into diplomacia_leendo_statuszok (miota,ki,kivel,mi,szoveg_id,kezdemenyezo,szoveg_reszlet,diplo_1,diplo_2,nyilvanos,felbontasi_ido)
			values("'.$lejarat_mikor.'",'.$ki_id.','.$kinek_id.',1,'.$aux[0].','.$ki_id.',"'.$szoveg_reszlet.'",'.$uid.',0,0,48),
			("'.$lejarat_mikor.'",'.$kinek_id.','.$ki_id.',1,'.$aux[0].','.$ki_id.',"'.$szoveg_reszlet.'",'.$uid.',0,0,48)');
			if ($kinek_id>0) diplouzenet($kinek_id,'Hadüzenet',"$nev hadat üzent a szövetségednek.$lejarat_str$megjegyzes",'Declaration of war',"$nev declared war against your alliance.$lejarat_str_en$megjegyzes");
			else diplouzenet($kinek_id,'Hadüzenet',"$nev hadat üzent neked.$lejarat_str$megjegyzes",'Declaration of war',"$nev declared war against you.$lejarat_str_en$megjegyzes");
		}
		kilep();
	break;
	case -2://testverszovi torlese
		$er=mysql_query('select * from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id.' and mi='.DIPLO_TESTVER);
		$aux=mysql_fetch_array($er);
		if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nem is vagytok testvérszövetségben.']);
		if ($aux['felbontasi_ido']>0) {
			$lejarat_str=' A felbontás '.$aux['felbontasi_ido'].' óra múlva lép életbe.';
			$lejarat_str_en=' The break-up takes effect in '.$aux['felbontasi_ido'].' hours.';
			$lejarat_mikor=date('Y-m-d H:i:s',time()+3600*$aux['felbontasi_ido']);
			mysql_query('update diplomacia_statuszok set felbontas_alatt=1,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$ki_id.' and kivel='.$kinek_id.' and mi='.DIPLO_TESTVER);
			mysql_query('update diplomacia_statuszok set felbontas_alatt=1,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$kinek_id.' and kivel='.$ki_id.' and mi='.DIPLO_TESTVER);
		} else {
			mysql_query('delete from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id);
			mysql_query('delete from diplomacia_statuszok where ki='.$kinek_id.' and kivel='.$ki_id);
			$lejarat_str='';
			$lejarat_str_en='';
		}
		if ($kinek_id>0) diplouzenet($kinek_id,'Testvérszövetség felbontása',"$nev felbontotta a szövetségeddel kötött testvérszövetséget.$lejarat_str$megjegyzes",'Break-up of brotherhood',"$nev broke up the brotherhood with your alliance.$lejarat_str_en$megjegyzes");
		else diplouzenet($kinek_id,'Testvérszövetség felbontása',"$nev felbontotta a veled kötött testvérszövetséget.$lejarat_str$megjegyzes",'Break-up of brotherhood',"$nev broke up the brotherhood with you.$lejarat_str_en$megjegyzes");
		kilep();
	break;
	case -3://mnt torlese
		$er=mysql_query('select * from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id.' and mi='.DIPLO_MNT);
		$aux=mysql_fetch_array($er);
		if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs is köztetek megnemtámadási egyezmény.']);
		//
		if ($aux['felbontasi_ido']>0) {
			$lejarat_str=' A felbontás '.$aux['felbontasi_ido'].' óra múlva lép életbe.';
			$lejarat_str_en=' The break-up takes effect in '.$aux['felbontasi_ido'].' hours.';
			$lejarat_mikor=date('Y-m-d H:i:s',time()+3600*$aux['felbontasi_ido']);
			mysql_query('update diplomacia_statuszok set felbontas_alatt=1,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$ki_id.' and kivel='.$kinek_id.' and mi='.DIPLO_MNT);
			mysql_query('update diplomacia_statuszok set felbontas_alatt=1,felbontas_mikor="'.$lejarat_mikor.'" where ki='.$kinek_id.' and kivel='.$ki_id.' and mi='.DIPLO_MNT);
		} else {
			mysql_query('delete from diplomacia_statuszok where ki='.$ki_id.' and kivel='.$kinek_id);
			mysql_query('delete from diplomacia_statuszok where ki='.$kinek_id.' and kivel='.$ki_id);
			$lejarat_str='';
			$lejarat_str_en='';
		}
		if ($kinek_id>0) diplouzenet($kinek_id,'Megnemtámadási egyezmény felbontása',"$nev felbontotta a szövetségeddel kötött megnemtámadási egyezményt.$lejarat_str$megjegyzes",'Break-up of non-aggression pact',"$nev broke up the non-aggression pact with your alliance.$lejarat_str_en$megjegyzes");
		else diplouzenet($kinek_id,'Megnemtámadási egyezmény felbontása',"$nev felbontotta a veled kötött megnemtámadási egyezményt.$lejarat_str$megjegyzes",'Break-up of non-aggression pact',"$nev broke up the non-aggression pact with you.$lejarat_str_en$megjegyzes");
		//
		kilep();
	break;
}

kilep('Nincs ilyen státusz.');
?>