<?
//adatbázis adatok
//
$zanda_db_name='zanda';//név, ehhez jön automatikusan a _nemlog szuffix bizonyos esetekben
$zanda_db_user='zandaadmin';//usernév
$zanda_db_password='password';//jelszó

//ezt kell cron-ból átadni, hogy lefusson a szim.php (különben kívülről, böngészőből bárki meghívhatná)
//illetve az admin könyvtárban található progikat is ezzel lehet futtatni command line-ból
//tehát legyen egy random karaktersorozat (tetszőleges hosszúságú)
$zanda_private_key='abcdefghijklmnopqrstuvwxyz';

//32 karakteres random karaktersorozat a jelszótitkosításhoz:
//lásd ujkuki.php: $jelszo_hash=hash('whirlpool',$_REQUEST['login_jelszo'].$ki_aux['jelszo_so'].$rendszer_so);
//illetve: http://en.wikipedia.org/wiki/Salt_%28cryptography%29
//ezt tessék randomra cserélni
$rendszer_so='0123456789abcdef0123456789abcdef';

//ez a kikommentelt ZandaNet-hez tartozó só
//mivel ki van kommentelve (mármint a ZandaNet-hez csatlakozó részek), ezért nem számít
$zandanet_rendszer_so='0123456789abcdef0123456789abcdef';

//ez régen s1,s2... volt, amúgy bármi lehet, ami beazonosítja, hogy a sok Zanda-szerver közül melyikről van szó
//a domain-hez/subdomain-hez nem kell, hogy köze legyen
//a legtöbbször a "Zandagort" szó után, esetleg a "szerver" szó előtt jelenik meg, pl automatikus levelekben
$szerver_prefix='Example';





//ha true, akkor a zandamail() függvény (a csatlak.php-ban) meg sem próbál emailt küldeni, simán true-val tér vissza
//ha false, akkor próbál emailt küldeni, és csak akkor ad vissza true-t, ha sikerül
//ha pl localhost-ban futtatod, akkor állítsd true-ra
//ha rendes publikus szervert futtatsz, akkor pedig false-ra
$no_mailserver=false;

//ha true, akkor sok mysql esetén, ha hiba történik, azt kiírja
//ha false, akkor ezekben az esetekben némán hal meg (mysql error logot érdemes mindenképp bekapcsolni)
//az viszont totál ad hoc, hogy mely sql hibáknál hal meg és melyeknél nem (ahol van "or hiba()" a mysql_query után, ott igen)
$debug_mode=false;


/*
a következőkben a 'hu' és 'en' változatok azt jelentik, hogy magyar vagy angol játékos számára jeleníti-e meg a rendszer
a nyelv két helyen dől el:
- egyrészt regisztrációkor befixálódik, hogy valaki magyarul vagy angolul regisztrált (index.php "insert into userek (...nyelv...) (..."'.$lang_lang.'"...)")
- másrészt játék közben ettől lehet eltérés, attól függően, hogy mi alapján határozza meg a rendszer a kívánt nyelvet

tehát regisztrálhat valaki magyarként, majd játszhat az angol felületen
ez esetben bizonyos dolgok magyarul, bizonyosak angolul fognak megjelenni (tipikusan az automatikus levelek a regisztráció nyelvén)

mindkét nyelvdöntés alapja a lang_s.php elején beállított $lang_lang változó
ez régen attól függött, hogy sx.zandagort.hu vagy sx.zandagort.com címen érte-e el a játékos az oldalt (akár regisztráció, akár játék céljából)
a jövőben attól függ, amitől akarod
*/

//admin email cím, ahonnan az automata emailek kimennek, és ami par helyen megjelenik, mint elérhetőség
$zanda_admin_email['hu']='info@example.com';
$zanda_admin_email['en']='info@example.com';
//erről az email címről akárhányszor lehet regisztrálni (szemben a többivel, ahol egy emailről csak egyszer)
$zanda_test_user_email='test@example.com';

//az url, ahol a www könyvtár kívülről, webszerveren keresztül elérhető, vagyis ahonnan játszani lehet
$zanda_game_url['hu']='http://example.com/';
$zanda_game_url['en']='http://example.com/';


