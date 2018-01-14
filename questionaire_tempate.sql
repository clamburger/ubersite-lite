PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
INSERT INTO questionnaires VALUES(1,'Camper Survey',replace('[
  {
    "Title": "Activity Feedback",
    "Sections": [
      {
        "Title": "Spiritual Input",
        "Questions": {
          "your-relationship-with-jesus-2": {
            "Question": "How would you describe your relationship with Jesus?",
            "AnswerType": "Radio",
            "AnswerOptions": [
              "Not interested in one",
              "I don't have one, but I would like one",
              "I have one, but I am inconsistent in it",
              "I have been struggling with my relationship with Jesus, but this camp has helped me turn back to Him",
              "I have dedicated my life to Christ and became a Christian on this camp",
              "My relationship with Jesus is strong and growing",
              "None of the above (please provide extra detail in the comments, if you feel comfortable doing so)"
            ]
          },
          "personal-bible-time-helpful-2": {
            "Question": "Did you find the Personal Bible Time helpful?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "discussion-topics-covered-relevant-2": {
            "Question": "Did you find the small group discussions relevant?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "in-your-small-group": {
            "Question": "Did you feel comfortable asking questions and contributing in your small group?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "any-other-comments": {
            "Question": "Any other comments?",
            "AnswerType": "Text"
          }
        }
      },
      {
        "Title": "Worship",
        "Questions": {
          "enjoy-the-songs-chosen": {
            "Question": "How much did you enjoy the songs chosen?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "any-other-comments-2": {
            "Question": "Any other comments?",
            "AnswerType": "Text"
          }
        }
      },
      {
        "Title": "Computer Games",
        "Questions": {
          "you-enjoy-game-strategy": {
            "Question": "How much did you enjoy Computer Games?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "was-your-favourite-game": {
            "Question": "What was your favourite game?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              "Trosnoth",
              "StarCraft",
              "Artemis",
              "Risk of Rain",
              "BZFlag",
              "Board Games"
            ]
          },
          "your-least-favourite-game": {
            "Question": "What was your least favourite game?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              "Trosnoth",
              "StarCraft",
              "Artemis",
              "Risk of Rain",
              "BZFlag",
              "Board Games"
            ]
          },
          "any-other-comments-3": {
            "Question": "Any other comments?",
            "AnswerType": "Text"
          }
        }
      },
      {
        "Title": "Outdoor Games",
        "Questions": {
          "you-enjoy-outdoor-games": {
            "Question": "How much did you enjoy Outdoor Games?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "the-rules-and-instructions": {
            "Question": "How clear were the rules and instructions?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "any-other-comments-4": {
            "Question": "Any other comments?",
            "AnswerType": "Text"
          }
        }
      }
    ],
    "Intro": "<div style=\"font-size: x-large; font-weight: bold; margin-bottom: 20px;\">5 = the best. 1 = the worst.</div>"
  },
  {
    "Title": "Elective Feedback",
    "Sections": [
      {
        "Title": "Example Elective",
        "Questions": {
          "lc-you-enjoy-this-elective": {
            "Question": "How much did you enjoy this elective?",
            "AnswerType": "Dropdown",
            "AnswerOptions": [
              5,
              4,
              3,
              2,
              1
            ]
          },
          "lc-us-about-this-elective": {
            "Question": "Anything else you want to tell us about this elective?",
            "AnswerType": "Text"
          }
        },
        "Collapsible": true
      },
      {
        "Title": "Electives in General",
        "Questions": {
          "about-electives-in-general": {
            "Question": "Any comments that you would like to make about electives in general?",
            "AnswerType": "Text"
          }
        }
      }
    ],
    "Intro": "<div style=\"font-size: x-large; font-weight: bold; margin-bottom: 20px;\">5 = the best. 1 = the worst.</div>"
  },
  {
    "Title": "Final Thoughts",
    "Sections": [
      {
        "Title": "General Feedback",
        "Questions": {
          "like-most-about-camp": {
            "Question": "What did you like most about camp?",
            "AnswerType": "Text"
          },
          "like-least-about-camp": {
            "Question": "What did you like least about camp?",
            "AnswerType": "Text"
          },
          "youd-like-to-make": {
            "Question": "Do you have any other final comments or suggestions that you''d like to make?",
            "AnswerType": "Text"
          },
          "with-you-after-camp": {
            "Question": "Who are some leaders who you would like to keep in touch with you after camp?",
            "AnswerType": "Text"
          }
        }
      }
    ]
  }
]
','\n',char(10)),replace('Good news everyone! The questionnaire is now shorter and more useful than ever.\n\nYour feedback is extremely useful and all of the leaders spend time after camp reviewing it to ensure that the next Übertweak is better than ever!\n\nWe encourage you to be completely honest: if you had a terrible time and hated all the leaders, make sure you tell us that. We will not hold any of this feedback against you.\n\nThe questionnaire is broken up into a number of sections. Take as much time as you need for each section and please be completely honest! Keep in mind that once you complete a session you <strong>cannot go back to that section</strong>.\n\nYour specimen has been processed and we are now ready to begin the test proper. ','\n',char(10)));
COMMIT;
