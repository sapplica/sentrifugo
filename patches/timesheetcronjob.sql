/*Table structure for table `tm_configuration` */

DROP TABLE IF EXISTS `tm_configuration`;

CREATE TABLE `tm_configuration` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ts_weekly_reminder_day` enum('day','sun','mon','tue','wed','thu','fri','sat') NOT NULL,
  `ts_block_dates_range` varchar(100) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tm_configuration` */