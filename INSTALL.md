Zandagort telepítési útmutató
=============================

**Fontos megjegyzés**: Mivel az adatbázis lényegi része `MEMORY` táblákban van, ezek a szerver minden újraindulásakor elvesznek. Ha localhost-ban akarsz privát Zandagort szervert futtatni, erre gondolnod kell, hogy amikor éjszakára lekapcsolod a gépet, minden el fog veszni. Ha ezt nem akarod, vagy dump-old mindig az adatbázist kikapcsolás előtt, és töltsd vissza bekapcsolás után, vagy cseréld le a `MEMORY` táblákat `MyISAM` táblákra (amitől viszont lassabb lesz a rendszer). Publikus szervernél pedig bár elvileg éjjel-nappal fut a szerver, de ha csak egy ici-pici újraindulás is történik, akkor nem csak a te játékod ment tönkre, hanem mindenkié, aki játszik. Szóval menteni, menteni, menteni!

Megjegyzés: A lentiekben *[opcionális]*-ként vannak jelölve azok a részek, amik nélkül is el fog indulni a játék, legfeljebb nem lesz biztonságos és hibamentes. Természetesen ezekkel együtt sem garantált semmi, lásd az `"AS IS"` szakaszt az MIT licenszben.

Megjegyzés 2: A lentiekben ha `.php` fájlra van hivatkozás, az (szinte) mindig a `www` könyvtárban található.

Megjegyzés 3: Ha nem a megadott sorrendben haladsz, előfordulhatnak kisebb problémák. Például még be sem állítottál mindent, és már regisztrált valaki (mert túl korán tetted publikussá a `www` könyvtárat).

## 0. Apache-MySQL-PHP

Telepítsd fel ezt a három programot a célgépre. Apache helyett választhatsz bármilyen más webszervert is, ami együtt tud működni a PHP-vel (Lighttpd, IIS, akármi). Én a chat "nagy" connection-igénye miatt (bár ez nyilván userszám-függő) Lighttpd-t használtam fastcgi-vel. Erről lásd: http://www.lighttpd.net/

Ha fogalmad nincs, mik ezek, vagy hogy kell őket felrakni, akkor keress rá neten. Van millióegy leírás, külön-külön is, és egyben is. Segítség: ezt a kombót `LAMP`-nak vagy `WAMP`-nak hívják.

Ha sehogy sem megy, fogadd el, hogy egy Zandagort-szerver üzemeltetése meghaladja *jelenlegi* képességeidet. Nincs ezzel semmi baj, végül az én képességeimet is meghaladta, még ha nem is technikai szempontból.


## 1. Fájlok és könyvtárak

### Kicsomagolás

Hozz létre egy alap könyvtárat, amibe kicsomagolsz mindent. Három alkönyvtára lesz:

* `www`: ezt kell a webszerveren beállítani publikusnak (lásd 6. pont)
* `szim`: itt van a körváltó Windows-os verziója és a körváltó log (lásd 7. pont)
* `mysql`: itt van az induló adatbázis dump (lásd 2. pont)

A `mysql` könyvtárban csomagold ki a két dump fájlt.

### Arial

