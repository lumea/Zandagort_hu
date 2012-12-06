<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

function mysql_swap_rows_in_table($table_name,$id_name,$id1,$id2,$skip_id_list=null) {
	$r=mysql_query("select * from $table_name where $id_name=$id1");
	unset($rows1);while ($aux=mysql_fetch_assoc($r)) $rows1[]=$aux;
	$r=mysql_query("select * from $table_name where $id_name=$id2");
	unset($rows2);while ($aux=mysql_fetch_assoc($r)) $rows2[]=$aux;
	//
	$fields=mysql_num_fields($r);
	for ($i=0; $i<$fields;$i++) {
		$ftype[mysql_field_name($r,$i)]=mysql_field_type($r,$i);
	}
	//
	mysql_query("delete from $table_name where $id_name in ($id1,$id2)");
	//
	if (is_array($rows1)) foreach($rows1 as $row) {
		$s1='';$s2='';
		foreach($row as $key=>$value) if (is_null($skip_id_list) || !in_array($key,$skip_id_list)) {
			if ($s1!='') $s1.=',';
			$s1.=$key;
			if ($s2!='') $s2.=',';
			if ($key==$id_name) $value=$id2;
			if ($key=='bolygo_id_mod') $value=$id2%15;
			if ($ftype[$key]=='string' || $ftype[$key]=='datetime' || $ftype[$key]=='date' || $ftype[$key]=='time') $s2.='"'.sanitstr($value).'"';else $s2.=sanitint($value);
		}
		mysql_query("insert into $table_name ($s1) values($s2)");
	}
	if (is_array($rows2)) foreach($rows2 as $row) {
		$s1='';$s2='';
		foreach($row as $key=>$value) {
			if ($s1!='') $s1.=',';
			$s1.=$key;
			if ($s2!='') $s2.=',';
			if ($key==$id_name) $value=$id1;
			if ($key=='bolygo_id_mod') $value=$id1%15;
			if ($ftype[$key]=='string' || $ftype[$key]=='datetime' || $ftype[$key]=='date' || $ftype[$key]=='time') $s2.='"'.sanitstr($value).'"';else $s2.=sanitint($value);
		}
		mysql_query("insert into $table_name ($s1) values($s2)");
	}
}


$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']);
$cel_bolygo=mysql_fetch_array($er2);

//tech 4-ig akarhanyszor (teleport elottig!!!)
if ($adataim['techszint']>3) kilep();
//
$bolygoim_szama=mysql2num('select count(1) from bolygok where tulaj='.$uid);
if ($bolygoim_szama!=1) kilep();
//
$er2=mysql_query('select * from bolygok where tulaj='.$uid);
$forras_bolygo=mysql_fetch_array($er2);
//
if ($cel_bolygo['alapbol_regisztralhato']!=1) kilep();

if ($cel_bolygo['letezik']!=1) kilep();
if ($cel_bolygo['tulaj']!=0) kilep();
if ($cel_bolygo['moral']!=100) kilep();

if ($cel_bolygo['osztaly']!=$forras_bolygo['osztaly']) kilep();
if ($cel_bolygo['terulet']!=$forras_bolygo['terulet']) kilep();
if ($cel_bolygo['hold']!=$forras_bolygo['hold']) kilep();

//bolygok (cel_bolygo tulaj,tulaj_szov)
mysql_query('lock tables bolygok bw write, bolygok br read');
$cel_tulaj=mysql2num('select tulaj from bolygok br where br.id='.$cel_bolygo['id']);
if ($cel_tulaj==0) {
	mysql_query('update bolygok bw set bw.tulaj='.$uid.',bw.tulaj_szov='.$adataim['tulaj_szov'].' where bw.id='.$cel_bolygo['id']);
}
mysql_query('unlock tables');
if ($cel_tulaj!=0) kilep();

//most mar biztosan koltozik
mysql_query('update userek set koltozesek_szama=koltozesek_szama+1 where id='.$uid);

