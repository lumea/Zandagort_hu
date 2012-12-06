<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$ucs=mysql2row('select * from ugynokcsoportok where tulaj='.$uid.' and id='.sanitint($_REQUEST['id']));
if (!$ucs) kilep();

$idotartam=(int)$_REQUEST['idotartam'];if ($idotartam<0) $idotartam=0;
$koltopenz=sanitint($_REQUEST['koltopenz']);if ($koltopenz<0) $koltopenz=0;

$cel_bolygo_id=(int)mysql2num('select id from bolygok where concat(kulso_nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['uj_bolygo']).'" and letezik=1 limit 1');

$statusz=(int)substr($_REQUEST['tevekenyseg'],0,1);
if ($statusz<0) $statusz=0;
if ($statusz>4) $statusz=4;
switch($statusz) {
	case 3://kem
		$reszletek=explode('_',$_REQUEST['tevekenyseg']);
		if (is_array($reszletek)) {
			$feladat_domen=(int)$reszletek[1];
			$feladat_id=(int)$reszletek[2];
			switch($feladat_domen) {
				case 1:
				case 2:
				break;
				default:
					$feladat_domen=0;
					$feladat_id=0;
			}
		} else {
			$feladat_domen=0;
			$feladat_id=0;
		}
	break;
	case 4://szabotor
		$reszletek=explode('_',$_REQUEST['tevekenyseg']);
		if (is_array($reszletek)) {
			$feladat_domen=(int)$reszletek[1];
			$feladat_id=(int)$reszletek[2];
			switch($feladat_domen) {
				case 1:
				break;
				default:
					$feladat_domen=0;
					$feladat_id=0;
			}
		} else {
			$feladat_domen=0;
			$feladat_id=0;
		}
	break;
	default:
		$feladat_domen=0;
		$feladat_id=0;
}

if ($cel_bolygo_id>0) {
	$mostani_x=sanitint($ucs['x']);
	$mostani_y=sanitint($ucs['y']);
	if ($ucs['bolygo_id']>0) {
		$mostani_bolygo=mysql2row('select x,y from bolygok where id='.$ucs['bolygo_id']);
		$mostani_x=sanitint($mostani_bolygo['x']);
		$mostani_y=sanitint($mostani_bolygo['y']);
	}
	mysql_query("update ugynokcsoportok set statusz=$statusz,feladat_domen=$feladat_domen,feladat_id=$feladat_id,hanyszor=$idotartam,shy_per_akcio=$koltopenz,cel_bolygo_id=$cel_bolygo_id,bolygo_id=0,x=$mostani_x,y=$mostani_y where id=".$ucs['id']);
} else {
	mysql_query("update ugynokcsoportok set statusz=$statusz,feladat_domen=$feladat_domen,feladat_id=$feladat_id,hanyszor=$idotartam,shy_per_akcio=$koltopenz where id=".$ucs['id']);
}

kilep();
?>