-- Disable Foreign Key Support - can drop table
PRAGMA foreign_keys = OFF;

BEGIN TRANSACTION;

-- --------------------------------------------------------

--
-- Table structure for table `change_stav`
--

DROP TABLE IF EXISTS `change_stav`;
CREATE TABLE `change_stav`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);

INSERT INTO `change_stav` (`id`, `nazev`)
VALUES (1, 'Otevřen'),
       (2, 'Přiřazen'),
       (3, 'Probíhá realizace'),
       (4, 'Vyřešeno'),
       (5, 'Uzavřeno');

-- --------------------------------------------------------

--
-- Table structure for table `change`
--

DROP TABLE IF EXISTS `change`;
CREATE TABLE `change`
(
    `id`              INTEGER  NOT NULL PRIMARY KEY,
    `datum_vytvoreni` datetime NOT NULL,
    `datum_ukonceni`  datetime NOT NULL,
    `datum_uzavreni`  datetime DEFAULT NULL,
    `obsah_uzavreni`  text     DEFAULT NULL,
    `zpusob_uzavreni` INTEGER  DEFAULT NULL,
    `typ_change`      INTEGER  DEFAULT NULL,
    `osoba_vytvoril`  INTEGER  NOT NULL,
    `osoba_prirazen`  INTEGER  DEFAULT NULL,
    `incident_stav`   INTEGER  NOT NULL,
    `change_stav`     INTEGER  NOT NULL,
    `priorita`        INTEGER  NOT NULL,
    FOREIGN KEY (`zpusob_uzavreni`) REFERENCES `zpusob_uzavreni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`typ_change`) REFERENCES `typ_change` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`osoba_vytvoril`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`osoba_prirazen`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`change_stav`) REFERENCES `change_stav` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`incident_stav`) REFERENCES `incident_stav` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`priorita`) REFERENCES `priorita` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE

);

-- --------------------------------------------------------

--
-- Table structure for table `change_log`
--

DROP TABLE IF EXISTS `change_log`;
CREATE TABLE `change_log`
(
    `id`              bigint(20) NOT NULL PRIMARY KEY,
    `change`          INTEGER    NOT NULL,
    `obsah`           text       NOT NULL,
    `datum_vytvoreni` datetime   NOT NULL,
    FOREIGN KEY (`change`) REFERENCES `change` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table `fronta`
--

DROP TABLE IF EXISTS `fronta`;
CREATE TABLE `fronta`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);


INSERT INTO `fronta` (`id`, `nazev`)
VALUES (1, 'Programatori'),
       (2, 'TIER 1'),
       (3, 'Grafika');

-- --------------------------------------------------------

--
-- Table structure for table `typ_osoby`
--

DROP TABLE IF EXISTS `typ_osoby`;
CREATE TABLE `typ_osoby`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);


INSERT INTO `typ_osoby` (`id`, `nazev`)
VALUES (1, 'Zákaznik'),
       (2, 'Specialista'),
       (3, 'System');

-- --------------------------------------------------------

--
-- Table structure for table `format_datum`
--

DROP TABLE IF EXISTS `format_datum`;
CREATE TABLE `format_datum`
(
    `id`     INTEGER      NOT NULL PRIMARY KEY,
    `nazev`  varchar(100) NOT NULL,
    `format` varchar(10)  NOT NULL
);

INSERT INTO `format_datum` (`id`, `nazev`, `format`)
VALUES (1, 'EU', 'd.m.Y');

-- --------------------------------------------------------

--
-- Table structure for table `time_zone`
--

DROP TABLE IF EXISTS `time_zone`;
CREATE TABLE `time_zone`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL,
    `cas`   varchar(10)  NOT NULL
);

INSERT INTO `time_zone` (`id`, `nazev`, `cas`)
VALUES (1, 'UTC-12 Y', '-12:00'),
       (2, 'UTC-11 X', '-11:00'),
       (3, 'UTC-10 W, HST (Hawaii-Aleutian Standard Time)', '-10:00'),
       (4, 'UTC-9.30 V*', '-09:30'),
       (5, 'UTC-9 V, AKST (Alaska Standard Time)', '-09:00'),
       (6, 'UTC-8 U, PST (Pacific Standard Time)', '-08:00'),
       (7, 'UTC-7 T, MST (Mountain Standard Time)', '-07:00'),
       (8, 'UTC-6 S, CST (Central Standard Time)', '-06:00'),
       (9, 'UTC-5 R, EST (Eastern Standard Time)', '-05:00'),
       (10, 'UTC-4.30, Q†', '-04:30'),
       (11, 'UTC-4 Q, AST (Atlantic Standard Time)', '-04:00'),
       (12, 'UTC-3.30 P*, NST (Newfoundland Standard Time)', '-03:30'),
       (13, 'UTC-3 P', '-03:00'),
       (14, 'UTC-2 O', '-02:00'),
       (15, 'UTC-1 N', '-01:00'),
       (16, 'GMT (Greenwich Mean Time)', '+00:00'),
       (17, 'SEČ (Středoevropský čas)', '+01:00'),
       (18, 'UTC+2 B, EET (East European Time)', '+02:00'),
       (19, 'UTC+3 C, MSK (Moscow Time)', '+03:00'),
       (20, 'UTC+3.30 C*', '+03:30'),
       (21, 'UTC+4 D', '+04:00'),
       (22, 'UTC+4.30 D*', '+04:30'),
       (23, 'UTC+5 E', '+05:00'),
       (24, 'UTC+5.30 E*, IST (Indian Standard Time)', '+05:30'),
       (25, 'UTC+5.45 E‡', '+05:45'),
       (26, 'UTC+6 F', '+06:00'),
       (27, 'UTC+6.30 F*', '+06:30'),
       (28, 'UTC+7 G', '+07:00'),
       (29, 'UTC+8 H, AWST (Australian Western Standard Time)', '+08:00'),
       (30, 'UTC+8.45 H‡', '+08:45'),
       (31, 'UTC+9 I, JST (Japan Standard Time), KST (Korea Standard Time)', '+09:00'),
       (32, 'UTC+9.30 I*, ACST (Australian Central Standard Time)', '+09:30'),
       (33, 'UTC+10 K, AEST (Australian Eastern Standard Time)', '+10:00'),
       (34, 'UTC+10.30 K*', '+10:30'),
       (35, 'UTC+11 L', '+11:00'),
       (36, 'UTC+11.30 L*', '+11:30'),
       (37, 'UTC+12 M', '+12:00'),
       (38, 'UTC+12.45 M‡', '+12:45'),
       (39, 'UTC+13 M*', '+13:00'),
       (40, 'UTC+14 M†', '+14:00');

-- --------------------------------------------------------

--
-- Table structure for table `zeme`
--

DROP TABLE IF EXISTS `zeme`;
CREATE TABLE `zeme`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);

INSERT INTO `zeme` (`id`, `nazev`)
VALUES (1, 'Česká republika'),
       (2, 'Slovenská republika'),
       (3, 'Polská republika'),
       (4, 'Republika Rakousko'),
       (5, 'Spolková republika Německo');


-- --------------------------------------------------------

--
-- Table structure for table `firma`
--

DROP TABLE IF EXISTS `firma`;
CREATE TABLE `firma`
(
    `id`              smallint(5)  NOT NULL PRIMARY KEY,
    `nazev`           varchar(250) NOT NULL,
    `ico`             varchar(20)  DEFAULT NULL,
    `dic`             varchar(20)  DEFAULT NULL,
    `datum_vytvoreni` datetime     NOT NULL,
    `datum_upravy`    datetime     NOT NULL,
    `ulice`           varchar(100) NOT NULL,
    `obec`            varchar(100) NOT NULL,
    `cislo_uctu`      varchar(50)  NOT NULL,
    `psc`             varchar(15)  NOT NULL,
    `zeme`            INTEGER      NOT NULL,
    `iban`            varchar(100) DEFAULT NULL,
    FOREIGN KEY (`zeme`) REFERENCES `zeme` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);

INSERT INTO `firma` (`id`, `nazev`, `ico`, `dic`, `datum_vytvoreni`, `datum_upravy`, `ulice`, `obec`, `cislo_uctu`,
                     `psc`, `zeme`, `iban`)
VALUES (1, 'Ing. Martin Patyk', '88230104', 'CZ8707145876', '2014-01-13 22:17:20', '2014-01-13 22:17:20',
        'Ratibořská 36', 'Opava', '670100-2209225998/6210', '747 05', 1, NULL),
       (2, 'PATYKDESIGN s.r.o.', '28648579', 'CZ28648579', '2014-01-13 22:24:39', '2014-01-13 22:25:18', 'Olomoucká 8',
        'Opava - Předměstí', '', '746 01', 1, NULL),
       (3, 'Opravna vah - Vladimír Patyk', '44197373', 'CZ5908041524', '2014-01-13 22:27:52', '2014-01-13 22:27:52',
        'U cukrovaru 12', 'Opava', '', '747 05', 1, NULL),
       (4, 'SEDKO group s.r.o.', '25857355', 'CZ25857355', '2014-01-13 22:31:07', '2014-01-13 22:31:07',
        'Rooseveltova 1940/33', 'Opava', '', '746 01', 1, NULL),
       (5, 'RD Rýmařov s.r.o.', '18953581', 'CZ18953581', '2014-01-13 22:32:26', '2014-01-13 22:32:26',
        '8. května 1191/45', 'Rýmařov', '', '795 01', 1, NULL),
       (6, 'PH&PM Trading s.r.o.', '2784301', 'CZ02784301', '2014-02-08 15:05:18', '2014-07-18 14:34:19',
        'Chudenická 1059/30', 'Praha - Hostivař', '2400578126/2010', '102 00', 1, NULL),
       (7, 'Minerva-Gastro s.r.o.', '26840987', '', '2014-04-19 13:05:57', '2014-04-19 13:37:59', 'Mařádkova 2913/28',
        'Opava', '', '746 01', 1, NULL),
       (8, 'KOMCENTRA s.r.o.', '41186991', 'CZ41186991', '2014-05-02 18:38:51', '2014-05-02 18:38:51',
        'Dejvická 574/33', 'Praha 6', '', '160 00', 1, NULL),
       (9, 'Jana Vimmerová', '71908129', '', '2014-05-02 18:51:22', '2014-05-02 18:51:22', 'Mánesova 8', 'Opava 1', '',
        '746 01', 1, NULL),
       (10, 'Markéta Hajdíková', '75270749', '', '2014-11-05 22:07:33', '2014-11-05 22:07:33', 'Olomoucká 2389/95',
        'Opava', '', '746 01', 1, NULL),
       (11, 'FMT spol.s.r.o.', '27796868', 'CZ27796868', '2015-04-07 21:28:48', '2015-04-07 21:28:48', 'Cihelni 238',
        'Neplachovice', '', '747 74', 1, NULL),
       (12, 'BLAŽEK PROJEKT s.r.o.', '3412105', 'CZ03412105', '2015-04-08 20:45:44', '2015-04-08 20:45:44',
        'Pekařská 1638/79', 'Opava - Kateřinky', '', '747 05', 1, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `osoba`
--

DROP TABLE IF EXISTS `osoba`;
CREATE TABLE `osoba`
(
    `id`                  INTEGER      NOT NULL PRIMARY KEY,
    `email`               varchar(150) NOT NULL,
    `password`            char(60)     NOT NULL,
    `jmeno`               varchar(100) NOT NULL,
    `prijmeni`            varchar(100) NOT NULL,
    `posledni_prihlaseni` datetime     NOT NULL,
    `datum_vytvoreni`     datetime     NOT NULL,
    `datum_zmeny_hesla`   datetime     NOT NULL,
    `typ_osoby`           INTEGER      NOT NULL,
    `time_zone`           INTEGER      NOT NULL,
    `format_datum`        INTEGER      NOT NULL,
    `je_admin`            tinyint(1)   NOT NULL,
    `firma`               smallint(5)  NOT NULL,
    FOREIGN KEY (`typ_osoby`) REFERENCES `typ_osoby` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`format_datum`) REFERENCES `format_datum` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`time_zone`) REFERENCES `time_zone` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`firma`) REFERENCES `firma` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);

INSERT INTO `osoba` (`id`, `email`, `password`, `jmeno`, `prijmeni`, `posledni_prihlaseni`, `datum_vytvoreni`,
                     `datum_zmeny_hesla`, `typ_osoby`, `time_zone`, `format_datum`, `je_admin`, `firma`)
VALUES (1, 'patyk.m@gmail.com', '$2y$07$zmghdrx76regp4uux3og9OZm4COjrFpvNPyFOlokRwvQPgA1t1dgW', 'Martin', 'Patyk',
        '0000-00-00 00:00:00', '2014-02-05 19:22:12', '0000-00-00 00:00:00', 2, 17, 1, 1, 1),
       (2, 'martin@patyk.cz', '$2y$07$gtl3l57nh4sw98hsl30y2eWLhVCadgv5hASkjwVTur0t5Tf7nUCsK', 'Martin', 'FakePatyk',
        '0000-00-00 00:00:00', '2014-02-23 13:29:02', '0000-00-00 00:00:00', 1, 17, 1, 0, 6),
       (3, 'info@pm-autodily.cz', '$2y$07$4w666frxb8y24cpd88qfpui3bTS1wA9J4glb.uTG1xiTOcg/VveXa', 'Petr', 'Miarka',
        '0000-00-00 00:00:00', '2014-03-18 20:01:44', '2014-04-19 11:39:51', 1, 17, 1, 0, 6),
       (4, 'martin.hradil@tieto.com', '$2y$07$s4zfbw2ceq3r9gnwprsejeCN1nQ2bRxQHfHnaIMV/8QrwMMnMzB/G', 'Martin',
        'Hradil', '0000-00-00 00:00:00', '2014-04-17 14:39:50', '0000-00-00 00:00:00', 1, 17, 1, 0, 1),
       (5, 'cd24h@patyk.cz', '$2y$07$fjlk0lc6n4f85qyjmkmdgu5SzwqeSkHUo8Hw41rnBgfg0LPWThGnO', 'Control', 'Desk',
        '0000-00-00 00:00:00', '2014-05-02 22:47:25', '0000-00-00 00:00:00', 3, 17, 1, 0, 1),
       (6, 'ondra.patyk@gmail.com', '$2y$07$qp6xnnfv468ls1l02gugguLxZM.rNQgELSuXUftehwzmjNcbBG8zu', 'Ondřej', 'Patyk',
        '0000-00-00 00:00:00', '2014-05-03 11:39:41', '0000-00-00 00:00:00', 2, 17, 1, 0, 2),
       (7, 'Oleg.Sedko@gmail.com', '$2y$07$o5xron8kph1z0seah703meajPLy.IbKVz8l5C0COoP5Aha5u4NAmq', 'Oleg', 'Sedko',
        '0000-00-00 00:00:00', '2014-05-03 11:41:09', '2014-12-12 09:50:53', 1, 17, 1, 0, 4),
       (8, 'sd24h@patyk.cz', '$2y$07$izec00j575jl04py1wdbjej7WHHAbHqK/ZmZvAnF9Tu.eoU1tDh2G', 'Service', 'Desk',
        '0000-00-00 00:00:00', '2014-05-04 19:58:26', '0000-00-00 00:00:00', 3, 17, 1, 0, 1),
       (9, 'ss@patyk.cz', '$2y$07$k5l3cu71eblda1ii8tpi2eRlhEBvm30NfXVQztmV70bLed5QRNIwu', 'Shift', 'Supervisor',
        '0000-00-00 00:00:00', '2014-06-08 00:45:08', '0000-00-00 00:00:00', 3, 17, 1, 0, 1),
       (10, 'martin.hajdik@seznam.cz', '$2y$07$x0jxhn25cqh83c69pgd2heDWQh9xRGKbxoAEwf9dj25/tBf5.XeNq', 'Markéta',
        'Hajdíková', '0000-00-00 00:00:00', '2014-12-04 10:20:53', '0000-00-00 00:00:00', 1, 17, 1, 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `stav_ci`
--

DROP TABLE IF EXISTS `stav_ci`;
CREATE TABLE `stav_ci`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);

INSERT INTO `stav_ci` (`id`, `nazev`)
VALUES (1, 'Otevřen'),
       (2, 'Instalace'),
       (3, 'Nasazen'),
       (4, 'Vyřazen');


--
-- Table structure for table `ci`
--

DROP TABLE IF EXISTS `ci`;
CREATE TABLE `ci`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `nazev`           varchar(250) NOT NULL,
    `datum_vytvoreni` datetime     NOT NULL,
    `obsah`           text         NOT NULL,
    `zobrazit`        tinyint(1)   NOT NULL,
    `fronta_tier_1`   INTEGER      NOT NULL,
    `fronta_tier_2`   INTEGER      NOT NULL,
    `fronta_tier_3`   INTEGER      NOT NULL,
    `osoba_vytvoril`  INTEGER      NOT NULL,
    `stav_ci`         INTEGER     DEFAULT NULL,
    `firma`           smallint(5) DEFAULT NULL,
    `tarif`           INTEGER     DEFAULT NULL,
    `ci`              INTEGER     DEFAULT NULL,
    FOREIGN KEY (`fronta_tier_2`) REFERENCES `fronta` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`osoba_vytvoril`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`stav_ci`) REFERENCES `stav_ci` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`firma`) REFERENCES `firma` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`tarif`) REFERENCES `tarif` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`ci`) REFERENCES `ci` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`fronta_tier_1`) REFERENCES `fronta` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`fronta_tier_3`) REFERENCES `fronta` (`id`) ON DELETE NO ACTION
);

INSERT INTO `ci` (`id`, `nazev`, `datum_vytvoreni`, `obsah`, `zobrazit`, `fronta_tier_1`, `fronta_tier_2`,
                  `fronta_tier_3`, `osoba_vytvoril`, `stav_ci`, `firma`, `tarif`, `ci`)
VALUES (3, 'Portal.patyk.cz', '0000-00-00 00:00:00', '[werf', 1, 2, 1, 1, 1, 2, 1, 10, NULL),
       (4, 'patykdesign.cz', '0000-00-00 00:00:00', 'webova prezentace firmy patykdesign.cz', 1, 2, 1, 1, 1, 3, 2, 8,
        NULL),
       (5, 'vahy-opava.cz', '0000-00-00 00:00:00', 'Webove stranky opravna vah opava', 1, 2, 1, 1, 1, 3, 3, 7, NULL),
       (6, 'Nette framework 2.1', '0000-00-00 00:00:00', 'Nette', 0, 2, 1, 1, 1, NULL, NULL, NULL, 3),
       (7, 'pm-autodily.cz', '0000-00-00 00:00:00', 'Petr Miarka E-shop na autodíly', 1, 2, 1, 1, 1, 3, 6, 6, NULL),
       (8, 'nabytek-vyhodne.cz', '0000-00-00 00:00:00', 'Eshop postaveny na presta shopu', 1, 2, 1, 1, 1, 2, 6, 6,
        NULL),
       (9, 'palivove-cerpadla.cz', '0000-00-00 00:00:00', 'PALIVOVE-CERPADLA.cz', 1, 2, 1, 1, 1, 3, 6, 6, NULL),
       (10, 'rs-tuning.cz', '0000-00-00 00:00:00', 'rs-tuning.cz', 1, 2, 1, 1, 1, 3, 6, 6, NULL),
       (11, 'Jídelna u Minervy', '0000-00-00 00:00:00', 'Webove stranky Jídelna u Minervy', 1, 2, 1, 1, 1, 3, 7, 10,
        NULL),
       (12, 'komcentra.cz', '2014-05-02 18:41:38', 'www.komcentra.cz', 1, 2, 1, 1, 1, 3, 8, 6, NULL),
       (13, 'toymee.cz', '2014-05-02 18:53:03', 'www.toymee.cz', 1, 2, 1, 1, 1, 3, 9, 6, NULL),
       (14, 'sedko.cz', '2014-05-02 18:58:55', 'http://www.sedko.cz/', 1, 2, 1, 1, 1, 3, 4, 9, NULL),
       (15, 'sedko.eu', '2014-05-02 18:59:42', 'http://sedko.eu/', 1, 2, 1, 1, 1, 3, 4, 9, NULL),
       (16, 'wrc-autodily.cz', '2014-07-06 13:16:15', 'http://wrc-autodily.cz/', 1, 2, 1, 1, 1, 3, 6, 6, NULL),
       (17, 'pm-autodiely.sk', '2014-08-13 14:51:47', 'http://pm-autodiely.sk', 1, 2, 1, 1, 1, 3, 6, 6, NULL),
       (18, 'mmprotein.cz', '2014-11-05 22:10:32', 'mmprotein.cz', 1, 2, 1, 1, 1, 2, 10, 6, NULL),
       (19, 'analytics.patyk.cz', '2015-02-23 07:06:27', 'Ahoj, Je k dispozici nová verze Piwiku', 1, 2, 1, 1, 1, 3, 1,
        10, NULL),
       (20, 'beeup.cz', '2015-02-26 18:07:50', 'beeup.cz', 1, 2, 1, 1, 1, 2, 4, 9, NULL),
       (21, 'studyforfuture.com', '2015-03-07 12:59:45', 'studyforfuture.com', 1, 2, 1, 1, 1, 2, 4, 9, NULL),
       (22, 'besplatnoeobrazovanie.com', '2015-03-07 13:01:16', 'besplatnoeobrazovanie.com', 1, 2, 1, 1, 1, 2, 4, 9,
        NULL),
       (23, 'fm-t.eu', '2015-04-08 20:41:58', 'fm-t.eu', 1, 2, 1, 1, 1, 2, 11, 10, NULL),
       (24, 'blazekprojekt.com', '2015-04-08 20:46:41', 'blazekprojekt.com', 1, 2, 1, 1, 1, 2, 12, 10, NULL),
       (25, 'vysha-osvita.com', '2015-05-03 16:54:45', 'vysha-osvita.com', 1, 2, 1, 1, 1, 2, 4, 9, NULL),
       (26, 'wpad.komcentra.cz', '2021-03-24 08:35:34', 'wpad.komcentra.cz', 1, 2, 1, 1, 1, 2, 8, 10, 12),
       (27, 'patyk.cz', '2021-03-24 09:10:44', 'patyk.cz', 1, 2, 2, 2, 1, 3, 1, 10, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `ci_log`
--

DROP TABLE IF EXISTS `ci_log`;
CREATE TABLE `ci_log`
(
    `id`              bigint(20) NOT NULL PRIMARY KEY,
    `ci`              INTEGER    NOT NULL,
    `obsah`           text       NOT NULL,
    `datum_vytvoreni` datetime   NOT NULL,
    FOREIGN KEY (`ci`) REFERENCES `ci` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO `ci_log` (`id`, `ci`, `obsah`, `datum_vytvoreni`)
VALUES (1, 3,
        'Název: Portal.patyk.cz<br />**Stav:**: Instalace <br />**Výchozí fronta:**: Programatori <br />**Firma:**: Ing. Martin Patyk <br />**Tarif:**: Tarif A <br />Zobrazit ? 1<br />Obsah: [werf<br />',
        '2014-02-05 22:21:35'),
       (2, 4,
        'Název: patykdesign.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: PATYKDESIGN s.r.o. <br />**Tarif:**: Tarif B <br />Zobrazit ? 1<br />Obsah: webova prezentace firmy patykdesign.cz<br />',
        '2014-02-08 11:14:43'),
       (3, 5,
        'Název: vahy-opava.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: Opravna vah - Vladimír Patyk <br />**Tarif:**: Tarif A <br />Zobrazit ? 1<br />Obsah: Webove stranky opravna vah opava<br />',
        '2014-02-08 11:44:30'),
       (4, 6, 'Název: Nette framework 2.1<br />**Předek:**: Portal.patyk.cz <br />Zobrazit ? <br />Obsah: Nette<br />',
        '2014-02-08 11:44:57'),
       (5, 7,
        'Název: http://www.pm-autodily.cz/<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: PM-AUTODILY.cz <br />**Tarif:**: Tarif A <br />Zobrazit ? 1<br />Obsah: Petr Miarka E-shop na autodíly<br />',
        '2014-02-08 15:06:41'),
       (6, 8,
        'Název: nabytek-vyhodne.cz<br />**Stav:**: Instalace <br />**Výchozí fronta:**: Programatori <br />**Firma:**: PM-AUTODILY.cz <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: Eshop postaveny na presta shopu<br />',
        '2014-03-03 19:21:14'),
       (7, 12,
        'Název: www.komcentra.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: KOMCENTRA s.r.o. <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: www.komcentra.cz<br />',
        '2014-05-02 18:41:38'),
       (8, 13,
        'Název: www.toymee.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: Jana Vimmerová <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: www.toymee.cz<br />',
        '2014-05-02 18:53:03'),
       (9, 14,
        'Název: sedko.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: SEDKO group s.r.o. <br />**Tarif:**: Sedko Group s.r.o. SLA <br />Zobrazit ? 1<br />Obsah: http://www.sedko.cz/<br />',
        '2014-05-02 18:58:55'),
       (10, 15,
        'Název: sedko.eu<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: SEDKO group s.r.o. <br />**Tarif:**: Sedko Group s.r.o. SLA <br />Zobrazit ? 1<br />Obsah: http://sedko.eu/<br />',
        '2014-05-02 18:59:42'),
       (11, 9,
        'Název: PALIVOVE-CERPADLA.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: PM-AUTODILY.cz <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: PALIVOVE-CERPADLA.cz<br />',
        '2014-04-03 11:08:44'),
       (12, 10,
        'Název: rs-tuning.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: PM-AUTODILY.cz <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: rs-tuning.cz<br />',
        '2014-04-10 21:24:35'),
       (13, 11,
        'Název: Jídelna u Minervy<br />**Stav:**: Nasazen <br />**Výchozí fronta:**: Programatori <br />**Firma:**: Jídelna u Minervy <br />**Tarif:**: patyk.cz SLA <br />Zobrazit ? 1<br />Obsah: Webove stranky Jídelna u Minervy<br />',
        '2014-04-19 13:06:54'),
       (14, 16,
        'Název: wrc-autodily.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta TIER 1:**: SD_24H <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: PM-AUTODILY.cz <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: http://wrc-autodily.cz/<br />',
        '2014-07-06 13:16:15'),
       (15, 17,
        'Název: pm-autodiely.sk<br />**Stav:**: Nasazen <br />**Výchozí fronta TIER 1:**: SD_24H <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: PH&PM Trading s.r.o. <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: http://pm-autodiely.sk<br />',
        '2014-08-13 14:51:47'),
       (16, 18,
        'Název: mmprotein.cz<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: SD_24H <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: Markéta Hajdíková <br />**Tarif:**: Miarka Petr SLA <br />Zobrazit ? 1<br />Obsah: mmprotein.cz<br />',
        '2014-11-05 22:10:32'),
       (17, 19,
        'Název: analytics.patyk.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: Ing. Martin Patyk <br />**Tarif:**: patyk.cz SLA <br />Zobrazit ? 1<br />Obsah: Ahoj,\n\nJe k dispozici nová verze Piwiku\n\nMůžete aktualizovat na verzi 2.11.1 automaticky, nebo si stáhněte balíček a nainstalujte jej manuálně:\n\nhttp://analytics.patyk.cz/index.php?module=CoreUpdater&action=newVersionAvailable\n\nZde můžete s týmem Piwiku sdílet nápady a návrhy:\nhttp://piwik.org/contact/<br />',
        '2015-02-23 07:06:27'),
       (18, 20,
        'Název: beeup.cz<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: SEDKO group s.r.o. <br />**Tarif:**: Sedko Group s.r.o. SLA <br />Zobrazit ? 1<br />Obsah: beeup.cz<br />',
        '2015-02-26 18:07:50'),
       (19, 21,
        'Název: studyforfuture.com<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: SEDKO group s.r.o. <br />**Tarif:**: Sedko Group s.r.o. SLA <br />Zobrazit ? 1<br />Obsah: studyforfuture.com<br />',
        '2015-03-07 12:59:45'),
       (20, 22,
        'Název: besplatnoeobrazovanie.com<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: SEDKO group s.r.o. <br />**Tarif:**: Sedko Group s.r.o. SLA <br />Zobrazit ? 1<br />Obsah: besplatnoeobrazovanie.com<br />',
        '2015-03-07 13:01:16'),
       (21, 23,
        'Název: fm-t.eu<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: FMT spol.s.r.o. <br />**Tarif:**: patyk.cz SLA <br />Zobrazit ? 1<br />Obsah: fm-t.eu<br />',
        '2015-04-08 20:41:58'),
       (22, 24,
        'Název: blazekprojekt.com<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: BLAŽEK PROJEKT s.r.o. <br />**Tarif:**: patyk.cz SLA <br />Zobrazit ? 1<br />Obsah: blazekprojekt.com<br />',
        '2015-04-08 20:46:41'),
       (23, 25,
        'Název: vysha-osvita.com<br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: SEDKO group s.r.o. <br />**Tarif:**: Sedko Group s.r.o. SLA <br />Zobrazit ? 1<br />Obsah: vysha-osvita.com<br />',
        '2015-05-03 16:54:45'),
       (24, 26,
        'Název: wpad.komcentra.cz<br />**Předek:**: komcentra.cz <br />**Stav:**: Instalace <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: Programatori <br />**Výchozí fronta TIER 3:**: Programatori <br />**Firma:**: KOMCENTRA s.r.o. <br />**Tarif:**: patyk.cz SLA <br />Zobrazit ? 1<br />Obsah: wpad.komcentra.cz<br />',
        '2021-03-24 08:35:35'),
       (25, 27,
        'Název: patyk.cz<br />**Stav:**: Nasazen <br />**Výchozí fronta TIER 1:**: TIER 1 <br />**Výchozí fronta TIER 2:**: TIER 1 <br />**Výchozí fronta TIER 3:**: TIER 1 <br />**Firma:**: Ing. Martin Patyk <br />**Tarif:**: patyk.cz SLA <br />Zobrazit ? 1<br />Obsah: patyk.cz<br />',
        '2021-03-24 09:10:44');

-- --------------------------------------------------------

--
-- Table structure for table `dph`
--

DROP TABLE IF EXISTS `dph`;
CREATE TABLE `dph`
(
    `id`         INTEGER     NOT NULL PRIMARY KEY,
    `nazev`      varchar(50) NOT NULL,
    `koeficient` float       NOT NULL,
    `vychozi`    tinyint(1) DEFAULT NULL,
    `procent`    INTEGER    DEFAULT 0
);

INSERT INTO `dph` (`id`, `nazev`, `koeficient`, `vychozi`, `procent`)
VALUES (1, 'Bez DPH', 1, 1, 0),
       (2, '21%', 1.21, 0, 21);

-- --------------------------------------------------------

--
-- Table structure for table `faktura`
--

DROP TABLE IF EXISTS `faktura`;
CREATE TABLE `faktura`
(
    `id`                   INTEGER      NOT NULL PRIMARY KEY,
    `dodavatel_nazev`      varchar(250) NOT NULL,
    `dodavatel_ico`        varchar(20)  NOT NULL,
    `dodavatel_dic`        varchar(20)  DEFAULT NULL,
    `dodavatel_ulice`      varchar(100) DEFAULT NULL,
    `dodavatel_obec`       varchar(100) NOT NULL,
    `dodavatel_psc`        varchar(15)  NOT NULL,
    `dodavatel_zeme`       varchar(100) NOT NULL,
    `dodavatel_cislo_uctu` varchar(50)  NOT NULL,
    `dodavatel_iban`       varchar(100) DEFAULT NULL,
    `odberatel_nazev`      varchar(250) NOT NULL,
    `odberatel_ico`        varchar(20)  NOT NULL,
    `odberatel_dic`        varchar(20)  DEFAULT NULL,
    `odberatel_ulice`      varchar(100) DEFAULT NULL,
    `odberatel_obec`       varchar(100) NOT NULL,
    `odberatel_psc`        varchar(15)  NOT NULL,
    `odberatel_zeme`       varchar(100) NOT NULL,
    `odberatel_cislo_uctu` varchar(50)  NOT NULL,
    `odberatel_iban`       varchar(100) DEFAULT NULL,
    `splatnost`            varchar(5)   NOT NULL,
    `datum_vystaveni`      datetime     NOT NULL,
    `datum_splatnosti`     date         NOT NULL,
    `datum_zaplaceni`      date         DEFAULT NULL,
    `vytvoril`             INTEGER      NOT NULL,
    `forma_uhrady`         INTEGER      NOT NULL,
    `vs`                   char(10)     NOT NULL,
    `ks`                   varchar(10)  NOT NULL,
    `pdf_soubor`           varchar(255) NOT NULL,
    FOREIGN KEY (`forma_uhrady`) REFERENCES `forma_uhrady` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`vytvoril`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
-- --------------------------------------------------------

--
-- Table structure for table `faktura_polozka`
--

DROP TABLE IF EXISTS `faktura_polozka`;
CREATE TABLE `faktura_polozka`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `faktura`         INTEGER      NOT NULL,
    `cssclass`        INTEGER      NOT NULL,
    `nazev`           varchar(250) NOT NULL,
    `dodatek`         varchar(250) NOT NULL,
    `dph`             INTEGER       DEFAULT NULL,
    `jednotka`        INTEGER       DEFAULT NULL,
    `koeficient_cena` float         DEFAULT NULL,
    `pocet_polozek`   smallint(5)   DEFAULT NULL,
    `sleva`           float         DEFAULT NULL,
    `cena`            decimal(8, 4) DEFAULT NULL,
    FOREIGN KEY (`dph`) REFERENCES `dph` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`jednotka`) REFERENCES `jednotka` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`faktura`) REFERENCES `faktura` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`cssclass`) REFERENCES `faktura_polozka_css` (`id`) ON DELETE NO ACTION
);



-- --------------------------------------------------------

--
-- Table structure for table `faktura_polozka_css`
--

DROP TABLE IF EXISTS `faktura_polozka_css`;
CREATE TABLE `faktura_polozka_css`
(
    `id`    INTEGER     NOT NULL PRIMARY KEY,
    `nazev` varchar(50) NOT NULL
);

INSERT INTO `faktura_polozka_css` (`id`, `nazev`)
VALUES (1, 'faktura-polozka'),
       (2, 'faktura-nadpis');



-- --------------------------------------------------------

--
-- Table structure for table `forma_uhrady`
--

DROP TABLE IF EXISTS `forma_uhrady`;
CREATE TABLE `forma_uhrady`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);

INSERT INTO `forma_uhrady` (`id`, `nazev`)
VALUES (1, 'převodním příkazem'),
       (2, 'hotově');


-- --------------------------------------------------------

--
-- Table structure for table `fronta_osoba`
--

DROP TABLE IF EXISTS `fronta_osoba`;
CREATE TABLE `fronta_osoba`
(
    `id`     INTEGER NOT NULL PRIMARY KEY,
    `fronta` INTEGER NOT NULL,
    `osoba`  INTEGER NOT NULL,
    FOREIGN KEY (`fronta`) REFERENCES `fronta` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`osoba`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION
);

INSERT INTO `fronta_osoba` (`id`, `fronta`, `osoba`)
VALUES (1, 1, 1),
       (2, 2, 8),
       (3, 3, 6),
       (4, 2, 5),
       (5, 1, 9),
       (6, 3, 9);


-- --------------------------------------------------------

--
-- Table structure for table `incident`
--

DROP TABLE IF EXISTS `incident`;
CREATE TABLE `incident`
(
    `id`                       INTEGER      NOT NULL PRIMARY KEY,
    `ukon`                     INTEGER    DEFAULT NULL,
    `ovlivneni`                INTEGER    DEFAULT NULL,
    `datum_vytvoreni`          datetime     NOT NULL,
    `datum_ukonceni`           datetime     NOT NULL,
    `datum_reakce`             datetime     NOT NULL,
    `datum_uzavreni`           datetime   DEFAULT NULL,
    `obsah_uzavreni`           text       DEFAULT NULL,
    `zpusob_uzavreni`          INTEGER    DEFAULT NULL,
    `typ_incident`             INTEGER    DEFAULT NULL,
    `osoba_vytvoril`           INTEGER      NOT NULL,
    `fronta_osoba`             INTEGER    DEFAULT NULL,
    `osoba_uzavrel`            INTEGER    DEFAULT NULL,
    `incident`                 INTEGER    DEFAULT NULL,
    `incident_stav`            INTEGER      NOT NULL,
    `priorita`                 INTEGER      NOT NULL,
    `maly_popis`               varchar(100) NOT NULL,
    `obsah`                    text         NOT NULL,
    `ci`                       INTEGER      NOT NULL,
    `faktura`                  INTEGER    DEFAULT NULL,
    `odezva_cekam`             tinyint(1) DEFAULT NULL,
    `odezva_odeslan_pozadavek` tinyint(1) DEFAULT NULL,
    FOREIGN KEY (`zpusob_uzavreni`) REFERENCES `zpusob_uzavreni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`incident`) REFERENCES `incident` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`faktura`) REFERENCES `faktura` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`osoba_uzavrel`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`fronta_osoba`) REFERENCES `fronta_osoba` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`ukon`) REFERENCES `ukon` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`ovlivneni`) REFERENCES `ovlivneni` (`id`) ON DELETE NO ACTION,
    FOREIGN KEY (`typ_incident`) REFERENCES `typ_incident` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`osoba_vytvoril`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`incident_stav`) REFERENCES `incident_stav` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`priorita`) REFERENCES `priorita` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`ci`) REFERENCES `ci` (`id`) ON DELETE NO ACTION
);

