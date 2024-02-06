
CREATE TABLE IF NOT EXISTS `resource_type` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `title` varchar(512) NOT NULL,
  `configuration` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `resource_type`
--

INSERT INTO `resource_type` (`id`, `name`, `title`, `configuration`) VALUES
(1, 'tag', 'Tag (HTML / CSS / JavaScript / PHP)', ''),
(2, 'webservice', 'Webservice', ''),
(3, 'html', 'HTML Tag', ''),
(4, 'object', 'Object (Definition)', ''),
(5, 'dbtable', 'Database Table', 'rc.db.table.config'),
(6, 'validator', 'Validator', ''),
(7, 'php-library', 'PHP Library', ''),
(8, 'java-app', 'Java App', ''),
(9, 'java-library', 'Java Library', '');