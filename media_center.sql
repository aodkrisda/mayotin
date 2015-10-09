-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2015 at 05:19 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `media_center`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
`comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `user_id`, `media_id`, `comment`, `date_created`) VALUES
(1, 2, 1, 'eerwerwe', '2015-05-26 19:50:07'),
(2, 2, 1, 'O yes...', '2015-05-26 19:56:23'),
(3, 2, 1, 'werwew', '2015-05-26 19:56:43'),
(4, 2, 1, 'How a', '2015-05-27 10:49:57'),
(5, 2, 1, '333', '2015-05-27 10:53:28'),
(6, 2, 1, 'sdsdfsds', '2015-05-27 10:54:36'),
(7, 2, 1, 'w3423', '2015-05-27 10:55:58'),
(8, 2, 1, 'wwww', '2015-05-27 11:06:47'),
(9, 2, 1, 'zzzzz', '2015-05-27 11:09:36'),
(10, 2, 4, 'wwwwwwwwwwwww', '2015-05-28 12:49:20'),
(11, 2, 4, 'zzzzzzzzzzzz', '2015-05-28 12:49:33'),
(12, 1, 2, 'test', '2015-06-02 09:22:39');

-- --------------------------------------------------------

--
-- Table structure for table `evaluate_form`
--

CREATE TABLE IF NOT EXISTS `evaluate_form` (
`evaluate_form_id` int(11) NOT NULL,
  `media_id` int(11) DEFAULT NULL,
  `evaluate_user_id` int(11) DEFAULT NULL,
  `evaluate_point` float(255,0) DEFAULT '0',
  `comment` text,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `evaluate_form_detail`
--

CREATE TABLE IF NOT EXISTS `evaluate_form_detail` (
`evaluate_form_detail_id` int(11) NOT NULL,
  `evaluate_topic_id` int(11) DEFAULT NULL,
  `evaluate_form_id` int(11) DEFAULT NULL,
  `evaluate_point` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `evaluate_group`
--

CREATE TABLE IF NOT EXISTS `evaluate_group` (
`evaluate_group_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `evaluate_topic`
--

CREATE TABLE IF NOT EXISTS `evaluate_topic` (
`evaluate_topic_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `evaluate_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
`group_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`group_id`, `name`) VALUES
(1, 'กลุ่มสาระวิทยาศาสตร์'),
(3, 'กลุ่มสาระภาษาศาสตร์');

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE IF NOT EXISTS `level` (
`level_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`level_id`, `name`) VALUES
(1, 'ประถมศึกษาปีที่ 1'),
(2, 'ประถมศึกษาปีที่ 2'),
(3, 'ประถมศึกษาปีที่ 3'),
(4, 'ประถมศึกษาปีที่ 4'),
(5, 'ประถมศึกษาปีที่ 5'),
(6, 'ประถมศึกษาปีที่ 6');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
`media_id` int(11) NOT NULL,
  `code` varchar(11) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `links` text,
  `status` int(11) DEFAULT '0',
  `evaluate_counter` int(11) DEFAULT NULL,
  `evaluate_point` float DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uploadable` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`media_id`, `code`, `topic`, `subject_id`, `level_id`, `type_id`, `author_id`, `group_id`, `description`, `url`, `thumb`, `links`, `status`, `evaluate_counter`, `evaluate_point`, `date_created`, `date_modified`, `uploadable`) VALUES
(1, 'A001', 'วิทยาการก้าวล้ำ', 4, 1, 2, 2, 1, 'ไร้คำบรรยาย...', 'http://content.jwplatform.com/videos/C4lp6Dtd-640.mp4', 'c4ca4238a0b923820dcc509a6f75849b.thumb.png', 'Youtube\nhttp://www.yoahoo.com\n\nYoutube2\nhttps://www.yoahoo.com2\n\n\nYahoo\nwww.yahoo.com', 1, NULL, 0, '2015-05-26 15:02:40', '2015-05-28 15:18:30', 0),
(2, 'Y001', 'ตัวอย่าง Youtube Video', 2, 1, 1, 2, 1, NULL, 'https://www.youtube.com/watch?v=qqC8LiwCbqQ', 'c81e728d9d4c2f636f067f89cc14862c.thumb.png', NULL, NULL, NULL, 0, '2015-05-28 03:52:05', '2015-05-28 15:01:54', 0),
(3, 'PDF', 'pdf', 1, 1, 1, 2, 1, NULL, 'eccbc87e4b5ce2fe28308fd9f2a7baf3.pdf', 'eccbc87e4b5ce2fe28308fd9f2a7baf3.thumb.png', NULL, NULL, NULL, 0, '2015-05-28 08:51:06', '2015-05-28 08:51:06', 1),
(4, 'SWF001', 'swf', 1, 1, 1, 2, 1, 'swfsdfsd www', 'a87ff679a2f3e71d9181a67b7542122c.swf', 'a87ff679a2f3e71d9181a67b7542122c.thumb.png', NULL, NULL, NULL, 0, '2015-05-28 08:52:34', '2015-05-28 10:17:31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `study`
--

CREATE TABLE IF NOT EXISTS `study` (
`study_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `first_used` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_used` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `view_count` int(11) DEFAULT '0',
  `rating` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `unlike_count` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `study`
--

INSERT INTO `study` (`study_id`, `user_id`, `media_id`, `first_used`, `last_used`, `view_count`, `rating`, `like_count`, `unlike_count`) VALUES
(4, 2, 4, '2015-05-28 12:41:20', '2015-05-28 18:15:56', 4, 0, 1, 0),
(5, 1, 4, '2015-05-28 12:46:06', '2015-05-28 12:47:28', 3, 0, 0, 1),
(6, 2, 1, '2015-05-28 13:06:14', '2015-05-28 14:45:53', 1, NULL, 0, 1),
(7, 1, 1, '2015-05-28 13:21:35', '2015-06-02 09:28:15', 4, NULL, 0, 1),
(8, 1, 3, '2015-05-28 13:34:59', '2015-05-28 13:38:28', 3, NULL, NULL, NULL),
(9, 2, 2, '2015-05-28 13:50:58', '2015-05-28 13:51:08', 1, NULL, NULL, NULL),
(10, 2, 3, '2015-05-28 13:51:36', '2015-05-28 18:13:16', 1, NULL, 1, 0),
(11, 3, 1, '2015-05-28 14:02:49', '2015-05-28 14:04:13', 1, NULL, NULL, NULL),
(12, 4, 1, '2015-05-28 18:16:57', '2015-05-28 18:16:57', 0, NULL, NULL, NULL),
(13, 4, 4, '2015-05-28 18:52:02', '2015-05-28 19:01:24', 3, NULL, NULL, NULL),
(14, 1, 2, '2015-06-02 09:22:09', '2015-06-02 09:22:34', 1, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE IF NOT EXISTS `subject` (
`subject_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `name`, `group_id`) VALUES
(1, 'ภาษาอังกฤษ', 3),
(2, 'วิทยาศาสตร์', 1),
(3, 'ศิลปะ', 3),
(4, 'คณิตศาสตร์', 1),
(6, 'ศิลปะ2', 3);

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE IF NOT EXISTS `type` (
`type_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`type_id`, `name`) VALUES
(1, 'สื่อประเภทวีดีโอ'),
(2, 'สื่อประเภทเสียง'),
(3, 'สื่อประเภทเอกสาร');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`user_id` int(11) NOT NULL,
  `user_number` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `title` varchar(25) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `user_type` int(11) NOT NULL DEFAULT '0' COMMENT '0: นักเรียน, 1: admin, 2: คณะกรรมการ,  3: ผู้บริหาร',
  `level_id` int(11) DEFAULT '0',
  `group_id` int(11) DEFAULT '0',
  `date_first_used` datetime DEFAULT NULL,
  `date_last_used` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_number`, `password`, `title`, `first_name`, `last_name`, `user_type`, `level_id`, `group_id`, `date_first_used`, `date_last_used`, `date_created`, `date_modified`) VALUES