//bolygok (egyeb)
$swap_fields=array('nev','kulso_nev','terulet_szabad','terulet_beepitett','terulet_beepitett_effektiv','raforditott_kornyezet_kp','terraformaltsag','latotav','moral','vedelem','anyabolygo'
,'kocsmaszazalek','kezelo','allomasozo_flottak_szama','moratorium_mikor_jar_le','fobolygo','kalozcelpont','uccso_foglalas_mikor','pontertek','aux_pontertek','vedelmi_bonusz'
,'keszlet_ttertek','keszlet_pontertek','foglalasi_sorszam');
$swap_field_types=array('string','string','int','int','int','int','int','int','int','int','int'
,'int','int','int','string','int','int','string','int','int','int'
,'int','int','int');
$forras_bolygo_ertekek='';
$cel_bolygo_ertekek='';
foreach($swap_fields as $i=>$swap_field) {
	if ($forras_bolygo_ertekek!='') $forras_bolygo_ertekek.=',';
	if ($swap_field_types[$i]=='string') $forras_bolygo_ertekek.=$swap_field.'="'.sanitstr($forras_bolygo[$swap_field]).'"';else $forras_bolygo_ertekek.=$swap_field.'='.sanitint($forras_bolygo[$swap_field]);
	if ($cel_bolygo_ertekek!='') $cel_bolygo_ertekek.=',';
	if ($swap_field_types[$i]=='string') $cel_bolygo_ertekek.=$swap_field.'="'.sanitstr($cel_bolygo[$swap_field]).'"';else $cel_bolygo_ertekek.=$swap_field.'='.sanitint($cel_bolygo[$swap_field]);
}
mysql_query("update bolygok set $forras_bolygo_ertekek where id=".$cel_bolygo['id']);
mysql_query("update bolygok set $cel_bolygo_ertekek where id=".$forras_bolygo['id']);
mysql_query('update bolygok set nev="B'.$forras_bolygo['id'].'",kulso_nev="B'.$forras_bolygo['id'].'" where id='.$forras_bolygo['id']);
//fantom uj bolygojanak a kulso_nev-e legyen B+id (ne pedig a regi bolygorol athozva)
if ($adataim['karrier']==3 and $adataim['speci']==3) {
	mysql_query('update bolygok set kulso_nev="B'.$cel_bolygo['id'].'" where id='.$cel_bolygo['id']);
}