-- --------------------------------------------------------

--
-- Table structure for table `incident_log`
--

DROP TABLE IF EXISTS `incident_log`;
CREATE TABLE `incident_log`
(
    `id`              bigint(20) NOT NULL PRIMARY KEY,
    `incident`        INTEGER    NOT NULL,
    `obsah`           text       NOT NULL,
    `datum_vytvoreni` datetime   NOT NULL,
    `osoba`           INTEGER DEFAULT NULL,
    FOREIGN KEY (`incident`) REFERENCES `incident` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`osoba`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION
);

-- --------------------------------------------------------

--
-- Table structure for table `incident_stav`
--

DROP TABLE IF EXISTS `incident_stav`;
CREATE TABLE `incident_stav`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);

INSERT INTO `incident_stav` (`id`, `nazev`)
VALUES (1, 'Otevřen'),
       (2, 'Přiřazen'),
       (3, 'Probíhá realizace'),
       (4, 'Vyřešeno'),
       (5, 'Uzavřeno'),
       (6, 'Čeká se na vyjádření zákazníka'),
       (7, 'Znovu otevřen');
-- --------------------------------------------------------

--
-- Table structure for table `jednotka`
--

DROP TABLE IF EXISTS `jednotka`;
CREATE TABLE `jednotka`
(
    `id`      INTEGER     NOT NULL PRIMARY KEY,
    `nazev`   varchar(50) NOT NULL,
    `zkratka` varchar(10) NOT NULL
);

