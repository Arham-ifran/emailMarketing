<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('countries')->delete();

        \DB::statement("INSERT INTO `countries`(`id`, `code`, `name`, `vat`, `apply_default_vat`, `status`, `created_at`, `updated_at`)
        VALUES (1,'AF','Afghanistan',0,0,1,NULL,'2020-11-16 12:27:16'),
            (2,'AL','Albania',0,0,1,NULL,NULL),
            (3,'DZ','Algeria',0,0,1,NULL,NULL),
            (4,'DS','American Samoa',0,0,1,NULL,NULL),
            (5,'AD','Andorra',0,0,1,NULL,NULL),
            (6,'AO','Angola',0,0,1,NULL,NULL),
            (7,'AI','Anguilla',0,0,1,NULL,NULL),
            (8,'AQ','Antarctica',0,0,1,NULL,NULL),
            (9,'AG','Antigua and Barbuda',0,0,1,NULL,NULL),
            (10,'AR','Argentina',0,0,1,NULL,NULL),
            (11,'AM','Armenia',0,0,1,NULL,NULL),
            (12,'AW','Aruba',0,0,1,NULL,NULL),
            (13,'AU','Australia',0,0,1,NULL,NULL),
            (14,'AT','Austria',20,0,1,NULL,'2020-11-04 05:48:45'),
            (15,'AZ','Azerbaijan',0,0,1,NULL,NULL),
            (16,'BS','Bahamas',0,0,1,NULL,NULL),
            (17,'BH','Bahrain',0,0,1,NULL,NULL),
            (18,'BD','Bangladesh',0,0,1,NULL,NULL),
            (19,'BB','Barbados',0,0,1,NULL,NULL),
            (20,'BY','Belarus',0,0,1,NULL,NULL),
            (21,'BE','Belgium',21,0,1,NULL,'2020-11-04 05:29:42'),
            (22,'BZ','Belize',0,0,1,NULL,NULL),
            (23,'BJ','Benin',0,0,1,NULL,NULL),
            (24,'BM','Bermuda',0,0,1,NULL,NULL),
            (25,'BT','Bhutan',0,0,1,NULL,NULL),
            (26,'BO','Bolivia',0,0,1,NULL,NULL),
            (27,'BA','Bosnia and Herzegovina',0,0,1,NULL,NULL),
            (28,'BW','Botswana',0,0,1,NULL,NULL),
            (29,'BV','Bouvet Island',0,0,1,NULL,NULL),
            (30,'BR','Brazil',0,0,1,NULL,NULL),
            (31,'IO','British Indian Ocean Territory',0,0,1,NULL,NULL),
            (32,'BN','Brunei Darussalam',0,0,1,NULL,NULL),
            (33,'BG','Bulgaria',20,0,1,NULL,'2020-11-04 05:30:17'),
            (34,'BF','Burkina Faso',0,0,1,NULL,NULL),
            (35,'BI','Burundi',0,0,1,NULL,NULL),
            (36,'KH','Cambodia',0,0,1,NULL,NULL),
            (37,'CM','Cameroon',0,0,1,NULL,NULL),
            (38,'CA','Canada',0,0,1,NULL,NULL),
            (39,'CV','Cape Verde',0,0,1,NULL,NULL),
            (40,'KY','Cayman Islands',0,0,1,NULL,NULL),
            (41,'CF','Central African Republic',0,0,1,NULL,NULL),
            (42,'TD','Chad',0,0,1,NULL,NULL),
            (43,'CL','Chile',0,0,1,NULL,NULL),
            (44,'CN','China',0,0,1,NULL,NULL),
            (45,'CX','Christmas Island',0,0,1,NULL,NULL),
            (46,'CC','Cocos (Keeling) Islands',0,0,1,NULL,NULL),
            (47,'CO','Colombia',0,0,1,NULL,NULL),
            (48,'KM','Comoros',0,0,1,NULL,NULL),
            (49,'CD','Democratic Republic of the Congo',0,0,1,NULL,NULL),
            (50,'CG','Republic of Congo',0,0,1,NULL,NULL),
            (51,'CK','Cook Islands',0,0,1,NULL,NULL),
            (52,'CR','Costa Rica',0,0,1,NULL,NULL),
            (53,'HR','Croatia (Hrvatska)',25,0,1,NULL,'2020-11-04 05:45:22'),
            (54,'CU','Cuba',0,0,1,NULL,NULL),
            (55,'CY','Cyprus',19,0,1,NULL,'2020-11-04 05:45:55'),
            (56,'CZ','Czech Republic',21,0,1,NULL,'2020-11-04 05:36:00'),
            (57,'DK','Denmark',25,0,1,NULL,'2020-11-04 05:36:19'),
            (58,'DJ','Djibouti',0,0,1,NULL,NULL),
            (59,'DM','Dominica',0,0,1,NULL,NULL),
            (60,'DO','Dominican Republic',0,0,1,NULL,NULL),
            (61,'TP','East Timor',0,0,1,NULL,NULL),
            (62,'EC','Ecuador',0,0,1,NULL,NULL),
            (63,'EG','Egypt',0,0,1,NULL,NULL),
            (64,'SV','El Salvador',0,0,1,NULL,NULL),
            (65,'GQ','Equatorial Guinea',0,0,1,NULL,NULL),
            (66,'ER','Eritrea',0,0,1,NULL,NULL),
            (67,'EE','Estonia',20,0,1,NULL,'2020-11-04 05:37:16'),
            (68,'ET','Ethiopia',0,0,1,NULL,NULL),
            (69,'FK','Falkland Islands (Malvinas)',0,0,1,NULL,NULL),
            (70,'FO','Faroe Islands',0,0,1,NULL,NULL),
            (71,'FJ','Fiji',0,0,1,NULL,NULL),
            (72,'FI','Finland',24,0,1,NULL,'2020-11-04 05:50:14'),
            (73,'FR','France',20,0,1,NULL,'2020-11-04 05:43:48'),
            (74,'FX','France, Metropolitan',20,0,1,NULL,'2020-11-04 05:45:14'),
            (75,'GF','French Guiana',0,0,1,NULL,NULL),
            (76,'PF','French Polynesia',0,0,1,NULL,NULL),
            (77,'TF','French Southern Territories',0,0,1,NULL,NULL),
            (78,'GA','Gabon',0,0,1,NULL,NULL),
            (79,'GM','Gambia',0,0,1,NULL,NULL),
            (80,'GE','Georgia',0,0,1,NULL,NULL),
            (81,'DE','Germany',19,0,1,NULL,'2020-11-04 05:36:51'),
            (82,'GH','Ghana',0,0,1,NULL,NULL),
            (83,'GI','Gibraltar',0,0,1,NULL,NULL),
            (84,'GK','Guernsey',0,0,1,NULL,NULL),
            (85,'GR','Greece',24,0,1,NULL,'2020-11-05 11:09:38'),
            (86,'GL','Greenland',0,0,1,NULL,NULL),
            (87,'GD','Grenada',0,0,1,NULL,NULL),
            (88,'GP','Guadeloupe',0,0,1,NULL,NULL),
            (89,'GU','Guam',0,0,1,NULL,NULL),
            (90,'GT','Guatemala',0,0,1,NULL,NULL),
            (91,'GN','Guinea',0,0,1,NULL,NULL),
            (92,'GW','Guinea-Bissau',0,0,1,NULL,NULL),
            (93,'GY','Guyana',0,0,1,NULL,NULL),
            (94,'HT','Haiti',0,0,1,NULL,NULL),
            (95,'HM','Heard and Mc Donald Islands',0,0,1,NULL,NULL),
            (96,'HN','Honduras',0,0,1,NULL,NULL),
            (97,'HK','Hong Kong',0,0,1,NULL,NULL),
            (98,'HU','Hungary',27,0,1,NULL,'2020-11-04 05:47:26'),
            (99,'IS','Iceland',0,0,1,NULL,NULL),
            (100,'IN','India',0,0,1,NULL,NULL),
            (101,'IM','Isle of Man',0,0,1,NULL,NULL),
            (102,'ID','Indonesia',0,0,1,NULL,NULL),
            (103,'IR','Iran (Islamic Republic of)',0,0,1,NULL,NULL),
            (104,'IQ','Iraq',0,0,1,NULL,NULL),
            (105,'IE','Ireland',23,0,1,NULL,'2020-11-18 10:27:00'),
            (106,'IL','Israel',0,0,1,NULL,NULL),
            (107,'IT','Italy',22,0,1,NULL,'2020-11-04 05:45:39'),
            (108,'CI','Ivory Coast',0,0,1,NULL,NULL),
            (109,'JE','Jersey',0,0,1,NULL,NULL),
            (110,'JM','Jamaica',0,0,1,NULL,NULL),
            (111,'JP','Japan',0,0,1,NULL,NULL),
            (112,'JO','Jordan',0,0,1,NULL,NULL),
            (113,'KZ','Kazakhstan',0,0,1,NULL,NULL),
            (114,'KE','Kenya',0,0,1,NULL,NULL),
            (115,'KI','Kiribati',0,0,1,NULL,NULL),
            (116,'KP','Korea, Democratic People\'s Republic of',0,0,1,NULL,NULL),
            (117,'KR','Korea, Republic of',0,0,1,NULL,NULL),
            (118,'XK','Kosovo',0,0,1,NULL,NULL),
            (119,'KW','Kuwait',0,0,1,NULL,NULL),
            (120,'KG','Kyrgyzstan',0,0,1,NULL,NULL),
            (121,'LA','Lao People\'s Democratic Republic',0,0,1,NULL,NULL),
            (122,'LV','Latvia',21,0,1,NULL,'2020-11-04 05:46:39'),
            (123,'LB','Lebanon',0,0,1,NULL,NULL),
            (124,'LS','Lesotho',0,0,1,NULL,NULL),
            (125,'LR','Liberia',0,0,1,NULL,NULL),
            (126,'LY','Libyan Arab Jamahiriya',0,0,1,NULL,NULL),
            (127,'LI','Liechtenstein',0,0,1,NULL,NULL),
            (128,'LT','Lithuania',21,0,1,NULL,'2020-11-04 05:46:55'),
            (129,'LU','Luxembourg',17,0,1,NULL,'2020-11-04 05:47:12'),
            (130,'MO','Macau',0,0,1,NULL,NULL),
            (131,'MK','North Macedonia',0,0,1,NULL,NULL),
            (132,'MG','Madagascar',0,0,1,NULL,NULL),
            (133,'MW','Malawi',0,0,1,NULL,NULL),
            (134,'MY','Malaysia',0,0,1,NULL,NULL),
            (135,'MV','Maldives',0,0,1,NULL,NULL),
            (136,'ML','Mali',0,0,1,NULL,NULL),
            (137,'MT','Malta',18,0,1,NULL,'2020-11-04 05:47:38'),
            (138,'MH','Marshall Islands',0,0,1,NULL,NULL),
            (139,'MQ','Martinique',0,0,1,NULL,NULL),
            (140,'MR','Mauritania',0,0,1,NULL,NULL),
            (141,'MU','Mauritius',0,0,1,NULL,NULL),
            (142,'TY','Mayotte',0,0,1,NULL,NULL),
            (143,'MX','Mexico',0,0,1,NULL,NULL),
            (144,'FM','Micronesia, Federated States of',0,0,1,NULL,NULL),
            (145,'MD','Moldova, Republic of',0,0,1,NULL,NULL),
            (146,'MC','Monaco',0,0,1,NULL,NULL),
            (147,'MN','Mongolia',0,0,1,NULL,NULL),
            (148,'ME','Montenegro',0,0,1,NULL,NULL),
            (149,'MS','Montserrat',0,0,1,NULL,NULL),
            (150,'MA','Morocco',0,0,1,NULL,NULL),
            (151,'MZ','Mozambique',0,0,1,NULL,NULL),
            (152,'MM','Myanmar',0,0,1,NULL,NULL),
            (153,'NA','Namibia',0,0,1,NULL,NULL),
            (154,'NR','Nauru',0,0,1,NULL,NULL),
            (155,'NP','Nepal',0,0,1,NULL,NULL),
            (156,'NL','Netherlands',21,0,1,NULL,'2020-11-04 05:48:18'),
            (157,'AN','Netherlands Antilles',21,0,1,NULL,'2020-11-04 05:48:21'),
            (158,'NC','New Caledonia',0,0,1,NULL,NULL),
            (159,'NZ','New Zealand',0,0,1,NULL,NULL),
            (160,'NI','Nicaragua',0,0,1,NULL,NULL),
            (161,'NE','Niger',0,0,1,NULL,NULL),
            (162,'NG','Nigeria',0,0,1,NULL,NULL),
            (163,'NU','Niue',0,0,1,NULL,NULL),
            (164,'NF','Norfolk Island',0,0,1,NULL,NULL),
            (165,'MP','Northern Mariana Islands',0,0,1,NULL,NULL),
            (166,'NO','Norway',0,0,1,NULL,NULL),
            (167,'OM','Oman',0,0,1,NULL,NULL),
            (168,'PK','Pakistan',0,0,1,NULL,'2020-11-16 08:14:04'),
            (169,'PW','Palau',0,0,1,NULL,NULL),
            (170,'PS','Palestine',0,0,1,NULL,NULL),
            (171,'PA','Panama',0,0,1,NULL,NULL),
            (172,'PG','Papua New Guinea',0,0,1,NULL,NULL),
            (173,'PY','Paraguay',0,0,1,NULL,NULL),
            (174,'PE','Peru',0,0,1,NULL,NULL),
            (175,'PH','Philippines',0,0,1,NULL,NULL),
            (176,'PN','Pitcairn',0,0,1,NULL,NULL),
            (177,'PL','Poland',23,0,1,NULL,'2020-11-04 05:48:57'),
            (178,'PT','Portugal',23,0,1,NULL,'2020-11-04 05:49:15'),
            (179,'PR','Puerto Rico',0,0,1,NULL,NULL),
            (180,'QA','Qatar',0,0,1,NULL,NULL),
            (181,'RE','Reunion',0,0,1,NULL,NULL),
            (182,'RO','Romania',19,0,1,NULL,'2020-11-04 05:49:32'),
            (183,'RU','Russian Federation',0,0,1,NULL,NULL),
            (184,'RW','Rwanda',0,0,1,NULL,NULL),
            (185,'KN','Saint Kitts and Nevis',0,0,1,NULL,NULL),
            (186,'LC','Saint Lucia',0,0,1,NULL,NULL),
            (187,'VC','Saint Vincent and the Grenadines',0,0,1,NULL,NULL),
            (188,'WS','Samoa',0,0,1,NULL,NULL),
            (189,'SM','San Marino',0,0,1,NULL,NULL),
            (190,'ST','Sao Tome and Principe',0,0,1,NULL,NULL),
            (191,'SA','Saudi Arabia',0,0,1,NULL,NULL),
            (192,'SN','Senegal',0,0,1,NULL,NULL),
            (193,'RS','Serbia',0,0,1,NULL,NULL),
            (194,'SC','Seychelles',0,0,1,NULL,NULL),
            (195,'SL','Sierra Leone',0,0,1,NULL,NULL),
            (196,'SG','Singapore',0,0,1,NULL,NULL),
            (197,'SK','Slovakia',20,0,1,NULL,'2020-11-04 05:50:01'),
            (198,'SI','Slovenia',22,0,1,NULL,'2020-11-04 05:49:43'),
            (199,'SB','Solomon Islands',0,0,1,NULL,NULL),
            (200,'SO','Somalia',0,0,1,NULL,NULL),
            (201,'ZA','South Africa',0,0,1,NULL,NULL),
            (202,'GS','South Georgia South Sandwich Islands',0,0,1,NULL,NULL),
            (203,'SS','South Sudan',0,0,1,NULL,NULL),
            (204,'ES','Spain',21,0,1,NULL,'2020-11-04 05:43:15'),
            (205,'LK','Sri Lanka',0,0,1,NULL,NULL),
            (206,'SH','St. Helena',0,0,1,NULL,NULL),
            (207,'PM','St. Pierre and Miquelon',0,0,1,NULL,NULL),
            (208,'SD','Sudan',0,0,1,NULL,NULL),
            (209,'SR','Suriname',0,0,1,NULL,NULL),
            (210,'SJ','Svalbard and Jan Mayen Islands',0,0,1,NULL,NULL),
            (211,'SZ','Swaziland',0,0,1,NULL,NULL),
            (212,'SE','Sweden',25,0,1,NULL,'2020-11-04 05:50:27'),
            (213,'CH','Switzerland',0,0,1,NULL,NULL),
            (214,'SY','Syrian Arab Republic',0,0,1,NULL,NULL),
            (215,'TW','Taiwan',0,0,1,NULL,NULL),
            (216,'TJ','Tajikistan',0,0,1,NULL,NULL),
            (217,'TZ','Tanzania, United Republic of',0,0,1,NULL,NULL),
            (218,'TH','Thailand',0,0,1,NULL,NULL),
            (219,'TG','Togo',0,0,1,NULL,NULL),
            (220,'TK','Tokelau',0,0,1,NULL,NULL),
            (221,'TO','Tonga',0,0,1,NULL,NULL),
            (222,'TT','Trinidad and Tobago',0,0,1,NULL,NULL),
            (223,'TN','Tunisia',0,0,1,NULL,NULL),
            (224,'TR','Turkey',0,0,1,NULL,NULL),
            (225,'TM','Turkmenistan',0,0,1,NULL,NULL),
            (226,'TC','Turks and Caicos Islands',0,0,1,NULL,NULL),
            (227,'TV','Tuvalu',0,0,1,NULL,NULL),
            (228,'UG','Uganda',0,0,1,NULL,NULL),
            (229,'UA','Ukraine',0,0,1,NULL,NULL),
            (230,'AE','United Arab Emirates',0,0,1,NULL,NULL),
            (231,'GB','United Kingdom',20,0,1,NULL,'2020-11-04 05:50:56'),
            (232,'US','United States',0,0,1,NULL,NULL),
            (233,'UM','United States minor outlying islands',0,0,1,NULL,NULL),
            (234,'UY','Uruguay',0,0,1,NULL,NULL),
            (235,'UZ','Uzbekistan',0,0,1,NULL,NULL),
            (236,'VU','Vanuatu',0,0,1,NULL,NULL),
            (237,'VA','Vatican City State',0,0,1,NULL,NULL),
            (238,'VE','Venezuela',0,0,1,NULL,NULL),
            (239,'VN','Vietnam',0,0,1,NULL,NULL),
            (240,'VG','Virgin Islands (British)',0,0,1,NULL,NULL),
            (241,'VI','Virgin Islands (U.S.)',0,0,1,NULL,NULL),
            (242,'WF','Wallis and Futuna Islands',0,0,1,NULL,NULL),
            (243,'EH','Western Sahara',0,0,1,NULL,NULL),
            (244,'YE','Yemen',0,0,1,NULL,NULL),
            (245,'ZM','Zambia',0,0,1,NULL,NULL),
            (246,'ZW','Zimbabwe',0,0,1,NULL,NULL),
            (247,'QS','Qustuntunia',15,0,1,NULL,'2020-12-16 07:08:59');
        ");
    }
}
