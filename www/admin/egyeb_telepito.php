<?
include('../csatlak.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);

function unsigned_parity($x) {return ($x>=0)?($x%2):((-$x)%2);}


//ezt a listát fejlesztés esetén frissíteni kell
//van, amit a galaxis_telepito.php vagy az okoszfera_es_gazdasag_telepito.php nulláz ki
mysql_query('truncate aktivitas_megosztas');
mysql_query('truncate cimzettek');
mysql_query('truncate cron_tabla');
mysql_query('truncate cron_tabla_eroforras_transzfer');
mysql_query('truncate csata_flotta');
mysql_query('truncate csata_flotta_hajo');
mysql_query('truncate csata_flottamatrix');
mysql_query('truncate csata_sebzesek');
mysql_query('truncate csata_user');
mysql_query('truncate csatak');
mysql_query('truncate cset_hozzaszolasok');
mysql_query('truncate cset_szoba_meghivok');
mysql_query('truncate cset_szoba_user');
mysql_query('truncate cset_szobak');mysql_query('alter table cset_szobak auto_increment=1000');//fenntartani helyet a szovi cseteknek
mysql_query('truncate diplomacia_ajanlatok');
mysql_query('truncate diplomacia_leendo_statuszok');
mysql_query('truncate diplomacia_statuszok');
mysql_query('truncate diplomacia_szovegek');
mysql_query('truncate feregjaratok');
mysql_query('truncate flotta_hajo');
mysql_query('truncate flottak');
mysql_query('truncate hexa_flotta');
mysql_query('truncate ideiglenes_kitiltasok');
mysql_query('truncate jegyzetek');
mysql_query('truncate lat_szov_flotta');
mysql_query('truncate lat_user_flotta');
mysql_query('truncate levelek');
mysql_query('truncate queue_epitkezesek');
mysql_query('truncate resz_flotta_aux');
mysql_query('truncate resz_flotta_hajo');
mysql_query('truncate szabadpiaci_ajanlatok');
mysql_query('truncate szabadpiaci_arfolyamok');
mysql_query('truncate szov_forum_kommentek');
mysql_query('truncate szov_forum_tema_olv');
mysql_query('truncate szov_forum_temak');
mysql_query('truncate szovetseg_meghivas_kerelmek');
mysql_query('truncate szovetseg_meghivok');
mysql_query('truncate szovetseg_szabalyzatok');
mysql_query('truncate szovetseg_tisztsegek');
mysql_query('truncate szovetseg_vendegek');
mysql_query('truncate szovetsegek');
mysql_query('truncate torlendo_userek');
mysql_query('truncate ugynokcsoportok');
mysql_query('truncate user_badge');
mysql_query('truncate user_beallitasok');
mysql_query('truncate user_kutatasi_szint');
mysql_query('truncate user_tagek');
mysql_query('truncate user_veteli_limit');
mysql_query('truncate userek');


//a _nemlog-ban lévő összes táblát lehet
$er=mysql_query('select table_name from information_schema.tables where table_schema="'.$database_mmog_nemlog.'"');
while($aux=mysql_fetch_array($er)) {
	mysql_query('truncate '.$database_mmog_nemlog.'.'.$aux[0]);
}


mysql_query('update ido set idopont=0');
mysql_query('update ido
set idopont_kezd=idopont,
idopont_npc=idopont,
idopont_monetaris=idopont,
idopont_termeles=idopont,
idopont_felderites=idopont,
idopont_flottamoral=idopont,
idopont_flottak=idopont,
idopont_csatak=idopont,
idopont_ostromok=idopont,
idopont_fog=idopont');



echo 'kesz';
mysql_close($mysql_csatlakozas);
?>