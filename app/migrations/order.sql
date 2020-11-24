CREATE TABLE `order` (
  `order_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `oid` varchar(12) NOT NULL,
  `product` varchar(100) NOT NULL,
  `datetime_local` datetime NOT NULL,
  `datetime_utc` datetime DEFAULT NULL,
  `datetime_timezone` varchar(100) NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_oid_IDX` (`oid`),
  KEY `order_user_id_IDX` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci