CREATE TABLE responses (
  Username TEXT NOT NULL,
  QuizId INTEGER NOT NULL,
  QuestionStage INTEGER NOT NULL DEFAULT 0,
  Responses TEXT,
  PRIMARY KEY (Username, QuizId)
);

CREATE TABLE questionnaires (
  Id INTEGER NOT NULL PRIMARY KEY,
  Name TEXT,
  Pages TEXT,
  Intro TEXT
);

CREATE TABLE users (
  Username TEXT NOT NULL PRIMARY KEY,
  Name TEXT NOT NULL,
  Role TEXT NOT NULL DEFAULT 'camper',
  SmallGroup TEXT,
  Password TEXT
);