//bolygo_ember
mysql_query('update bolygo_ember t1, bolygo_ember t2
set t1.pop=t2.pop
where t1.bolygo_id in ('.$forras_bolygo['id'].','.$cel_bolygo['id'].')
and t2.bolygo_id in ('.$forras_bolygo['id'].','.$cel_bolygo['id'].')
and t1.bolygo_id!=t2.bolygo_id');

//bolygo_eroforras
mysql_swap_rows_in_table('bolygo_eroforras','bolygo_id',$forras_bolygo['id'],$cel_bolygo['id']);

//bolygo_gyar
mysql_swap_rows_in_table('bolygo_gyar','bolygo_id',$forras_bolygo['id'],$cel_bolygo['id']);

//bolygo_gyar_eroforras
mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$forras_bolygo['id']);
mysql_query('
insert into bolygo_gyar_eroforras
select bgy.bolygo_id,bgy.gyar_id,gye.eroforras_id,bgy.aktiv_db,gye.io,coalesce(if(gye.io>=0,0,round(bgy.aktiv_db*gye.io/sumiotabla.sumio*1000000000)),0) as reszarany
from (
select bgy.bolygo_id,gye.eroforras_id,sum(bgy.aktiv_db*if(gye.io>=0,0,gye.io)) as sumio
from bolygo_gyar bgy,gyar_eroforras gye
where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id='.$forras_bolygo['id'].'
group by bgy.bolygo_id,gye.eroforras_id
) sumiotabla,bolygo_gyar bgy,gyar_eroforras gye
where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id=sumiotabla.bolygo_id and gye.eroforras_id=sumiotabla.eroforras_id
');
mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$cel_bolygo['id']);
mysql_query('
insert into bolygo_gyar_eroforras
select bgy.bolygo_id,bgy.gyar_id,gye.eroforras_id,bgy.aktiv_db,gye.io,coalesce(if(gye.io>=0,0,round(bgy.aktiv_db*gye.io/sumiotabla.sumio*1000000000)),0) as reszarany
from (
select bgy.bolygo_id,gye.eroforras_id,sum(bgy.aktiv_db*if(gye.io>=0,0,gye.io)) as sumio
from bolygo_gyar bgy,gyar_eroforras gye
where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id='.$cel_bolygo['id'].'
group by bgy.bolygo_id,gye.eroforras_id
) sumiotabla,bolygo_gyar bgy,gyar_eroforras gye
where bgy.gyar_id=gye.gyar_id and bgy.bolygo_id=sumiotabla.bolygo_id and gye.eroforras_id=sumiotabla.eroforras_id
');


//cron_tabla
mysql_swap_rows_in_table('cron_tabla','bolygo_id',$forras_bolygo['id'],$cel_bolygo['id']);

//cron_tabla_eroforras_transzfer
//npc-re nem lehet szallitani
//egybolygos szinten nem tud, utemezett eladas/vetel viszont lehet
mysql_query('update cron_tabla_eroforras_transzfer set honnan_bolygo_id='.$cel_bolygo['id'].' where honnan_bolygo_id='.$forras_bolygo['id']);
mysql_query('update cron_tabla_eroforras_transzfer set hova_bolygo_id='.$cel_bolygo['id'].' where hova_bolygo_id='.$forras_bolygo['id']);

//queue_epitkezesek
mysql_swap_rows_in_table('queue_epitkezesek','bolygo_id',$forras_bolygo['id'],$cel_bolygo['id']);

//ugynokcsoportok
mysql_swap_rows_in_table('ugynokcsoportok','bolygo_id',$forras_bolygo['id'],$cel_bolygo['id']);

//NPC vedo flotta ha van
$r=mysql_query('select * from flottak where tulaj=0 and statusz=1 and bolygo='.$cel_bolygo['id']);
while($aux=mysql_fetch_array($r)) {
	mysql_query('update flottak set x='.$forras_bolygo['x'].',y='.$forras_bolygo['y'].',bolygo='.$forras_bolygo['id'].' where id='.$aux['id']);
}


//bolygo_transzfer_log
insert_into_bolygo_transzfer_log($forras_bolygo['id'],$forras_bolygo['uccso_emberi_tulaj'],$forras_bolygo['uccso_emberi_tulaj_szov'],$forras_bolygo['tulaj'],$forras_bolygo['tulaj_szov'],0,0,0,$forras_bolygo['pontertek'],0,$forras_bolygo['pontertek']);
insert_into_bolygo_transzfer_log($cel_bolygo['id'],$cel_bolygo['uccso_emberi_tulaj'],$cel_bolygo['uccso_emberi_tulaj_szov'],0,0,$uid,$adataim['tulaj_szov'],0,$cel_bolygo['pontertek'],$forras_bolygo['pontertek'],$cel_bolygo['pontertek']-$forras_bolygo['pontertek']);

//kaloz flottakat attenni:
mysql_query('update flottak set x='.($cel_bolygo['x']-$forras_bolygo['x']).'+x,y='.($cel_bolygo['y']-$forras_bolygo['y']).'+y,kaloz_bolygo_id='.$cel_bolygo['id'].' where tulaj=0 and kaloz_bolygo_id='.$forras_bolygo['id']);

//reset forras_bolygo
bolygo_reset($forras_bolygo['id'],$forras_bolygo['osztaly'],$forras_bolygo['terulet']);

//bolygok (forras_bolygo tulaj,tulaj_szov)
mysql_query('update bolygok set tulaj=0,tulaj_szov=0 where id='.$forras_bolygo['id']);

kilep();
?>