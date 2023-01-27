CREATE TABLE `apibox_logs` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `guid` varchar(155) DEFAULT 'global', 
  `groupuid` varchar(55) NOT NULL DEFAULT 'hq', 
  `api_id` varchar(155) NOT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'NA', 
  `payload` text, 
  `params` text, 
  `response` text, 
  `addon_params` text, 
  `blocked` enum('false','true') DEFAULT 'false', 
  `created_on` datetime NOT NULL, 
  `created_by` varchar(155) NOT NULL, 
  `edited_on` datetime NOT NULL, 
  `edited_by` varchar(155) NOT NULL, 
  PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
