<?
include('csatlak.php');
include('ujkuki.php');

function php_kilep($s) {
	kilep('<html><body onload="window.top.window.inline_toggle(\'uj_cimer_tolto\');alert(\''.$s.'\');"></body></html>');
}


if ($adataim['szovetseg']==0) php_kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res=mysql_query('select * from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$szovetseg=mysql_fetch_array($res);

if ($szovetseg['alapito']!=$uid) php_kilep($lang[$lang_lang]['kisphpk']['Nem te vagy az alapító.']);

if ($_FILES['kep']['size']==0) php_kilep($lang[$lang_lang]['kisphpk']['Add meg egy kép nevét.']);


$max_meret=320;

if (is_uploaded_file($_FILES['kep']['tmp_name'])) {
	$kep_meret_tipus=getimagesize($_FILES['kep']['tmp_name']);
	$w=$kep_meret_tipus[0];$h=$kep_meret_tipus[1];
	switch($kep_meret_tipus[2]) {
		case IMAGETYPE_JPEG:
			$kep=imagecreatefromjpeg($_FILES['kep']['tmp_name']);
			$ext='jpg';
		break;
		case IMAGETYPE_PNG:
			$kep=imagecreatefrompng($_FILES['kep']['tmp_name']);
			$ext='png';
		break;
		case IMAGETYPE_GIF:
			$kep=imagecreatefromgif($_FILES['kep']['tmp_name']);
			$ext='gif';
		break;
		default:
			php_kilep($lang[$lang_lang]['kisphpk']['JPEG, PNG vagy GIF formátumban tölthetsz csak fel képet.']);
	}
	$orig_meret=0;
	if ($w>$h) {
		if ($w<=$max_meret) {$tw=$w;$th=$h;$orig_meret=1;} else {$tw=$max_meret;$th=round($max_meret*$h/$w);}
	} else {
		if ($h<=$max_meret) {$tw=$w;$th=$h;$orig_meret=1;} else {$th=$max_meret;$tw=round($max_meret*$w/$h);}
	}
	if ($orig_meret) {//eredeti meret
		move_uploaded_file($_FILES['kep']['tmp_name'],'img/cimerek/p'.$szovetseg['id'].$szovetseg['cimer_crc'].'.'.$ext);
	} else {//atmeret
		$tn=imagecreatetruecolor($tw,$th);
		imagecopyresampled($tn,$kep,0,0,0,0,$tw,$th,$w,$h);
		$cel_fajl='img/cimerek/p'.$szovetseg['id'].$szovetseg['cimer_crc'].'.'.$ext;
		switch($ext) {
			case 'jpg':
				imagejpeg($tn,$cel_fajl);
			break;
			case 'png':
				imagepng($tn,$cel_fajl);
			break;
			case 'gif':
				imagegif($tn,$cel_fajl);
			break;
		}
	}
	mysql_query('update szovetsegek set cimer_ext="'.$ext.'",kepfajl_random="'.randomgen(32).'" where id='.$szovetseg['id']);
} else php_kilep($lang[$lang_lang]['kisphpk']['Nem sikerült feltölteni a képet.']);

kilep('<html><body onload="window.top.window.frissit_aktiv_oldal()"></body></html>');
?>