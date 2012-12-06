<?
include('../csatlak.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);

function unsigned_parity($x) {return ($x>=0)?($x%2):((-$x)%2);}

//1. lépés: galaxis legenerálása
/*
a galaxis könyvtárban található valamelyik generátorral létrehozol egy galaxist
ez egy ilyen strukturájú táblába kerül: (x,y,hexa_x,hexa_y,alapbol_regisztralhato,terulet,osztaly,hold,galaktikus_regio)
végül a blokk0(''); rakja át a bolygok táblába

VAGY

más módon töltöd fel a bolygok táblát (saját generátor)
ezeket a mezőket kell megadni: (id,x,y,hexa_x,hexa_y,alapbol_regisztralhato,terulet,osztaly,hold,regio)
*/


//2. lépés: ezeken sorban végigmenni (a biztonság kedvéért érdemes egyesével):
//blokk1();
//blokk2();//erre csak akkor van szükség, ha nagyon megváltozik a galaxis mérete, és nem fér bele az eddigi hexakörbe
//blokk3();
//blokk4();//Voronoi-cellák generálása, ez nagyon sokáig (talán 1-2 óráig?) fut, itt lehet ellenőrizni, hol tart: select if(voronoi_bolygo_id=0,0,1) as v,count(1) from hexak group by v
//blokk5();
//blokk6();
//blokk7();


function blokk0($prefix) {
	//ZandaNet->celszerver
	global $mysql_csatlakozas;
	mysql_query('truncate bolygok',$mysql_csatlakozas);
	$tavoli_zandanet_csatlak=mysql_connect('HOST','USER','PASSWORD');
	mysql_select_db('mmog',$tavoli_zandanet_csatlak);
	mysql_query('set names "utf8"',$tavoli_zandanet_csatlak);
	$r=mysql_query('select id,x,y,hexa_x,hexa_y,alapbol_regisztralhato,terulet,osztaly,hold,galaktikus_regio from bolygok_'.$prefix.' order by id',$tavoli_zandanet_csatlak);
	while($aux=mysql_fetch_array($r)) {
		$s='';for($i=0;$i<10;$i++) $s.=','.$aux[$i];
		echo 'insert into bolygok (id,x,y,hexa_x,hexa_y,alapbol_regisztralhato,terulet,osztaly,hold,regio) values('.substr($s,1).')'."<br />\n";
		mysql_query('insert into bolygok (id,x,y,hexa_x,hexa_y,alapbol_regisztralhato,terulet,osztaly,hold,regio) values('.substr($s,1).')',$mysql_csatlakozas);
	}
	mysql_close($tavoli_zandanet_csatlak);
}

function blokk1() {
	//alapok
	mysql_query('update bolygok set terulet_szabad=terulet, terulet_beepitett=0, raforditott_kornyezet_kp=0, terraformaltsag=10000');
	mysql_query('update bolygok set bolygo_id_mod=id%15');
	mysql_query('update bolygok set nev=concat("B",id),kulso_nev=concat("B",id)');
	mysql_query('update bolygo_eroforras set bolygo_id_mod=bolygo_id%15');
}



function blokk2() {
	//hexak
	mysql_query('truncate hexak');
	$bkts3=round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3));
	//ha teljes negyzetben akarod
	//for($y=-340;$y<=340;$y++) for($x=-395;$x<=395;$x++) mysql_query("insert ignore into hexak (x,y) values($x,$y)");
	//ha kor alakban akarod
	for($y=-340;$y<=340;$y++) for($x=-400;$x<=400;$x++) if (pow($x*$bkts3/40000,2)+pow(($y*BOLYGOK_KOZTI_TAVOLSAG*2-(($x%2==0)?0:BOLYGOK_KOZTI_TAVOLSAG))/40000,2)<=4.5) mysql_query("insert ignore into hexak (x,y) values($x,$y)");
}





