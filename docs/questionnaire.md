# Questionnaire Specification

This document details how questionnaires are stored in the database. It's recommended to use the editor rather than
editing the JSON manually, as it's significantly easier and less prone to error.

At the top level, a questionnaire is a JSON object containing a list of pages.

## Pages

* `Title` **required**: the title of the page.
* `Sections` **required**: a list of section objects that will be shown on the page.
* `Intro`: if provided, the text specified here will appear at the top of the page. Full HTML can be used.


## Sections

* `Title` **required**: the title of the section.
* `Questions` **required**: a dictionary of question objects, indexed by their ID.
* `Collapsible`: if true, the section will appear with the questions hidden and a button to show them. *Default: false*


## Questions

* `Question` **required**: the actual question that will be displayed.
* `QuestionShort`: a short version of the question that will be used for table headers on the feedback page. If not provided, will default to the value of Question.
* `AnswerType` **required**: the type of response that is expected. Valid values are:
 * `Text`: text input
 * `Radio`: radio buttons
 * `Dropdown`: dropdown menu
* `AnswerOptions`: an array of answers that the user will be able to choose from. **Required** if using AnswerType "Radio" or "Dropdown", ignored otherwise.
* `AnswerOther`: if true, adds a single-line text input under the last radio button. Only valid for AnswerType "Radio". Note that this doesn't add a corresponding label, so you should include an "Other" option as the last AnswerOption if you use this. *Default: false*


# Things to note

* Pages, sections and questions will all be shown in the order that they are defined.
* Question IDs **must not** end in `-other` as that value is used internally.
* Sections **should** keep all dropdown questions before all non-dropdown questions. No data loss will occur if you don't, but the feedback won't look as neat.