INSERT INTO `jednotka` (`id`, `nazev`, `zkratka`)
VALUES (1, 'Neurčito', 'x'),
       (2, 'Korun českých', 'Kč'),
       (3, 'Kusů', 'Ks'),
       (4, 'Kilo', 'Kg');

-- --------------------------------------------------------

--
-- Table structure for table `maily`
--

DROP TABLE IF EXISTS `maily`;
CREATE TABLE `maily`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `datum_vytvoreni` datetime     NOT NULL,
    `od`              varchar(150) NOT NULL,
    `tiket`           INTEGER      NOT NULL,
    `telo`            text         NOT NULL,
    FOREIGN KEY (`tiket`) REFERENCES `incident` (`id`) ON DELETE NO ACTION
);

-- --------------------------------------------------------

--
-- Table structure for table `od_ci`
--

DROP TABLE IF EXISTS `od_ci`;
CREATE TABLE `od_ci`
(
    `id` smallint(5)  NOT NULL PRIMARY KEY,
    `od` varchar(150) NOT NULL,
    `ci` INTEGER      NOT NULL,
    FOREIGN KEY (`ci`) REFERENCES `ci` (`id`) ON DELETE NO ACTION
);

INSERT INTO `od_ci` (`id`, `od`, `ci`)
VALUES (2, 'noreply@www.patykdesign.cz', 4),
       (3, 'noreply@patykdesign.cz', 4),
       (4, 'noreply@komcentra.eu', 12),
       (5, 'noreply@www.komcentra.eu', 12),
       (6, 'noreply@www.komcentra.cz', 12),
       (7, 'noreply@komcentra.cz', 12),
       (10, 'noreply@www.komcentra.com', 12),
       (11, 'noreply@komcentra.com', 12),
       (12, 'noreply@www.toymee.cz', 13),
       (13, 'noreply@toymee.cz', 13),
       (14, 'noreply@portal.patyk.cz', 3),
       (15, 'noreply@www.sedko.eu', 15),
       (16, 'noreply@sedko.eu', 15),
       (17, 'noreply@www.sedko.cz', 14),
       (18, 'noreply@sedko.cz', 14),
       (19, 'noreply@www.minervagastro.cz', 11),
       (20, 'noreply@minervagastro.cz', 11),
       (21, 'noreply@vahy-opava.cz', 5),
       (22, 'noreply@www.vahy-opava.cz', 5),
       (23, 'info@studyforfuture.com', 21),
       (24, 'info@sedko.eu', 15),
       (25, 'info@sedko.cz', 14),
       (26, 'info@vahy-opava.cz', 5),
       (27, 'noreply@wpad.komcentra.cz', 12),
       (29, 'ondrej@patyk.cz', 4),
       (30, 'no-reply.cerna@patyk.cz', 3);

