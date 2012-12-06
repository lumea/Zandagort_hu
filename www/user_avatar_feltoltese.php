<?
include('csatlak.php');
include('ujkuki.php');

function php_kilep($s) {
	kilep('<html><body onload="window.top.window.inline_toggle(\'uj_user_avatar_tolto\');alert(\''.$s.'\');"></body></html>');
}

//if (premium_szint()==0) php_kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

if ($_FILES['kep']['size']==0) php_kilep($lang[$lang_lang]['kisphpk']['Add meg egy kép nevét.']);


$max_meret=32;

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
		move_uploaded_file($_FILES['kep']['tmp_name'],'img/user_avatarok/p'.$uid.$adataim['avatar_crc'].'.'.$ext);
	} else {//atmeret
		$tn=imagecreatetruecolor($tw,$th);
		imagecopyresampled($tn,$kep,0,0,0,0,$tw,$th,$w,$h);
		$cel_fajl='img/user_avatarok/p'.$uid.$adataim['avatar_crc'].'.'.$ext;
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
	mysql_query('update userek set avatar_ext="'.$ext.'" where id='.$uid);
} else php_kilep($lang[$lang_lang]['kisphpk']['Nem sikerült feltölteni a képet.']);

kilep('<html><body onload="window.top.window.frissit_aktiv_oldal()"></body></html>');
?>