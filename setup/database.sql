--
-- Definition of table `dutyteams`
--

CREATE TABLE `dutyteams` (
  `ID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Colour` varchar(20) NOT NULL DEFAULT 'FFFFFF',
  `FontColour` varchar(20) NOT NULL DEFAULT '000000',
  `WarCry` text,
  `Group` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`,`Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Definition of table `errors`
--

CREATE TABLE `errors` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UserID` varchar(20) NOT NULL,
  `RequestString` varchar(100) NOT NULL,
  `RequestMethod` char(4) NOT NULL,
  `Query` text NOT NULL,
  `Error` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Definition of table `people`
--

CREATE TABLE `people` (
  `UserID` varchar(20) NOT NULL,
  `Name` text NOT NULL,
  `Category` varchar(10) NOT NULL DEFAULT 'camper',
  `Admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Nickname` varchar(30) DEFAULT NULL,
  `DutyTeam` int(11) NOT NULL DEFAULT '0',
  `StudyGroup` varchar(10) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Definition of table `questionnaire`
--

CREATE TABLE `questionnaire` (
  `UserID` varchar(20) NOT NULL,
  `QuizId` int(11) NOT NULL,
  `QuestionStage` smallint(5) NOT NULL DEFAULT '0',
  `Responses` text,
  PRIMARY KEY (`UserID`,`QuizId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Definition of table `questionnaire_electives`
--

CREATE TABLE `questionnaire_electives` (
  `ShortName` varchar(30) NOT NULL,
  `LongName` varchar(60) NOT NULL,
  `Type` varchar(10) NOT NULL,
  `Order` double unsigned NOT NULL,
  PRIMARY KEY (`ShortName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `questionnaire_pages`
--

CREATE TABLE `questionnaire_pages` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Definition of table `questionnaire_questions`
--

CREATE TABLE `questionnaire_questions` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text,
  `HideName` tinyint(4) DEFAULT '0',
  `Questions` text,
  `PageId` int(11) DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `Expandable` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Definition of table `questionnaires`
--

CREATE TABLE `questionnaires` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text,
  `Pages` text,
  `Intro` text,
  `Outro` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;