-- --------------------------------------------------------

--
-- Table structure for table `osoba_firma`
--

DROP TABLE IF EXISTS `osoba_firma`;
CREATE TABLE `osoba_firma`
(
    `id`    INTEGER     NOT NULL PRIMARY KEY,
    `osoba` INTEGER     NOT NULL,
    `firma` smallint(5) NOT NULL,
    FOREIGN KEY (`osoba`) REFERENCES `osoba` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`firma`) REFERENCES `firma` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table `ovlivneni`
--

DROP TABLE IF EXISTS `ovlivneni`;
CREATE TABLE `ovlivneni`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `nazev`           varchar(100) NOT NULL,
    `koeficient_cena` float        NOT NULL,
    `koeficient_cas`  float        NOT NULL
);

INSERT INTO `ovlivneni` (`id`, `nazev`, `koeficient_cena`, `koeficient_cas`)
VALUES (1, 'Malé', 0.5, 1.2),
       (2, 'Normalní', 1, 1),
       (3, 'Vysoké', 1.5, 0.8);

-- --------------------------------------------------------

--
-- Table structure for table `tarif`
--

DROP TABLE IF EXISTS `tarif`;
CREATE TABLE `tarif`
(
    `id`    INTEGER        NOT NULL PRIMARY KEY,
    `nazev` varchar(100)   NOT NULL,
    `cena`  decimal(10, 2) NOT NULL
);

