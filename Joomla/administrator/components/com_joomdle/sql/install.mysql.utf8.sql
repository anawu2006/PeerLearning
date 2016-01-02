CREATE TABLE IF NOT EXISTS `#__joomdle_bundles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courses` text NOT NULL,
  `cost` float NOT NULL,
  `currency` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_course_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `application_date` datetime NOT NULL,
  `confirmation_date` datetime NOT NULL,
  `motivation` text NOT NULL,
  `experience` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_course_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moodle_forum_id` int(11) NOT NULL,
  `kunena_forum_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_course_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_field_mappings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `joomla_app` varchar(45) NOT NULL,
  `joomla_field` varchar(45) NOT NULL,
  `moodle_field` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_mailinglists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_profiletypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profiletype_id` int(11) NOT NULL,
  `create_on_moodle` int(11) NOT NULL,
  `moodle_role` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__joomdle_purchased_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
