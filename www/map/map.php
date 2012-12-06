<?
include('../csatlak.php');$font_cim='../img/arial.ttf';
header('Cache-Control: no-cache');
header('Expires: -1');

$_REQUEST['regb']=(int)$_REQUEST['regb'];if ($_REQUEST['regb']!==1) $_REQUEST['regb']=0;//csak regelheto bolygok


function imagebigellipse($kep,$x,$y,$w,$h,$szin) {
	if (max($w,$h)==0) return false;
	$kvazi_kerulet=max($w,$h)*M_PI;
	$felosztas=360/$kvazi_kerulet;
	for($alfa=0;$alfa<360;$alfa+=$felosztas) imageline($kep,
	$x+$w/2*cos($alfa/180*M_PI),$y+$h/2*sin($alfa/180*M_PI),
	$x+$w/2*cos(($alfa+$felosztas)/180*M_PI),$y+$h/2*sin(($alfa+$felosztas)/180*M_PI),
	$szin);
	return true;
}

$eltolas_mennyiseg=20000;
$zoom=1;$kis_meret=1;$nagy_meret=3;$eltolas_x=0;$eltolas_y=0;
if (isset($_REQUEST['zoom'])) {
	$zoom=2;$kis_meret=1;$nagy_meret=5;
	switch($_REQUEST['zoom']) {
		case 2:
			$eltolas_x=-$eltolas_mennyiseg;$eltolas_y=-$eltolas_mennyiseg;
		break;
		case 3:
			$eltolas_x=$eltolas_mennyiseg;$eltolas_y=-$eltolas_mennyiseg;
		break;
		case 4:
			$eltolas_x=-$eltolas_mennyiseg;$eltolas_y=$eltolas_mennyiseg;
		break;
		case 5:
			$eltolas_x=$eltolas_mennyiseg;$eltolas_y=$eltolas_mennyiseg;
		break;
	}
}


if (isset($_REQUEST['ofs_x'])) {
	$zoom=8;$kis_meret=3;$nagy_meret=7;
	$eltolas_x=(int)$_REQUEST['ofs_x'];
	$eltolas_y=(int)$_REQUEST['ofs_y'];
}


$nagy_meretek=array(null,null,6,9,12,16);


$meret=900;
$skala=200/$zoom;
$felmeret=round($meret/2);
$kep=imagecreatetruecolor($meret,$meret);
$hatar=80000/$zoom;

$nagyon_sotet_szurke=imagecolorallocate($kep,30,30,30);
$sotet_szurke=imagecolorallocate($kep,50,50,50);
$szurke=imagecolorallocate($kep,160,160,160);
$zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);
$feher=imagecolorallocate($kep,255,255,255);
$piros=imagecolorallocate($kep,255,0,0);
$bolygo_szin[1]=imagecolorallocate($kep,83,154,148);
$bolygo_szin[2]=imagecolorallocate($kep,236,164,62);
$bolygo_szin[3]=imagecolorallocate($kep,196,199,110);
$bolygo_szin[4]=imagecolorallocate($kep,70,97,56);
$bolygo_szin[5]=imagecolorallocate($kep,225,234,241);
for($i=1;$i<=5;$i++) {
	$x=imagecolorsforindex($kep,$bolygo_szin[$i]);$x_atl=($x['red']+$x['green']+$x['blue'])/3;
	$foglalt_bolygo_szin[$i]=imagecolorallocate($kep,($x['red']+$x_atl)/4,($x['green']+$x_atl)/4,($x['blue']+$x_atl)/4);
	$nemregelheto_bolygo_szin[$i]=imagecolorallocatealpha($kep,($x['red']+$x_atl)/4,($x['green']+$x_atl)/4,($x['blue']+$x_atl)/4,100);
}

