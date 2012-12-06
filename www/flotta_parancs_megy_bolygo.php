<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];

$er=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($er);
if (!$flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($flotta['tulaj']!=$uid && $flotta['kezelo']!=$uid && ($flotta['kozos']!=1 || $jogaim[5]!=1 || $flotta['tulaj_szov']!=$adataim['tulaj_szov'])) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

if ($_REQUEST['bolygo_id']) {
	$er=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
} else {
	$er=mysql_query('select * from bolygok where concat(kulso_nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['nev']).'" and letezik=1');
	$aux=mysql_fetch_array($er);
	if ($aux) {
		$x=$aux['x'];
		$y=$aux['y'];
	} else {
		$koord=explode(',',$_REQUEST['nev']);
		$y_str=trim($koord[0]);
		$x_str=trim($koord[1]);
		if ($lang_lang=='en') {
			$k=mb_substr($y_str,0,1,'UTF-8');$y_num=(int)strtr(trim(mb_substr($y_str,1,1000,'UTF-8')),array(' '=>''));
			if ($k=='N' || $k=='n') $y=-2*$y_num;
			elseif ($k=='S' || $k=='s') $y=2*$y_num;
			else $y=2*((int)$y_str);
			$k=mb_substr($x_str,0,1,'UTF-8');$x_num=(int)strtr(trim(mb_substr($x_str,1,1000,'UTF-8')),array(' '=>''));
			if ($k=='W' || $k=='w') $x=-2*$x_num;
			elseif ($k=='E' || $k=='e') $x=2*$x_num;
			else $x=2*((int)$x_str);
		} else {
			$k=mb_substr($y_str,0,1,'UTF-8');$y_num=(int)strtr(trim(mb_substr($y_str,1,1000,'UTF-8')),array(' '=>''));
			if ($k=='É' || $k=='E' || $k=='é' || $k=='e') $y=-2*$y_num;
			elseif ($k=='D' || $k=='d') $y=2*$y_num;
			else $y=2*((int)$y_str);
			$k=mb_substr($x_str,0,1,'UTF-8');$x_num=(int)strtr(trim(mb_substr($x_str,1,1000,'UTF-8')),array(' '=>''));
			if ($k=='N' || $k=='n') $x=-2*(int)strtr(trim(mb_substr($x_str,2,1000,'UTF-8')),array(' '=>''));
			elseif ($k=='K' || $k=='k') $x=2*$x_num;
			else $x=2*((int)$x_str);
/*
			if (strpos($y_str,' ')!==false) {
				$y_num=(int)strtr(substr($y_str,strpos($y_str,' ')),array(' '=>''));
				$k=mb_substr($y_str,0,1,'UTF-8');
				if ($k=='É' || $k=='E' || $k=='é' || $k=='e') $y=-2*$y_num;
				else $y=2*$y_num;
			} else {
				$y=2*((int)$y_str);
			}
			if (strpos($x_str,' ')!==false) {
				$x_num=(int)strtr(substr($x_str,strpos($x_str,' ')),array(' '=>''));
				if (strtoupper(substr($x_str,0,2))=='NY') $x=-2*$x_num;
				else $x=2*$x_num;
			} else {
				$x=2*((int)$x_str);
			}
*/
		}
	}
}

if ($aux) {
	if (($aux['x']==$flotta['x'])&&($aux['y']==$flotta['y'])) {//mar ott van
		$dsz=(int)mysql2num('select mi from diplomacia_statuszok where ki='.$aux['tulaj_szov'].' and kivel='.$flotta['tulaj_szov']);
		if ($flotta['tulaj_szov']==$aux['tulaj_szov'] || $dsz==DIPLO_TESTVER) {
			mysql_query('update flottak set bolygo='.$aux['id'].',statusz='.STATUSZ_ALLOMAS.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
		} else {
			mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_ALL.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
		}
	} else {
		//ha idegen bolygo ellen mesz, akkor atrak megy_xy-ra
		$idegen=false;
		if ($flotta['tulaj_szov']!=$aux['tulaj_szov']) {
			$diplo=(int)mysql2num('select coalesce(mi,0) from diplomacia_statuszok where ki='.$flotta['tulaj_szov'].' and kivel='.$aux['tulaj_szov']);
			if ($diplo!=DIPLO_TESTVER) $idegen=true;
		}
		if ($idegen) {
			mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_MEGY_XY.',cel_x='.$aux['x'].',cel_y='.$aux['y'].' where id='.$_REQUEST['flotta_id']);
		} else {
			mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_MEGY_BOLYGO.',cel_bolygo='.$aux['id'].' where id='.$_REQUEST['flotta_id']);
		}
	}
} else {
	mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_MEGY_XY.',cel_x='.$x.',cel_y='.$y.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
}

mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['flotta_id']);
if (flotta_fejvadasz_frissites($_REQUEST['flotta_id'])) flotta_minden_frissites($_REQUEST['flotta_id']);

kilep();
?>