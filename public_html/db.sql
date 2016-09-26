--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` varchar(255) NOT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  `resetComplete` varchar(3) DEFAULT 'No',
  `rank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainingsession`
--

CREATE TABLE IF NOT EXISTS `trainingsession` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `date` DATE NOT NULL DEFAULT '0000-00-00',
  `time` TIME NOT NULL DEFAULT '00:00:00',
  `start_location` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '(lat, long)',
  `end_location` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '(lat, long)',
  `waypoints` varchar(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT '(lat, long)',
  `start_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'undefined',
  `distance` int(20) unsigned NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'type',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `my_unique_key` (`user_id`, `date`, `time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainingkeeper`
--

CREATE TABLE IF NOT EXISTS `trainingkeeper` (
    `users` INT UNSIGNED NOT NULL,
    `trainingsession` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`users`, `trainingsession`),
    CONSTRAINT `Constr_trainingkeeper_users_fk`
        FOREIGN KEY `users_fk` (`users`) REFERENCES `users` (`userID`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `Constr_trainingkeeper_trainingsession_fk`
        FOREIGN KEY `trainingsession_fk` (`trainingsession`) REFERENCES `trainingsession` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB CHARACTER SET ascii COLLATE ascii_general_ci

-- --------------------------------------------------------