/*
honlap, facebook, tutorial... url-je, ezek itt-ott be vannak linkelve
a csataszim, okoszim és még pár dologról feltételezett, hogy a zanda_homepage_url/... url-en érhető el, vagyis így kell kialakítani a honlapot (lásd pl index_belso.php HELP részében)

rövidtávon ez nyilván a zandagort.hu ill zandagort.com lesz
hosszútávon viszont mindenki, aki indít szervert, saját honlapot, wikit, tutorialt kell, hogy csináljon,
egyrészt mert amik a zandagort.hu-n vannak, azok nem fognak változni, vagyis el fognak térni, amint fejleszt bárki bármit (pl űrhajóparaméterek vs csataszimulátor)
másrészt ha valamelyik független zandaverzió befut, nem kéne a több millió usert mind a szerveremre zúdítani :-)

ehhez kapcsolódik, hogy a
- tutorial_szovegek.php
- tut_levelek_hu.php
- tut_levelek_en.php
fájlokban jópár hivatkozás történik a *mostani* Enciklopaedia Zandagorticára (wiki), nagyobb részt képek, kisebb részt szócikkek
ezeket nem változósítottam, mert ha valaki elkezdi átalakítani a Zandát, úgyis a teljes tutorial szövegeket át kell írnia, nem csak az url-t
*/
$zanda_homepage_url['hu']='http://example.com/';
$zanda_homepage_url['en']='http://example.com/';
$zanda_facebook_url['hu']='http://facebook.com/pandagort_hu';
$zanda_facebook_url['en']='http://facebook.com/pandagort_en';
$zanda_tutorial_url['hu']='http://example.com/wiki/Tutorial_S8';
$zanda_tutorial_url['en']='http://example.com/wiki/Tutorial_S8';
$zanda_wiki_url['hu']='http://example.com/wiki/';
$zanda_wiki_url['en']='http://example.com/wiki/';
$zanda_forum_url['hu']='http://forum.example.com/';
$zanda_forum_url['en']='http://forum.example.com/';

//belső automatikus üzenetek utóirata
$zanda_ingame_msg_ps['hu']="u.i. Ez egy automatikusan generált üzenet, ne válaszolj rá, mert nem lesz, aki elolvassa. Írj a <a href=\"http://forum.example.com/\" target=\"_blank\">fórum</a>ra, vagy ha privát ügy, akkor az <a href=\"mailto:info@example.com\">info@example.com</a> címre.";
$zanda_ingame_msg_ps['en']="p.s. This is an automatically generated message, so please don't reply to it, since no one will read it. Use the <a href=\"http://forum.example.com/\" target=\"_blank\">forums</a> for discussion, or if it is private matter, write a mail to <a href=\"mailto:info@example.com\">info@example.com</a>.";




/*
inaktiv szerver (pl ha régi adatbázisból csinálsz tesztszervert)
- automatikus aktiválás (szim.php elején): megmaradjanak a tesztuserek
- összeomlott üzenetek stop: ne kapjanak egy számukra nem létező szerverről értesítést
- valszeg még más üzenetküldésekhez is be kéne tenni
*/
$inaktiv_szerver=0;

//ha 1-re van állítva, akkor nincs aktivációs kényszer, és nincsenek törölve az inaktívok
$admin_nyaral=0;

//megy a körváltó vagy sem
$fut_a_szim=1;

//fog of war be van-e kapcsolva
//lásd terkep_adatok.php, ajax_autocomplete_flottak.php, flotta_parancs_megy_flotta_nev.php, flotta_parancs_tamad_flotta_nev.php, amikre hat
$fog_of_war=1;

//végjátékban vagyunk-e
//0 = normal jatek
//1 = vegjatek: bolygoosszeomlas-ertesites kikapcsolasa, flottalimit kikapcs, morál Zandával szemben 100%, orankenti statisztikak...
//2 = vegjatek utan: bolygoosszeomlas-ertesites kikapcsolasa, flottalimit kikapcs, morál mindenkivel szemben 100%, stat normal idokozonkent...
$vegjatek=0;

//ezt akkor kell bebillenteni, ha jönnek már előflották, h a stat-ba bekerüljön
//lásd stat/stat_hist.php
$vegjatek_stat=0;


//a Központi Bank kereslet-kínálat egyensúly esetén mennyi pénzt toljon be a gazdaságba (vö. ügynökök mint pénznyelők)
//lásd szim.php: select tk.regio,tk.termek_id,2*sum(if(vevo=0,0,tk.mennyiseg))/sum(tk.mennyiseg)-1+'.$inflacio.' as delta_ar
$inflacio=0;

//ez részben a prémiumok (és azok árainak) meghatározásához kell
//részben a regisztráció megnyitásához
$szerver_indulasa='2012-06-26 18:00:00';

//ezek már csak a prémiumhoz
$szerver_varhato_vege='2012-11-26 00:00:00';//vege = indulas plusz 5 honap (pentek: nov 16)
$szerver_amikortol_mindenki_premium='2012-11-16 00:00:00';//vege minusz 1 het (pentekre kerekitve)
//amúgy az index.php-ban a regisztrációnál szabályozható, hogy alapból mennyi prémium jár (a kód mostani állapotában mindenki ajándék emelt szintűt kap végig)

//zélóta specializáció mettől meddig választható
$zelota_mikortol_valaszthato='2012-11-02';//vege minusz 3 het (pentekre kerekitve)
$zelota_meddig_valaszthato='2012-11-09';//vege minusz 2 het (pentekre kerekitve)


//speckó userek és szövetségek

//be van drótozva (van ahol userek.admin=1 szerepel, van ahol userek.id=1):
//1-es user: az admin (néhai cucu)
//1-es szövetség: az admin szövetség (néhai Szövetség'39)

//ezeknek elvileg változhat az id-ja, de azért csak óvatosan módosítsd (ergó: inkább ne módosítsd)
//userek
define('KOZPONTI_SZOLGALTATOHAZ_HU_USER_ID',2);//ez küldi a welcome, tech szint és egyéb achievement leveleket magyarul
define('KOZPONTI_SZOLGALTATOHAZ_EN_USER_ID',3);//ez küldi a welcome, tech szint és egyéb achievement leveleket angolul
define('ZHARG_AL_TANITVANYAI_USER_ID',4);//Zharg'al Tanítványai

//szövik
define('KALOZOK_HADA_SZOV_ID',2);
//ha a szokásos NPC-ken kívül van külön (aktív) kalóz szövi, akkor ez az
//ha nincs, egy slot-ot akkor is üresen kell hagyni a szovetsegek táblában (mert a kódban itt-ott megjelenik)
//a slot átugratás a mellékelt adatbázisban megtörtént (egyébként lásd ADMIN.md)

//toplistából, statisztikából kimaradnak:
$specko_userek_listaja=array(1,KOZPONTI_SZOLGALTATOHAZ_HU_USER_ID,KOZPONTI_SZOLGALTATOHAZ_EN_USER_ID,ZHARG_AL_TANITVANYAI_USER_ID);
$specko_szovetsegek_listaja=array(1,KALOZOK_HADA_SZOV_ID);

/*
még egy fontos dolog: Zandagort "szövetsége" (valójában tulaj_szov-je)

egy normál játékosnak, ha magányos farkas, a tulaj_szov-je az id-jának a -1-szerese
ha szövi tagja, akkor a tulaj_szov-je a szövetség id-ja

mivel Zandagort id-ja fixen -1, és magányos farkas, ezért a tulaj_szov-je elvileg 1 (-1-szer -1) kéne, hogy legyen
akkor viszont tagja lenne az admin szövinek, pedig nem (es ne is legyen az)

úgyhogy Zandagort tulaj_szov-je egy olyan negatív szám, amihez nem tartozik játékos (vagyis egy átugrott slot a userek táblában)

ez most a kódban sehol sem jelenik meg, mivel csak a Zanda-flották felrakásánál kell (az meg per pillanat nem része a publikált kódnak)
*/
define('ZANDAGORT_TULAJ_SZOV',-5);//vagyis az 5-ös számú id át van ugratva a userek táblában
//a slot átugratás a mellékelt adatbázisban megtörtént (egyébként lásd ADMIN.md)


//a szim.php által alkalmazott named lock neve, ami ahhoz kell, hogy ha 1 percnél tovább fut a körváltó, akkor ne induljon el még egyszer
//a névnek csak akkor van jelentősége, ha többet is használsz egy rendszerben
$szimlock_name='zanda_szimlock';

//ezekhez nyilván nem kell nyúlni, régi maradvány nevek, amik kiküszöbölésére túl sok replace-t kéne nyomni, úgyhogy inkább maradnak
$database_mmog=$zanda_db_name;
$database_mmog_nemlog=$zanda_db_name.'_nemlog';
$mysql_username=$zanda_db_user;
$mysql_password=$zanda_db_password;
