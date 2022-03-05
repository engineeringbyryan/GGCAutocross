SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `GGC_Autocross`
--
CREATE DATABASE IF NOT EXISTS `GGC_Autocross` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `GGC_Autocross`;

-- --------------------------------------------------------

--
-- Table structure for table `autox_cars`
--

CREATE TABLE `autox_cars` (
  `car_id` int(11) NOT NULL DEFAULT '0',
  `car` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `year_start` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `year_end` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `chassis` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `points` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `rear_wheel_diameter` decimal(3,1) DEFAULT NULL,
  `rear_wheel_width` decimal(3,1) DEFAULT NULL,
  `rear_tire_width` smallint(5) UNSIGNED DEFAULT NULL,
  `rear_tire_profile` smallint(5) UNSIGNED DEFAULT NULL,
  `front_wheel_diameter` decimal(3,1) DEFAULT NULL,
  `front_wheel_width` decimal(3,1) DEFAULT NULL,
  `LSD_standard` char(1) COLLATE latin1_general_ci DEFAULT NULL,
  `weight` smallint(5) UNSIGNED DEFAULT NULL,
  `weight_pct` decimal(3,2) DEFAULT NULL,
  `engine_type` varchar(1) COLLATE latin1_general_ci DEFAULT NULL,
  `engine_code` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `engine_displacement` smallint(5) UNSIGNED DEFAULT NULL,
  `engine_no_cylinders` smallint(5) UNSIGNED DEFAULT NULL,
  `BHP` smallint(5) UNSIGNED DEFAULT NULL,
  `BHP_at_RPM` smallint(5) UNSIGNED DEFAULT NULL,
  `torque` smallint(5) UNSIGNED DEFAULT NULL,
  `torque_at_RPM` smallint(5) UNSIGNED DEFAULT NULL,
  `wheel_base` decimal(4,1) DEFAULT NULL,
  `final_drive` decimal(3,2) DEFAULT NULL,
  `lsd_points` smallint(2) UNSIGNED DEFAULT NULL,
  `engine_level` smallint(2) UNSIGNED DEFAULT NULL,
  `suspension_code` varchar(1) COLLATE latin1_general_ci DEFAULT NULL,
  `2nd_gear_ratio` decimal(3,2) DEFAULT NULL,
  `drive_type` varchar(3) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_classes`
--

CREATE TABLE `autox_classes` (
  `class` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `start_points` int(3) NOT NULL,
  `end_points` int(3) NOT NULL,
  `adjusted_class` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `adjusting_formula` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_classifications`
--

CREATE TABLE `autox_classifications` (
  `pk` int(11) NOT NULL,
  `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `class` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `points` int(5) NOT NULL DEFAULT '0',
  `car_year` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `car_model` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `serializedcar` varchar(2048) DEFAULT NULL,
  `serializedmods` varchar(2048) DEFAULT NULL,
  `serializedengine` varchar(2048) DEFAULT NULL,
  `BHP` varchar(4) DEFAULT NULL,
  `date` datetime NOT NULL,
  `active` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hpclaim` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_close`
--

CREATE TABLE `autox_close` (
  `pk` int(11) NOT NULL,
  `close` varchar(128) DEFAULT NULL,
  `open` varchar(128) DEFAULT NULL,
  `message` varchar(2048) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_closeoverride`
--

CREATE TABLE `autox_closeoverride` (
  `override` varchar(10) DEFAULT NULL,
  `message` varchar(2048) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_dates`
--

CREATE TABLE `autox_dates` (
  `pk` int(11) NOT NULL,
  `autoxdate` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `location` varchar(256) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_engine_levels`
--

CREATE TABLE `autox_engine_levels` (
  `lsd` char(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `engine_level` smallint(2) NOT NULL DEFAULT '0',
  `class_points` smallint(2) NOT NULL DEFAULT '0',
  `percent_from` smallint(3) NOT NULL DEFAULT '0',
  `percent_to` smallint(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `autox_logs`
--

CREATE TABLE `autox_logs` (
  `date` varchar(10) DEFAULT NULL,
  `time` varchar(8) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `user` varchar(128) DEFAULT NULL,
  `log` varchar(2048) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_modifications`
--

CREATE TABLE `autox_modifications` (
  `mod_id` int(11) NOT NULL,
  `default` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'N',
  `category_id` int(11) NOT NULL,
  `mod_name` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `mod_points` int(11) DEFAULT NULL,
  `suspension_code` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_modlist`
--

CREATE TABLE `autox_modlist` (
  `modnumber` int(3) NOT NULL DEFAULT '0',
  `modname` varchar(128) NOT NULL DEFAULT '',
  `percent` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_mods_engine`
--

CREATE TABLE `autox_mods_engine` (
  `engine_mod_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `engine_mod_name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `percent` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_mod_categories`
--

CREATE TABLE `autox_mod_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `mandatory_selection` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `default_mod_id` int(11) DEFAULT NULL,
  `increase_amount_for mod_points` decimal(4,2) DEFAULT NULL,
  `description` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `notes` varchar(1000) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `multiselect` varchar(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_msr_users`
--

CREATE TABLE `autox_msr_users` (
  `id` int(11) NOT NULL,
  `wp_id` varchar(5000) DEFAULT NULL,
  `msr_id` varchar(5000) DEFAULT NULL,
  `msr_email` varchar(5000) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `autox_numbers`
--

CREATE TABLE `autox_numbers` (
  `pk` int(11) NOT NULL,
  `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `drivernumber` int(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_optional_car_wheels`
--

CREATE TABLE `autox_optional_car_wheels` (
  `wheel_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `wheel_description` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `package_code` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `rear_wheel_diameter` decimal(3,1) NOT NULL,
  `rear_wheel_width` decimal(3,1) NOT NULL,
  `rear_tire_width` smallint(5) NOT NULL,
  `rear_tire_profile` smallint(5) NOT NULL,
  `front_wheel_diameter` decimal(3,1) NOT NULL,
  `front_wheel_width` decimal(2,1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_packages`
--

CREATE TABLE `autox_packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `package_code` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `suspension_code` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `autox_reg_button_log`
--

CREATE TABLE `autox_reg_button_log` (
  `pk` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `ip_address` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `autox_revision_history`
--

CREATE TABLE `autox_revision_history` (
  `id` int(6) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `revision` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `autox_user_info`
--

CREATE TABLE `autox_user_info` (
  `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `password` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `first` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `last` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `user_type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `ccanumber` varchar(7) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `type` char(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `signup_ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `autox_cars`
--
ALTER TABLE `autox_cars`
  ADD PRIMARY KEY (`car_id`);

--
-- Indexes for table `autox_classifications`
--
ALTER TABLE `autox_classifications`
  ADD PRIMARY KEY (`pk`);

--
-- Indexes for table `autox_close`
--
ALTER TABLE `autox_close`
  ADD PRIMARY KEY (`pk`);

--
-- Indexes for table `autox_dates`
--
ALTER TABLE `autox_dates`
  ADD PRIMARY KEY (`pk`);

--
-- Indexes for table `autox_modifications`
--
ALTER TABLE `autox_modifications`
  ADD PRIMARY KEY (`mod_id`);

--
-- Indexes for table `autox_mods_engine`
--
ALTER TABLE `autox_mods_engine`
  ADD PRIMARY KEY (`engine_mod_id`);

--
-- Indexes for table `autox_mod_categories`
--
ALTER TABLE `autox_mod_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `autox_msr_users`
--
ALTER TABLE `autox_msr_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `autox_numbers`
--
ALTER TABLE `autox_numbers`
  ADD PRIMARY KEY (`pk`);

--
-- Indexes for table `autox_optional_car_wheels`
--
ALTER TABLE `autox_optional_car_wheels`
  ADD PRIMARY KEY (`wheel_id`);

--
-- Indexes for table `autox_packages`
--
ALTER TABLE `autox_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `autox_reg_button_log`
--
ALTER TABLE `autox_reg_button_log`
  ADD PRIMARY KEY (`pk`);

--
-- Indexes for table `autox_revision_history`
--
ALTER TABLE `autox_revision_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `autox_user_info`
--
ALTER TABLE `autox_user_info`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `autox_classifications`
--
ALTER TABLE `autox_classifications`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_close`
--
ALTER TABLE `autox_close`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_dates`
--
ALTER TABLE `autox_dates`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_modifications`
--
ALTER TABLE `autox_modifications`
  MODIFY `mod_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_msr_users`
--
ALTER TABLE `autox_msr_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_numbers`
--
ALTER TABLE `autox_numbers`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_reg_button_log`
--
ALTER TABLE `autox_reg_button_log`
  MODIFY `pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autox_revision_history`
--
ALTER TABLE `autox_revision_history`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
