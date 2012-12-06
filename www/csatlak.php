<?
$szkript_mikor_indul=microtime(true);

include_once('config.php');
include_once('lang_s.php');
include_once('tutorial_szovegek.php');
include_once('tut_levelek_hu.php');
include_once('tut_levelek_en.php');


$jogok_szama=12;//jskod_akciok/uj_tisztseg_felvetele, jskod/, lista a csatlak.php vegen
$mysql_csatlakozas=mysql_connect('localhost',$mysql_username,$mysql_password) or hiba(__FILE__,__LINE__,mysql_error());
$result=mysql_select_db($database_mmog) or die();
mysql_query('set names "utf8"');
mysql_query('SET SESSION sql_mode=""');//strict mode kikapcsolasa; ahhoz tul "laza" a kod

//
//mysql_query('insert into '.$database_mmog_nemlog.'.php_debug_log (szkript,hasznalt_memoria,hasznalt_memoria_true,futasi_ido) values("*csatlak.php/start",'.memory_get_peak_usage().','.memory_get_peak_usage(true).','.round(1000*(microtime(true)-$szkript_mikor_indul)).')');
//

function hiba($f,$l,$e) {
	global $debug_mode;
	if ($debug_mode) die('HIBA a '.$f.' file '.$l.'. sorában: '.$e);
	die();
}

function kilep($s='') {
	global $mysql_csatlakozas;
	mysql_close($mysql_csatlakozas);
	exit($s);
}
function kilep_and_unlock($s='') {
	global $mysql_csatlakozas;
	mysql_query('unlock tables');
	mysql_close($mysql_csatlakozas);
	exit($s);
}

function randomgen($hossz) {
	$x='';for($i=0;$i<$hossz;$i++) $x.=chr(96+mt_rand(1,26));
	return $x;
}

//benchmark függvények, optimalizáláshoz
function insert_into_php_debug_log($futasi_ido) {
	//global $database_mmog_nemlog;
	//mysql_query('insert into '.$database_mmog_nemlog.'.php_debug_log (szkript,hasznalt_memoria,hasznalt_memoria_true,futasi_ido) values("'.$_SERVER['PHP_SELF'].'",'.memory_get_peak_usage().','.memory_get_peak_usage(true).','.$futasi_ido.')');
}
function insert_into_php_debug_log_resz($futasi_ido,$resz) {
	//global $database_mmog_nemlog;
	//mysql_query('insert into '.$database_mmog_nemlog.'.php_debug_log (szkript,hasznalt_memoria,hasznalt_memoria_true,futasi_ido) values("'.$_SERVER['PHP_SELF'].'/'.$resz.'",'.memory_get_peak_usage().','.memory_get_peak_usage(true).','.$futasi_ido.')');
}

function sanitint($x) {
	$x=trim((string)$x);
	$y='';for($i=0;$i<strlen($x);$i++) if (strpos('0123456789',$x[$i])!==false) $y.=$x[$i];
	if ($y=='') return 0;
	if (strtolower($x[strlen($x)-1])=='k') $y.='000';
	if (strtolower($x[strlen($x)-1])=='m') $y.='000000';
	if (strtolower($x[strlen($x)-1])=='g') $y.='000000000';
	if (strpos($x,'-')!==false) return -$y;
	return $y;
}
function sanitint_poz($x) {
	$x=trim((string)$x);
	$y='';for($i=0;$i<strlen($x);$i++) if (strpos('0123456789',$x[$i])!==false) $y.=$x[$i];
	if ($y=='') return 0;
	if (strtolower($x[strlen($x)-1])=='k') $y.='000';
	if (strtolower($x[strlen($x)-1])=='m') $y.='000000';
	if (strtolower($x[strlen($x)-1])=='g') $y.='000000000';
	return $y;
}
function sanitstr($x) {
	if (get_magic_quotes_gpc()) $x=stripslashes($x);
	return mysql_real_escape_string(trim(strip_tags($x)));
}
function sanitstr_html($x) {
	if (get_magic_quotes_gpc()) $x=stripslashes($x);
	return mysql_real_escape_string(trim($x));
}
function sanitstr_html_special($x) {
	if (get_magic_quotes_gpc()) $x=stripslashes($x);
	return mysql_real_escape_string(trim(htmlspecialchars($x)));
}

function megengedhetove_tesz($s) {
	$megengedheto_karakterek='áéíóöőúüűÁÉÍÓÖŐÚÜŰaAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789 \'"+-*?!%/=()[]{}_.;:@$';// vesszo, tagkezdo-vegzo, ampersand, hashmark (helyettesiteshez) NEM, mind mas, ami gepelheto, IGEN
	$ss='';
	$hossz=mb_strlen($s,'UTF-8');
	for($i=0;$i<$hossz;$i++) if (mb_strpos($megengedheto_karakterek,mb_substr($s,$i,1,'UTF-8'),0,'UTF-8')!==false) $ss.=mb_substr($s,$i,1,'UTF-8');
	return $ss;
}


/*
lehetoseg arra, hogy tetszoleges mailszerverrol menjen ki az *osszes* level
vagy pl arra is, hogy domainenkent kulonbozo szerverrol, igy az SPF-et jol be lehet allitani
a lenti kod persze nem ilyen, mert localbol megy
*/
function zandamail($lang,$post=null) {
	global $zanda_admin_email,$zanda_homepage_url,$no_mailserver;
	if ($no_mailserver) return true;
	include('class.phpmailer.php');
	//
	$cimzett_email=$post['email'];
	$cimzett_nev=$post['name'];
	$subject=$post['subject'];
	$html_szoveg=$post['html'];
	$text_szoveg=$post['plain'];
	//
	$mail = new PHPMailer();
	$mail->CharSet='utf-8';
	$mail->From=$zanda_admin_email[$lang];
	$mail->FromName='Zandagort';
	//$mail->Sender=$zanda_admin_email[$lang];//Return-Path
	$mail->Subject=$subject;
	$mail->AddAddress($cimzett_email,$cimzett_nev);//ha nev ures, akkor nem adja hozza a nevet

	$html_header='<html><body><style type="text/css">'."\n";
	$html_header.='body {font-size: 12pt; font-family: arial, helvetica, sans-serif; background: rgb(4,6,10); color: white}'."\n";
	$html_header.='a {color: rgb(100,160,255); text-decoration: none}'."\n";
	$html_header.='img {border: none}'."\n";
	$html_header.='</style><div style="width: 600px">'."\n";
	$html_footer='</div></body></html>';
	$url_szokasos=$zanda_homepage_url[$lang];

	$html_uzi='';$text_uzi='';
	//
	$html_uzi.=$html_header;
	if ($lang=='hu') {
		$html_uzi.='<div><a href="'.$url_szokasos.'"><img src="img/zandagort_logo.jpg" alt="Zandagort online stratégiai játék" /></a></div>';
	} else {
		$html_uzi.='<div><a href="'.$url_szokasos.'"><img src="img/zandagort_logo.jpg" alt="Zandagort online strategy game" /></a></div>';
	}
	//
	$html_uzi.=$html_szoveg;
	$text_uzi.=$text_szoveg;
	//
	$html_uzi.='<p><br /></p>'."\n";
	if ($lang=='hu') {
		$html_uzi.='<p><a href="'.$url_szokasos.'">Zandagort és népe</a></p>'."\n";
	} else {
		$html_uzi.='<p><a href="'.$url_szokasos.'">Zandagort and his people</a></p>'."\n";
	}
	//
	if ($lang=='hu') {
		$text_uzi.="\n\nZandagort és népe\n$url_szokasos\n\n";
	} else {
		$text_uzi.="\n\nZandagort and his people\n$url_szokasos\n\n";
	}
	//
	$mail->MsgHTML($html_uzi);
	$mail->Body=($mail->Body).$html_footer;
	$mail->AltBody=$text_uzi;
	//
	return $mail->Send();
}




function premium_szint() {
	global $adataim;
	if ($adataim['premium']>0) return $adataim['premium'];
	if (time()<strtotime($adataim['premium_emelt'])) return 2;
	if (time()<strtotime($adataim['premium_alap'])) return 1;
	return 0;
}
function user_premium_szint($jatekos) {
	if ($jatekos['premium']>0) return $jatekos['premium'];
	if (time()<strtotime($jatekos['premium_emelt'])) return 2;
	if (time()<strtotime($jatekos['premium_alap'])) return 1;
	return 0;
}
function szov_premium_szint($szov_id) {
	$res=mysql_query('select coalesce(max(premium),0),coalesce(max(premium_emelt),"0000-00-00 00:00:00"),coalesce(max(premium_alap),"0000-00-00 00:00:00") from userek where szovetseg='.$szov_id) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_row($res);
	if ($aux[0]>0) return $aux[0];
	if (time()<strtotime($aux[1])) return 2;
	if (time()<strtotime($aux[2])) return 1;
	return 0;
}


function array2jsonmatrix($tomb,$oszlopszam,$tipusok) {
	$jsonmatrix='[';
	$z=0;
	for($i=0;$i<count($tomb);$i++) {
		$z++;if ($z>1) $jsonmatrix.=',';
		$jsonmatrix.='[';
		for($k=0;$k<$oszlopszam;$k++) {
			if ($k>0) $jsonmatrix.=',';
			if ($tipusok[$k]=='s') $jsonmatrix.='"'.addslashes($tomb[$i][$k]).'"';
			else $jsonmatrix.=$tomb[$i][$k];
		}
		$jsonmatrix.=']';
	}
	return $jsonmatrix.']';
}

function mysql2num($mit) {
	$res=mysql_query($mit);
	$aux=mysql_fetch_row($res);
	return $aux[0];
}
function mysql2row($mit) {
	$res=mysql_query($mit);
	$aux=mysql_fetch_array($res);
	return $aux;
}
function mysql2array($mit) {
	$res=mysql_query($mit);
	while($aux=mysql_fetch_array($res)) $x[]=$aux;
	return $x;
}

function mysql2jsonarray($mit) {
	$jsonmatrix='[';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		$k=0;
		if (mysql_field_type($res,$k)=='timestamp' || mysql_field_type($res,$k)=='datetime' || mysql_field_type($res,$k)=='string' || mysql_field_type($res,$k)=='blob') $jsonmatrix.='"'.addslashes($aux[$k]).'"';
		else $jsonmatrix.=$aux[$k];
	}
	return $jsonmatrix.']';
}
function mysql2jsonmatrix($mit) {
	$jsonmatrix='[';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		$jsonmatrix.='[';
		for($k=0;$k<$oszlopszam;$k++) {
			if ($k>0) $jsonmatrix.=',';
			if (mysql_field_type($res,$k)=='timestamp' || mysql_field_type($res,$k)=='datetime' || mysql_field_type($res,$k)=='string' || mysql_field_type($res,$k)=='blob') $jsonmatrix.='"'.addslashes($aux[$k]).'"';
			else $jsonmatrix.=$aux[$k];
		}
		$jsonmatrix.=']';
	}
	return $jsonmatrix.']';
}
function mysql2jsonmatrix_v2($mit) {
	$jsonmatrix='[';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		$jsonmatrix.='[';
		for($k=0;$k<$oszlopszam;$k++) {
			if ($k>0) $jsonmatrix.=',';
			$jsonmatrix.=json_encode($aux[$k]);
		}
		$jsonmatrix.=']';
	}
	return $jsonmatrix.']';
}
function mysql2jsonassoc($mit) {
	$jsonmatrix='{';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		if (mysql_field_type($res,0)=='timestamp' || mysql_field_type($res,0)=='datetime' || mysql_field_type($res,0)=='string' || mysql_field_type($res,0)=='blob') $jsonmatrix.='"'.addslashes($aux[0]).'"';
		else $jsonmatrix.=$aux[0];
		$jsonmatrix.=':[';
		for($k=1;$k<$oszlopszam;$k++) {
			if ($k>1) $jsonmatrix.=',';
			if (mysql_field_type($res,$k)=='timestamp' || mysql_field_type($res,$k)=='datetime' || mysql_field_type($res,$k)=='string' || mysql_field_type($res,$k)=='blob') $jsonmatrix.='"'.addslashes($aux[$k]).'"';
			else $jsonmatrix.=$aux[$k];
		}
		$jsonmatrix.=']';
	}
	return $jsonmatrix.'}';
}
function mysql2jsonassoc_v2($mit) {
	$jsonmatrix='{';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		$jsonmatrix.=json_encode($aux[0]);
		$jsonmatrix.=':[';
		for($k=1;$k<$oszlopszam;$k++) {
			if ($k>1) $jsonmatrix.=',';
			$jsonmatrix.=json_encode($aux[$k]);
		}
		$jsonmatrix.=']';
	}
	return $jsonmatrix.'}';
}
function mysql2jsonmultiassoc($mit) {
	$jsonmatrix='{';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$id=-1;
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;
		if ($id!=$aux[0]) {
			if ($i>1) $jsonmatrix.='],';
			if (mysql_field_type($res,0)=='timestamp' || mysql_field_type($res,0)=='datetime' || mysql_field_type($res,0)=='string' || mysql_field_type($res,0)=='blob') $jsonmatrix.='"'.addslashes($aux[0]).'"';
			else $jsonmatrix.=$aux[0];
			$jsonmatrix.=':[';
			$id=$aux[0];
		} else {
			if ($i>1) $jsonmatrix.=',';
		}
		$jsonmatrix.='[';
		for($k=1;$k<$oszlopszam;$k++) {
			if ($k>1) $jsonmatrix.=',';
			if (mysql_field_type($res,$k)=='timestamp' || mysql_field_type($res,$k)=='datetime' || mysql_field_type($res,$k)=='string' || mysql_field_type($res,$k)=='blob') $jsonmatrix.='"'.addslashes($aux[$k]).'"';
			else $jsonmatrix.=$aux[$k];
		}
		$jsonmatrix.=']';
	}
	if ($i>0) $jsonmatrix.=']';
	return $jsonmatrix.'}';
}

