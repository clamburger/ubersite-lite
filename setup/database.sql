CREATE TABLE responses (
  Username TEXT NOT NULL,
  QuizId INTEGER NOT NULL,
  QuestionStage INTEGER NOT NULL DEFAULT 0,
  Responses TEXT,
  PRIMARY KEY (Username, QuizId)
);

CREATE TABLE electives (
  ShortName TEXT NOT NULL PRIMARY KEY,
  LongName TEXT NOT NULL,
  Type TEXT NOT NULL,
  Sorting double unsigned NOT NULL
);

CREATE TABLE questionnaires (
  Id INTEGER NOT NULL PRIMARY KEY,
  Name TEXT,
  Pages TEXT,
  Intro TEXT
);

INSERT INTO `questionnaires` (`Id`, `Name`, `Pages`, `Intro`) VALUES
  (1, 'Camp Questionnaire', '{
    "Questions": {},
    "Groups": {},
    "Pages": {},
    "PageOrder": []
}', 'At the end of each Übertweak we get all campers to fill out a questionnaire about camp.
Your feedback is extremely useful and all of the leaders spend time after camp reviewing it to ensure that the next Übertweak is better than ever!<br><br>
We encourage you to be completely honest: if you had a terrible time and hated all the leaders, make sure you tell us that. We will not hold any of this feedback against you.<br><br>
The questionnaire is broken up into a number of sections. Take as much time as you need for each section and please be completely honest! Keep in mind that once you complete a session you <strong>cannot go back to that section</strong>.<br><br>
Your specimen has been processed and we are now ready to begin the test proper.');

CREATE TABLE users (
  Username TEXT NOT NULL PRIMARY KEY,
  Name TEXT NOT NULL,
  Role TEXT NOT NULL DEFAULT 'camper',
  DutyTeam TEXT,
  Password TEXT
);
