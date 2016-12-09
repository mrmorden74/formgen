CREATE TABLE `dblist` (
  `id` int(11) NOT NULL,
  `dbname` varchar(55) NOT NULL,
  `username` varchar(55) NOT NULL,
  `password` varchar(55) NOT NULL,
  `dbtype` varchar(55) NOT NULL,
  `server` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(95) NOT NULL,
  `type` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `user` (`id`, `username`, `password`, `type`) VALUES
(1, 'user', '$2y$10$/DhkaxjPcDMbhHLP.7jIn.fpzwHVLoR7BMRMm22H5InKEAHrZbqG.', 'user'),
(2, 'admin', '$2y$10$jhtPYpAIZKvQ4yIM3wqsjeR5nabE8aYE61G3FGuG8afo4ZSjpnC52', 'admin');
ALTER TABLE `dblist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dbname` (`dbname`,`dbtype`);
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);
ALTER TABLE `dblist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