function mysql2jsonlabeledmatrix($mit,$fejlec) {
	$jsonmatrix='[';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		$jsonmatrix.='{';
		for($k=0;$k<$oszlopszam;$k++) {
			if ($k>0) $jsonmatrix.=',';
			$jsonmatrix.='"'.$fejlec[$k].'":';
			if (mysql_field_type($res,$k)=='timestamp' || mysql_field_type($res,$k)=='datetime' || mysql_field_type($res,$k)=='string' || mysql_field_type($res,$k)=='blob') $jsonmatrix.='"'.addslashes($aux[$k]).'"';
			else $jsonmatrix.=$aux[$k];
		}
		$jsonmatrix.='}';
	}
	return $jsonmatrix.']';
}
function mysql2jsonlabeledmatrix_v2($mit,$fejlec) {
	$jsonmatrix='[';
	$res=mysql_query($mit) or hiba(__FILE__,__LINE__,mysql_error());
	$oszlopszam=mysql_num_fields($res);
	$i=0;while($aux=mysql_fetch_row($res)) {
		$i++;if ($i>1) $jsonmatrix.=',';
		$jsonmatrix.='{';
		for($k=0;$k<$oszlopszam;$k++) {
			if ($k>0) $jsonmatrix.=',';
			$jsonmatrix.=json_encode($fejlec[$k]).':'.json_encode($aux[$k]);
		}
		$jsonmatrix.='}';
	}
	return $jsonmatrix.']';
}


function szovi_belepo_kilepo_uzenet($kinek,$targy,$uzenet,$targy_en='',$uzenet_en='') {
	if ($kinek>0) {
		$er=mysql_query('select u.id from userek u
		left join szovetseg_tisztsegek szt on szt.szov_id='.$kinek.' and szt.id=u.tisztseg
		where u.szovetseg='.$kinek.' and (u.tisztseg=-1 or szt.jog_2=1)') or hiba(__FILE__,__LINE__,mysql_error());
		while($aux=mysql_fetch_array($er)) rendszeruzenet($aux[0],$targy,$uzenet,$targy_en,$uzenet_en);
	} else rendszeruzenet(-$kinek,$targy,$uzenet,$targy_en,$uzenet_en);
}
function diplouzenet($kinek,$targy,$uzenet,$targy_en='',$uzenet_en='') {
	if ($kinek>0) {
		$er=mysql_query('select u.id from userek u
		left join szovetseg_tisztsegek szt on szt.szov_id='.$kinek.' and szt.id=u.tisztseg
		where u.szovetseg='.$kinek.' and (u.tisztseg=-1 or szt.jog_7=1)') or hiba(__FILE__,__LINE__,mysql_error());
		while($aux=mysql_fetch_array($er)) rendszeruzenet($aux[0],$targy,$uzenet,$targy_en,$uzenet_en);
	} else rendszeruzenet(-$kinek,$targy,$uzenet,$targy_en,$uzenet_en);
}
function rendszeruzenet($kinek,$targy,$uzenet,$targy_en='',$uzenet_en='') {
	$er=mysql_query('select nyelv from userek where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
	$leveltulaj=mysql_fetch_array($er);
	$datum=date('Y-m-d H:i:s');
	mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek,mappa) values(0,'.$kinek.',"'.$datum.'","'.mysql_real_escape_string(trim(strip_tags(($leveltulaj[0]=='hu')?$targy:$targy_en))).'","'.mysql_real_escape_string(trim(strip_tags(($leveltulaj[0]=='hu')?$uzenet:$uzenet_en))).'",",'.$kinek.',","'.(($leveltulaj[0]=='hu')?'Bejövő':'Incoming').'")') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from levelek') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$kinek.')') or hiba(__FILE__,__LINE__,mysql_error());
}
function rendszeruzenet_html($kinek,$targy,$uzenet,$targy_en='',$uzenet_en='') {//erre nem lehet reply-t nyomni, igy itt nem gond a html
	$er=mysql_query('select nyelv from userek where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
	$leveltulaj=mysql_fetch_array($er);
	$datum=date('Y-m-d H:i:s');
	mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek,mappa) values(0,'.$kinek.',"'.$datum.'","'.mysql_real_escape_string(trim(strip_tags(($leveltulaj[0]=='hu')?$targy:$targy_en))).'","'.mysql_real_escape_string(($leveltulaj[0]=='hu')?$uzenet:$uzenet_en).'",",'.$kinek.',","'.(($leveltulaj[0]=='hu')?'Bejövő':'Incoming').'")') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from levelek') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$kinek.')') or hiba(__FILE__,__LINE__,mysql_error());
}
function badgeuzenet($kinek,$targy,$uzenet,$targy_en='',$uzenet_en='') {//erre nem lehet reply-t nyomni, igy itt nem gond a html
	$er=mysql_query('select nyelv from userek where id='.$kinek);
	$leveltulaj=mysql_fetch_array($er);
	$datum=date('Y-m-d H:i:s');
	mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek,mappa) values(0,'.$kinek.',"'.$datum.'","'.mysql_real_escape_string(trim(strip_tags(($leveltulaj[0]=='hu')?$targy:$targy_en))).'","'.mysql_real_escape_string(($leveltulaj[0]=='hu')?$uzenet:$uzenet_en).'",",'.$kinek.',","'.(($leveltulaj[0]=='hu')?'Bejövő':'Incoming').'")');
	$er=mysql_query('select last_insert_id() from levelek');
	$aux=mysql_fetch_array($er);
	mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$kinek.')');
}
function rendszeruzenet_a_kozponti_szolgaltatohaztol($kinek,$targy,$uzenet,$nyelv='hu') {
	$datum=date('Y-m-d H:i:s');
	//strip_tags kiszedve, h linkeket is lehessen
	if ($nyelv=='hu') {
		mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek) values('.KOZPONTI_SZOLGALTATOHAZ_HU_USER_ID.','.$kinek.',"'.$datum.'","'.mysql_real_escape_string(trim(strip_tags($targy))).'","'.mysql_real_escape_string(trim($uzenet)).'",",'.$kinek.',")') or hiba(__FILE__,__LINE__,mysql_error());
	} else {
		mysql_query('insert into levelek (felado,tulaj,ido,targy,uzenet,cimzettek,mappa) values('.KOZPONTI_SZOLGALTATOHAZ_EN_USER_ID.','.$kinek.',"'.$datum.'","'.mysql_real_escape_string(trim(strip_tags($targy))).'","'.mysql_real_escape_string(trim($uzenet)).'",",'.$kinek.',","Incoming")') or hiba(__FILE__,__LINE__,mysql_error());
	}
	$er=mysql_query('select last_insert_id() from levelek') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	mysql_query('insert ignore into cimzettek (level_id,cimzett_tipus,cimzett_id) values('.$aux[0].','.CIMZETT_TIPUS_USER.','.$kinek.')') or hiba(__FILE__,__LINE__,mysql_error());
}

