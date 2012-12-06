<?
include('csatlak.php');
$nem_szamit_aktivitasnak=1;include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$res=mysql_query('select * from bolygok where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($res);

if ($bolygo['id']) if ($bolygo['tulaj']==$uid || $bolygo['kezelo']==$uid) {
?>
/*{"oke":1,"leendo_gyarak":<?
echo mysql2jsonmatrix('
select timestampdiff(second,now(),c.mikor_aktualis),gy.id,gyt.nev'.$lang__lang.',gyt.id,gy.szint,gy.uzemmod,gyt.uzemmod_szam_'.$bolygo['osztaly'].',c.id,c.aktiv,c.darab,coalesce(round(100*(1-timestampdiff(second,now(),c.mikor_aktualis)/timestampdiff(second,c.mikor_kiadva,c.mikor_aktualis))),0)
from cron_tabla c,gyarak gy,gyartipusok gyt
where c.feladat='.FELADAT_GYAR_EPIT.' and c.bolygo_id='.$bolygo['id'].' and c.gyar_id=gy.id and gy.tipus=gyt.id
order by c.mikor_aktualis,gyt.iparag_sorszam,gyt.id,gy.uzemmod');
?>,"volt_gyarak":<?
echo mysql2jsonmatrix('
select timestampdiff(second,now(),c.mikor_aktualis),gy.id,gyt.nev'.$lang__lang.',gyt.id,gy.szint,gy.uzemmod,gyt.uzemmod_szam_'.$bolygo['osztaly'].',c.id,c.aktiv,c.darab,coalesce(round(100*(1-timestampdiff(second,now(),c.mikor_aktualis)/timestampdiff(second,c.mikor_kiadva,c.mikor_aktualis))),0)
from cron_tabla c,gyarak gy,gyartipusok gyt
where c.feladat='.FELADAT_GYAR_LEROMBOL.' and c.bolygo_id='.$bolygo['id'].' and c.gyar_id=gy.id and gy.tipus=gyt.id
order by c.mikor_aktualis,gyt.iparag_sorszam,gyt.id,gy.uzemmod');
?>,"term_mikor":<?
$term_mikor=($bolygo['id']%15)-((mysql2num('select idopont from ido')-1)%15);
if ($term_mikor<=0) $term_mikor+=15;
echo $term_mikor;
?>,"premium":<?
echo premium_szint();

?>,"befagy_eplista":<?
echo $bolygo['befagy_eplista'];

?>,"queue":<?
//if (premium_szint()) echo mysql2jsonmatrix('select gy.id,gyt.nev'.$lang__lang.',gyt.id,gy.uzemmod,gyt.uzemmod_szam_'.$bolygo['osztaly'].',q.id,q.aktiv,q.darab,cast(q.aktiv*q.darab as signed)*cast((-gye.io+if(gy.id=77,-250,if(gy.id=78,-15000,0))) as signed) from queue_epitkezesek q, gyarak gy, gyartipusok gyt, gyar_eroforras gye where q.bolygo_id='.$bolygo['id'].' and q.gyar_id=gy.id and gy.tipus=gyt.id and gye.gyar_id=gy.id and gye.eroforras_id=57 order by q.sorszam');
//else echo '[]';
//if (premium_szint())
if (true) echo mysql2jsonmatrix('
select gy.id,gyt.nev'.$lang__lang.',gyt.id,gy.uzemmod,gyt.uzemmod_szam_'.$bolygo['osztaly'].',q.id,q.aktiv,q.darab,coalesce(cast(q.aktiv*q.darab as signed)*cast((-coalesce(gye.io,0)+if(gy.id=77,-250,if(gy.id=78,-15000,0))) as signed),0)
from queue_epitkezesek q
inner join gyarak gy on q.gyar_id=gy.id
inner join gyartipusok gyt on gy.tipus=gyt.id
left join gyar_eroforras gye on gye.gyar_id=gy.id and gye.eroforras_id=57
where q.bolygo_id='.$bolygo['id'].' order by q.sorszam
');
else echo '[]';

?>,"queue_eroforrasigeny":<?
//if (premium_szint())
if (true) echo '"'.mysql2num('
select group_concat(t.mikell order by t.id separator ", ") from (
select gyek.eroforras_id as id,concat(if(sum(q.darab*gyek.db)>=1000,if(round(sum(q.darab*gyek.db)/1000)*1000=sum(q.darab*gyek.db),concat(round(sum(q.darab*gyek.db)/1000),"k"),concat(round(sum(q.darab*gyek.db)/1000,1),"k")),sum(q.darab*gyek.db))," ",e.nev'.$lang__lang.') as mikell
from queue_epitkezesek q, gyarak gy, gyar_epitesi_koltseg gyek, eroforrasok e
where q.gyar_id=gy.id and gy.tipus=gyek.tipus and gyek.szint=1
and q.bolygo_id='.$bolygo['id'].' and gyek.eroforras_id=e.id
group by gyek.eroforras_id) t
').'"';
else echo '""';

?>,"queue_darabszam":<?
//if (premium_szint())
if (true) echo ''.mysql2jsonmatrix('
select gy.tipus,sum(q.darab)
from queue_epitkezesek q, gyarak gy
where q.gyar_id=gy.id and q.bolygo_id='.$bolygo['id'].'
group by gy.tipus
').'';
else echo '[]';

?>}*/
<?
} else {
?>
/*{"oke":0}*/
<?
}

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>