INSERT INTO `tarif` (`id`, `nazev`, `cena`)
VALUES (6, 'Miarka Petr SLA', 300.00),
       (7, 'vahy-opava SLA', 10.00),
       (8, 'patykdesign.cz SLA', 10.00),
       (9, 'Sedko Group s.r.o. SLA', 300.00),
       (10, 'patyk.cz SLA', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `priorita`
--

DROP TABLE IF EXISTS `priorita`;
CREATE TABLE `priorita`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `nazev`           varchar(100) NOT NULL,
    `koeficient_cena` float        NOT NULL,
    `koeficient_cas`  float        NOT NULL
);

INSERT INTO `priorita` (`id`, `nazev`, `koeficient_cena`, `koeficient_cas`)
VALUES (1, 'Velmi malá', 0.9, 1.7),
       (2, 'Malá', 0.95, 1.5),
       (3, 'Normální', 1, 1),
       (4, 'Vysoká', 1.3, 0.8),
       (5, 'Kritická', 1.5, 0.7);

-- --------------------------------------------------------

--
-- Table structure for table `typ_incident`
--

DROP TABLE IF EXISTS `typ_incident`;
CREATE TABLE `typ_incident`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `typ_incident`    INTEGER DEFAULT NULL,
    `nazev`           varchar(100) NOT NULL,
    `zkratka`         varchar(10)  NOT NULL,
    `zobrazit`        tinyint(1)   NOT NULL,
    `koeficient_cena` float        NOT NULL,
    `koeficient_cas`  float        NOT NULL,
    FOREIGN KEY (`typ_incident`) REFERENCES `typ_incident` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);