function achievement_uzenet($kinek,$tema,$nyelv='hu') {
	global $zanda_ingame_msg_ps;
	$er=mysql_query('select nev,achievementek&'.$tema.' as volt_mar from userek where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux['volt_mar']==0) {//csak ha nem volt meg
		if ($nyelv=='hu') {
			global $achievement_szovegek_hu;
			$uzi="Kedves ".$aux['nev']."!\n\n";
			switch($tema) {
				case ACHIEVEMENT_ELSO_VAROS:
					$targy='Gratulálunk az első városodhoz!';
				break;
				case ACHIEVEMENT_ELSO_HIRSZERZO:
					$targy='Gratulálunk az első hírszerző központodhoz!';
				break;
				case ACHIEVEMENT_ELSO_KUTATO:
					$targy='Gratulálunk az első kutatóintézetedhez!';
				break;
				case ACHIEVEMENT_ELSO_URHAJOGYAR:
					$targy='Gratulálunk az első űrhajógyáradhoz!';
				break;
				case ACHIEVEMENT_ELSO_TELEPORT:
					$targy='Gratulálunk az első teleportodhoz!';
				break;
			}
			$uzi.=$targy.' '.$achievement_szovegek_hu[$tema];
			$uzi.="\n\n\nZandagort és népe";
			$uzi.="\n\n".$zanda_ingame_msg_ps['hu'];
		} else {
			global $achievement_szovegek_en;
			$uzi="Dear ".$aux['nev']."!\n\n";
			switch($tema) {
				case ACHIEVEMENT_ELSO_VAROS:
					$targy='Congratulations on your first city!';
				break;
				case ACHIEVEMENT_ELSO_HIRSZERZO:
					$targy='Congratulations on your first intelligence HQ!';
				break;
				case ACHIEVEMENT_ELSO_KUTATO:
					$targy='Congratulations on your first research lab!';
				break;
				case ACHIEVEMENT_ELSO_URHAJOGYAR:
					$targy='Congratulations on your first spaceship factory!';
				break;
				case ACHIEVEMENT_ELSO_TELEPORT:
					$targy='Congratulations on your first teleport!';
				break;
			}
			$uzi.=$targy.' '.$achievement_szovegek_en[$tema];
			$uzi.="\n\n\nZandagort and his people";
			$uzi.="\n\n".$zanda_ingame_msg_ps['en'];
		}
		rendszeruzenet_a_kozponti_szolgaltatohaztol($kinek,$targy,$uzi,$nyelv);
		mysql_query('update userek set achievementek=achievementek|'.$tema.' where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
	}
}
function osszeomlott_uzenet($kinek,$nev,$email,$nyelv='hu') {
	if ($nyelv=='hu') {
		global $tech_szint_szovegek_hu;
		$uzi="Kedves $nev!\n\n";
		$targy='Összeomlott a bolygód?';
		$uzi.=$targy.' '.$tech_szint_szovegek_hu[7];
		$uzi_html='<p>'.strtr($uzi,array("\n"=>"<br />\n","</p><p>"=>"</p>\n<p>")).'</p>';
		zandamail('hu',array(
			'email'	=>	$email,
			'name'	=>	$nev,
			'subject'	=>	'Zandagort - Összeomlott a bolygód?',
			'html'	=>	$uzi_html,
			'plain'	=>	$uzi
		));
	} else {
		global $tech_szint_szovegek_en;
		$uzi="Dear $nev!\n\n";
		$targy='Did your planet collapse?';
		$uzi.=$targy.' '.$tech_szint_szovegek_en[7];
		$uzi_html='<p>'.strtr($uzi,array("\n"=>"<br />\n","</p><p>"=>"</p>\n<p>")).'</p>';
		zandamail('en',array(
			'email'	=>	$email,
			'name'	=>	$nev,
			'subject'	=>	'Zandagort - Did your planet collapse?',
			'html'	=>	$uzi_html,
			'plain'	=>	$uzi
		));
	}
	rendszeruzenet_a_kozponti_szolgaltatohaztol($kinek,$targy,$uzi,$nyelv);
	mysql_query('update userek set osszeomlott=1 where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
}
function premium_lejar_uzenet($kinek,$nev,$nyelv='hu') {
	global $zanda_ingame_msg_ps;
	if ($nyelv=='hu') {
		$uzi="Kedves $nev!\n\n";
		$targy='5 nap múlva lejár a prémiumod';
		$uzi.="Ez egy figyelmeztető üzenet: 5 nap múlva lejár a prémiumod. Ha szeretnéd meghosszabbítani, gondolj rá időben, mert az átutalás nem azonnal történik meg (főleg, ha egy hétvége is közbe esik). Az új előfizetés pedig a meglevő végéhez adódik hozzá, vagyis ha időben befizetsz, nem veszítesz vele semmit, viszont biztosan megmaradnak az építési listáid és az ütemezett szállításaid.

Ha nem tudod, mi ez az egész, íme a rövid magyarázat. A Zandagort egy úgynevezett független ('indie') játék. Ez azt jelenti, hogy nem áll mögötte semmilyen nagy pénzű játékfejlesztő vállalat. Ennek számos előnye van, például hogy a megszokotthoz képest jelentősen eltérő ötletek is megvalósulhatnak, vagy hogy a játékosok és a fejlesztő közötti kapcsolat jóval szorosabb. És van egy nagy hátránya: a finanszírozás bizonytalansága.

A játék fejlesztése, de még a fenntartása is pénzbe kerül, és ez az elégedett játékosok hozzájárulásaiból származik. Ha közéjük tartozol, te is besegíthetsz. Természetesen *nem* kötelező, de ha úgy döntesz, hogy adakozol, cserébe kapsz néhány extra lehetőséget. Ezekről bővebben a Zandagort honlap prémium oldalán olvashatsz.

Köszi a figyelmet.";
		$uzi.="\n\n\nZandagort és népe";
		$uzi.="\n\n".$zanda_ingame_msg_ps['hu'];
	} else {
		$uzi="Dear $nev!\n\n";
		$targy='Your premium expires in 5 days';
		$uzi.="This is a message of warning: your premium expires in 5 days. If you would like to extend it, don't wait, because it takes some time between you pay and you get the premium. And the new subscription is added to the current one, so you lose nothing, if you pay in time.

If you don't know what it's all about, here's a short explanation. Zandagort is an independent (so called 'indie') game. Meaning there's no big budget company behind it. This has some advantages, like the courage to implement not mainstream ideas or the stronger connection between fans and the developer. And one major drawback: insecurity in financing.

The development and even the maintenance of the game costs some money, and it comes from the contributions of satisfied players. If you are among them, you can make a contribution too. It's *not* compulsory, of course, but if you decide to donate, you get some extra features in return. You can read about them in the premium section of the Zandagort homepage.

Thanks for your attention.";
		$uzi.="\n\n\nZandagort and his people";
		$uzi.="\n\n".$zanda_ingame_msg_ps['en'];
	}
	rendszeruzenet_a_kozponti_szolgaltatohaztol($kinek,$targy,$uzi,$nyelv);
	mysql_query('update userek set premium_lejar_ertesito_mikor=now() where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
}


function tut_level($kinek,$level,$egyeb_adatok=null) {
	global $zanda_ingame_msg_ps;
	if (!is_array($egyeb_adatok)) {
		$aux=mysql2row('select nev,nyelv from userek where id='.$kinek);
		$egyeb_adatok=array(
			$aux['nev'],$aux['nyelv']
		);
	}
	if ($egyeb_adatok[1]=='hu') {
		global $tut_level_szovegek_hu;
		$uzi="Kedves ".$egyeb_adatok[0]."!\n\n";
		$uzi.=$tut_level_szovegek_hu[$level][1];
		$uzi.="\n\n\nZandagort és népe";
		$uzi.="\n\n".$zanda_ingame_msg_ps['hu'];
		rendszeruzenet_a_kozponti_szolgaltatohaztol($kinek,$tut_level_szovegek_hu[$level][0],$uzi,$egyeb_adatok[1]);
	} else {
		global $tut_level_szovegek_en;
		$uzi="Dear ".$egyeb_adatok[0]."!\n\n";
		$uzi.=$tut_level_szovegek_en[$level][1];
		$uzi.="\n\n\nZandagort and his people";
		$uzi.="\n\n".$zanda_ingame_msg_ps['en'];
		rendszeruzenet_a_kozponti_szolgaltatohaztol($kinek,$tut_level_szovegek_en[$level][0],$uzi,$egyeb_adatok[1]);
	}
	mysql_query('update userek set tut_level='.$level.',tut_uccso_level=now() where id='.$kinek);
}
function techszint_uzenet($kinek,$nev,$szint,$email='',$nyelv='hu',$aktivalva_van_e_mar=0,$ebsz_tag=0) {
	switch($szint) {
		case 1:
			tut_level($kinek,4,array($nev,$nyelv));
		break;
		case 2:
			tut_level($kinek,6,array($nev,$nyelv));
		break;
		case 3:
			tut_level($kinek,8,array($nev,$nyelv));
		break;
		case 4:
			tut_level($kinek,10,array($nev,$nyelv));
		break;
		case 5:
			mysql_query('update userek set karrier=leendo_karrier where id='.$kinek.' and karrier=0');
			tut_level($kinek,13,array($nev,$nyelv));
		break;
		case 6:
			tut_level($kinek,14,array($nev,$nyelv));
		break;
	}
	mysql_query('update userek set techszint_ertesites='.$szint.' where id='.$kinek);
	badge_adasa($kinek,$szint+6);
}
function uj_kaloz_jutalom_flottat_felrak($x,$y,$bolygo_id,$nev,$hajok) {
	mysql_query('insert into flottak (nev,kaloz_bolygo_id,statusz,sebesseg,x,y) values("'.$nev.'",'.$bolygo_id.','.STATUSZ_ALL.',0,'.$x.','.$y.')');
	$flotta_id=mysql2num('select last_insert_id() from flottak');
	mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$flotta_id.',0,100)');
	foreach($hajok as $tipus=>$darab) {
		mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$flotta_id.','.$tipus.','.(100*$darab).')');
	}
	flotta_minden_frissites($flotta_id);
}

function fantom_bolygo_uzenet($random_bolygo,$ucs) {
	$forras_info='';$forras_info_en='';
	if ($ucs['helyezes']<=50) {
		$forras_info=' A fantom támadás lehetséges forrása: '.$random_bolygo['kulso_nev'].' ('.terkep_koordinatak($random_bolygo['x'],$random_bolygo['y'],'hu').').';
		$forras_info_en=' Possible source of the phantom attack: '.$random_bolygo['kulso_nev'].' ('.terkep_koordinatak($random_bolygo['x'],$random_bolygo['y'],'en').').';
	} else {
		if ($ucs['helyezes']<=100) {//1000pc
			$fantom_x=round($random_bolygo['x']/2000)*2000;
			$fantom_y=round($random_bolygo['x']/2000)*2000;
		} else {//2000pc
			$fantom_x=round($random_bolygo['x']/4000)*4000;
			$fantom_y=round($random_bolygo['x']/4000)*4000;
		}
		$forras_info=' A fantom támadás lehetséges forrása valahol errefelé van: '.terkep_koordinatak($fantom_x,$fantom_y,'hu').'.';
		$forras_info_en=' Possible source of the phantom attack is somewhere around here: '.terkep_koordinatak($fantom_x,$fantom_y,'en').'.';
	}
	return array($forras_info,$forras_info_en);
}





function insert_into_penz_transzfer_log($tulaj,$tulaj_szov,$cel_tulaj,$cel_tulaj_szov,$mennyiseg) {
	global $database_mmog,$database_mmog_nemlog;
	mysql_select_db($database_mmog_nemlog);
	mysql_query("insert into penz_transzfer_log (user_id_1,tulaj_szov_1,user_id_2,tulaj_szov_2,mennyiseg,mikor) values($tulaj,$tulaj_szov,$cel_tulaj,$cel_tulaj_szov,$mennyiseg,\"".date('Y-m-d H:i:s')."\")") or hiba(__FILE__,__LINE__,mysql_error());
	mysql_select_db($database_mmog);
}
function insert_into_transzfer_log($forras_orig_tulaj,$forras_orig_tulaj_szov,$tulaj,$tulaj_szov,$forras_bid,$cel_tulaj,$cel_tulaj_szov,$cel_bid,$ef_id,$mennyiseg,$auto_vagy_ado) {
	global $database_mmog,$database_mmog_nemlog;
	mysql_select_db($database_mmog_nemlog);
	mysql_query("insert into transzfer_log (user_id_0,tulaj_szov_0,user_id_1,tulaj_szov_1,bolygo_id_1,user_id_2,tulaj_szov_2,bolygo_id_2,eroforras_id,mennyiseg,auto_vagy_ado,mikor) values($forras_orig_tulaj,$forras_orig_tulaj_szov,$tulaj,$tulaj_szov,$forras_bid,$cel_tulaj,$cel_tulaj_szov,$cel_bid,$ef_id,$mennyiseg,$auto_vagy_ado,\"".date('Y-m-d H:i:s')."\")") or hiba(__FILE__,__LINE__,mysql_error());
	mysql_select_db($database_mmog);
}
function tomeges_insert_into_transzfer_log($forras_orig_tulaj,$forras_orig_tulaj_szov,$tulaj,$tulaj_szov,$forras_bid,$cel_tulaj,$cel_tulaj_szov,$cel_bid,$ef_id_mennyiseg_tomb,$auto_vagy_ado) {
	global $database_mmog,$database_mmog_nemlog;
	mysql_select_db($database_mmog_nemlog);
	$datum=date('Y-m-d H:i:s');
	foreach($ef_id_mennyiseg_tomb as $ef_id_mennyiseg) {
		mysql_query("insert into transzfer_log (user_id_0,tulaj_szov_0,user_id_1,tulaj_szov_1,bolygo_id_1,user_id_2,tulaj_szov_2,bolygo_id_2,eroforras_id,mennyiseg,auto_vagy_ado,mikor) values($forras_orig_tulaj,$forras_orig_tulaj_szov,$tulaj,$tulaj_szov,$forras_bid,$cel_tulaj,$cel_tulaj_szov,$cel_bid,".((int)$ef_id_mennyiseg[0]).",".((int)$ef_id_mennyiseg[1]).",$auto_vagy_ado,\"$datum\")") or hiba(__FILE__,__LINE__,mysql_error());
	}
	mysql_select_db($database_mmog);
}
function insert_into_bolygo_transzfer_log($bolygo_id,$tulaj_0,$tulaj_szov_0,$tulaj_1,$tulaj_szov_1,$tulaj_2,$tulaj_szov_2,$foglalas,$ertek_elotte,$ertek_utana,$veszteseg) {
	global $database_mmog,$database_mmog_nemlog;
	mysql_select_db($database_mmog_nemlog);
	mysql_query('insert into bolygo_transzfer_log (bolygo_id,tulaj_0,tulaj_szov_0,tulaj_1,tulaj_szov_1,tulaj_2,tulaj_szov_2,mikor,foglalas,ertek_elotte,ertek_utana,veszteseg) values('.$bolygo_id.','.$tulaj_0.','.$tulaj_szov_0.','.$tulaj_1.','.$tulaj_szov_1.','.$tulaj_2.','.$tulaj_szov_2.',"'.date('Y-m-d H:i:s').'",'.$foglalas.','.$ertek_elotte.','.$ertek_utana.','.$veszteseg.')') or hiba(__FILE__,__LINE__,mysql_error());
	mysql_select_db($database_mmog);
}
function insert_into_hajo_transzfer_log($tulaj_1,$tulaj_szov_1,$forras_id,$tulaj_2,$tulaj_szov_2,$cel_id,$ertek) {
	global $database_mmog,$database_mmog_nemlog;
	mysql_select_db($database_mmog_nemlog);
	mysql_query("insert into hajo_transzfer_log (tulaj_1,tulaj_szov_1,forras_id,tulaj_2,tulaj_szov_2,cel_id,ertek,mikor) values($tulaj_1,$tulaj_szov_1,$forras_id,$tulaj_2,$tulaj_szov_2,$cel_id,$ertek,\"".date('Y-m-d H:i:s')."\")") or hiba(__FILE__,__LINE__,mysql_error());
	mysql_select_db($database_mmog);
}
function hist_snapshot($hist_termelesek=1) {
	global $database_mmog,$database_mmog_nemlog,$specko_szovetsegek_listaja,$specko_userek_listaja;
	mysql_select_db($database_mmog_nemlog);
	mysql_query('insert into hist_idopontok(id) values(null)') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from hist_idopontok') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);$idopont=$aux[0];
	if ($hist_termelesek) {//a vegjatek alatt is csak 6 orankent legyen, kulonben megugrik a tozsdei limit
	//hist_termelesek -> ez nincs felosztva osztaly,regio-ra, csak sima szumma eroforrasonkent -> a tozsdei limithez
	mysql_query('
insert into hist_termelesek(idopont,id,brutto_termeles)
select '.$idopont.',gye.eroforras_id,coalesce(sum(bgy.aktiv_db*if(gye.io>0,gye.io,0)),0)
from '.$database_mmog.'.bolygok b, '.$database_mmog.'.bolygo_gyar bgy, '.$database_mmog.'.gyar_eroforras gye
where b.id=bgy.bolygo_id and bgy.gyar_id=gye.gyar_id and b.tulaj>0 and gye.eroforras_id>54
group by gye.eroforras_id
	') or hiba(__FILE__,__LINE__,mysql_error());
	}
	//hist_bolygok
	mysql_query('
insert into hist_bolygok(idopont,id,nev,tulaj,tulaj_szov,pop)
select '.$idopont.',b.id,b.nev,b.tulaj,b.tulaj_szov,be.pop
from '.$database_mmog.'.bolygok b, '.$database_mmog.'.bolygo_ember be
where b.id=be.bolygo_id
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_eroforrasok
	mysql_query('
insert into hist_eroforrasok(idopont,osztaly,regio,id,db)
select '.$idopont.',b.osztaly,b.regio,be.eroforras_id, sum(be.db)
from '.$database_mmog.'.bolygo_eroforras be, '.$database_mmog.'.bolygok b
where be.bolygo_id=b.id
group by b.osztaly,b.regio,be.eroforras_id
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_flottak
	mysql_query('
insert into hist_flottak(idopont,id,nev,tulaj,x,y,tulaj_szov,egyenertek)
select '.$idopont.',f.id,f.nev,f.tulaj,f.x,f.y,f.tulaj_szov,round(sum(fh.ossz_hp/100*h.ar))
from '.$database_mmog.'.flottak f, '.$database_mmog.'.flotta_hajo fh, '.$database_mmog.'.hajok h
where f.id=fh.flotta_id and fh.hajo_id=h.id
group by f.id
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_gyarak
	mysql_query('
insert into hist_gyarak(idopont,osztaly,regio,id,db)
select '.$idopont.',b.osztaly,b.regio,bgy.gyar_id,sum(bgy.db)
from '.$database_mmog.'.bolygo_gyar bgy, '.$database_mmog.'.bolygok b
where bgy.bolygo_id=b.id
group by b.osztaly,b.regio,bgy.gyar_id
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_hajok
	mysql_query('
insert into hist_hajok(idopont,id,tul,ossz_hp)
select '.$idopont.',fh.hajo_id,if(f.tulaj>0,1,if(f.tulaj<0,-1,0)),sum(fh.ossz_hp)
from '.$database_mmog.'.flottak f, '.$database_mmog.'.flotta_hajo fh
where fh.flotta_id=f.id
group by fh.hajo_id,if(f.tulaj>0,1,if(f.tulaj<0,-1,0))
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_szovetsegek
	mysql_query('
insert into hist_szovetsegek(idopont,id,nev,rovid_nev)
select '.$idopont.',id,nev,rovid_nev
from '.$database_mmog.'.szovetsegek
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_userek
	mysql_query('
insert into hist_userek(idopont,id,nev,szovetseg,pontszam,uccso_akt,terulet,pontszam_exp_atlag,karrier,speci)
select '.$idopont.',id,nev,szovetseg,pontszam,uccso_akt,jelenlegi_terulet,pontszam_exp_atlag,karrier,speci
from '.$database_mmog.'.userek
	') or hiba(__FILE__,__LINE__,mysql_error());
	//hist_userek helyezes
	$r=mysql_query('select id,karrier,speci from hist_userek where idopont='.$idopont.' and szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and id not in ('.implode(',',$specko_userek_listaja).') order by pontszam_exp_atlag desc');
	$n=0;while($aux=mysql_fetch_array($r)) {
		if ($aux['karrier']!=3 or $aux['speci']!=3) $n++;
		mysql_query('update hist_userek set helyezes='.$n.' where idopont='.$idopont.' and id='.$aux[0]);
		mysql_query('update '.$database_mmog.'.userek set helyezes='.$n.' where id='.$aux[0]);
	}
	//
	//hist_diplomacia_statuszok
	mysql_query('
insert into hist_diplomacia_statuszok(idopont,ki,kivel,mi,miota,kezdemenyezo,felbontasi_ido,felbontas_alatt,felbontas_mikor,diplo_1,diplo_2,nyilvanos)
select '.$idopont.',ki,kivel,mi,miota,kezdemenyezo,felbontasi_ido,felbontas_alatt,felbontas_mikor,diplo_1,diplo_2,nyilvanos
from '.$database_mmog.'.diplomacia_statuszok
	') or hiba(__FILE__,__LINE__,mysql_error());
	//
	mysql_select_db($database_mmog);
}







function szabadpiac_tisztit($piac_id) {
	global $database_mmog,$database_mmog_nemlog;
	$kotesek=array();
	$referencia_arfolyam=mysql2num('select min(arfolyam) from tozsdei_arfolyamok where termek_id='.$piac_id);
	//LOCK
	mysql_query('lock tables szabadpiaci_ajanlatok write');
	$vanmeg=true;
	while($vanmeg) {
		$er=mysql_query('select * from szabadpiaci_ajanlatok where vetel=1 and termek_id='.$piac_id.' order by arfolyam desc limit 1');
		$veteli_oldal=mysql_fetch_array($er);
		$er=mysql_query('select * from szabadpiaci_ajanlatok where vetel=0 and termek_id='.$piac_id.' order by arfolyam limit 1');
		$eladasi_oldal=mysql_fetch_array($er);
		//ha lehet, teljesitsuk, legalabb reszben
		if ($veteli_oldal && $eladasi_oldal) {
			if ($eladasi_oldal['arfolyam']<=$veteli_oldal['arfolyam']) {
				$kotesi_arfolyam=round(($eladasi_oldal['arfolyam']+$veteli_oldal['arfolyam'])/2);
				if ($eladasi_oldal['mennyiseg']<$veteli_oldal['mennyiseg']) {//marad veteli
					$kotesi_mennyiseg=$eladasi_oldal['mennyiseg'];
					mysql_query('delete from szabadpiaci_ajanlatok where id='.$eladasi_oldal['id']);
					mysql_query('update szabadpiaci_ajanlatok set mennyiseg=if(mennyiseg>'.$kotesi_mennyiseg.',mennyiseg-'.$kotesi_mennyiseg.',0) where id='.$veteli_oldal['id']);
					mysql_query('delete from szabadpiaci_ajanlatok where mennyiseg=0 and id='.$veteli_oldal['id']);//ez elvileg sosincs
				} elseif ($ellenoldal['mennyiseg']==$veteli_oldal['mennyiseg']) {//kiutik egymast
					$kotesi_mennyiseg=$veteli_oldal['mennyiseg'];
					mysql_query('delete from szabadpiaci_ajanlatok where id='.$eladasi_oldal['id']);
					mysql_query('delete from szabadpiaci_ajanlatok where id='.$veteli_oldal['id']);
					$vanmeg=false;
				} else {//marad eladasi
					$kotesi_mennyiseg=$veteli_oldal['mennyiseg'];
					mysql_query('delete from szabadpiaci_ajanlatok where id='.$veteli_oldal['id']);
					mysql_query('update szabadpiaci_ajanlatok set mennyiseg=if(mennyiseg>'.$kotesi_mennyiseg.',mennyiseg-'.$kotesi_mennyiseg.',0) where id='.$eladasi_oldal['id']);
					mysql_query('delete from szabadpiaci_ajanlatok where mennyiseg=0 and id='.$eladasi_oldal['id']);//ez elvileg sosincs
				}
				//kotes
				$kotesek[]=array(
					$kotesi_arfolyam,$kotesi_mennyiseg
					,$veteli_oldal['user_id'],$eladasi_oldal['user_id']
					,$veteli_oldal['bolygo_id']
					,$veteli_oldal['arfolyam']
					,$eladasi_oldal['bolygo_id']
				);
			} else $vanmeg=false;
		} else $vanmeg=false;
	}
	mysql_query('unlock tables');
	//UNLOCK
	if (is_array($kotesek)) if (count($kotesek)>0) {
		$kotes_idopontja=date('Y-m-d H:i:s');
		//egyeb
		foreach($kotesek as $kotes) {
			mysql_query('update szabadpiaci_arfolyamok set arfolyam='.$kotes[0].',uccso_kotes_forgalom='.$kotes[1].',uccso_kotes_mikor="'.$kotes_idopontja.'" where termek_id='.$piac_id);
			if ($kotes[2]>0) mysql_query('update userek set vagyon=vagyon+'.($kotes[1]*($kotes[5]-$kotes[0])).' where id='.$kotes[2]);
			if ($kotes[3]>0) mysql_query('update userek set vagyon=vagyon+'.($kotes[1]*$kotes[0]).' where id='.$kotes[3]);
			if ($piac_id<150) {//nyersi
				if ($kotes[4]>0) mysql_query('update bolygo_eroforras set db=db+'.$kotes[1].' where bolygo_id='.$kotes[4].' and eroforras_id='.$piac_id);
			} else {//KP
				if ($kotes[2]>0) mysql_query('update userek set kp=kp+'.$kotes[1].' where id='.$kotes[2]);
			}
		}
		//kotesek
		mysql_select_db($database_mmog_nemlog);
		foreach($kotesek as $kotes) {
			mysql_query('insert into szabadpiaci_kotesek (termek_id,vevo,elado,vevo_bolygo,elado_bolygo,mennyiseg,arfolyam,mikor,referencia_arfolyam) values('.$piac_id.','.$kotes[2].','.$kotes[3].','.$kotes[4].','.$kotes[6].','.$kotes[1].','.$kotes[0].',"'.$kotes_idopontja.'",'.$referencia_arfolyam.')');
		}
		mysql_select_db($database_mmog);
	}
}



function terkep_koordinatak($x,$y,$nyelv='hu') {
	$s='';
	if ($nyelv=='hu') {
		if ($y<0) $s.='É '.number_format(round(-$y/2),0,',',' ');
		if ($y>0) $s.='D '.number_format(round($y/2),0,',',' ');
		$s.=', ';
		if ($x<0) $s.='NY '.number_format(round(-$x/2),0,',',' ');
		if ($x>0) $s.='K '.number_format(round($x/2),0,',',' ');
	} else {
		if ($y<0) $s.='N '.number_format(round(-$y/2),0,',',' ');
		if ($y>0) $s.='S '.number_format(round($y/2),0,',',' ');
		$s.=', ';
		if ($x<0) $s.='W '.number_format(round(-$x/2),0,',',' ');
		if ($x>0) $s.='E '.number_format(round($x/2),0,',',' ');
	}
	return $s;
}

function sok_hajo_felrakasa_bolygora($bid) {
	$er=mysql_query('select id from hajok where id<=218') or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) {
		mysql_query('update bolygo_eroforras set db=1000000 where eroforras_id='.$aux[0].' and bolygo_id='.$bid);
	}
}

function bolygo_reset($bid,$osztaly,$terulet,$tulajt_is=0) {
	if ($tulajt_is) {
		//user reset, ha van tulaj
		$user_id=mysql2num('select tulaj from bolygok where id='.$bid);
		if ($user_id>0) {
			mysql_query('update userek set techszint_ertesites=0,techszint=0,vagyon=0,kp=0,megoszthato_kp=0,tut_level=0,tut_fa=0,tut_ko=0,tut_kalozjutalom=0,tut_kaloz=0 where id='.$user_id);
			mysql_query('update user_kutatasi_szint set szint=0 where user_id='.$user_id);//fejlesztett kp se legyen
			tut_level($user_id,1);
			//badge-eket torolni
			mysql_query('delete from user_badge where user_id='.$user_id);
			//kalozflottakat leszedni, ha vannak
			$r=mysql_query('select id from flottak where tulaj=0 and kaloz_bolygo_id='.$bid);
			while($aux=mysql_fetch_array($r)) flotta_torles($aux[0]);
		}
	}
	//okoszfera
	$er=mysql_query('select * from bolygo_faj_celszam where osztaly='.$osztaly.' and terulet='.round($terulet/100000)) or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) {
		mysql_query('update bolygo_eroforras set db='.$aux['db'].' where eroforras_id='.$aux['eroforras_id'].' and bolygo_id='.$bid);
	}
	//minden eroforrast kinullazni (pl flottak es hasonlok), kiveve okoszfera!!!
	mysql_query('update bolygo_eroforras set db=0 where eroforras_id>=55 and bolygo_id='.$bid);
	//nyersanyagok
	if ($terulet>2000000) {
		mysql_query('update bolygo_eroforras set db='.($osztaly==3?(3.5*$terulet):(1.75*$terulet)).' where eroforras_id=60 and bolygo_id='.$bid);
		mysql_query('update bolygo_eroforras set db='.($osztaly==2?round($terulet/2):round($terulet/4)).' where eroforras_id=61 and bolygo_id='.$bid);
		mysql_query('update bolygo_eroforras set db='.($osztaly==5?$terulet:round($terulet/2)).' where eroforras_id=62 and bolygo_id='.$bid);
		mysql_query('update bolygo_eroforras set db='.($osztaly==1?round($terulet/400):round($terulet/2000)).' where eroforras_id=63 and bolygo_id='.$bid);
	} else {
		mysql_query('update bolygo_eroforras set db='.round($terulet*1.75).' where eroforras_id=60 and bolygo_id='.$bid);
		mysql_query('update bolygo_eroforras set db='.round($terulet/4).' where eroforras_id=61 and bolygo_id='.$bid);
		mysql_query('update bolygo_eroforras set db='.round($terulet/2).' where eroforras_id=62 and bolygo_id='.$bid);
		mysql_query('update bolygo_eroforras set db='.round($terulet/2000).' where eroforras_id=63 and bolygo_id='.$bid);
	}
	//eroforrasok
	mysql_query('update bolygo_eroforras set db=400000 where eroforras_id=64 and bolygo_id='.$bid);//fa
	mysql_query('update bolygo_eroforras set db=250000 where eroforras_id=65 and bolygo_id='.$bid);//ko
	mysql_query('update bolygo_eroforras set db=20000 where eroforras_id=72 and bolygo_id='.$bid);//uveg
	//alap izek
	mysql_query('update bolygo_ember set pop=30000 where bolygo_id='.$bid);
	mysql_query('update bolygo_eroforras set db=30000 where eroforras_id=55 and bolygo_id='.$bid);//lakohely
	mysql_query('update bolygo_eroforras set db=40000 where eroforras_id=56 and bolygo_id='.$bid);//kaja
	mysql_query('update bolygo_eroforras set db=15000 where eroforras_id=57 and bolygo_id='.$bid);//me
	mysql_query('update bolygo_eroforras set db=40000 where eroforras_id=59 and bolygo_id='.$bid);//energia
	mysql_query('update bolygo_eroforras set db=5000,delta_db=5000 where eroforras_id=58 and bolygo_id='.$bid);//kepzett me
	mysql_query('update bolygo_eroforras set db=5000,delta_db=5000 where eroforras_id=77 and bolygo_id='.$bid);//kepzett me
	//
	mysql_query('delete from queue_epitkezesek where bolygo_id='.$bid);
	mysql_query('delete from cron_tabla where bolygo_id='.$bid);
	mysql_query('delete from cron_tabla_eroforras_transzfer where honnan_bolygo_id='.$bid);
	mysql_query('delete from cron_tabla_eroforras_transzfer where hova_bolygo_id='.$bid);
	//ipar
	mysql_query('delete from bolygo_gyar where bolygo_id='.$bid);
	mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bid.',4,60,60)');//szeleromu
	$ize=array(0,17,18,20,21,23);mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bid.','.$ize[$osztaly].',1,1)');//hus (leghatekonyabb)
	$ize=array(0,26,29,33,34,25);mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bid.','.$ize[$osztaly].',1,1)');//vega (leghatekonyabb)
	mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bid.',78,1,1)');//varos
	$ize=array(0,45,46,47,48,49);mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bid.','.$ize[$osztaly].',1,1)');//fa
	mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bid.',50,1,1)');//ko
	//
	mysql_query('delete from szabadpiaci_ajanlatok where bolygo_id='.$bid);
	//
	mysql_query('update bolygok set raforditott_kornyezet_kp=0,terraformaltsag=10000,moral=100 where id='.$bid);
	bolygo_terulet_frissites($bid);
	//bgye
	mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$bid);
	mysql_query('
insert into bolygo_gyar_eroforras
select bgy.bolygo_id,bgy.gyar_id,gye.eroforras_id,bgy.aktiv_db,gye.io,coalesce(if(gye.io>=0,0,round(bgy.aktiv_db*gye.io/sumiotabla.sumio*1000000000)),0) as reszarany
from (
select bgy.bolygo_id,gye.eroforras_id,sum(bgy.aktiv_db*if(gye.io>=0,0,gye.io)) as sumio
from bolygo_gyar bgy,gyar_eroforras gye
where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id='.$bid.'
group by bgy.bolygo_id,gye.eroforras_id
) sumiotabla,bolygo_gyar bgy,gyar_eroforras gye
where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id=sumiotabla.bolygo_id and gye.eroforras_id=sumiotabla.eroforras_id
	');
}



function del_ures_user($user_id) {
	$er2=mysql_query('select * from bolygok where tulaj='.$user_id) or hiba(__FILE__,__LINE__,mysql_error());
	while($aux2=mysql_fetch_array($er2)) {
		$bolygo_id=$aux2['id'];
		$osztaly=$aux2['osztaly'];
		mysql_query('update bolygok set nev=concat("B",id),kulso_nev=concat("B",id),tulaj=0,anyabolygo=0,kezelo=0,tulaj_szov=0,fobolygo=0,vedelmi_bonusz=0 where id='.$bolygo_id) or hiba(__FILE__,__LINE__,mysql_error());
		//bolygo resetelese
		bolygo_reset($bolygo_id,$osztaly,$aux2['terulet'],1);//a usert is, ami azert kell, hogy minden epitkezes es hasonlo torolve legyen
	}
	//bar elvileg a bolygo_reset torli, de menjunk biztosra:
	mysql_query('delete from szabadpiaci_ajanlatok where user_id='.$user_id);
	//kilepes szovbol, esetleges feloszlatas
	$alapito=0;$szovi_torolve=0;
	$er2=mysql_query('select * from userek where id='.$user_id) or hiba(__FILE__,__LINE__,mysql_error());
	$adataim=mysql_fetch_array($er2);
	if ($adataim['szovetseg']>0) {
		$er=mysql_query('select alapito from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);$alapito=$aux[0];
		$er=mysql_query('select count(1) from userek where szovetseg='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);
		mysql_query('update szovetsegek set tagletszam='.($aux[0]-1).' where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
		if ($aux[0]==1) {//szovetseg feloszlatasa
			mysql_query('delete from szovetseg_meghivok where hova='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('delete from szovetseg_szabalyzatok where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('delete from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('delete from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('delete from diplomacia_ajanlatok where ki='.$adataim['szovetseg'].' or kinek='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('delete from diplomacia_statuszok where ki='.$adataim['szovetseg'].' or kivel='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			$alapito=0;$szovi_torolve=1;
		} elseif ($adataim['tisztseg']==-1) {//ha megmarad, viszont alapitot kell cserelni
			$er=mysql_query('select id from userek where szovetseg='.$adataim['szovetseg'].' and id!='.$user_id.' order by szov_belepes,id limit 1') or hiba(__FILE__,__LINE__,mysql_error());
			$aux=mysql_fetch_array($er);//aki a legregebben lepett be
			mysql_query('update szovetsegek set alapito='.$aux[0].' where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('update userek set tisztseg=-1 where id='.$aux[0]) or hiba(__FILE__,__LINE__,mysql_error());
			$alapito=$aux[0];
		}
	} else {//ha maganyos farkas volt
		mysql_query('delete from diplomacia_ajanlatok where ki=-'.$user_id.' or kinek=-'.$user_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('delete from diplomacia_statuszok where ki=-'.$user_id.' or kivel=-'.$user_id) or hiba(__FILE__,__LINE__,mysql_error());
	}
	//reszflottak torlese,kivonasa
	$er=mysql_query('select distinct flotta_id from resz_flotta_hajo where user_id='.$user_id);
	while($aux=mysql_fetch_array($er)) {
		flotta_reszflotta_frissites($aux[0]);
		$er2=mysql_query('select * from resz_flotta_hajo where user_id='.$user_id.' and flotta_id='.$aux[0]);
		while($aux2=mysql_fetch_array($er2)) mysql_query('update flotta_hajo set ossz_hp=if(ossz_hp>'.$aux2['hp'].',ossz_hp-'.$aux2['hp'].',0) where flotta_id='.$aux[0].' and hajo_id='.$aux2['hajo_id']);
		mysql_query('delete from resz_flotta_hajo where user_id='.$user_id.' and flotta_id='.$aux[0]);
		flotta_reszflotta_frissites($aux[0]);
		//ha maradnak reszei, es tulaj nelkul marad, akkor atruhazni
		$vannak_e_reszflottai=sanitint(mysql2num('select count(1) from resz_flotta_hajo where flotta_id='.$aux[0]));
		if ($vannak_e_reszflottai>0) {
			$tulaj=mysql2num('select tulaj from flottak where id='.$aux[0]);
			if ($tulaj==$user_id) {
				$legnagyobb_maradek_tulaj=mysql2num('select rfh.user_id from resz_flotta_hajo rfh, hajok h where rfh.flotta_id='.$aux[0].' and rfh.hajo_id=h.id group by rfh.user_id order by sum(rfh.hp*h.ar) desc limit 1');
				if ($legnagyobb_maradek_tulaj>0) {//ha nincs, akkor marad nala, vagyis lentebb torlodik
					$legnagyobb_maradek_tulaj_szov=mysql2num('select tulaj_szov from userek where id='.$legnagyobb_maradek_tulaj);
					mysql_query('update flottak set tulaj='.$legnagyobb_maradek_tulaj.',tulaj_szov='.$legnagyobb_maradek_tulaj_szov.' where id='.$aux[0]);
				}
			}
		}
		flotta_minden_frissites($aux[0]);
	}
	//flottak torlese
	$er=mysql_query('select id from flottak where tulaj='.$user_id);
	while($aux=mysql_fetch_array($er)) {
		mysql_query('delete from flotta_hajo where flotta_id='.$aux[0]) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update flottak set statusz='.STATUSZ_VISSZA.' where (statusz='.STATUSZ_MEGY_FLOTTAHOZ.' or statusz='.STATUSZ_TAMAD_FLOTTARA.' or statusz='.STATUSZ_TAMAD_FLOTTAT.') and cel_flotta='.$aux[0]) or hiba(__FILE__,__LINE__,mysql_error());
	}
	mysql_query('delete from flottak where tulaj='.$user_id);
	//ugynokok torlese
	mysql_query('delete from ugynokcsoportok where tulaj='.$user_id);
	//levelek
	mysql_query('delete from levelek where tulaj='.$user_id);
	//csetszobak
	$er=mysql_query('select id from cset_szobak where tulaj='.$user_id);
	while($aux=mysql_fetch_array($er)) {
		mysql_query('delete from cset_szoba_user where cset_szoba_id='.$aux[0]);
		mysql_query('delete from cset_szoba_meghivok where cset_szoba_id='.$aux[0]);
	}
	mysql_query('delete from cset_szobak where tulaj='.$user_id);
	//legvegen user torlese
	mysql_query('delete from user_beallitasok where user_id='.$user_id);
	mysql_query('delete from user_kutatasi_szint where user_id='.$user_id);
	mysql_query('delete from userek where id='.$user_id);
	/*
	//ZandaNet-en deleted-re allitani a szerver regisztraciot
	if ($adataim['zanda_id']>0) {
		global $szerver_prefix,$mysql_username,$mysql_password;
		$mysql_csatlakozas_zandanet=mysql_connect('HOST','USER','PASSWORD');
		mysql_select_db('mmog',$mysql_csatlakozas_zandanet) or die();
		mysql_query('set names "utf8"',$mysql_csatlakozas_zandanet);
		mysql_query('update zandanet_server_accounts set deleted=1, deletion_time="'.date('Y-m-d H:i:s').'" where zanda_id='.$adataim['zanda_id'].' and server_prefix="'.$szerver_prefix.'" and user_id='.$user_id,$mysql_csatlakozas_zandanet);
		//
		mysql_connect('localhost',$mysql_username,$mysql_password);//hogy ujra a helyi szerver legyen a default
	}
	*/
}


function jatekos_szovivaltas($user_id,$regi_szov_id,$uj_szov_id) {
	global $database_mmog_nemlog;
	if ($regi_szov_id!=$uj_szov_id) {
		//logolas
		mysql_query("insert into $database_mmog_nemlog.user_szovi_valtasok (user_id,regi_szov_id,uj_szov_id) values($user_id,$regi_szov_id,$uj_szov_id)");
		//kozos flottak visszavetele
		mysql_query('update flottak set kozos=0 where tulaj='.$user_id);
		//ugynokcsoportok tulaj_szov valtasa
		mysql_query('update ugynokcsoportok set tulaj_szov='.(($uj_szov_id>0)?$uj_szov_id:(-$user_id)).' where tulaj='.$user_id);
		//ha szukseg van barmi masra
	}
}

function bolygo_tulaj_valtozas($bolygo_id,$regi_tulaj_id,$uj_tulaj_id,$regi_tulaj_szov,$uj_tulaj_szov) {
	//regiok: ha a regi tulajnak nem marad bolygoja vmelyik aktualis regioban, akkor valtani
	if ($regi_tulaj_id>0) {
		$regi_tulaj=mysql2row('select * from userek where id='.$regi_tulaj_id);
		if ($regi_tulaj['karrier']==1 and $regi_tulaj['speci']==2) {//kereskedo
			$bolygok_szama_az_aktualis_regioban=(int)mysql2num('select count(1) from bolygok where tulaj='.$regi_tulaj_id.' and regio='.$regi_tulaj['aktualis_regio']);
			$bolygok_szama_az_aktualis_regio2ben=(int)mysql2num('select count(1) from bolygok where tulaj='.$regi_tulaj_id.' and regio='.$regi_tulaj['aktualis_regio2']);
			if ($bolygok_szama_az_aktualis_regioban==0 and $bolygok_szama_az_aktualis_regio2ben==0) {//ilyen elvileg nem fordulhat elo
				$uj_tobbsegi_regio=(int)mysql2num('select regio from bolygok where tulaj='.$regi_tulaj_id.' group by regio order by sum(iparmeret) desc limit 1');
				mysql_query('update userek set tobbsegi_regio='.$uj_tobbsegi_regio.',aktualis_regio='.$uj_tobbsegi_regio.',aktualis_regio2='.$uj_tobbsegi_regio.',valasztott_regio='.$uj_tobbsegi_regio.',valasztott_regio2='.$uj_tobbsegi_regio.' where id='.$regi_tulaj_id);
			} elseif ($bolygok_szama_az_aktualis_regioban==0) {
				mysql_query('update userek set aktualis_regio=aktualis_regio2,valasztott_regio=valasztott_regio2 where id='.$regi_tulaj_id);
			} elseif ($bolygok_szama_az_aktualis_regio2ben==0) {
				mysql_query('update userek set aktualis_regio2=aktualis_regio,valasztott_regio2=valasztott_regio where id='.$regi_tulaj_id);
			}
		} else {//nem kereskedo
			$bolygok_szama_az_aktualis_regioban=(int)mysql2num('select count(1) from bolygok where tulaj='.$regi_tulaj_id.' and regio='.$regi_tulaj['aktualis_regio']);
			if ($bolygok_szama_az_aktualis_regioban==0) {//ha mar nem maradt, akkor ujraszamolni a tobbsegit
				$uj_tobbsegi_regio=(int)mysql2num('select regio from bolygok where tulaj='.$regi_tulaj_id.' group by regio order by sum(iparmeret) desc limit 1');
				mysql_query('update userek set tobbsegi_regio='.$uj_tobbsegi_regio.',aktualis_regio='.$uj_tobbsegi_regio.',aktualis_regio2='.$uj_tobbsegi_regio.',valasztott_regio='.$uj_tobbsegi_regio.',valasztott_regio2='.$uj_tobbsegi_regio.' where id='.$regi_tulaj_id);
			}
		}
	}
	//szabadpiaci ajanlatokat is atadni
	mysql_query('update szabadpiaci_ajanlatok aj, bolygok b
set aj.user_id=b.tulaj
where aj.bolygo_id=b.id and aj.user_id!=b.tulaj');
	//autotranszfereket leallitani, ha a tulaj_szov megvaltozott
	if ($uj_tulaj_szov!=$regi_tulaj_szov) {
		mysql_query('delete from cron_tabla_eroforras_transzfer where honnan_bolygo_id='.$bolygo_id);
		mysql_query('delete from cron_tabla_eroforras_transzfer where hova_bolygo_id='.$bolygo_id);
	}
	//specko epuletek inaktivalasa, ha kell
$er=mysql_query('
select bgy.gyar_id,coalesce(min(if(uksz.szint>=gyksz.szint,1,0))) from bolygo_gyar bgy, gyar_kutatasi_szint gyksz, user_kutatasi_szint uksz
where bgy.bolygo_id='.$bolygo_id.' and bgy.aktiv_db>0 and gyksz.gyar_id=bgy.gyar_id and gyksz.kf_id=uksz.kf_id and uksz.user_id='.$uj_tulaj_id.'
group by bgy.gyar_id
');
	while($aux=mysql_fetch_array($er)) if ($aux[1]==0) {
		mysql_query('update bolygo_gyar set aktiv_db=0 where bolygo_id='.$bolygo_id.' and gyar_id='.$aux[0]);
	}
	bgye_frissites($bolygo_id);
	//flottak bazisa: az adott bolygóról el kell tűnniük
	$er_anya=mysql_query('select id from bolygok where tulaj='.$regi_tulaj_id.' order by nev limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux_anya=mysql_fetch_array($er_anya);
	mysql_query('update flottak f
set f.bazis_bolygo='.((int)$aux_anya[0]).'
where f.bazis_bolygo='.$bolygo_id.' and f.tulaj_szov!='.$uj_tulaj_szov) or hiba(__FILE__,__LINE__,mysql_error());
	//flottak allomasozasa
	mysql_query('
update flottak f,bolygok b
set f.statusz='.STATUSZ_ALL.'
where f.bolygo=b.id and f.statusz='.STATUSZ_ALLOMAS.' and b.tulaj_szov!=f.tulaj_szov') or hiba(__FILE__,__LINE__,mysql_error());
	//felderites (szim.php-ben megtortenik)
	//regi es uj tulaj vedelmi szintjeit ujraszamolni
	$bolygo_terulete=mysql2num('select round(terulet/1000000) from bolygok where id='.$bolygo_id);
	//
	if ($uj_tulaj_id>0) {
		$uj_tulaj_terulete=mysql2num('select round(sum(terulet)/1000000) from bolygok where tulaj='.$uj_tulaj_id);
		mysql_query('update userek set valaha_elert_max_terulet=greatest(valaha_elert_max_terulet,'.$uj_tulaj_terulete.') where id='.$uj_tulaj_id);
		frissit_user_vedelmi_szintek($uj_tulaj_id,0,$uj_tulaj_terulete-$bolygo_terulete);
	} else {//ha npc lesz, akkor vedelmi bonusz nullazasa
		mysql_query('update bolygok set vedelmi_bonusz=0 where id='.$bolygo_id);
	}
	if ($regi_tulaj_id>0) {
		$regi_tulaj_terulete=mysql2num('select round(sum(terulet)/1000000) from bolygok where tulaj='.$regi_tulaj_id);
		frissit_user_vedelmi_szintek($regi_tulaj_id,0,$regi_tulaj_terulete+$bolygo_terulete);
	}
}
function frissit_user_vedelmi_szintek($user_id,$teruletet_frissit=0,$regi_terulet=0) {
	global $database_mmog,$database_mmog_nemlog;
	mysql_select_db($database_mmog_nemlog);
	mysql_query('insert into terulet_valtozasok (user_id,terulet) values('.$user_id.','.$regi_terulet.')');
	mysql_select_db($database_mmog);
	//
	$ter=mysql2num('select round(sum(terulet)/1000000) from bolygok where tulaj='.$user_id);
	if ($teruletet_frissit) {
		mysql_query('update userek set valaha_elert_max_terulet=greatest(valaha_elert_max_terulet,'.$ter.') where id='.$user_id);
	}
	$valaha_elert_max_terulet=mysql2num('select valaha_elert_max_terulet from userek where id='.$user_id);
	$x=$valaha_elert_max_terulet/2;
	if ($x==0) {
		$abszolut_vedettseg=0;
	} elseif ($x==1) {
		$abszolut_vedettseg=1;
	} elseif ($x<3) {
		$abszolut_vedettseg=0.5;
	} else {
		$abszolut_vedettseg=0.4;
	}
	if ($x==0) {
		$relativ_vedettseg=0;
	} elseif ($x<=2) {
		$relativ_vedettseg=1;
	} elseif ($x<6) {
		$relativ_vedettseg=0.75;
	} else {
		$relativ_vedettseg=0.6;
	}
	$abszolut_vedett_terulet=floor($abszolut_vedettseg*$valaha_elert_max_terulet);
	$reszben_vedett_terulet=floor($relativ_vedettseg*$valaha_elert_max_terulet);
	$er=mysql_query('select id,terulet from bolygok where tulaj='.$user_id.' order by uccso_foglalas_mikor');
	$utolso_abszolut_vedett=0;$elso_nem_vedett=0;
	$n=0;$terx=0;while(($aux=mysql_fetch_array($er))&&($elso_nem_vedett==0)) {
		$n++;$terx+=round($aux['terulet']/1000000);
		if ($terx<=$abszolut_vedett_terulet) $utolso_abszolut_vedett=$n;
		if ($elso_nem_vedett==0) if ($terx>$reszben_vedett_terulet) $elso_nem_vedett=$n;
	}
	$jelenlegi_terulet=$ter;
	if ($elso_nem_vedett==0) $elso_nem_vedett=$n+1;
	if ($n>0) {//legalabb 1 bolygoja van
		mysql_data_seek($er,0);
		$n=0;while($aux=mysql_fetch_array($er)) {
			$n++;
			if ($n<=$utolso_abszolut_vedett) {
				if ($abszolut_vedett_terulet>=10) {//5 2M-es vedett bolygotol kezdve az elso is foszthato
					if ($utolso_abszolut_vedett==1) $vb=900;
					else $vb=900-100*($n-1)/($utolso_abszolut_vedett-1);
				} else {
					if ($utolso_abszolut_vedett==1) $vb=1000;
					else $vb=1000-200*($n-1)/($utolso_abszolut_vedett-1);
				}
			} elseif ($n<$elso_nem_vedett) {
				if ($utolso_abszolut_vedett==1) $vb=(($abszolut_vedett_terulet>=10)?900:1000)*(1-($n-$utolso_abszolut_vedett)/($elso_nem_vedett-$utolso_abszolut_vedett));
				else $vb=800*(1-($n-$utolso_abszolut_vedett)/($elso_nem_vedett-$utolso_abszolut_vedett));
			} else $vb=0;
			mysql_query('update bolygok set vedelmi_bonusz='.round($vb).',foglalasi_sorszam='.$n.' where id='.$aux[0]);
		}
	}
	mysql_query('update userek set jelenlegi_terulet='.$jelenlegi_terulet.',abszolut_vedett_terulet='.$abszolut_vedett_terulet.',reszben_vedett_terulet='.$reszben_vedett_terulet.' where id='.$user_id);
}


function elerheto_ez_a_gyar($bolygo_osztaly,$bolygo_hold,$gyar_id,$user_id) {
	$er=mysql_query('select * from gyarak where id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	$gyar=mysql_fetch_array($er);
	$er=mysql_query('select * from eroforrasok e where id='.$gyar['uzemmod']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	$lehet=true;
	if ($aux['tipus']==EROFORRAS_TIPUS_FAJ) {
		if ($aux['bolygo_osztaly']&pow(2,$bolygo_osztaly-1)) $lehet=true;else $lehet=false;
	}
	switch($gyar['tipus']) {
		case 1:if ($bolygo_osztaly!=2) $lehet=false;//nap
		break;
		case 5:if ($bolygo_osztaly!=1 && $bolygo_osztaly!=4) $lehet=false;//viz
		break;
		case 6:if ($bolygo_osztaly<3) $lehet=false;//hullam
		break;
		case 7:if ($bolygo_osztaly<3 || $bolygo_hold==0) $lehet=false;//arapaly
		break;
		case 8:if ($bolygo_osztaly!=4) $lehet=false;//ozmozis
		break;
		case 9:if ($bolygo_osztaly!=5) $lehet=false;//geoterm
		break;
		case 11:if ($bolygo_osztaly==5) $lehet=false;//bioetanol
		break;
	}
	$er2=mysql_query('
	select coalesce(min(if(uksz.szint>=gyksz.szint,1,0)),999)
	from gyar_kutatasi_szint gyksz, user_kutatasi_szint uksz
	where gyksz.gyar_id='.$gyar_id.' and gyksz.kf_id=uksz.kf_id and uksz.user_id='.$user_id.'
	') or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($er2);
	if (!$aux2) return $lehet;//nincs is ra igeny
	if ($aux2[0]==0) return false;else return $lehet;
}

function uj_gyar_felhuzasa($bolygo_id,$gyar_id,$aktiv_e,$darab=1) {
	if ($darab<=0) return false;
	$er=mysql_query('select * from bolygok where id='.$bolygo_id) or hiba(__FILE__,__LINE__,mysql_error());
	$bolygo=mysql_fetch_array($er);
	//
	//van-e eleg hely, mire van eleg hely
	$er=mysql_query('select gyt.terulet from gyartipusok gyt, gyarak gy where gyt.id=gy.tipus and gy.id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	$gyartipus=mysql_fetch_array($er);
	//ha (terulet_beepitett+db*gyar_terulet)/terraformaltsag*10000 > terulet, akkor csak részteljesítés:
	//db = floor((terulet/10000*terraformaltsag-terulet_beepitett)/gyar_terulet)
	//if (($bolygo['terulet_beepitett']+$darab*$gyartipus['terulet'])/$bolygo['terraformaltsag']*10000>$bolygo['terulet']) {
	if ($bolygo['terulet_beepitett']/$bolygo['terraformaltsag']*10000>$bolygo['terulet']) {//mivel az epulofelben levo mar beleszamit, es itt meg nincs torolve, ezert sajat magat mar ne adja hozza
		$darab=floor(($bolygo['terulet']/10000*$bolygo['terraformaltsag']-$bolygo['terulet_beepitett'])/$gyartipus['terulet']);
		if ($darab<=0) return false;
	}
	//
	if (!elerheto_ez_a_gyar($bolygo['osztaly'],$bolygo['hold'],$gyar_id,$bolygo['tulaj'])) $aktiv_e=0;//ha barmi miatt nem kaphat ilyet, akkor inaktivalni
	$er=mysql_query('select * from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux) {//van mar ilyen -> update
		if ($aktiv_e) mysql_query('update bolygo_gyar set db=db+'.$darab.',aktiv_db=aktiv_db+'.$darab.' where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		else mysql_query('update bolygo_gyar set db=db+'.$darab.' where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	} else {//nincs meg -> insert
		if ($aktiv_e) $a=$darab;else $a=0;
		mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bolygo_id.','.$gyar_id.','.$darab.','.$a.')') or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('
insert into bolygo_gyar_eroforras
select '.$bolygo_id.','.$gyar_id.',eroforras_id,0,0,0
from gyar_eroforras
where gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	}
	bgye_frissites($bolygo_id);
}

function regi_gyar_lerombolasa($bolygo_id,$gyar_id,$aktiv_e,$darab=1) {//ezt legfeljebb mar csak az admin hivja meg
	if ($darab<=0) return false;
	$er=mysql_query('select db from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux[0]>$darab) {//darabnal tobb ilyen van -> update
		if ($aktiv_e) mysql_query('update bolygo_gyar set db=if(db>'.$darab.',db-'.$darab.',0),aktiv_db=if(aktiv_db>'.$darab.',aktiv_db-'.$darab.',0) where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		else mysql_query('update bolygo_gyar set db=if(db>'.$darab.',db-'.$darab.',0) where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update bolygo_gyar set aktiv_db=least(db,aktiv_db) where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	} else {//csak darab vagy kevesebb van -> delete
		mysql_query('delete from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	}
	bgye_frissites($bolygo_id);
}
function regi_gyar_lerombolasa_lassan($bolygo_id,$gyar_id,$aktiv_e,$darab=1,$ido) {//keszrol lerombolas!!!, indulo_allapot=2
	if ($darab<=0) return false;
	$er=mysql_query('select db from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux[0]>$darab) {//darabnal tobb ilyen van -> update
		if ($aktiv_e) mysql_query('update bolygo_gyar set db=if(db>'.$darab.',db-'.$darab.',0),aktiv_db=if(aktiv_db>'.$darab.',aktiv_db-'.$darab.',0) where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		else mysql_query('update bolygo_gyar set db=if(db>'.$darab.',db-'.$darab.',0) where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update bolygo_gyar set aktiv_db=least(db,aktiv_db) where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		$vegul_darab=$darab;
	} else {//csak darab vagy kevesebb van -> delete
		mysql_query('delete from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$bolygo_id.' and gyar_id='.$gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		$vegul_darab=$aux[0];
	}
	mysql_query('insert into cron_tabla (mikor_aktualis,feladat,bolygo_id,gyar_id,aktiv,darab,indulo_allapot) values("'.date('Y-m-d H:i:s',time()+$ido).'",'.FELADAT_GYAR_LEROMBOL.','.$bolygo_id.','.$gyar_id.','.$aktiv_e.','.$vegul_darab.',2)')or hiba(__FILE__,__LINE__,mysql_error());
	bgye_frissites($bolygo_id);
}

function gyar_uj_uzemmodba_allitasa($bolygo_id,$regi_gyar_id,$uj_gyar_id,$aktiv_e,$darab) {
	if ($darab<=0) return false;
	//
	//LOCK ELEJE
	mysql_query('lock tables bolygo_gyar write, bolygo_gyar_eroforras write, gyar_eroforras read');
		//
		$er=mysql_query('select db from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$regi_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);
		if ($aux[0]>$darab) {//darabnal tobb ilyen van -> update
			if ($aktiv_e) mysql_query('update bolygo_gyar set db=if(db>'.$darab.',db-'.$darab.',0),aktiv_db=if(aktiv_db>'.$darab.',aktiv_db-'.$darab.',0) where bolygo_id='.$bolygo_id.' and gyar_id='.$regi_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
			else mysql_query('update bolygo_gyar set db=if(db>'.$darab.',db-'.$darab.',0) where bolygo_id='.$bolygo_id.' and gyar_id='.$regi_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('update bolygo_gyar set aktiv_db=least(db,aktiv_db) where bolygo_id='.$bolygo_id.' and gyar_id='.$regi_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		} else {//csak darab vagy kevesebb van -> delete
			$darab=$aux[0];
			mysql_query('delete from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$regi_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$bolygo_id.' and gyar_id='.$regi_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		}
		//
		if ($darab>0) {
		$er=mysql_query('select * from bolygo_gyar where bolygo_id='.$bolygo_id.' and gyar_id='.$uj_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);
		if ($aux) {//van mar ilyen -> update
			if ($aktiv_e) mysql_query('update bolygo_gyar set db=db+'.$darab.',aktiv_db=aktiv_db+'.$darab.' where bolygo_id='.$bolygo_id.' and gyar_id='.$uj_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
			else mysql_query('update bolygo_gyar set db=db+'.$darab.' where bolygo_id='.$bolygo_id.' and gyar_id='.$uj_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		} else {//nincs meg -> insert
			if ($aktiv_e) $a=$darab;else $a=0;
			mysql_query('insert into bolygo_gyar (bolygo_id,gyar_id,db,aktiv_db) values('.$bolygo_id.','.$uj_gyar_id.','.$darab.','.$a.')') or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('
insert into bolygo_gyar_eroforras
select '.$bolygo_id.','.$uj_gyar_id.',eroforras_id,0,0,0
from gyar_eroforras
where gyar_id='.$uj_gyar_id) or hiba(__FILE__,__LINE__,mysql_error());
		}
		}
		//
	mysql_query('unlock tables');
	//LOCK VEGE
	//
	bgye_frissites($bolygo_id);
}


function bolygo_terulet_frissites($melyik) {
	//beepitett terulet
	$terulet_beepitett=mysql2num('select coalesce(sum(bgy.db*gyt.terulet),0) from bolygo_gyar bgy, gyarak gy, gyartipusok gyt where bgy.gyar_id=gy.id and gy.tipus=gyt.id and bgy.bolygo_id='.$melyik);
	//epulofelben levok hozzaadasa
	$terulet_beepitett+=mysql2num('select coalesce(sum(c.darab*gyt.terulet),0) from cron_tabla c,gyarak gy,gyartipusok gyt where c.feladat='.FELADAT_GYAR_EPIT.' and c.bolygo_id='.$melyik.' and c.gyar_id=gy.id and gy.tipus=gyt.id');
	//rombolofelben levok hozzaadasa
	$terulet_beepitett+=mysql2num('select coalesce(sum(c.darab*gyt.terulet),0) from cron_tabla c,gyarak gy,gyartipusok gyt where c.feladat='.FELADAT_GYAR_LEROMBOL.' and c.bolygo_id='.$melyik.' and c.gyar_id=gy.id and gy.tipus=gyt.id');
	//terraformaltsag, effektiv beepitett terulet
	$er2=mysql_query('select terraformaltsag from bolygok where id='.$melyik) or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($er2);
	$terulet_beepitett_effektiv=round($terulet_beepitett/$aux2[0]*10000);
	//
	mysql_query('update bolygok set terulet_beepitett='.$terulet_beepitett.',terulet_beepitett_effektiv='.$terulet_beepitett_effektiv.',terulet_szabad=greatest(terulet-'.$terulet_beepitett_effektiv.',0) where id='.$melyik);
}
function bgye_frissites($melyik) {
	//ha valamelyik gyarbol 0 maradt, akkor a bgy es bgye-bol torolni kell
	$er=mysql_query('select * from bolygo_gyar where bolygo_id='.$melyik.' and db=0') or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) {
		mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$melyik.' and gyar_id='.$aux['gyar_id']);
	}
	mysql_query('delete from bolygo_gyar where db=0 and bolygo_id='.$melyik);
	//
	bolygo_terulet_frissites($melyik);
mysql_query('
update bolygo_gyar_eroforras bgye,(
	select bgy.bolygo_id,bgy.gyar_id,gye.eroforras_id,bgy.aktiv_db,gye.io,coalesce(if(gye.io>=0,0,round(bgy.aktiv_db*gye.io/sumiotabla.sumio*1000000000)),0) as reszarany
	from (
		select bgy.bolygo_id,gye.eroforras_id,sum(bgy.aktiv_db*if(gye.io>=0,0,gye.io)) as sumio
		from bolygo_gyar bgy,gyar_eroforras gye
		where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id='.$melyik.'
		group by bgy.bolygo_id,gye.eroforras_id
	) sumiotabla,bolygo_gyar bgy,gyar_eroforras gye
	where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id='.$melyik.' and gye.eroforras_id=sumiotabla.eroforras_id
) apdet
set bgye.aktiv_db=apdet.aktiv_db,
bgye.io=apdet.io,
bgye.reszarany=apdet.reszarany
where bgye.bolygo_id='.$melyik.' and bgye.gyar_id=apdet.gyar_id and bgye.eroforras_id=apdet.eroforras_id
') or hiba(__FILE__,__LINE__,mysql_error());
}

/******************************************************** HABORU ELEJE ******************************************************************/
function flotta_fejvadasz_frissites($melyik) {
	$flotta=mysql2row('select f.fejvadasz_bonusz,f.tulaj as flotta_tulaj,coalesce(ui.id,0) as iranyito,ui.karrier,ui.speci from flottak f left join userek ui on ui.id=f.uccso_parancs_by where f.id='.$melyik);
	$fejvadasz=0;
	if (mysql2num('select count(1) from resz_flotta_hajo where flotta_id='.$melyik)==0) {//nem osszevont flotta
		if ($flotta['flotta_tulaj']==$flotta['iranyito']) {//sajat flottat iranyit
			if ($flotta['karrier']==2) if ($flotta['speci']==3) {//fejvadasz
				$fejvadasz=1;
			}
		}
	}
	if ($fejvadasz!=$flotta['fejvadasz_bonusz']) {
		mysql_query('update flottak set fejvadasz_bonusz='.$fejvadasz.' where id='.$melyik);
		return true;
	}
	return false;
}
function flotta_minden_frissites($melyik) {
//specko hajok aranya
mysql_query('
update flottak f,(
select sum(if(h.id='.HAJO_TIPUS_KOORDI.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as koordi_arany
,sum(if(h.id='.HAJO_TIPUS_OHS.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as ohs_arany
,coalesce(sum(if(h.id='.HAJO_TIPUS_ANYA.',fh.ossz_hp*h.ar,null))/sum(if(h.id!='.HAJO_TIPUS_FULGUR.',fh.ossz_hp*h.ar,null)),0) as anyahajo_arany
,sum(if(h.id='.HAJO_TIPUS_CASTOR.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as castor_arany
,sum(if(h.id='.HAJO_TIPUS_POLLUX.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as pollux_arany
from flotta_hajo fh, hajok h
where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id
) t
set f.koordi_arany=round(100*t.koordi_arany)
,f.ohs_arany=round(100*t.ohs_arany)
,f.anyahajo_arany=round(100*t.anyahajo_arany)
,f.castor_arany=round(100*t.castor_arany)
,f.pollux_arany=round(100*t.pollux_arany)
where f.id='.$melyik) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flotta_hajo fh, hajok h, flottak f
set fh.koordi_arany=f.koordi_arany
,fh.ohs_arany=f.ohs_arany
,fh.anyahajo_arany=f.anyahajo_arany
,fh.castor_arany=f.castor_arany
,fh.pollux_arany=f.pollux_arany
,fh.tamado_ero=h.tamado_ero
,fh.valodi_hp=h.valodi_hp
where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and f.id='.$melyik) or hiba(__FILE__,__LINE__,mysql_error());

//latotav, rejtozes, egyenertek
mysql_query('
update flottak f, (
	select max(if(fh.ossz_hp>0,h.latotav,0)) as ossz_latotav,
	round(sum(if(h.vadasz=1,fh.ossz_hp*h.ar,0))/sum(fh.ossz_hp*h.ar)*coalesce(min(if(fh.ossz_hp>0 and h.vadasz=1,h.rejtozes,null)),0)+sum(if(h.vadasz=0,fh.ossz_hp*h.ar,0))/sum(fh.ossz_hp*h.ar)*coalesce(min(if(fh.ossz_hp>0 and h.vadasz=0,h.rejtozes,null)),0)) as ossz_rejtozes,
	round(sum(fh.ossz_hp/100*h.ar)) as ossz_egyenertek
	from flotta_hajo fh, hajok h
	where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id
) ossztab
set
f.latotav=ossztab.ossz_latotav,
f.rejtozes=ossztab.ossz_rejtozes,
f.egyenertek=ossztab.ossz_egyenertek
where f.id='.$melyik);

//sebesseg
$vadasz_egyenertek=mysql2num('select sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.hajo_id=h.id and h.vadasz=1 and fh.flotta_id='.$melyik);
$nemvadasz_egyenertek=mysql2num('select sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.hajo_id=h.id and h.vadasz=0 and fh.flotta_id='.$melyik);
$anyahajo_egyenertek=4*mysql2num('select sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.hajo_id=h.id and h.id='.HAJO_TIPUS_ANYA.' and fh.flotta_id='.$melyik);
if ($vadasz_egyenertek<=$nemvadasz_egyenertek) {
	//befert minden vadasz
	//csak a nemvadaszokat kell bepakolni az anyahajokba
	if ($anyahajo_egyenertek>0) {//van anyahajo
		$cipelendo_egyenertek=mysql2num('select sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.hajo_id=h.id and h.vadasz=0 and h.id not in ('.HAJO_TIPUS_ANYA.','.HAJO_TIPUS_FULGUR.') and fh.flotta_id='.$melyik);
		if ($cipelendo_egyenertek<=$anyahajo_egyenertek) {//minden befert
			$anyahajok_terhelese = $cipelendo_egyenertek/$anyahajo_egyenertek;
			$sebesseg = 40-10*$anyahajok_terhelese;
		} else {//van ami kimaradt
			$szabad_hely=$anyahajo_egyenertek;
			$r=mysql_query('select h.sebesseg,sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and h.vadasz=0 and h.sebesseg<80 and fh.ossz_hp>0 group by h.sebesseg');
			unset($leglassabb_ami_kimarad);
			while($aux=mysql_fetch_array($r)) {
				$szabad_hely-=$aux[1];
				if (!isset($leglassabb_ami_kimarad)) if ($szabad_hely<0) $leglassabb_ami_kimarad=$aux[0];
			}
			$sebesseg = $leglassabb_ami_kimarad/2;
		}
	} else {//nincs anyahajo
		$sebesseg = mysql2num('select min(if(fh.ossz_hp>0,h.sebesseg,1000)) from flotta_hajo fh, hajok h where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and h.vadasz=0')/2;
	}
} else {
	//kimaradt vadasz
	$kimaradt_vadasz_egyenertek=$vadasz_egyenertek-$nemvadasz_egyenertek;
	//a nemvadaszokat es a kimaradt vadaszokat bepakolni az anyahajokba
	if ($anyahajo_egyenertek>0) {//van anyahajo
		$cipelendo_egyenertek=$kimaradt_vadasz_egyenertek+mysql2num('select sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.hajo_id=h.id and h.vadasz=0 and h.id not in ('.HAJO_TIPUS_ANYA.','.HAJO_TIPUS_FULGUR.') and fh.flotta_id='.$melyik);
		if ($cipelendo_egyenertek<=$anyahajo_egyenertek) {//minden befert
			$anyahajok_terhelese = $cipelendo_egyenertek/$anyahajo_egyenertek;
			$sebesseg = 40-10*$anyahajok_terhelese;
		} else {//van ami kimaradt
			$szabad_hely=$anyahajo_egyenertek;
			$aux_vadasz_egyenertek=0;$volt_mar_kimaradt_vadasz=false;
			$r=mysql_query('select h.sebesseg,sum(fh.ossz_hp*h.ar),h.vadasz from flotta_hajo fh, hajok h where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and h.sebesseg<80 and fh.ossz_hp>0 group by h.sebesseg');
			unset($leglassabb_ami_kimarad);
			while($aux=mysql_fetch_array($r)) {
				if ($aux[2]==1) {//vadasz
					$aux_vadasz_egyenertek+=$aux[1];
					if ($aux_vadasz_egyenertek>$nemvadasz_egyenertek) {
						if (!$volt_mar_kimaradt_vadasz) $aux[1]=$aux_vadasz_egyenertek-$nemvadasz_egyenertek;
						$szabad_hely-=$aux[1];
						if (!isset($leglassabb_ami_kimarad)) if ($szabad_hely<0) $leglassabb_ami_kimarad=$aux[0];
						$volt_mar_kimaradt_vadasz=true;
					}
				} else {//nem vadasz
					$szabad_hely-=$aux[1];
					if (!isset($leglassabb_ami_kimarad)) if ($szabad_hely<0) $leglassabb_ami_kimarad=$aux[0];
				}
			}
			$sebesseg = $leglassabb_ami_kimarad/2;
		}
	} else {//nincs anyahajo
		//kimaradt vadaszok
		$szabad_hely=$nemvadasz_egyenertek;
		$r=mysql_query('select h.sebesseg,sum(fh.ossz_hp*h.ar) from flotta_hajo fh, hajok h where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and h.vadasz=1 and fh.ossz_hp>0 group by h.sebesseg');
		unset($leglassabb_ami_kimarad);
		while($aux=mysql_fetch_array($r)) {
			$szabad_hely-=$aux[1];
			if (!isset($leglassabb_ami_kimarad)) if ($szabad_hely<0) $leglassabb_ami_kimarad=$aux[0];
		}
		$sebesseg = $leglassabb_ami_kimarad/2;
		$sebesseg_nemvadasz = mysql2num('select min(if(fh.ossz_hp>0,h.sebesseg,1000)) from flotta_hajo fh, hajok h where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and h.vadasz=0')/2;
		if ($sebesseg_nemvadasz<$sebesseg) $sebesseg=$sebesseg_nemvadasz;
	}
}

//fejvadasz bonusz
flotta_fejvadasz_frissites($melyik);
$flotta=mysql2row('select * from flottak where id='.$melyik);
if ($flotta['fejvadasz_bonusz']>0) {
	if ($sebesseg<40) {
		$sebesseg+=4;if ($sebesseg>40) $sebesseg=40;
	}
	if ($flotta['rejtozes']<270) {
		mysql_query('update flottak set rejtozes=least(rejtozes+50,270) where id='.$melyik);
	}
}

//a sebesseg felparsecben van tarolva
mysql_query('update flottak set sebesseg=round('.$sebesseg.')*2 where id='.$melyik);


}


function flotta_reszflotta_frissites($fid) {
	mysql_query('delete from resz_flotta_hajo where flotta_id='.$fid.' and hp=0');
	//
	mysql_query('update resz_flotta_hajo rfh, flotta_hajo fh
set rfh.ossz_hp=fh.ossz_hp
where rfh.flotta_id='.$fid.' and fh.flotta_id='.$fid.' and rfh.hajo_id=fh.hajo_id');
	//
	mysql_query('update resz_flotta_hajo rfh, (
select rfh1.hajo_id,rfh1.user_id,round(rfh1.hp/sum(rfh2.hp)*rfh1.ossz_hp) as uj_hp
from resz_flotta_hajo rfh1, resz_flotta_hajo rfh2
where rfh1.flotta_id='.$fid.' and rfh2.flotta_id='.$fid.' and rfh1.hajo_id=rfh2.hajo_id
group by rfh1.hajo_id,rfh1.user_id
) t
set rfh.hp=t.uj_hp
where rfh.flotta_id='.$fid.' and rfh.hajo_id=t.hajo_id and rfh.user_id=t.user_id');
	//
	mysql_query('delete from resz_flotta_hajo where flotta_id='.$fid.' and hp=0');
	//
	//ha a flottanak kizarolag egy resztulaja van, akkor a resztulajsagot felszamolni es a tulajt arra beallitani
	$resztulajok_szama=mysql2num('select count(distinct user_id) from resz_flotta_hajo where flotta_id='.$fid);
	if ($resztulajok_szama==1) {
		$valodi_tulaj=mysql2num('select user_id from resz_flotta_hajo where flotta_id='.$fid.' limit 1');
		if ($valodi_tulaj>0) {
			$valodi_tulaj_szov=mysql2num('select tulaj_szov from userek where id='.$valodi_tulaj);
			mysql_query('update flottak set tulaj='.$valodi_tulaj.', tulaj_szov='.$valodi_tulaj_szov.' where id='.$fid);
			mysql_query('delete from resz_flotta_hajo where flotta_id='.$fid);
		}
	}
}


function flotta_torles($melyik) {
	mysql_query('delete from resz_flotta_hajo where flotta_id='.$melyik) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('delete from flotta_hajo where flotta_id='.$melyik) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('delete from flottak where id='.$melyik) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('update flottak set statusz='.STATUSZ_ALL.',cel_flotta=0 where cel_flotta='.$melyik.' and (statusz='.STATUSZ_MEGY_FLOTTAHOZ.' or statusz='.STATUSZ_TAMAD_FLOTTARA.' or statusz='.STATUSZ_TAMAD_FLOTTAT.')') or hiba(__FILE__,__LINE__,mysql_error());
}
/******************************************************** HABORU VEGE ******************************************************************/

function get_badgek_szama($idk) {
	foreach($idk as $id) $b[$id]=array(0,0,0,0);
	$r=mysql_query('select badge_id,count(1),coalesce(sum(szin=1),0),coalesce(sum(szin=2),0),coalesce(sum(szin=3),0) from user_badge where badge_id in ('.implode(',',$idk).') group by badge_id');
	while($aux=mysql_fetch_array($r)) $b[$aux[0]]=array($aux[1],$aux[2],$aux[3],$aux[4]);
	return $b;
}
function get_badgek_case_str($idk) {
	$badgek_szama=get_badgek_szama($idk);
	$case_str='';
	foreach($badgek_szama as $id=>$szam) $case_str.="when $id then ".($szam[0]==0?'1':((($szam[1]>0)and($szam[2]<9)and($szam[3]==0))?'2':'3'))."\n";
	return $case_str;
}
function badge_adasa($user_id,$badge_id) {
	$aux=mysql2row('select * from user_badge where user_id='.$user_id.' and badge_id='.$badge_id);
	if ($aux) return false;
	$aux=mysql2row('select count(1),coalesce(sum(szin=1),0),coalesce(sum(szin=2),0),coalesce(sum(szin=3),0) from user_badge where badge_id='.$badge_id);
	if ($aux[0]==0) $szin=1;
	elseif (($szam[1]>0) and ($szam[2]<9) and ($szam[3]==0)) $szin=2;
	else $szin=3;
	mysql_query('insert ignore into user_badge (user_id,badge_id,szin) values('.$user_id.','.$badge_id.','.$szin.')');
	return true;
}




define('FELADAT_GYAR_EPIT',1);
define('FELADAT_GYAR_LEROMBOL',2);

define('EROFORRAS_TIPUS_FAJ',1);
define('EROFORRAS_TIPUS_EROFORRAS',2);
define('EROFORRAS_TIPUS_URHAJO',3);
define('EROFORRAS_TIPUS_SPECKO',4);
define('EROFORRAS_TIPUS_REJTETT',5);

define('CIMZETT_TIPUS_USER',1);
define('CIMZETT_TIPUS_CSOPORT',2);
define('CIMZETT_TIPUS_SZOVETSEG',3);
define('CIMZETT_TIPUS_MINDENKI',4);

define('LAKOHELY_ID',55);
define('KAJA_ID',56);
define('MUNKAERO_ID',57);
define('KEPZETT_MUNKAERO_ID',58);
define('KEPZETT_MUNKAHELY_ID',77);

define('IPARAG_EROMUVEK',1);
define('IPARAG_ELELMISZER',2);
define('IPARAG_KITERMELES',3);
define('IPARAG_FELDOLGOZO',4);
define('IPARAG_HAZTARTASOK',5);
define('IPARAG_SPECKO',6);
define('IPARAG_HADIIPAR',7);
//define('IPARAG_',1);

define('HAJO_TIPUS_SZONDA',206);
define('HAJO_TIPUS_KOORDI',212);
define('HAJO_TIPUS_OHS',218);
define('HAJO_TIPUS_ANYA',216);
define('HAJO_TIPUS_FULGUR',210);
define('HAJO_TIPUS_AKNA',222);
define('HAJO_TIPUS_CASTOR',223);
define('HAJO_TIPUS_POLLUX',224);

define('STATUSZ_ALLOMAS',1);
define('STATUSZ_ALL',2);
define('STATUSZ_PATROL_1',3);
define('STATUSZ_PATROL_2',4);//idaig ne szamozd at, mert itt-ott be van drotozva
define('STATUSZ_MEGY_XY',5);
define('STATUSZ_MEGY_BOLYGO',6);
define('STATUSZ_TAMAD_BOLYGORA',7);
define('STATUSZ_TAMAD_BOLYGOT',8);
define('STATUSZ_RAID_BOLYGORA',9);
define('STATUSZ_RAID_BOLYGOT',10);
define('STATUSZ_VISSZA',11);
define('STATUSZ_MEGY_FLOTTAHOZ',12);
define('STATUSZ_TAMAD_FLOTTARA',13);
define('STATUSZ_TAMAD_FLOTTAT',14);

define('DIPLO_HADI',1);
define('DIPLO_TESTVER',2);//egykori DIPLO_BEKE
define('DIPLO_MNT',3);
$diplo_ajanlatok[0]='Tűzszüneti';
$diplo_ajanlatok[2]='Testvérszövetségi';
$diplo_ajanlatok[3]='Megnemtámadási';
$diplo_ajanlatok_en[0]='Cease-fire';
$diplo_ajanlatok_en[2]='Brotherhood';
$diplo_ajanlatok_en[3]='Non-aggression pact';

define('BOLYGOK_KOZTI_TAVOLSAG',125);//valszeg be van drotozva (hexak merete)

define('BOLYGO_MORAL_NOVELES_FOGLALASKOR',20);

define('BOLYGO_MORAL_CSOKKENES',20);
define('FLOTTA_MORAL_NOVEKEDES',10);


//oraatallitas hack
if (date('Y-m-d')=='2012-10-28' and date('H')<=5) {
	define('MORATORIUM_HOSSZA',10800);//3 ora
} else {
	define('MORATORIUM_HOSSZA',3600);//1 ora
}
//define('MORATORIUM_HOSSZA',3600);//1 ora


define('MULTI_LIMIT',1000);

define('BOLYGOATADAS_IDEJE',86400);//24 ora




$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben=array(0.00,0.05,0.15,0.30,0.60,1.00);
$maximalis_fosztas_tablazat_a_vedelmi_pont_fuggvenyeben=array(0.30,0.25,0.20,0.15,0.10,0.00);

$mernok_8_oras_gyarai=array(13,30,31,21,22);//fúziós erőmű, város, kutatóintézet, titánmű, urándúsító

/*
JOGOK:

2 meghív
3 kirúg
4 kinevez
5 közös
7 diplo
6 vendég
8 újtéma
9 mod
1 belső fórum
10 radar
11 nagy radar
12 kém
*/



//
//mysql_query('insert into '.$database_mmog_nemlog.'.php_debug_log (szkript,hasznalt_memoria,hasznalt_memoria_true,futasi_ido) values("*csatlak.php/stop",'.memory_get_peak_usage().','.memory_get_peak_usage(true).','.round(1000*(microtime(true)-$szkript_mikor_indul)).')');
//