$regio_szinek[1]=imagecolorallocate($kep,255,20,40);
$regio_szinek[2]=imagecolorallocate($kep,247,91,51);
$regio_szinek[3]=imagecolorallocate($kep,255,180,0);
$regio_szinek[4]=imagecolorallocate($kep,255,255,0);
$regio_szinek[5]=imagecolorallocate($kep,0,255,0);
$regio_szinek[6]=imagecolorallocate($kep,0,255,200);
$regio_szinek[7]=imagecolorallocate($kep,20,160,255);
$regio_szinek[8]=imagecolorallocate($kep,40,80,255);
$regio_szinek[9]=imagecolorallocate($kep,160,30,255);
$regio_szinek[10]=imagecolorallocate($kep,255,64,128);
$regio_szinek[11]=imagecolorallocate($kep,255,128,192);



if ($_REQUEST['tobbi']==1) imagefill($kep,0,0,$feher);


function imagefillhexa($hexa_x,$hexa_y,&$kep,$szin,$korvonal_szin=null) {
	global $felmeret,$skala,$eltolas_x,$eltolas_y;
	$x=$hexa_x*round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3));
	$y=$hexa_y*BOLYGOK_KOZTI_TAVOLSAG*2-(($hexa_x%2==0)?0:BOLYGOK_KOZTI_TAVOLSAG);
	for($alfa=0;$alfa<360;$alfa+=60) {
		$csucsok[]=$felmeret+($x+(BOLYGOK_KOZTI_TAVOLSAG+$skala)*cos($alfa/180*M_PI)-2*$eltolas_x)/$skala;
		$csucsok[]=$felmeret+($y+(BOLYGOK_KOZTI_TAVOLSAG+$skala)*sin($alfa/180*M_PI)-2*$eltolas_y)/$skala;
	}
	imagefilledpolygon($kep,$csucsok,6,$szin);
	if (!is_null($korvonal_szin)) imagepolygon($kep,$csucsok,6,$korvonal_szin);
}

