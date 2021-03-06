--
-- Table structure for table `reg_login_attempt`
--

CREATE TABLE IF NOT EXISTS `reg_login_attempt` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` int(11) unsigned NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reg_users`
--

CREATE TABLE IF NOT EXISTS `reg_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `rank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `token` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'not set',
  `token_validity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `prefered_ip` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`token`),
  KEY `rank` (`rank`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `reg_logged_ip`
--

CREATE TABLE IF NOT EXISTS `reg_logged_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ip` int(11) unsigned NOT NULL,
  `uniquekey` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniquekey` (`uniquekey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `reg_requests`
--

CREATE TABLE IF NOT EXISTS `reg_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `run_circut_id` int(10) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `content` varchar(640) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'not set',
  `joining_users` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no joined users yet',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `my_uniquekey` (`start_time`, `user_id`),
  KEY `user_id` (`user_id`),
  KEY `run_circut_id` (`run_circut_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reg_run_circuts`
--

CREATE TABLE IF NOT EXISTS `reg_run_circuts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `length` int(10) COLLATE utf8_unicode_ci NOT NULL,
  `time_easy` TIME NOT NULL DEFAULT '00:00:00',
  `time_medium` TIME NOT NULL DEFAULT '00:00:00',
  `time_advanced` TIME NOT NULL DEFAULT '00:00:00',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
