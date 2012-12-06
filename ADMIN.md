Zandagort admin
===============

Az alábbiak mind a `www/admin` könyvtárra vonatkoznak.

Ezeket a PHP-kat csak konzolból lehet futtatni a `$zanda_private_key` megadásával:

	php -f valami.php $zanda_private_key


## Szerver resetelése

Ha fejlesztesz, és utána akarsz új szervert indítani, ezeken kell végigmenned.

### Táblák kiürítése

Nézd meg az `egyeb_telepito.php`-ban a táblák listáját, hogy van-e ami kimaradt (ez csak akkor fontos, ha fejlesztettél, és van új tábla). Utána futtasd le.

### Bolygók létrehozása

Nézd meg a `galaxis_telepito.php` elején a leírást, aztán futtasd le egyesével a blokkokat. (Nem minden esetben kell mindegyiket.)

### Ökoszférák és gazdaságok telepítése

Futtasd le az `okoszfera_es_gazdasag_telepito.php`-t. Hogy mi van egy bolygón induláskor, azt a `csatlak.php`-ban található `bolygo_reset()` függvény határozza meg.

### NPC flották telepítése

Futtasd le az `npc_telepito.php`-t.

### Admin és néhány alap user létrehozása

A következőkben mindig csekkold le, hogy a `userek` és a `szovetsegek` táblában jó ID-kat kaptál-e, és hogy azok passzolnak a `config.php`-ban leírtakhoz.

1. Regisztrálj a normál regisztráló oldalon egy admin usert.

2. Alapíts vele egy admin szövit.

3. `UPDATE userek SET admin=1 WHERE id=1;`

4. Futtasd le a `user_telepito.php`-t.

5. `ALTER TABLE `zanda`.`szovetsegek` AUTO_INCREMENT = 3;` (egy slot meghagyása a kalóz szövinek: `config.php/KALOZOK_HADA_SZOV_ID`)

6. `ALTER TABLE `zanda`.`userek` AUTO_INCREMENT = 6;` (egy slot meghagyása a Zandagortnak: `config.php/ZANDAGORT_TULAJ_SZOV`)


## Új galaxis generálása

A `galaxis` könyvtárban találsz néhány fájlt. Ezek nincsenek dokumentálva, szóval magadnak kell rájönnöd a működésükre, leszámítva a következő pár infót.

s7-en két fázisban készült a galaxis, s8-on egyben. A fájlok tele vannak régi maradványokkal. s7-en például egy JPG kép alapján készült a galaxis (a valóban létező Kocsikerék-galaxis egy űrfotójáról), mégis vannak benne algoritmusos maradványok s6-ról (test, belsosegek, farok, uszok, szaj).

Eredetileg ezek egy külön szerveren futottak, ahol a ZandaNet van. Így az összes galaxis eredeti, szűz formában megmaradt. A `galaxis_telepito.php`-ban található `blokk0()` függvény innen másolja át a megfelelő szerverre.

Ha ezeket használni szeretnéd, hozz létre egy `szuz_bolygok` vagy hasonló nevű táblát (ez simán lehet `MyISAM` is), módosítsd úgy a kódot, hogy a `galaxis_generator_....php` oda generálja a bolygókat, és a `galaxis_telepito.php` onnan másolja be őket a tényleges `bolygok` táblába.


## Egyéb

Ha bármi van, pl változik a `config.php/$rendszer_so`, akkor a `reset_admin_pw.php`-val tudod újra beállítani az admin user jelszavát.