if ($_REQUEST['all']==1) {//szovik hexaja



//	$er=mysql_query('select sz.id,sz.nev,count(1) from bolygok b, szovetsegek sz where b.tulaj_szov=sz.id and sz.id not in ('.implode(',',$specko_szovetsegek_listaja).') group by sz.id order by count(1) desc,sz.id limit 10');
$er=mysql_query('select sz.id,sz.nev,sum(u.bolygo_szam) as darab
from szovetsegek sz, (
select u.szovetseg,u.pontszam_exp_atlag,count(1) as bolygo_szam,premium,premium_alap from userek u, bolygok b where b.tulaj=u.id and b.letezik=1 group by u.id
) u
where u.szovetseg=sz.id and sz.id not in ('.implode(',',$specko_szovetsegek_listaja).')
group by sz.id
order by sum(u.pontszam_exp_atlag) desc,sz.nev limit 10');
	$str_szovi_lista='';while($aux=mysql_fetch_array($er)) {$szovi_toplista[]=$aux['id'];$szovi_lista[]=$aux;$str_szovi_lista.=','.$aux['id'];}
	$str_szovi_lista=substr($str_szovi_lista,1);

$szovi_szin[$szovi_toplista[0]]=imagecolorallocate($kep,255,20,40);
$szovi_szin[$szovi_toplista[1]]=imagecolorallocate($kep,247,91,51);
$szovi_szin[$szovi_toplista[2]]=imagecolorallocate($kep,255,180,0);
$szovi_szin[$szovi_toplista[3]]=imagecolorallocate($kep,255,255,0);
$szovi_szin[$szovi_toplista[4]]=imagecolorallocate($kep,0,255,0);
$szovi_szin[$szovi_toplista[5]]=imagecolorallocate($kep,0,255,200);
$szovi_szin[$szovi_toplista[6]]=imagecolorallocate($kep,20,160,255);
$szovi_szin[$szovi_toplista[7]]=imagecolorallocate($kep,40,80,255);
$szovi_szin[$szovi_toplista[8]]=imagecolorallocate($kep,160,30,255);
$szovi_szin[$szovi_toplista[9]]=imagecolorallocate($kep,255,64,128);

if ($zoom>1) {
	for($i=0;$i<10;$i++) {
		$x=imagecolorsforindex($kep,$szovi_szin[$szovi_toplista[$i]]);
		$szovi_szin[$szovi_toplista[$i]]=imagecolorallocate($kep,$x['red']/2,$x['green']/2,$x['blue']/2);
	}
}


	$min_hexa_x=floor((2*$eltolas_x-$hatar)/round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3)));
	$max_hexa_x=ceil((2*$eltolas_x+$hatar)/round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3)));
	$min_hexa_y=floor((2*$eltolas_y-$hatar)/BOLYGOK_KOZTI_TAVOLSAG/2);
	$max_hexa_y=ceil((2*$eltolas_y+$hatar)/BOLYGOK_KOZTI_TAVOLSAG/2);
	
	//
	if ($_REQUEST['tobbi']==1) {
		$er=mysql_query("select h.*,b.tulaj_szov,b.milyen_terulet from hexak h, bolygok b
where b.letezik=1 and h.voronoi_bolygo_id=b.id".((strlen($str_szovi_lista)>0)?(" and b.tulaj_szov not in ($str_szovi_lista)"):"")."
and b.tulaj>0
and h.x between $min_hexa_x and $max_hexa_x and h.y between $min_hexa_y and $max_hexa_y");
		while($aux=mysql_fetch_array($er)) {
			$hexa_x=$aux['x'];
			$hexa_y=$aux['y'];
			$x=$hexa_x*round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3));
			$y=$hexa_y*BOLYGOK_KOZTI_TAVOLSAG*2-(($hexa_x%2==0)?0:BOLYGOK_KOZTI_TAVOLSAG);
			if ($x-2*$eltolas_x>=-$hatar)
			if ($x-2*$eltolas_x<=$hatar)
			if ($y-2*$eltolas_y>=-$hatar)
			if ($y-2*$eltolas_y<=$hatar) {
				imagefillhexa($hexa_x,$hexa_y,$kep,$szurke);
			}
		}
	}
	//
	
	
	if ($_REQUEST['mt']==1) {
		$er=mysql_query("select h.*,b.tulaj_szov,b.milyen_terulet from hexak h, bolygok b
where b.letezik=1 and h.voronoi_bolygo_id=b.id".((strlen($str_szovi_lista)>0)?(" and b.milyen_terulet in (-1,$str_szovi_lista)"):"")."
and h.x between $min_hexa_x and $max_hexa_x and h.y between $min_hexa_y and $max_hexa_y");
	} else {
		$er=mysql_query("select h.*,b.tulaj_szov,b.milyen_terulet from hexak h, bolygok b
where b.letezik=1 and h.voronoi_bolygo_id=b.id".((strlen($str_szovi_lista)>0)?(" and b.tulaj_szov in ($str_szovi_lista)"):"")."
and h.x between $min_hexa_x and $max_hexa_x and h.y between $min_hexa_y and $max_hexa_y");
	}
	while($aux=mysql_fetch_array($er)) {
		$hexa_x=$aux['x'];
		$hexa_y=$aux['y'];
		$x=$hexa_x*round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3));
		$y=$hexa_y*BOLYGOK_KOZTI_TAVOLSAG*2-(($hexa_x%2==0)?0:BOLYGOK_KOZTI_TAVOLSAG);
		if ($x-2*$eltolas_x>=-$hatar)
		if ($x-2*$eltolas_x<=$hatar)
		if ($y-2*$eltolas_y>=-$hatar)
		if ($y-2*$eltolas_y<=$hatar) {
			if ($_REQUEST['mt']==1) {
				if ($aux['milyen_terulet']==-1) imagefillhexa($hexa_x,$hexa_y,$kep,$feher);//vegyes
				else imagefillhexa($hexa_x,$hexa_y,$kep,$szovi_szin[$aux['milyen_terulet']]);//vmelyik szovi
			} else imagefillhexa($hexa_x,$hexa_y,$kep,$szovi_szin[$aux['tulaj_szov']]);
		}
	}
	/*
	$er=mysql_query("select distinct voronoi_bolygo_id from hexak where voronoi_bolygo_id>0 and x between $min_hexa_x and $max_hexa_x and y between $min_hexa_y and $max_hexa_y");
	while($aux=mysql_fetch_array($er)) {
		if (!isset($idszin[$aux['voronoi_bolygo_id']])) $idszin[$aux['voronoi_bolygo_id']]=imagecolorallocate($kep,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
	}
	$er=mysql_query("select * from hexak where voronoi_bolygo_id>0 and x between $min_hexa_x and $max_hexa_x and y between $min_hexa_y and $max_hexa_y");
	while($aux=mysql_fetch_array($er)) {
		$hexa_x=$aux['x'];
		$hexa_y=$aux['y'];
		$x=$hexa_x*round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3));
		$y=$hexa_y*BOLYGOK_KOZTI_TAVOLSAG*2-(($hexa_x%2==0)?0:BOLYGOK_KOZTI_TAVOLSAG);
		if ($x-2*$eltolas_x>=-$hatar)
		if ($x-2*$eltolas_x<=$hatar)
		if ($y-2*$eltolas_y>=-$hatar)
		if ($y-2*$eltolas_y<=$hatar) {
			imagefillhexa($hexa_x,$hexa_y,$kep,$idszin[$aux['voronoi_bolygo_id']]);
		}
	}
	*/
	/*$er=mysql_query("select * from hexak where bolygo_id>0 and x between $min_hexa_x and $max_hexa_x and y between $min_hexa_y and $max_hexa_y");
	while($aux=mysql_fetch_array($er)) {
		$hexa_x=$aux['x'];
		$hexa_y=$aux['y'];
		imagefillhexa($hexa_x,$hexa_y,$kep,$piros);
	}*/
}



