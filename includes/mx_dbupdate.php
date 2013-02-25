<?php
/* ---
 * Project: musxpand
 * File:    mx_dbupdate.php
 * Author:  phil
 * Date:    Apr 15, 2011
 * ---
 * License:

    This file is part of musxpand.

    musxpand is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    musxpand is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with musxpand.  If not, see <http://www.gnu.org/licenses/>.

    Copyright � 2010 by Philippe Hilger
 */

$db_countries="
INSERT INTO mx_countries (cc_fips, cc_iso, tld, country_name) VALUES
('AA', 'AW', '.aw', 'Aruba'),
('AC', 'AG', '.ag', 'Antigua and Barbuda'),
('AE', 'AE', '.ae', 'United Arab Emirates'),
('AF', 'AF', '.af', 'Afghanistan'),
('AG', 'DZ', '.dz', 'Algeria'),
('AJ', 'AZ', '.az', 'Azerbaijan'),
('AL', 'AL', '.al', 'Albania'),
('AM', 'AM', '.am', 'Armenia'),
('AN', 'AD', '.ad', 'Andorra'),
('AO', 'AO', '.ao', 'Angola'),
('AQ', 'AS', '.as', 'American Samoa'),
('AR', 'AR', '.ar', 'Argentina'),
('AS', 'AU', '.au', 'Australia'),
('AT', '-', '-', 'Ashmore and Cartier Islands'),
('AU', 'AT', '.at', 'Austria'),
('AV', 'AI', '.ai', 'Anguilla'),
('AX', 'AX', '.ax', 'Åland Islands'),
('AY', 'AQ', '.aq', 'Antarctica'),
('BA', 'BH', '.bh', 'Bahrain'),
('BB', 'BB', '.bb', 'Barbados'),
('BC', 'BW', '.bw', 'Botswana'),
('BD', 'BM', '.bm', 'Bermuda'),
('BE', 'BE', '.be', 'Belgium'),
('BF', 'BS', '.bs', 'Bahamas, The'),
('BG', 'BD', '.bd', 'Bangladesh'),
('BH', 'BZ', '.bz', 'Belize'),
('BK', 'BA', '.ba', 'Bosnia and Herzegovina'),
('BL', 'BO', '.bo', 'Bolivia'),
('BM', 'MM', '.mm', 'Myanmar'),
('BN', 'BJ', '.bj', 'Benin'),
('BO', 'BY', '.by', 'Belarus'),
('BP', 'SB', '.sb', 'Solomon Islands'),
('BQ', '-', '-', 'Navassa Island'),
('BR', 'BR', '.br', 'Brazil'),
('BS', '-', '-', 'Bassas da India'),
('BT', 'BT', '.bt', 'Bhutan'),
('BU', 'BG', '.bg', 'Bulgaria'),
('BV', 'BV', '.bv', 'Bouvet Island'),
('BX', 'BN', '.bn', 'Brunei'),
('BY', 'BI', '.bi', 'Burundi'),
('CA', 'CA', '.ca', 'Canada'),
('CB', 'KH', '.kh', 'Cambodia'),
('CD', 'TD', '.td', 'Chad'),
('CE', 'LK', '.lk', 'Sri Lanka'),
('CF', 'CG', '.cg', 'Congo, Republic of the'),
('CG', 'CD', '.cd', 'Congo, Democratic Republic of the'),
('CH', 'CN', '.cn', 'China'),
('CI', 'CL', '.cl', 'Chile'),
('CJ', 'KY', '.ky', 'Cayman Islands'),
('CK', 'CC', '.cc', 'Cocos (Keeling) Islands'),
('CM', 'CM', '.cm', 'Cameroon'),
('CN', 'KM', '.km', 'Comoros'),
('CO', 'CO', '.co', 'Colombia'),
('CQ', 'MP', '.mp', 'Northern Mariana Islands'),
('CR', '-', '-', 'Coral Sea Islands'),
('CS', 'CR', '.cr', 'Costa Rica'),
('CT', 'CF', '.cf', 'Central African Republic'),
('CU', 'CU', '.cu', 'Cuba'),
('CV', 'CV', '.cv', 'Cape Verde'),
('CW', 'CK', '.ck', 'Cook Islands'),
('CY', 'CY', '.cy', 'Cyprus'),
('DA', 'DK', '.dk', 'Denmark'),
('DJ', 'DJ', '.dj', 'Djibouti'),
('DO', 'DM', '.dm', 'Dominica'),
('DQ', 'UM', '-', 'Jarvis Island'),
('DR', 'DO', '.do', 'Dominican Republic'),
('DX', '-', '-', 'Dhekelia Sovereign Base Area'),
('EC', 'EC', '.ec', 'Ecuador'),
('EG', 'EG', '.eg', 'Egypt'),
('EI', 'IE', '.ie', 'Ireland'),
('EK', 'GQ', '.gq', 'Equatorial Guinea'),
('EN', 'EE', '.ee', 'Estonia'),
('ER', 'ER', '.er', 'Eritrea'),
('ES', 'SV', '.sv', 'El Salvador'),
('ET', 'ET', '.et', 'Ethiopia'),
('EU', '-', '-', 'Europa Island'),
('EZ', 'CZ', '.cz', 'Czech Republic'),
('FG', 'GF', '.gf', 'French Guiana'),
('FI', 'FI', '.fi', 'Finland'),
('FJ', 'FJ', '.fj', 'Fiji'),
('FK', 'FK', '.fk', 'Falkland Islands (Islas Malvinas)'),
('FM', 'FM', '.fm', 'Micronesia, Federated States of'),
('FO', 'FO', '.fo', 'Faroe Islands'),
('FP', 'PF', '.pf', 'French Polynesia'),
('FQ', 'UM', '-', 'Baker Island'),
('FR', 'FR', '.fr', 'France'),
('FS', 'TF', '.tf', 'French Southern and Antarctic Lands'),
('GA', 'GM', '.gm', 'Gambia, The'),
('GB', 'GA', '.ga', 'Gabon'),
('GG', 'GE', '.ge', 'Georgia'),
('GH', 'GH', '.gh', 'Ghana'),
('GI', 'GI', '.gi', 'Gibraltar'),
('GJ', 'GD', '.gd', 'Grenada'),
('GK', '-', '.gg', 'Guernsey'),
('GL', 'GL', '.gl', 'Greenland'),
('GM', 'DE', '.de', 'Germany'),
('GO', '-', '-', 'Glorioso Islands'),
('GP', 'GP', '.gp', 'Guadeloupe'),
('GQ', 'GU', '.gu', 'Guam'),
('GR', 'GR', '.gr', 'Greece'),
('GT', 'GT', '.gt', 'Guatemala'),
('GV', 'GN', '.gn', 'Guinea'),
('GY', 'GY', '.gy', 'Guyana'),
('GZ', '-', '-', 'Gaza Strip'),
('HA', 'HT', '.ht', 'Haiti'),
('HK', 'HK', '.hk', 'Hong Kong'),
('HM', 'HM', '.hm', 'Heard Island and McDonald Islands'),
('HO', 'HN', '.hn', 'Honduras'),
('HQ', 'UM', '-', 'Howland Island'),
('HR', 'HR', '.hr', 'Croatia'),
('HU', 'HU', '.hu', 'Hungary'),
('IC', 'IS', '.is', 'Iceland'),
('ID', 'ID', '.id', 'Indonesia'),
('IM', 'IM', '.im', 'Isle of Man'),
('IN', 'IN', '.in', 'India'),
('IO', 'IO', '.io', 'British Indian Ocean Territory'),
('IP', '-', '-', 'Clipperton Island'),
('IR', 'IR', '.ir', 'Iran'),
('IS', 'IL', '.il', 'Israel'),
('IT', 'IT', '.it', 'Italy'),
('IV', 'CI', '.ci', 'Cote d''Ivoire'),
('IZ', 'IQ', '.iq', 'Iraq'),
('JA', 'JP', '.jp', 'Japan'),
('JE', 'JE', '.je', 'Jersey'),
('JM', 'JM', '.jm', 'Jamaica'),
('JN', 'SJ', '-', 'Jan Mayen'),
('JO', 'JO', '.jo', 'Jordan'),
('JQ', 'UM', '-', 'Johnston Atoll'),
('JU', '-', '-', 'Juan de Nova Island'),
('KE', 'KE', '.ke', 'Kenya'),
('KG', 'KG', '.kg', 'Kyrgyzstan'),
('KN', 'KP', '.kp', 'Korea, North'),
('KQ', 'UM', '-', 'Kingman Reef'),
('KR', 'KI', '.ki', 'Kiribati'),
('KS', 'KR', '.kr', 'Korea, South'),
('KT', 'CX', '.cx', 'Christmas Island'),
('KU', 'KW', '.kw', 'Kuwait'),
('KV', 'KV', '-', 'Kosovo'),
('KZ', 'KZ', '.kz', 'Kazakhstan'),
('LA', 'LA', '.la', 'Laos'),
('LE', 'LB', '.lb', 'Lebanon'),
('LG', 'LV', '.lv', 'Latvia'),
('LH', 'LT', '.lt', 'Lithuania'),
('LI', 'LR', '.lr', 'Liberia'),
('LO', 'SK', '.sk', 'Slovakia'),
('LQ', 'UM', '-', 'Palmyra Atoll'),
('LS', 'LI', '.li', 'Liechtenstein'),
('LT', 'LS', '.ls', 'Lesotho'),
('LU', 'LU', '.lu', 'Luxembourg'),
('LY', 'LY', '.ly', 'Libyan Arab'),
('MA', 'MG', '.mg', 'Madagascar'),
('MB', 'MQ', '.mq', 'Martinique'),
('MC', 'MO', '.mo', 'Macau'),
('MD', 'MD', '.md', 'Moldova, Republic of'),
('MF', 'YT', '.yt', 'Mayotte'),
('MG', 'MN', '.mn', 'Mongolia'),
('MH', 'MS', '.ms', 'Montserrat'),
('MI', 'MW', '.mw', 'Malawi'),
('MJ', 'ME', '.me', 'Montenegro'),
('MK', 'MK', '.mk', 'The Former Yugoslav Republic of Macedonia'),
('ML', 'ML', '.ml', 'Mali'),
('MN', 'MC', '.mc', 'Monaco'),
('MO', 'MA', '.ma', 'Morocco'),
('MP', 'MU', '.mu', 'Mauritius'),
('MQ', 'UM', '-', 'Midway Islands'),
('MR', 'MR', '.mr', 'Mauritania'),
('MT', 'MT', '.mt', 'Malta'),
('MU', 'OM', '.om', 'Oman'),
('MV', 'MV', '.mv', 'Maldives'),
('MX', 'MX', '.mx', 'Mexico'),
('MY', 'MY', '.my', 'Malaysia'),
('MZ', 'MZ', '.mz', 'Mozambique'),
('NC', 'NC', '.nc', 'New Caledonia'),
('NE', 'NU', '.nu', 'Niue'),
('NF', 'NF', '.nf', 'Norfolk Island'),
('NG', 'NE', '.ne', 'Niger'),
('NH', 'VU', '.vu', 'Vanuatu'),
('NI', 'NG', '.ng', 'Nigeria'),
('NL', 'NL', '.nl', 'Netherlands'),
('NM', '', '', 'No Man''s Land'),
('NO', 'NO', '.no', 'Norway'),
('NP', 'NP', '.np', 'Nepal'),
('NR', 'NR', '.nr', 'Nauru'),
('NS', 'SR', '.sr', 'Suriname'),
('NT', 'AN', '.an', 'Netherlands Antilles'),
('NU', 'NI', '.ni', 'Nicaragua'),
('NZ', 'NZ', '.nz', 'New Zealand'),
('PA', 'PY', '.py', 'Paraguay'),
('PC', 'PN', '.pn', 'Pitcairn Islands'),
('PE', 'PE', '.pe', 'Peru'),
('PF', '-', '-', 'Paracel Islands'),
('PG', '-', '-', 'Spratly Islands'),
('PK', 'PK', '.pk', 'Pakistan'),
('PL', 'PL', '.pl', 'Poland'),
('PM', 'PA', '.pa', 'Panama'),
('PO', 'PT', '.pt', 'Portugal'),
('PP', 'PG', '.pg', 'Papua New Guinea'),
('PS', 'PW', '.pw', 'Palau'),
('PU', 'GW', '.gw', 'Guinea-Bissau'),
('QA', 'QA', '.qa', 'Qatar'),
('RE', 'RE', '.re', 'Reunion'),
('RI', 'RS', '.rs', 'Serbia'),
('RM', 'MH', '.mh', 'Marshall Islands'),
('RN', 'MF', '-', 'Saint Martin'),
('RO', 'RO', '.ro', 'Romania'),
('RP', 'PH', '.ph', 'Philippines'),
('RQ', 'PR', '.pr', 'Puerto Rico'),
('RS', 'RU', '.ru', 'Russia'),
('RW', 'RW', '.rw', 'Rwanda'),
('SA', 'SA', '.sa', 'Saudi Arabia'),
('SB', 'PM', '.pm', 'Saint Pierre and Miquelon'),
('SC', 'KN', '.kn', 'Saint Kitts and Nevis'),
('SE', 'SC', '.sc', 'Seychelles'),
('SF', 'ZA', '.za', 'South Africa'),
('SG', 'SN', '.sn', 'Senegal'),
('SH', 'SH', '.sh', 'Saint Helena'),
('SI', 'SI', '.si', 'Slovenia'),
('SL', 'SL', '.sl', 'Sierra Leone'),
('SM', 'SM', '.sm', 'San Marino'),
('SN', 'SG', '.sg', 'Singapore'),
('SO', 'SO', '.so', 'Somalia'),
('SP', 'ES', '.es', 'Spain'),
('ST', 'LC', '.lc', 'Saint Lucia'),
('SU', 'SD', '.sd', 'Sudan'),
('SV', 'SJ', '.sj', 'Svalbard'),
('SW', 'SE', '.se', 'Sweden'),
('SX', 'GS', '.gs', 'South Georgia and the Islands'),
('SY', 'SY', '.sy', 'Syrian Arab Republic'),
('SZ', 'CH', '.ch', 'Switzerland'),
('TD', 'TT', '.tt', 'Trinidad and Tobago'),
('TE', '-', '-', 'Tromelin Island'),
('TH', 'TH', '.th', 'Thailand'),
('TI', 'TJ', '.tj', 'Tajikistan'),
('TK', 'TC', '.tc', 'Turks and Caicos Islands'),
('TL', 'TK', '.tk', 'Tokelau'),
('TN', 'TO', '.to', 'Tonga'),
('TO', 'TG', '.tg', 'Togo'),
('TP', 'ST', '.st', 'Sao Tome and Principe'),
('TS', 'TN', '.tn', 'Tunisia'),
('TT', 'TL', '.tl', 'East Timor'),
('TU', 'TR', '.tr', 'Turkey'),
('TV', 'TV', '.tv', 'Tuvalu'),
('TW', 'TW', '.tw', 'Taiwan'),
('TX', 'TM', '.tm', 'Turkmenistan'),
('TZ', 'TZ', '.tz', 'Tanzania, United Republic of'),
('UG', 'UG', '.ug', 'Uganda'),
('UK', 'GB', '.uk', 'United Kingdom'),
('UP', 'UA', '.ua', 'Ukraine'),
('US', 'US', '.us', 'United States'),
('UV', 'BF', '.bf', 'Burkina Faso'),
('UY', 'UY', '.uy', 'Uruguay'),
('UZ', 'UZ', '.uz', 'Uzbekistan'),
('VC', 'VC', '.vc', 'Saint Vincent and the Grenadines'),
('VE', 'VE', '.ve', 'Venezuela'),
('VI', 'VG', '.vg', 'British Virgin Islands'),
('VM', 'VN', '.vn', 'Vietnam'),
('VQ', 'VI', '.vi', 'Virgin Islands (US)'),
('VT', 'VA', '.va', 'Holy See (Vatican City)'),
('WA', 'NA', '.na', 'Namibia'),
('WE', '-', '-', 'West Bank'),
('WF', 'WF', '.wf', 'Wallis and Futuna'),
('WI', 'EH', '.eh', 'Western Sahara'),
('WQ', 'UM', '-', 'Wake Island'),
('WS', 'WS', '.ws', 'Samoa'),
('WZ', 'SZ', '.sz', 'Swaziland'),
('YI', 'CS', '.yu', 'Serbia and Montenegro'),
('YM', 'YE', '.ye', 'Yemen'),
('ZA', 'ZM', '.zm', 'Zambia'),
('ZI', 'ZW', '.zw', 'Zimbabwe')
";

