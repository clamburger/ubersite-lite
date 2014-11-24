CREATE TABLE `questionnaire` (
  `UserID` varchar(20) NOT NULL,
  `QuizId` int(11) NOT NULL,
  `QuestionStage` smallint(5) NOT NULL DEFAULT '0',
  `Responses` text,
  PRIMARY KEY (`UserID`,`QuizId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `questionnaire_electives` (
  `ShortName` varchar(30) NOT NULL,
  `LongName` varchar(60) NOT NULL,
  `Type` varchar(10) NOT NULL,
  `Order` double unsigned NOT NULL,
  PRIMARY KEY (`ShortName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `questionnaire_pages` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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

CREATE TABLE `questionnaires` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text,
  `Pages` text,
  `Intro` text,
  `Outro` text,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `questionnaires` (`Id`, `Name`, `Pages`, `Intro`, `Outro`) VALUES (1, 'Camp Questionnaire', '{}', 'At the end of each &Uuml;bertweak we get all campers to fill out a questionnaire about camp.\r\nYour feedback is extremely useful and all of the leaders spend time after camp reviewing it to ensure that the next &Uuml;bertweak is better than ever!<br><br>\r\nWe encourage you to be completely honest: if you had a terrible time and hated all the leaders, make sure you tell us that. We will not hold any of this feedback against you.<br><br>\r\nThe questionnaire is broken up into a number of sections. Take as much time as you need for each section and please be completely honest! Keep in mind that once you complete a session you <strong>cannot go back to that section</strong>.<br><br>\r\nYour specimen has been processed and we are now ready to begin the test proper.', '<h3>Your questionnaire has been submitted!</h3>\r\n<h3>Thank you for all your responses!<br>\r\n&nbsp;&nbsp;&nbsp;<director names> and<br>\r\n&nbsp;&nbsp;&nbsp;the <camp name> team</h3>\r\nIf you made a mistake you would like to correct, please contact the nearest leader for assistance.');

CREATE TABLE `users` (
  `UserID` varchar(20) NOT NULL,
  `Name` text NOT NULL,
  `Category` varchar(10) NOT NULL DEFAULT 'camper',
  `DutyTeam` varchar(20) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;