if ($_REQUEST['noracs']==1) {
} else {
for($x=-$hatar;$x<=$hatar;$x+=10000) {
	imageline($kep,round($felmeret+$x/$skala),round($felmeret-$hatar/$skala),round($felmeret+$x/$skala),round($felmeret+$hatar/$skala),($x==-2*$eltolas_x)?$v_zold:$zold);
	imageline($kep,round($felmeret-$hatar/$skala),round($felmeret+$x/$skala),round($felmeret+$hatar/$skala),round($felmeret+$x/$skala),($x==-2*$eltolas_y)?$v_zold:$zold);
	imagettftext($kep,8,90,round($felmeret+$x/$skala+4),round($felmeret-$hatar/$skala-5),$zold,$font_cim,str_pad($x/2+$eltolas_x,6,' ',STR_PAD_LEFT));
	imagettftext($kep,8,0,round($felmeret-$hatar/$skala-35),round($felmeret+$x/$skala+4),$zold,$font_cim,str_pad($x/2+$eltolas_y,6,' ',STR_PAD_LEFT));
	imagettftext($kep,8,90,round($felmeret+$x/$skala+4),round($felmeret+$hatar/$skala+38),$zold,$font_cim,str_pad($x/2+$eltolas_x,6,' ',STR_PAD_LEFT));
	imagettftext($kep,8,0,round($felmeret+$hatar/$skala+5),round($felmeret+$x/$skala+4),$zold,$font_cim,str_pad($x/2+$eltolas_y,6,' ',STR_PAD_LEFT));
}
}

