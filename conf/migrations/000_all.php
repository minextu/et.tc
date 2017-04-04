<?php namespace Minextu\Ettc\Database\Migration;
class all extends AbstractMigration
{
	public function upgrade()
	{
		$sqlArr = array (
  2 => 'CREATE TABLE `failedLogins` (  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,  `nick` varchar(100) NOT NULL,  `ip` varchar(100) NOT NULL,  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8',
  6 => 'CREATE TABLE `projects` (  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,  `title` varchar(100) NOT NULL,  `description` varchar(10000) NOT NULL,  `html` varchar(10000) DEFAULT NULL,  `image` varchar(100) DEFAULT NULL,  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8',
  10 => 'CREATE TABLE `ranks` (  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,  `title` varchar(30) NOT NULL,  `permissions` text NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8',
  12 => 'INSERT INTO `ranks` VALUES (1,\'Guest\',\'\'),(2,\'Admin\',\'\')',
  15 => 'CREATE TABLE `userApiKeys` (  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,  `title` varchar(1000) DEFAULT NULL,  `userId` int(255) unsigned DEFAULT NULL,  `key` varchar(100) NOT NULL,  `permissions` text NOT NULL,  `used` timestamp NULL DEFAULT NULL,  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`id`),  UNIQUE KEY `key` (`key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8',
  19 => 'CREATE TABLE `users` (  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,  `nick` varchar(30) NOT NULL,  `email` varchar(100) DEFAULT NULL,  `hash` varchar(100) DEFAULT NULL,  `rank` int(255) NOT NULL,  `permissions` text NOT NULL,  `registerDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`id`),  UNIQUE KEY `nick` (`nick`)) ENGINE=InnoDB DEFAULT CHARSET=utf8',
);

		foreach ($sqlArr as $sql) {
		$this->db->getPdo()->prepare($sql)->execute();
		}
		return 6;
	}

	public function downgrade()
	{
		return false;
	}
}