function blokk3() {
//bolygo_id-k beallitasa
mysql_query('update hexak h, bolygok b
set h.bolygo_id=b.id
where h.x=b.hexa_x and h.y=b.hexa_y and b.letezik=1');

//szomszed_bolygo_id-k beallitasa
function set_hexa_szomszed($x,$y,$id) {
	$er=mysql_query("select szomszed_bolygo_id from hexak where x=$x and y=$y");
	$aux=mysql_fetch_array($er);
	mysql_query("update hexak set szomszed_bolygo_id=".(($aux[0]==0)?$id:-1)." where x=$x and y=$y");
}
$er=mysql_query('select id,hexa_x,hexa_y from bolygok where letezik=1');
while($aux=mysql_fetch_array($er)) {
	$hexa_x=$aux['hexa_x'];
	$hexa_y=$aux['hexa_y'];
	set_hexa_szomszed($hexa_x,$hexa_y-1,$aux['id']);
	set_hexa_szomszed($hexa_x,$hexa_y+1,$aux['id']);
	set_hexa_szomszed($hexa_x-1,$hexa_y-2*unsigned_parity($hexa_x)+1,$aux['id']);
	set_hexa_szomszed($hexa_x+1,$hexa_y-2*unsigned_parity($hexa_x)+1,$aux['id']);
	set_hexa_szomszed($hexa_x-1,$hexa_y,$aux['id']);
	set_hexa_szomszed($hexa_x+1,$hexa_y,$aux['id']);
}
}





function blokk4() {
	//voronoi_bolygo_id-k beallitasa
	//reset
	mysql_query('update hexak set voronoi_bolygo_id=0');
	//ez nagyon sokaig fut
	//itt lehet csekkolni, hol tart: select if(voronoi_bolygo_id=0,0,1) as v,count(1) from hexak group by v
	$er=mysql_query('select * from hexak where voronoi_bolygo_id=0');
	while($aux=mysql_fetch_array($er)) {
		$x=$aux['x'];$y=$aux['y'];
		//$er2=mysql_query("select id,pow(($x*round(".BOLYGOK_KOZTI_TAVOLSAG."*sqrt(3)))-(x),2)+pow(($y*".BOLYGOK_KOZTI_TAVOLSAG."*2-if($x%2=0,0,".BOLYGOK_KOZTI_TAVOLSAG."))-y,2) as tav from bolygok order by tav limit 1");
		$er2=mysql_query("select id,pow($x*217-x,2)+pow($y*125*2-if($x%2=0,0,125)-y,2) as tav
from bolygok
where x between $x*217-5000 and $x*217+5000
and y between $y*250-125-5000 and $y*250+5000
order by tav limit 1");
		$bolygo=mysql_fetch_array($er2);
		if ($bolygo['tav']<=25000000) mysql_query("update hexak set voronoi_bolygo_id=".$bolygo['id']." where x=$x and y=$y");
		//ha dontetlen van, akkor ad hoc modon valaszt, ahogy a mysql kidobja
		//ha 2500pc-nel (5000 felpc-nel) nagyobb a tav, akkor az mar sehova nem tartozik
	}
}


function blokk5() {
//segedmezo kitoltese, ami megadja minden bolygonak, h mekkora a felsegterulete
mysql_query('update bolygok b,(
select b.id,count(1) as hsz
from hexak h, bolygok b
where h.voronoi_bolygo_id=b.id
group by b.id) t
set b.hexak_szama=t.hsz
where b.id=t.id');
//minden bolygohoz meghatarozni a voronoi cellaja kozeppontjat (kalozterkephez pl hasznos lehet)
mysql_query('update bolygok b,(select h.voronoi_bolygo_id as id,round(avg(h.x*round(125*sqrt(3)))) as x,round(avg(h.y*125*2-if(h.x%2=0,0,125))) as y from hexak h group by id) t
set b.voronoi_x=t.x, b.voronoi_y=t.y
where b.id=t.id');
}



function blokk6() {
//fog of war-hoz fix tabla (csak akkor kell ujrahuzni, ha valtozik a bolygok max latotavja
mysql_query('truncate hexa_kor');
for($y=-10;$y<=10;$y++)
for($x=-10;$x<=10;$x++) {
	if ($y==0) $H=sqrt(pow($x,2));
	else $H=sqrt(pow($x,2)+pow(abs($y)-1,2));
	$r=floor(125*($H-1));if ($r<0) $r=0;
	if ($r<=1000) mysql_query("insert into hexa_kor (x,y,r) values($x,$y,$r)");
}
//galaxisfüggő fix tábla
mysql_query('truncate hexa_bolygo');
mysql_query('insert into hexa_bolygo (x,y,id)
select b.hexa_x+hk.x,b.hexa_y+hk.y,b.id
from bolygok b, hexa_kor hk');
}



function blokk7() {
//regiok
mysql_query('truncate regiok');
mysql_query('insert into regiok (id,nev,x,y,bolygok_szama,ossz_felszin,szuz)
select regio,concat("R",if(regio<10,"0",""),regio),round(avg(x)),round(avg(y)),count(*),round(sum(terulet)/1000000),1 from bolygok group by regio');
//tozsdei_arfolyamok = technikai_ar
mysql_query('truncate tozsdei_arfolyamok');
mysql_query('insert into tozsdei_arfolyamok (termek_id,arfolyam,ppm_arfolyam,regio)
select e.id,e.technikai_ar,1000000*e.technikai_ar,r.id
from regiok r, eroforrasok e
where e.tozsdezheto=1');
//szabadpiaci_arfolyamok = 0
mysql_query('truncate szabadpiaci_arfolyamok');
mysql_query('insert into szabadpiaci_arfolyamok (termek_id,arfolyam)
select id,0 from eroforrasok where tozsdezheto=1');
}



echo 'kesz';
mysql_close($mysql_csatlakozas);
?>