$db_tables="
-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 23, 2012 at 03:41 AM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

--
-- Database: `musxpand`
--

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2acc`
--

CREATE TABLE IF NOT EXISTS `mx_acc2acc` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account1_id` bigint(20) NOT NULL,
  `account2_id` bigint(20) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '100',
  `role2` tinyint(4) DEFAULT NULL,
  `role3` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role` (`role`),
  KEY `account1_id` (`account1_id`),
  KEY `account2_id` (`account2_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2arc`
--

CREATE TABLE IF NOT EXISTS `mx_acc2arc` (
  `account_id` bigint(20) NOT NULL,
  `archi_id` int(11) NOT NULL,
  `role_id` smallint(6) NOT NULL,
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `role_id` (`role_id`),
  KEY `archi_id` (`archi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='links between accounts and arquipelagos + roles' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2gen`
--

CREATE TABLE IF NOT EXISTS `mx_acc2gen` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `genre` smallint(6) NOT NULL,
  `position` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `genre` (`genre`),
  KEY `userid` (`userid`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2isl`
--

CREATE TABLE IF NOT EXISTS `mx_acc2isl` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL,
  `island_id` bigint(20) NOT NULL,
  `role_id` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `account_id` (`account_id`),
  KEY `island_id` (`island_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='links between accounts, islands and roles' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2loc`
--

CREATE TABLE IF NOT EXISTS `mx_acc2loc` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `accountid` bigint(20) NOT NULL,
  `locationid` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2play`
--

CREATE TABLE IF NOT EXISTS `mx_acc2play` (
  `id` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `hour` tinyint(4) NOT NULL,
  `playtime` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `date` (`date`),
  KEY `hour` (`hour`),
  KEY `playtime` (`playtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='hours stats per accounts';

-- --------------------------------------------------------

--
-- Table structure for table `mx_acc2tast`
--

CREATE TABLE IF NOT EXISTS `mx_acc2tast` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `genre` smallint(6) NOT NULL,
  `position` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `genre` (`genre`),
  KEY `userid` (`userid`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_account`
--

CREATE TABLE IF NOT EXISTS `mx_account` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `privpublic` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privfriends` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privartists` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privfans` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwdhash` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hashdir` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'hash for storage',
  `shortbio` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longbio` text COLLATE utf8_unicode_ci,
  `acctype` tinyint(4) NOT NULL COMMENT 'fan, artist,...',
  `status` tinyint(4) NOT NULL COMMENT 'account status (disabled...)',
  `fbid` bigint(20) DEFAULT NULL COMMENT 'FB user id',
  `firstname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `birthdate` date NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fbverified` tinyint(1) DEFAULT NULL,
  `island_id` bigint(20) DEFAULT NULL,
  `archi_id` int(11) DEFAULT NULL,
  `background_id` bigint(20) DEFAULT NULL,
  `transparency` int(11) DEFAULT NULL,
  `confirmationcode` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `artistname` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acccreation` date DEFAULT NULL COMMENT 'account creation date',
  `msgnotif` tinyint(4) DEFAULT NULL,
  `reqnotif` tinyint(4) DEFAULT NULL,
  `lastseen` datetime DEFAULT NULL,
  `PROid` bigint(20) NOT NULL,
  `PROmemberid` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `agreement` datetime DEFAULT NULL,
  `invites` smallint(6) NOT NULL,
  `invitecode` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referrer` bigint(20) DEFAULT NULL,
  `featured` datetime NOT NULL COMMENT 'feature expiration date',
  `sponsored` tinyint(4) DEFAULT NULL COMMENT 'ad-sponsored account',
  `badges` tinyint(4) NOT NULL,
  `modules` text COLLATE utf8_unicode_ci,
  `mxfeatures` int(11) NOT NULL,
  `approved` tinyint(4) NOT NULL COMMENT 'artist approval status',
  `isapro` tinyint(4) NOT NULL COMMENT 'registered with a PRO',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `country` (`country`),
  KEY `state` (`state`),
  KEY `city` (`city`),
  KEY `hashdir` (`hashdir`),
  KEY `acctype` (`acctype`),
  KEY `status` (`status`),
  KEY `fbid` (`fbid`),
  KEY `locale` (`locale`),
  KEY `birthdate` (`birthdate`),
  KEY `gender` (`gender`),
  KEY `lastname` (`lastname`),
  KEY `firstname` (`firstname`),
  KEY `fullname` (`fullname`),
  KEY `archi_id` (`archi_id`),
  KEY `privacy` (`privpublic`),
  KEY `background_id` (`background_id`),
  KEY `transparency` (`transparency`),
  KEY `island_id` (`island_id`),
  KEY `confirmationcode` (`confirmationcode`),
  KEY `username` (`username`),
  KEY `artistname` (`artistname`),
  KEY `acccreation` (`acccreation`),
  KEY `lastseen` (`lastseen`),
  KEY `invitecode` (`invitecode`),
  KEY `referrer` (`referrer`),
  KEY `invites` (`invites`),
  KEY `featured` (`featured`),
  KEY `sponsored` (`sponsored`),
  KEY `badges` (`badges`),
  KEY `mxfeatures` (`mxfeatures`),
  KEY `approved` (`approved`),
  KEY `isapro` (`isapro`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9592 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_address`
--

CREATE TABLE IF NOT EXISTS `mx_address` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `addresstype` tinyint(4) NOT NULL,
  `accountid` bigint(20) NOT NULL,
  `cartid` bigint(20) NOT NULL,
  `salutation` varchar(10) NOT NULL,
  `first` varchar(30) NOT NULL,
  `middle` varchar(30) NOT NULL,
  `last` varchar(40) NOT NULL,
  `suffix` varchar(12) NOT NULL,
  `business` varchar(127) NOT NULL,
  `shiptoname` varchar(32) NOT NULL,
  `street1` varchar(100) NOT NULL,
  `street2` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(40) NOT NULL,
  `countrycode` varchar(3) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `addressstatus` varchar(15) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(127) NOT NULL,
  `pppayerid` varchar(13) NOT NULL,
  `pppayerstatus` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `first` (`first`),
  KEY `middle` (`middle`),
  KEY `last` (`last`),
  KEY `shiptoname` (`shiptoname`),
  KEY `city` (`city`),
  KEY `state` (`state`),
  KEY `countrycode` (`countrycode`),
  KEY `zip` (`zip`),
  KEY `addressstatus` (`addressstatus`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `pppayerid` (`pppayerid`),
  KEY `pppayerstatus` (`pppayerstatus`),
  KEY `business` (`business`),
  KEY `accountid` (`accountid`),
  KEY `addrestype` (`addresstype`),
  KEY `cartid` (`cartid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=188 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_ads`
--

CREATE TABLE IF NOT EXISTS `mx_ads` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `content` (`content`),
  KEY `link` (`link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='text advertisements' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_apiconsumers`
--

CREATE TABLE IF NOT EXISTS `mx_apiconsumers` (
  `id` mediumint(9) NOT NULL,
  `key` varchar(32) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0: ok - 1:throttled - 2:revoked',
  `name` varchar(30) NOT NULL,
  `description` tinytext NOT NULL,
  `website` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `secret` (`secret`),
  KEY `name` (`name`),
  KEY `website` (`website`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx_apitokens`
--

CREATE TABLE IF NOT EXISTS `mx_apitokens` (
  `id` bigint(20) NOT NULL,
  `token_key` varchar(32) NOT NULL,
  `token_secret` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `token_key` (`token_key`),
  KEY `token_secret` (`token_secret`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx_archipelago`
--

CREATE TABLE IF NOT EXISTS `mx_archipelago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `artist_id` bigint(20) DEFAULT NULL,
  `open` tinyint(1) NOT NULL,
  `maxislands` smallint(6) DEFAULT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `z` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `artist_id` (`artist_id`),
  KEY `open` (`open`),
  KEY `maxislands` (`maxislands`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_arcrole`
--

CREATE TABLE IF NOT EXISTS `mx_arcrole` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_background`
--

CREATE TABLE IF NOT EXISTS `mx_background` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `media_id` bigint(20) NOT NULL,
  `artist_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `artist_id` (`artist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_cart`
--

CREATE TABLE IF NOT EXISTS `mx_cart` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoicenum` varchar(20) NOT NULL,
  `accountid` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(4) NOT NULL,
  `transactionid` varchar(30) NOT NULL,
  `receiptid` varchar(30) NOT NULL,
  `ordertime` datetime NOT NULL,
  `paymentstatus` varchar(30) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `taxes` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `paypalfee` decimal(10,2) NOT NULL,
  `finalamount` decimal(10,2) NOT NULL,
  `exchangerate` decimal(10,3) NOT NULL,
  `pendingreason` varchar(30) NOT NULL,
  `reasoncode` varchar(30) NOT NULL,
  `token` varchar(30) NOT NULL,
  `payerid` varchar(20) NOT NULL,
  `billingid` bigint(20) NOT NULL,
  `shippingid` bigint(20) NOT NULL,
  `taxcountrycode` varchar(3) NOT NULL,
  `statusstamp` datetime DEFAULT NULL,
  `memo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `accountid` (`accountid`),
  KEY `status` (`status`),
  KEY `transactionid` (`transactionid`),
  KEY `ordertime` (`ordertime`),
  KEY `total` (`total`),
  KEY `taxes` (`taxes`),
  KEY `currency` (`currency`),
  KEY `paypalfee` (`paypalfee`),
  KEY `finalamount` (`finalamount`),
  KEY `exchangerate` (`exchangerate`),
  KEY `pendingreason` (`pendingreason`),
  KEY `reasoncode` (`reasoncode`),
  KEY `token` (`token`),
  KEY `payerid` (`payerid`),
  KEY `billingid` (`billingid`),
  KEY `shippingid` (`shippingid`),
  KEY `paymentstatus` (`paymentstatus`),
  KEY `taxcountrycode` (`taxcountrycode`),
  KEY `receiptid` (`receiptid`),
  KEY `statusstamp` (`statusstamp`),
  KEY `invoicenum` (`invoicenum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='one cart' AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_cartline`
--

CREATE TABLE IF NOT EXISTS `mx_cartline` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cartid` bigint(20) NOT NULL,
  `prodtype` tinyint(4) NOT NULL,
  `prodref` bigint(20) NOT NULL,
  `prodvar` tinyint(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cartid` (`cartid`),
  KEY `prodtype` (`prodtype`),
  KEY `prodref` (`prodref`),
  KEY `prodvar` (`prodvar`),
  KEY `price` (`price`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='any cart line' AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_cities`
--

CREATE TABLE IF NOT EXISTS `mx_cities` (
  `cc_fips` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `full_name_nd` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  KEY `idx_cc_fips` (`cc_fips`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `mx_config`
--

CREATE TABLE IF NOT EXISTS `mx_config` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `optionname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option` (`optionname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_countries`
--

CREATE TABLE IF NOT EXISTS `mx_countries` (
  `cc_fips` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `cc_iso` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `tld` varchar(3) COLLATE utf8_bin DEFAULT NULL,
  `country_name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  KEY `idx_cc_fips` (`cc_fips`),
  KEY `idx_cc_iso` (`cc_iso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `mx_fans`
--

CREATE TABLE IF NOT EXISTS `mx_fans` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fanid` bigint(20) NOT NULL,
  `artistid` bigint(20) NOT NULL,
  `confirmed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `confirmed` (`confirmed`),
  KEY `fanid` (`fanid`),
  KEY `artistid` (`artistid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_favorites`
--

CREATE TABLE IF NOT EXISTS `mx_favorites` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `favtype` tinyint(4) NOT NULL,
  `favid` bigint(20) NOT NULL,
  `position` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `favid` (`favid`),
  KEY `favtype` (`favtype`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_fbpages`
--

CREATE TABLE IF NOT EXISTS `mx_fbpages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(20) NOT NULL,
  `accountid` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pageid` (`pageid`),
  KEY `accountid` (`accountid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_friends`
--

CREATE TABLE IF NOT EXISTS `mx_friends` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account1_id` bigint(20) NOT NULL,
  `account2_id` bigint(20) NOT NULL,
  `confirmed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `confirmed` (`confirmed`),
  KEY `account1_id` (`account1_id`),
  KEY `account2_id` (`account2_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_genres`
--

CREATE TABLE IF NOT EXISTS `mx_genres` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) NOT NULL,
  `genre` varchar(40) NOT NULL,
  `wiki` varchar(100) NOT NULL,
  `cat` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `genre` (`genre`),
  KEY `cat` (`cat`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3082 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_island`
--

CREATE TABLE IF NOT EXISTS `mx_island` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `archi_id` int(11) NOT NULL,
  `open` tinyint(1) NOT NULL,
  `maxpersons` smallint(6) NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dx` int(11) NOT NULL,
  `dy` int(11) NOT NULL,
  `dz` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `open` (`open`),
  KEY `maxpersons` (`maxpersons`),
  KEY `locale` (`locale`),
  KEY `archi_id` (`archi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_islrole`
--

CREATE TABLE IF NOT EXISTS `mx_islrole` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_likes`
--

CREATE TABLE IF NOT EXISTS `mx_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wallid` bigint(20) NOT NULL,
  `authid` bigint(20) NOT NULL,
  `type` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wallid` (`wallid`),
  KEY `authid` (`authid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=191 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_location`
--

CREATE TABLE IF NOT EXISTS `mx_location` (
  `id` bigint(20) NOT NULL,
  `street1` varchar(100) NOT NULL,
  `street2` varchar(100) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(20) NOT NULL,
  `country` varchar(3) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `zip` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `street1` (`street1`),
  KEY `street2` (`street2`),
  KEY `city` (`city`),
  KEY `state` (`state`),
  KEY `country` (`country`),
  KEY `phone` (`phone`),
  KEY `zip` (`zip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx_log`
--

CREATE TABLE IF NOT EXISTS `mx_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `pag` varchar(10) NOT NULL,
  `opt` varchar(10) NOT NULL,
  `act` varchar(60) NOT NULL,
  `ref` varchar(100) NOT NULL,
  `useragent` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `page` (`pag`),
  KEY `option` (`opt`),
  KEY `action` (`act`),
  KEY `date` (`date`),
  KEY `referrer` (`ref`),
  KEY `referer` (`ref`),
  FULLTEXT KEY `useragent` (`useragent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31687 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_med2bun`
--

CREATE TABLE IF NOT EXISTS `mx_med2bun` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mediaid` bigint(20) NOT NULL,
  `bundleid` bigint(20) NOT NULL,
  `position` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mediaid` (`mediaid`),
  KEY `bunxid` (`bundleid`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_med2med`
--

CREATE TABLE IF NOT EXISTS `mx_med2med` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mediaid1` bigint(20) NOT NULL COMMENT 'media receiving (e.g. bundle)',
  `mediaid2` bigint(20) NOT NULL COMMENT 'media linked (shown)',
  `position` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mediaid1` (`mediaid1`),
  KEY `mediaid2` (`mediaid2`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='media to media association' AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_med2play`
--

CREATE TABLE IF NOT EXISTS `mx_med2play` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mediaid` bigint(20) NOT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `userid` bigint(20) NOT NULL,
  `start` datetime NOT NULL,
  `stop` datetime DEFAULT NULL,
  `played` smallint(6) DEFAULT NULL COMMENT 'percentage played',
  `rating` tinyint(4) DEFAULT NULL,
  `playtime` smallint(6) NOT NULL COMMENT 'seconds played',
  `status` tinyint(4) DEFAULT NULL COMMENT 'ok/error',
  PRIMARY KEY (`id`),
  KEY `mediaid` (`mediaid`),
  KEY `userid` (`userid`),
  KEY `start` (`start`),
  KEY `played` (`played`),
  KEY `rating` (`rating`),
  KEY `playtime` (`playtime`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='play stats' AUTO_INCREMENT=92 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_media`
--

CREATE TABLE IF NOT EXISTS `mx_media` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) NOT NULL,
  `type` smallint(6) DEFAULT '99',
  `filename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `filesize` int(11) DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hashcode` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `activation` date DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completion` tinyint(4) DEFAULT NULL,
  `id3info` mediumtext COLLATE utf8_unicode_ci,
  `likes` bigint(20) NOT NULL,
  `dislikes` bigint(20) NOT NULL,
  `haspic` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `preview` tinyint(4) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `month` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hashcode` (`hashcode`),
  KEY `owner_id` (`owner_id`),
  KEY `type` (`type`),
  KEY `description` (`description`(333)),
  KEY `temp` (`status`),
  KEY `filename` (`filename`),
  KEY `songname` (`title`),
  KEY `filesize` (`filesize`),
  KEY `completion` (`completion`),
  KEY `haspic` (`haspic`),
  KEY `preview` (`preview`),
  KEY `year` (`year`),
  KEY `month` (`month`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=122 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_messages`
--

CREATE TABLE IF NOT EXISTS `mx_messages` (
  `msgid` bigint(20) NOT NULL AUTO_INCREMENT,
  `authid` bigint(20) NOT NULL,
  `subject` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `priority` tinyint(4) DEFAULT NULL,
  `flags` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `refmsgid` bigint(20) DEFAULT NULL COMMENT 'referred msg id',
  `sstatus` smallint(5) unsigned NOT NULL COMMENT 'sender status',
  PRIMARY KEY (`msgid`),
  KEY `authid` (`authid`),
  KEY `level` (`priority`),
  KEY `date` (`date`),
  KEY `refmsgid` (`refmsgid`),
  KEY `sstatus` (`sstatus`),
  FULLTEXT KEY `body` (`body`),
  FULLTEXT KEY `subject` (`subject`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=95 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_msg2acc`
--

CREATE TABLE IF NOT EXISTS `mx_msg2acc` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `msgid` bigint(20) NOT NULL,
  `sender` bigint(20) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `status` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `msgid` (`msgid`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_pros`
--

CREATE TABLE IF NOT EXISTS `mx_pros` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `website` varchar(60) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `website` (`website`),
  KEY `userid` (`userid`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_refs`
--

CREATE TABLE IF NOT EXISTS `mx_refs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `wallid` bigint(20) NOT NULL,
  `showid` bigint(20) NOT NULL,
  `mediaid` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wallid` (`wallid`),
  KEY `showid` (`showid`),
  KEY `mediaid` (`mediaid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_region`
--

CREATE TABLE IF NOT EXISTS `mx_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(2) NOT NULL,
  `region` varchar(2) NOT NULL,
  `name` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country` (`country`),
  KEY `region` (`region`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='FIPS codes for regions' AUTO_INCREMENT=3983 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_show`
--

CREATE TABLE IF NOT EXISTS `mx_show` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `venueid` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `mxprice` decimal(4,2) DEFAULT NULL,
  `fanprice` decimal(4,2) DEFAULT NULL,
  `description` tinytext NOT NULL,
  `likes` bigint(20) NOT NULL,
  `dislikes` bigint(20) NOT NULL,
  `mediaid` bigint(20) NOT NULL,
  `flags` tinyint(4) NOT NULL,
  `comments` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venueid` (`venueid`),
  KEY `date` (`date`),
  KEY `price` (`price`),
  KEY `mxprice` (`mxprice`),
  KEY `fanprice` (`fanprice`),
  KEY `dislikes` (`dislikes`),
  KEY `dislikes_2` (`dislikes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_subscriptions`
--

CREATE TABLE IF NOT EXISTS `mx_subscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fanid` bigint(20) NOT NULL,
  `objectid` bigint(20) NOT NULL,
  `subcat` tinyint(4) NOT NULL,
  `subtype` tinyint(4) NOT NULL,
  `expiry` date NOT NULL,
  `status` tinyint(4) NOT NULL,
  `statusstamp` datetime NOT NULL,
  `firstsub` date DEFAULT NULL,
  `renewal` smallint(6) DEFAULT NULL,
  `ppprofileid` varchar(20) DEFAULT NULL,
  `ppstatus` varchar(20) DEFAULT NULL,
  `renewaldate` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'price paid',
  PRIMARY KEY (`id`),
  KEY `fanid` (`fanid`),
  KEY `subtype` (`subtype`),
  KEY `expiry` (`expiry`),
  KEY `status` (`status`),
  KEY `statusstamp` (`statusstamp`),
  KEY `FIRSTSUB` (`firstsub`),
  KEY `ppprofileid` (`ppprofileid`),
  KEY `ppstatus` (`ppstatus`),
  KEY `renewal` (`renewal`),
  KEY `renewaldate` (`renewaldate`),
  KEY `subcat` (`subcat`),
  KEY `objectid` (`objectid`),
  KEY `amount` (`amount`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_timezones`
--

CREATE TABLE IF NOT EXISTS `mx_timezones` (
  `name` char(64) NOT NULL,
  `Time_zone_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Time zone names';

-- --------------------------------------------------------

--
-- Table structure for table `mx_walls`
--

CREATE TABLE IF NOT EXISTS `mx_walls` (
  `msgid` bigint(20) NOT NULL AUTO_INCREMENT,
  `authid` bigint(20) NOT NULL,
  `body` text NOT NULL,
  `mediaid` bigint(20) NOT NULL,
  `filter` tinyint(4) NOT NULL COMMENT 'recipients (friends, fans, etc...)',
  `flags` tinyint(4) DEFAULT NULL,
  `date` datetime NOT NULL,
  `likes` bigint(20) DEFAULT '0',
  `dislikes` bigint(20) DEFAULT '0',
  `refid` bigint(20) DEFAULT NULL,
  `comments` int(11) DEFAULT '0',
  PRIMARY KEY (`msgid`),
  KEY `authid` (`authid`),
  KEY `mediaid` (`mediaid`),
  KEY `filter` (`filter`),
  KEY `status` (`flags`),
  KEY `date` (`date`),
  KEY `likes` (`likes`),
  KEY `dislikes` (`dislikes`),
  KEY `refid` (`refid`),
  KEY `comments` (`comments`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `mx_wishline`
--

CREATE TABLE IF NOT EXISTS `mx_wishline` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `prodtype` tinyint(4) NOT NULL,
  `prodref` bigint(20) NOT NULL,
  `prodvar` tinyint(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `prodtype` (`prodtype`),
  KEY `prodref` (`prodref`),
  KEY `prodvar` (`prodvar`),
  KEY `price` (`price`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='any cart line' AUTO_INCREMENT=63 ;

";