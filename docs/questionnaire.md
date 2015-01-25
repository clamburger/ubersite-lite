# Questionnaire Format

A questionnaire is a JSON object containing a list of pages, which then contain groups and questions.


## Pages

* `Title` **required**: the title of the page.
* `Questions` **required**: a dictionary of Groups and Questions, indexed by their ID.
* `Intro`: if provided, the text specified here will appear at the top of the page. Full HTML can be used.


## Groups

* `Title` **required**: the title of the group.
* `Questions` **required**: a dictionary of Questions, indexed by their ID.
* `Collapsible`: if true, group will appear with the questions hidden and a button to show them. *Default: false*
* `Comments`: if true, a multi-line text input will be added at the end of the group with a corresponding label. *Default: true*


## Questions

* `Question` **required**: the actual question that will be displayed.
* `QuestionShort`: a short version of the question that will be used for table headers on the feedback page. If not provided, will default to the value of Question.
* `AnswerType` **required**: the type of response that is expected. Valid values are:
 * `Text`: single-line text input
 * `Textarea`: multi-line text input
 * `Radio`: radio buttons
 * `Dropdown`: dropdown menu
 * `1-5`: dropdown menu containing the numbers 5 to 1
 * `Length`: dropdown menu containing values related to length of time
* `AnswerOptions`: an array of answers that the user will be able to choose from. **Required** if using AnswerType "Radio" or "Dropdown", ignored otherwise.
* `AnswerOther`: if true, adds a single-line text input under the last radio button. Only valid for AnswerType "Radio". Note that this doesn't add a corresponding label, so you should include an "Other" option as the last AnswerOption if you use this. *Default: false*


# Things to note

* Pages, Groups and Questions will all be shown in the order that they are defined.
* Question and Group IDs **must not** end in `-comments` or `-other` as these are used internally.
* Groups are designed primarily for dropdowns. Other AnswerTypes **may** be used, but they might not be fully styled.
* Groups **should** keep all dropdown questions before all non-dropdown questions. No data loss will occur if you don't, but the feedback won't look as neat.
