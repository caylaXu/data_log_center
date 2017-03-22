-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: DataLog
-- ------------------------------------------------------
-- Server version	5.6.23-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AddrDef`
--

DROP TABLE IF EXISTS `AddrDef`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AddrDef` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地点ID',
  `SystemId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '系统标识',
  `Name` varchar(32) NOT NULL DEFAULT '' COMMENT '地点名称',
  `Mark` varchar(32) NOT NULL DEFAULT '' COMMENT '地点备注',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `System_Name` (`SystemId`,`Name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='地点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AddrEvent`
--

DROP TABLE IF EXISTS `AddrEvent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AddrEvent` (
  `AddrId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地点标识',
  `EventId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件标识',
  PRIMARY KEY (`AddrId`,`EventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AttrDef`
--

DROP TABLE IF EXISTS `AttrDef`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AttrDef` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '事件属性ID',
  `SystemId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '系统标识',
  `Name` varchar(32) NOT NULL DEFAULT '' COMMENT '属性名称',
  `Mark` varchar(32) NOT NULL DEFAULT '' COMMENT '属性备注',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `System_Name` (`SystemId`,`Name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='事件属性表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DataNode`
--

DROP TABLE IF EXISTS `DataNode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DataNode` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '节点ID',
  `Host` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数据节点IP',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='数据节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EventAttr`
--

DROP TABLE IF EXISTS `EventAttr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EventAttr` (
  `EventId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件标识',
  `AttrId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件属性标识',
  PRIMARY KEY (`EventId`,`AttrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EventDef`
--

DROP TABLE IF EXISTS `EventDef`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EventDef` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '事件ID',
  `SystemId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '系统标识',
  `Name` varchar(64) NOT NULL DEFAULT '' COMMENT '事件名称',
  `Mark` varchar(64) NOT NULL DEFAULT '' COMMENT '事件备注',
  `Show` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '默认显示',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `System_Name` (`SystemId`,`Name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='事件定义表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EventLog`
--

DROP TABLE IF EXISTS `EventLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EventLog` (
  `Pk` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `Id` varchar(32) NOT NULL COMMENT '日志ID',
  `SystemId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统标识',
  `UserId` varchar(32) NOT NULL DEFAULT '' COMMENT '用户标识',
  `UserType` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型',
  `EventId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件标识',
  `EventAttr` varchar(512) NOT NULL DEFAULT '' COMMENT '事件属性',
  `EventDesc` varchar(512) NOT NULL DEFAULT '' COMMENT '事件描述',
  `EventTime` int(11) NOT NULL DEFAULT '0' COMMENT '发生时间',
  `EventAddr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件发生来源',
  `HttpRequest` varchar(255) NOT NULL DEFAULT '' COMMENT '事件请求的路径',
  `HttpParams` varchar(255) NOT NULL DEFAULT '' COMMENT '事件请求的参数',
  `HttpResponse` varchar(255) NOT NULL DEFAULT '' COMMENT '事件请求的响应',
  `BindUserId1` varchar(32) NOT NULL DEFAULT '' COMMENT '关联用户标识1',
  `BindUserType1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联用户类型1',
  `BindUserId2` varchar(32) NOT NULL DEFAULT '' COMMENT '关联用户标识2',
  `BindUserType2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联用户类型2',
  `BindUserId3` varchar(32) NOT NULL DEFAULT '' COMMENT '关联用户标识3',
  `BindUserType3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联用户类型3',
  `Address` varchar(255) NOT NULL DEFAULT '' COMMENT '地理位置',
  `IP` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'IP地址',
  PRIMARY KEY (`Pk`),
  KEY `IndexUser` (`UserId`,`UserType`) USING BTREE,
  KEY `IndexSystemId` (`SystemId`),
  KEY `IndexEventTime` (`EventTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EventLog_new`
--

DROP TABLE IF EXISTS `EventLog_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EventLog_new` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `Guid` varchar(32) NOT NULL COMMENT '日志ID',
  `SystemId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统标识',
  `UserId` varchar(32) NOT NULL DEFAULT '' COMMENT '用户标识',
  `UserType` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型',
  `EventId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件标识',
  `EventAttr` varchar(512) NOT NULL DEFAULT '' COMMENT '事件属性',
  `EventDesc` varchar(512) NOT NULL DEFAULT '' COMMENT '事件描述',
  `EventTime` int(11) NOT NULL DEFAULT '0' COMMENT '发生时间',
  `EventAddr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '事件发生来源',
  `HttpRequest` varchar(255) NOT NULL DEFAULT '' COMMENT '事件请求的路径',
  `HttpParams` varchar(255) NOT NULL DEFAULT '' COMMENT '事件请求的参数',
  `HttpResponse` varchar(255) NOT NULL DEFAULT '' COMMENT '事件请求的响应',
  `BindUserId1` varchar(32) NOT NULL DEFAULT '' COMMENT '关联用户标识1',
  `BindUserType1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联用户类型1',
  `BindUserId2` varchar(32) NOT NULL DEFAULT '' COMMENT '关联用户标识2',
  `BindUserType2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联用户类型2',
  `BindUserId3` varchar(32) NOT NULL DEFAULT '' COMMENT '关联用户标识3',
  `BindUserType3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联用户类型3',
  `Address` varchar(255) NOT NULL DEFAULT '' COMMENT '地理位置',
  `IP` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'IP地址',
  PRIMARY KEY (`Id`),
  KEY `IndexUser` (`UserId`,`UserType`) USING BTREE,
  KEY `IndexSystemId` (`SystemId`),
  KEY `IndexEventTime` (`EventTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RltTimeTable`
--

DROP TABLE IF EXISTS `RltTimeTable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RltTimeTable` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MinTime` int(11) NOT NULL COMMENT '最小时间',
  `MaxTime` int(11) NOT NULL COMMENT '最大时间',
  `TableName` varchar(16) NOT NULL DEFAULT '' COMMENT '表名',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='时间表名映射表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Statistic`
--

DROP TABLE IF EXISTS `Statistic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Statistic` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SystemId` int(11) NOT NULL COMMENT '系统ID',
  `EventId` int(11) NOT NULL COMMENT '时间ID',
  `EventTime` int(11) NOT NULL COMMENT '事件发生时间(周期为一天）',
  `AddrId` int(11) NOT NULL COMMENT '事件地点ID',
  `AttrId` int(11) NOT NULL COMMENT '属性ID',
  `Value` varchar(64) NOT NULL DEFAULT '' COMMENT '对应属性的值',
  `Count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '计数',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System`
--

DROP TABLE IF EXISTS `System`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `System` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统ID',
  `Name` varchar(32) NOT NULL DEFAULT '' COMMENT '系统名称',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='系统表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserDef`
--

DROP TABLE IF EXISTS `UserDef`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserDef` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户类型ID',
  `SystemId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '系统标识',
  `Name` varchar(32) NOT NULL DEFAULT '' COMMENT '用户类型名称',
  `Mark` varchar(32) NOT NULL DEFAULT '' COMMENT '类型备注',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `System_Name` (`SystemId`,`Name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户定义表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserMap`
--

DROP TABLE IF EXISTS `UserMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserMap` (
  `UserId1` varchar(32) NOT NULL DEFAULT '' COMMENT '用户ID1',
  `UserType1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型1',
  `UserId2` varchar(32) NOT NULL DEFAULT '' COMMENT '用户ID2',
  `UserType2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型2',
  PRIMARY KEY (`UserId1`,`UserType1`,`UserId2`,`UserType2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户映射表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-12 15:34:45
