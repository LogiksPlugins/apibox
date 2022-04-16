CREATE TABLE `apibox_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(155) DEFAULT 'global',
  `groupuid` varchar(55) NOT NULL DEFAULT 'hq',
  `title` varchar(155) NOT NULL,
  `category` varchar(155) NOT NULL,
  `debug` enum('false','true') NOT NULL  DEFAULT 'false',
  `cache` enum('false','true') NOT NULL  DEFAULT 'false',
 .`use_mock` enum('false','true') NOT NULL DEFAULT 'false'
  `format` varchar(15) NOT NULL DEFAULT 'raw',
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `end_point` varchar(250) NOT NULL,
  `authorization` varchar(25) NOT NULL DEFAULT '',
  `authorization_token` VARCHAR(180) NOT NULL DEFAULT '',
  `input_validation` text,
  `params` text,
  `headers` text,
  `body` text,
  `output_transformation` text,
  `mockdata` text,
  `remarks` varchar(250) NOT NULL DEFAULT '',
  `blocked` enum('false','true') DEFAULT 'false',
  `created_on` datetime NOT NULL,
  `created_by` varchar(155) NOT NULL,
  `edited_on` datetime NOT NULL,
  `edited_by` varchar(155) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