INSERT INTO `typ_incident` (`id`, `typ_incident`, `nazev`, `zkratka`, `zobrazit`, `koeficient_cena`, `koeficient_cas`)
VALUES (1, NULL, 'Objednávka', 'ORD', 1, 1, 1),
       (2, NULL, 'Incident', 'INC', 1, 1, 1),
       (3, 2, 'Incident úkol', 'ITASK', 0, 0.9, 0.8);


-- --------------------------------------------------------

--
-- Table structure for table `sla`
--

DROP TABLE IF EXISTS `sla`;
CREATE TABLE `sla`
(
    `id`              smallint(5) NOT NULL PRIMARY KEY,
    `tarif`           INTEGER     NOT NULL,
    `priorita`        INTEGER     NOT NULL,
    `typ_incident`    INTEGER     NOT NULL,
    `reakce_mesic`    INTEGER     NOT NULL,
    `reakce_den`      INTEGER     NOT NULL,
    `reakce_hod`      INTEGER     NOT NULL,
    `reakce_min`      INTEGER     NOT NULL,
    `hotovo_mesic`    INTEGER     NOT NULL,
    `hotovo_den`      INTEGER     NOT NULL,
    `hotovo_hod`      INTEGER     NOT NULL,
    `hotovo_min`      INTEGER     NOT NULL,
    `cena_koeficient` float       NOT NULL,
    FOREIGN KEY (`tarif`) REFERENCES `tarif` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`priorita`) REFERENCES `priorita` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`typ_incident`) REFERENCES `typ_incident` (`id`) ON DELETE NO ACTION
);