A játék a tőzsdei grafikonok, minitérkép, satöbbik felirataihoz az [Arial](http://en.wikipedia.org/wiki/Arial) betűtípust használja (melynek nagy előnye, hogy keskeny, így kis helyen sok betű-számjegy elfér). Hogy ezek működjenek, az szükséges, hogy a `www/img` könyvtárban legyen egy példány az `arial.ttf` fájlból. Mivel az Arial *nem* szabadon felhasználható font, hanem a [Monotype Corporation](http://en.wikipedia.org/wiki/Monotype_Corporation) védett tulajdona, ezért elvileg nem rakhatom bele a letölthető csomagba. Innentől kezdve két lehetőséged van:

1. Valahonnan beszerzed az `arial.ttf`-et (például a `Windows/Fonts` könyvtárból a gépedről), bemásolod a `www/img` könyvtárba, és vállalod a kockázatot, hogy egy éjjel rád töri az ajtót a Monotype-kommandó.

2. Vagy keresel egy [alternatív, szabadon felhasználható fontot](http://en.wikipedia.org/wiki/Arial#Free_alternatives), azt másolod be a `www/img` könyvtárba, és az összes hivatkozást kicseréled (ez nem sok, mert a legtöbb grafikus szöveget kiíró szkript elején van egy `$font_cim='img/arial.ttf';` sor, és csak ezt kell lecserélni).

A harmadik lehetőség az lenne, hogy hagyod a francba, max néhány dolog nem működik fullosan. De vedd figyelembe, hogy ez a regisztrációs kapcsára (CAPTCHA) is vonatkozik, így senki nem fog tudni regisztrálni.

### JS-kód [opcionális]

A `www/jskod_...` és `www/jskod_..._v2` könyvtárakat nevezd át, a `...` helyére random karaktersorozatot írva, és a `jskod.php`-ban add meg az új könyvtárnevet a `$konyvtar` változónak. Különben hiába kapcsolod ki a könyvtár listázását (lásd 4-es pont "Directory listing kikapcsolása"), a standard név miatt magát a js kódot bárki láthatja. Ami persze csak akkor baj, ha olyan fejlesztéseket eszközölsz, amiket nem akarsz publikálni.

### Linux könyvtár jogosultságok

Ahova megy képfeltöltés, oda adj `write` jogot (a `read`-et nem muszáj elvenned, vagyis `733` helyett lehet `777` is, lásd 4. pont "Directory listing kikapcsolása"):

	chmod 733 www/img/cimerek
	chmod 733 www/img/minicimerek
	chmod 733 www/img/okoszim
	chmod 733 www/img/user_avatarok
	chmod 733 www/img/user_nagy_avatarok

A `www/jskod_...` és `www/jskod_..._v2` könyvtáraknak `write` jogot adni (de a `read`-et is megtartani), különben nem fog tudni beleírni a `jskod.php`:

	chmod 777 www/jskod_...
	chmod 777 www/jskod_..._v2

## 2. MySQL

### User és adatbázisok létrehozása

Találj ki egy adatbázis nevet (maradhat a mostani), egy mysql admin user nevet (maradhat a mostani) és jelszavót (semmiképp nem maradhat a mostani), és állítsd be a `config.php`-ban:

	$zanda_db_name='zanda';
	$zanda_db_user='zandaadmin';
	$zanda_db_password='password';

*Fontos*: a jelszót mindenképp változtasd meg, ne legyen `password`.

Majd MySQL-ben futtasd le ezeket (`root`-ként), persze úgy, hogy a fenti neveket/jelszót helyettesíted be:

	create database zanda;
	create database zanda_nemlog;
	create database zanda_homokozo;
	grant all on zanda.* to 'zandaadmin'@'%' identified by 'password';
	grant all on zanda_nemlog.* to 'zandaadmin'@'%' identified by 'password';
	grant all on zanda_homokozo.* to 'zandaadmin'@'%' identified by 'password';
	flush privileges;

A `_nemlog` és a `_homokozo` kötelező szuffixumok, vagyis bármi is az alap adatbázis neve, ahhoz kell hozzáfűzni ezeket.

Megjegyzés: biztonsági okokból lehet `'zandaadmin'@'localhost'` is a user definíciója. Ez esetben a játék ugyanúgy el tudja érni az adatbázist, hiszen localhost-ban van. Kívülről viszont lehetetlen, így nehezebb feltörni a rendszer. Aminek az a hátránya, hogy a fejlesztő csak ssh-n keresztül tud hozzányúlni az adatbázishoz. Egyébként ha igazán parás vagy, akkor állítsd be a `bind-address = 127.0.0.1`-et is a `my.cnf`-ben, és akkor semmilyen adatbázishoz nem lehet kívülről csatlakozni.

### Fontos beállítások

Az alábbiak mind a `my.cnf` fájlra vonatkoznak.

Mivel a játékban vannak nagy `MEMORY` táblák (a legnagyobb jelenleg a kb 120 megás `hexa_bolygo` tábla), növeld meg a `max_heap_table_size`-ot (alapértelmezésben csak 16M):

	max_heap_table_size=256M

Egyéb:

	character-set-server = utf8
	collation-server = utf8_hungarian_ci

### Kevésbé fontos beállítások [opcionális]

Kapcsold ki a query cache-t, ha van:

	query_cache_type = OFF
	query_cache_size = 0

mert nem hatékony (állandóan invalidálódnak a cache bejegyzések).

Ha bekapcsoltad a binary logolást, akkor tedd be ezt a két sort:

	binlog_ignore_db	= zanda_nemlog
	binlog_ignore_db	= zanda_homokozo

mert

- a `zanda_nemlog` egy szinte csak logtáblákból álló adatbázis, logot logolni pedig értelmetlen
- a `zanda_homokozo` tipikus használata, hogy oda betöltesz egy állapotot, és ráfuttatsz x környi szimulációt, ez pedig mind-mind fölöslegesen terhelné a binary log-ot.

Egyéb:

	skip-name-resolve

Ez ahhoz kell, hogy ne DNS-ezzen állandóan, amitől totál be tud lassulni a külső query browser-es hozzáférés.

### MySQL újraindítása

Az előzőek miatt kell egy teljes restart (a reload csak a grant-okat tölti újra, szóval nem elég). Linux-on:

	sudo /etc/init.d/mysql stop
	sudo /etc/init.d/mysql start

Windows-on:

	Control Panel > Adminstrative Tools > Services > MySQL / Stop és Start

### Adatbázis betöltése

A `mysql` könyvtárból add ki ezt a két parancsot (és amikor kéri, add meg a `config.php`-ban megadott `$zanda_db_password` jelszót):

	mysql -u zandaadmin -p --default-character-set=utf8 zanda < zanda_install_dump.sql
	mysql -u zandaadmin -p --default-character-set=utf8 zanda_nemlog < zanda_nemlog_install_dump.sql

Nem kell beszarni, hogy az "üres" adatbázis is sok idő alatt megy fel (10-15 perc). Van egy nagyon nagy tábla: `hexa_bolygo`, meg néhány középnagy: `bolygo_eroforras`, `flotta_hajo`, `hexak`, `bolygo_gyar_eroforras`. És ne feledd, a mellékelt adatbázis valójában nem üres, ott van benne a szűz Omen-galaxis (s8) egy csomó bolygóval, hexával és NPC-flottával.


## 3. PHP

Az alábbiak mind a `php.ini` fájlra vonatkoznak.

Sokan bad practice-nek tartják, de kizárólag short tag-eket használok (`<?` és nem `<?php`), éppen ezért ezt engedélyezd, ha nem lenne:

	short_open_tag = On

(Egyéb rossz szokásom, pl `register_globals`, `magic_quotes_gpc` tudtommal nincs.)

A `GD` library-t is engedélyezd, mert az avatárfeltölés, beépített ökoszimulátor, tőzsdei grafikon, mini térkép, CAPTCHA használja:

	extension=gd.so

Az esetek 99%-ában egyébként úgyis alapból be van kapcsolva.

Képfeltöltés (címer, avatár) miatt érdemes valami ésszerű méretkorlátot beállítanod, pl:

	post_max_size = 8M
	upload_max_filesize = 7M

A kettő közt az a különbség, hogy a `post_max_size`-ba benne vannak a metainfók is (HTTP request header), vagyis annak picit nagyobbnak kell lennie, mint az `upload_max_filesize`-nak.

Nem kötelező, de érdemes `APC`-t (vagy hasonló gyorsítót) használnod, legalábbis ha a minimálisnál nagyobb forgalomra tervezel szervert. (De lehet utólag is, *amikor* megnő a forgalom.) Ez becache-eli a bytecode-ra fordított php szkripteket, így a szervernek nem kell minden egyes lekéréskor újrafordítania, csak előrántja a memóriából. Bővebben lásd: http://php.net/manual/en/book.apc.php


## 4. Egyéb apróságok

### Mentés, helyreállítás

Röviden: lásd a "Fontos megjegyzés"-t az előszóban

Hosszan: http://zandagort.blog.hu/2012/06/25/gebasz_kezelo_rendszer

### Directory listing kikapcsolása [opcionális]

Állítsd be, hogy böngészőből ne lehessen kilistázni könyvtárakat. Ez a `jskod_...` és az `img` könyvtárak (és utóbbi alkönyvtárai) miatt fontos.

Apache-ban a `httpd.conf`-ban szedd ki az `Options`-ökből az `Indexes`-eket (vagy tegyél be `-Indexes`-t). Bővebb infó: http://wiki.apache.org/httpd/DirectoryListings

Lighttpd-ban a `lighttpd.conf`-ban állítsd be ezt: `server.dir-listing="disable"`

Ha nagyon nem megy, akkor szedd le a `group`/`other` `read` jogot az összes olyan könyvtárról, amelyikben nincs `index.php` vagy `index.html`.

### Mail szerver [opcionális]

Az összes email küldés a `csatlak.php`-ban található `zandamail()` függvényből van kezelve. Alapértelmezésben itt a localhost `mail()`-je van hívódik meg (`PHPMailer`-en keresztül), de átírhatod bárhogy. Vagy akár a `config.php`-ban a `$no_mailserver`-t `true`-ra is állíthatod (és akkor nincs email küldés).

### Logolás [opcionális]

Egyéni ízlés kérdése, de amiket érdemes lehet használnod:

- mysql binary log (hibakereséshez, újraszimuláláshoz)
- mysql slow query log (optimalizáláshoz)
- mysql error log (hibakereséshez)
- webszerver access log (forgalomelemzéshez, hibakereséshez)
- webszerver error log (hibakereséshez)
- php error log (hibakereséshez)

Mivel nagy forgalomnál ezek egy része elég durván tud nőni, érdemes forgatnod őket (`logrotate`).

### Ökoszim [opcionális]

Az `img/okoszim` könyvtár a használat során egyre jobban telik, mert ha valaki a beépített ökoszimet használja, annak oda generálódik az eredmény. Tisztogasd meg tehát időnként egy szkripttel (pl `cron`-nal naponta egyszer), valahogy így:

	find www/img/okoszim -type f -delete

### Időzóna beállítások [opcionális]

Évenként egyszer, az őszi óraátállítás problémát okoz, mert kétszer egymás után van hajnali 2 és 3 óra között. A gond az, hogy a fosztási moratórium ilyenkor összezavarodik. Mert ha az első 2 és 3 óra közti időszakban történik fosztás, akkor a moratórium a második 2 és 3 óra közti időszakba kerül, vagyis kvázi ugyanakkor, mint maga a fosztás történt. Így egy órán keresztül percenként mennek a fosztások.

Ennek primitív kiküszöbölését lásd a `csatlak.php` végefelé: `oraatallitas hack`. Ezt vagy frissítsd minden évben, vagy keress jobb, tartós megoldást.


## 5. Zandagort konfigurálása

A `config.php`-ban le van írva minden. Ha úgy érzed, túl sok is, itt egy rövid lista, hogy mi az, amihez mindenképp hozzá kell nyúlnod (vagyis a többi maradhat, ahogy van):

`$zanda_db_password`

Generálj szépen egy random karaktersorozatot (ott van pl a `csatlak.php`-ban a `randomgen()` függvény), és azt használd. Úgysem kell fejből tudnod ezt a jelszót, akkor legalább legyen biztonságos. Különben valaki simán ellopja a nálad regisztrált játékosok adatait, és azzal nem csak a játékosokat veszíted el, hanem a jó hírneved is.

`$zanda_private_key`

Ez szintén random és titkos kell, hogy legyen.

`$rendszer_so`

Szintén változasd meg randomra. Utána az adatbázisban meg kell "birizgálni" az admin játékos jelszavát, mert részben ez a só lett felhasználva a jelszó titkosított tárolására.

Arról nem is beszélve, hogy alapértelmezésben `password` az admin játékos jelszava, úgyhogy nyisd meg a `admin/reset_admin_pw.php`-t, írd át valami titkos jelszóra, és futtasd ezt (a `$zanda_private_key` helyére persze a fentebb kitalált random karaktersorozatot írd):

	php -f reset_admin_pw.php $zanda_private_key

`$szerver_prefix`

Ezt ugyan nem kötelező, de ha például a toplistában nem azt akarod látni, hogy "Zandagort Example szerver toplista", akkor cseréld le.

`$no_mailserver`

Ha csak úgy a magad kedvéért csinálod localhost-ban, akkor állítsd `true`-ra.

`$zanda_admin_email`

Ha publikus szervert futtatsz, ez valami valós elérhetőség legyen.

`$zanda_game_url`, `$zanda_homepage_url`, `$zanda_facebook_url`, `$zanda_tutorial_url`, `$zanda_wiki_url`, `$zanda_forum_url`, `$zanda_ingame_msg_ps`

Ezek eredetileg mind a nagy közös Zandagort honlapra, fórumra, wikire (Enciklopaedia Zandagortica), facebook oldalra mutattak. Ha saját szervered van, idővel nem árt, ha ezeknek is elkészül a saját verziója. A `zandagort.hu` megmarad egyfajta központnak, ahonnan pl be vannak linkelve a különféle verziók, de ennél több karbantartása nem lesz.

`$szerver_indulasa`

Mikortól lehet regisztrálni a szerverre. Ha publikus szervert indítasz, ez nyilván fontos, hogy telepítés közben vagy a zárt teszt időszakban ne lépjen be senki.

`$szerver_varhato_vege`, `$szerver_amikortol_mindenki_premium`

Ezeket a prémium miatt kell beállítanod.

`$zelota_mikortol_valaszthato`, `$zelota_meddig_valaszthato`

Ezeket pedig a zélóta specializáció miatt.


## 6. Webszerver

A `www` könyvtárat tedd kívülről elérhetővé (ott jelenik meg a játék felülete). Előtte csekkold le, hogy a `config.php`-ban a `$szerver_indulasa`-t jól állítottad-e be.

Egyéb konfigurációról semmi okosat nem tudok mondani. (Tehát: "alap" beállítások vannak, már amennyire ez értelmezhető.)


## 7. Körváltó beindítása

Az alábbiakban a `PATH_TO_ZANDA` a Zandagort főkönyvtár (amin belül a `www` és a `szim` található) abszolút elérési útvonala. Írd át mindenhol megfelelően, ahol előfordul.

### Linux

	crontab -e
	* * * * * php -f PATH_TO_ZANDA/www/szim.php $zanda_private_key >> PATH_TO_ZANDA/szim/szim_log

### Windows

A `szim/szim.bat`-ban helyettesítsd be a `$zanda_private_key`-t és a `PATH_TO_ZANDA`-t, utána pedig:

	schtasks /create /ru "System" /tn "ZandaSzim" /sc minute /tr "PATH_TO_ZANDA/szim/szim.bat"


## 8. Móka és kacagás

Lépj be az adminnal vagy regisztrálj új játékosként! Játssz! Hívj meg másokat! Nyúlj bele az adatbázisba, és csalj! Nyúlj bele a kódba, és rettegj, hogy mikor fagy ki, esik szét!