$er=mysql_query('select x,y,osztaly,terulet,tulaj,alapbol_regisztralhato,random_regisztralhato,hexa_x,hexa_y,moral,pontertek,regio from bolygok where letezik=1 and x between '.(2*$eltolas_x-$hatar).' and '.(2*$eltolas_x+$hatar).' and y between '.(2*$eltolas_y-$hatar).' and '.(2*$eltolas_y+$hatar));
while($aux=mysql_fetch_array($er)) {
	if ($aux['tulaj']==0) $szin=$bolygo_szin[$aux['osztaly']];
	else {
		if ($_REQUEST['foglalt']==1) $szin=$piros;
		else $szin=$foglalt_bolygo_szin[$aux['osztaly']];
	}
	if ($_REQUEST['all']==2) {
		$szin=$regio_szinek[$aux['regio']];
	}
	if ($_REQUEST['regb']) if (!$aux['alapbol_regisztralhato'] || $aux['moral']!=100) {
		$szin=$nemregelheto_bolygo_szin[$aux['osztaly']];
	}
	if ($zoom>=8) {
		if ($aux['terulet']>2000000) {
			$meret=$nagy_meretek[$aux['terulet']/2000000];
			imageellipse($kep,round($felmeret+($aux[0]-2*$eltolas_x)/$skala),round($felmeret+($aux[1]-2*$eltolas_y)/$skala),$meret,$meret,$szin);
		}
		imagefilledellipse($kep,round($felmeret+($aux[0]-2*$eltolas_x)/$skala),round($felmeret+($aux[1]-2*$eltolas_y)/$skala),$kis_meret,$kis_meret,$szin);
		if ($aux['alapbol_regisztralhato'] && $aux['moral']==100) {
			imageline($kep,
			round($felmeret+($aux[0]-2*$eltolas_x)/$skala-2),round($felmeret+($aux[1]-2*$eltolas_y)/$skala-2),
			round($felmeret+($aux[0]-2*$eltolas_x)/$skala+2),round($felmeret+($aux[1]-2*$eltolas_y)/$skala+2),
			$szin);
			imageline($kep,
			round($felmeret+($aux[0]-2*$eltolas_x)/$skala-2),round($felmeret+($aux[1]-2*$eltolas_y)/$skala+2),
			round($felmeret+($aux[0]-2*$eltolas_x)/$skala+2),round($felmeret+($aux[1]-2*$eltolas_y)/$skala-2),
			$szin);
		}
	} else {
		$meret=$kis_meret;if ($aux['terulet']>2000000) $meret=$nagy_meret;
		if ($_REQUEST['foglalt']==1) {
			if ($aux['tulaj']!=0) $meret=$nagy_meret;
			if ($aux['terulet']>2000000) $meret=0;
		}
		if ($_REQUEST['regb']) if ($aux['alapbol_regisztralhato'] && $aux['moral']==100) $meret=$nagy_meret;
		if ($_REQUEST['tobbi']==1) $meret=round(max(log10($aux['pontertek']/$aux['terulet'])-3,0)/2000000*$aux['terulet']);
		imagefilledellipse($kep,round($felmeret+($aux[0]-2*$eltolas_x)/$skala),round($felmeret+($aux[1]-2*$eltolas_y)/$skala),$meret,$meret,$szin);
	}
}




if ($_REQUEST['reg']==1) {
	$x=2*((int)$_REQUEST['reg_x']);
	$y=2*((int)$_REQUEST['reg_y']);
	$meret=30;
	imageellipse($kep,round($felmeret+($x-2*$eltolas_x)/$skala),round($felmeret+($y-2*$eltolas_y)/$skala),$meret,$meret,$piros);
	imageline($kep,
	round($felmeret+($x-2*$eltolas_x)/$skala-20),round($felmeret+($y-2*$eltolas_y)/$skala),
	round($felmeret+($x-2*$eltolas_x)/$skala+20),round($felmeret+($y-2*$eltolas_y)/$skala),
	$piros);
	imageline($kep,
	round($felmeret+($x-2*$eltolas_x)/$skala),round($felmeret+($y-2*$eltolas_y)/$skala+20),
	round($felmeret+($x-2*$eltolas_x)/$skala),round($felmeret+($y-2*$eltolas_y)/$skala-20),
	$piros);
}


mysql_close($mysql_csatlakozas);

header('Content-type: image/png');imagepng($kep);
?>