(1, 'admin', 'admin', 'นาย', 'ผู้ดูแลระบบ', '-', 1, 0, 1, '0000-00-00 00:00:00', NULL, '2015-05-26 13:30:29', '2015-05-26 13:30:29'),
(2, 'yotin', 'yotin', 'นาย', 'โยธิน', 'อ้ายพิงชัย', 2, 2, 1, NULL, NULL, '2015-05-26 13:44:27', '2015-05-26 13:44:27'),
(3, 'abc', 'def', 'นาย', 'abc', 'def', 0, 1, 3, NULL, NULL, '2015-05-26 14:16:57', '2015-05-26 14:16:57'),
(4, 'manager', 'manager', 'นาย', 'manager', 'manager', 3, 1, 1, NULL, NULL, '2015-05-28 15:17:28', '2015-05-28 15:17:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
 ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `evaluate_form`
--
ALTER TABLE `evaluate_form`
 ADD PRIMARY KEY (`evaluate_form_id`);

--
-- Indexes for table `evaluate_form_detail`
--
ALTER TABLE `evaluate_form_detail`
 ADD PRIMARY KEY (`evaluate_form_detail_id`);

--
-- Indexes for table `evaluate_group`
--
ALTER TABLE `evaluate_group`
 ADD PRIMARY KEY (`evaluate_group_id`);

--
-- Indexes for table `evaluate_topic`
--
ALTER TABLE `evaluate_topic`
 ADD PRIMARY KEY (`evaluate_topic_id`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
 ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `level`
--
ALTER TABLE `level`
 ADD PRIMARY KEY (`level_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
 ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `study`
--
ALTER TABLE `study`
 ADD PRIMARY KEY (`study_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
 ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
 ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `evaluate_form`
--
ALTER TABLE `evaluate_form`
MODIFY `evaluate_form_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `evaluate_form_detail`
--
ALTER TABLE `evaluate_form_detail`
MODIFY `evaluate_form_detail_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `evaluate_group`
--
ALTER TABLE `evaluate_group`
MODIFY `evaluate_group_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `evaluate_topic`
--
ALTER TABLE `evaluate_topic`
MODIFY `evaluate_topic_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `study`
--
ALTER TABLE `study`
MODIFY `study_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