INSERT INTO `sla` (`id`, `tarif`, `priorita`, `typ_incident`, `reakce_mesic`, `reakce_den`, `reakce_hod`, `reakce_min`,
                   `hotovo_mesic`, `hotovo_den`, `hotovo_hod`, `hotovo_min`, `cena_koeficient`)
VALUES (1, 6, 1, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (2, 6, 2, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.85),
       (3, 6, 3, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (4, 6, 4, 1, 2, 0, 0, 0, 5, 0, 0, 0, 1.25),
       (5, 6, 5, 1, 2, 0, 0, 0, 4, 0, 0, 0, 1.5),
       (6, 7, 1, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (7, 7, 2, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (8, 7, 3, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (9, 7, 4, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (10, 7, 5, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (11, 8, 1, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (12, 8, 2, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (13, 8, 3, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (14, 8, 4, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (15, 8, 5, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (16, 9, 1, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (17, 9, 2, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (18, 9, 3, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (19, 9, 4, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (20, 9, 5, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (21, 10, 1, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (22, 10, 2, 1, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (23, 10, 3, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (24, 10, 4, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (25, 10, 5, 1, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (26, 6, 1, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (27, 6, 2, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.85),
       (28, 6, 3, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (29, 6, 4, 2, 2, 0, 0, 0, 5, 0, 0, 0, 1.25),
       (30, 6, 5, 2, 2, 0, 0, 0, 4, 0, 0, 0, 1.5),
       (31, 7, 1, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (32, 7, 2, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (33, 7, 3, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (34, 7, 4, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (35, 7, 5, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (36, 8, 1, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (37, 8, 2, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (38, 8, 3, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (39, 8, 4, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (40, 8, 5, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (41, 9, 1, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (42, 9, 2, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (43, 9, 3, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (44, 9, 4, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (45, 9, 5, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (46, 10, 1, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (47, 10, 2, 2, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (48, 10, 3, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (49, 10, 4, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (50, 10, 5, 2, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (51, 6, 1, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (52, 6, 2, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.85),
       (53, 6, 3, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (54, 6, 4, 3, 2, 0, 0, 0, 5, 0, 0, 0, 1.25),
       (55, 6, 5, 3, 2, 0, 0, 0, 4, 0, 0, 0, 1.5),
       (56, 7, 1, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (57, 7, 2, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (58, 7, 3, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (59, 7, 4, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (60, 7, 5, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (61, 8, 1, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (62, 8, 2, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (63, 8, 3, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (64, 8, 4, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (65, 8, 5, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (66, 9, 1, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (67, 9, 2, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (68, 9, 3, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (69, 9, 4, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (70, 9, 5, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.5),
       (71, 10, 1, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.5),
       (72, 10, 2, 3, 3, 0, 0, 0, 6, 0, 0, 0, 0.75),
       (73, 10, 3, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1),
       (74, 10, 4, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.25),
       (75, 10, 5, 3, 3, 0, 0, 0, 6, 0, 0, 0, 1.5);

-- --------------------------------------------------------

--
-- Table structure for table `typ_change`
--

DROP TABLE IF EXISTS `typ_change`;
CREATE TABLE `typ_change`
(
    `id`    INTEGER      NOT NULL PRIMARY KEY,
    `nazev` varchar(100) NOT NULL
);

INSERT INTO `typ_change` (`id`, `nazev`)
VALUES (1, 'Standartni'),
       (2, 'Urgentní');

-- --------------------------------------------------------

--
-- Table structure for table `ukon`
--

DROP TABLE IF EXISTS `ukon`;
CREATE TABLE `ukon`
(
    `id`            INTEGER        NOT NULL PRIMARY KEY,
    `nazev`         varchar(255)   NOT NULL,
    `popis`         text           NOT NULL,
    `cena`          decimal(10, 2) NOT NULL,
    `cas_realizace` INTEGER        NOT NULL,
    `cas_reakce`    INTEGER        NOT NULL
);

INSERT INTO `ukon` (`id`, `nazev`, `popis`, `cena`, `cas_realizace`, `cas_reakce`)
VALUES (1, 'Web alarm', 'Web alarm', 100.00, 5184000, 2592000),
       (2, 'HTML - Tvorba nove stranky', 'Tvorba nove webove stranky', 300.00, 5184000, 2592000),
       (3, 'HTML - úprava stránky', 'Úpravy webové stránky', 199.00, 5184000, 2592000),
       (4, 'HTML+CSS Tvorba webové šablony - pouze HTML CSS', 'HTML+CSS Tvorba webové šablony - pouze HTML CSS',
        1000.00, 5184000, 2592000),
       (5, 'HTML+CSS Tvorba webové šablony - Nette', 'HTML+CSS Tvorba webové šablony - Nette', 2000.00, 5184000,
        2592000),
       (6, 'HTML+CSS Tvorba webové šablony - Drupal', 'HTML+CSS Tvorba webové šablony - Drupal', 3500.00, 5184000,
        2592000),
       (7, 'CSS - Úprava kaskadových stylů', 'CSS - Úprava kaskadových stylů', 250.00, 5184000, 2592000),
       (8, 'PHP - Nová funkce aplikace vytvorena třetí stranou',
        'PHP - Nová funkce aplikace, která byla vytvořena jinou firmou', 1000.00, 5184000, 2592000),
       (9, 'PHP - Nová funkce aplikace', 'PHP - Nová funkce aplikace', 600.00, 5184000, 2592000),
       (10, 'Drupal - aktualizace modulu', 'Aktualizace modulu v drupalu', 299.00, 5184000, 2592000),
       (11, 'Drupal - nová instalace', 'Drupal - nová instalace', 1500.00, 5184000, 2592000),
       (12, 'Drupal - Tvorba obsahu', 'Drupal - Vytvoření typu obsahu', 700.00, 5184000, 2592000),
       (13, 'PrestaShop - nová instalace', 'PrestaShop - nová instalace', 1500.00, 5184000, 2592000),
       (14, 'Piwic - nová instalace', 'Piwic - nová instalace', 1000.00, 5184000, 2592000),
       (15, 'Piwic - aktualizace', 'Piwic - aktualizace', 300.00, 5184000, 2592000),
       (16, 'Web aplikace - propojeni s web analytics', 'Web aplikace - propojeni s web analytics', 500.00, 5184000,
        2592000),
       (17, 'Drupal - instalace plug-in', 'Drupal - instalace plug-in', 300.00, 5184000, 2592000),
       (18, 'Drupal - plnění obsahu', 'Drupal - plnění obsahu', 300.00, 5184000, 2592000),
       (19, 'Drupal - nastavování plug-inu', 'Drupal - nastavování plug-inu', 400.00, 5184000, 2592000),
       (20, 'Drupal - programování modulu', 'Drupal - programování modulu', 3000.00, 5184000, 2592000),
       (21, 'PHP - Úprava funkce aplikace vytvorena třetí stranou',
        'PHP - Úprava funkce aplikace vytvorena třetí stranou', 500.00, 5184000, 2592000),
       (22, 'Webhosting vytvoření nového hostingu', 'Webhosting vytvoření nového hostingu', 500.00, 5184000, 2592000),
       (23, 'Drupal - migrace', 'Migrace drupalu na jiny webhosting', 750.00, 5184000, 2592000),
       (24, 'Databaze migrace', 'Databaze migrace', 499.00, 5184000, 2592000),
       (25, 'Web hosting nastaveni', 'Web hosting nastaveni', 199.00, 5184000, 2592000),
       (26, 'Drupal - uprava struktury obsahu', 'Uprava typu obsahu', 499.00, 5184000, 2592000),
       (27, 'Drupal - SEO nastaveni', 'Drupal SEO nastaveni', 299.00, 5184000, 2592000),
       (28, 'Drupal - aktualizace jádra', 'Drupal - aktualizace jádra', 799.00, 5184000, 2592000),
       (29, 'Drupal - instalace vzhledu', 'Instalace vzhledu do drupalu', 1000.00, 5184000, 2592000);

-- --------------------------------------------------------

--
-- Table structure for table `zpusob_uzavreni`
--

DROP TABLE IF EXISTS `zpusob_uzavreni`;
CREATE TABLE `zpusob_uzavreni`
(
    `id`              INTEGER      NOT NULL PRIMARY KEY,
    `nazev`           varchar(100) NOT NULL,
    `koeficient_cena` float        NOT NULL
);

INSERT INTO `zpusob_uzavreni` (`id`, `nazev`, `koeficient_cena`)
VALUES (1, 'Zpracováno', 1),
       (2, 'Žádna akce', 0.1),
       (3, 'Zamítnuto', 0.3);

COMMIT;

-- Enabling Foreign Key Support
PRAGMA foreign_keys = ON;