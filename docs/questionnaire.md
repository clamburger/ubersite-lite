# Questionnaire Format

A questionnaire is a JSON object containing a list of pages.


## Pages

* `Title` **required**: the title of the page.
* `Sections` **required**: a list of section objects that will be shown on the page.
* `Intro`: if provided, the text specified here will appear at the top of the page. Full HTML can be used.


## Sections

* `Title` **required**: the title of the section.
* `Questions` **required**: a dictionary of question objects, indexed by their ID.
* `Collapsible`: if true, the section will appear with the questions hidden and a button to show them. *Default: false*
* `Comments`: if true, a multi-line text input will be added at the end of the section with a corresponding label. *Default: true*
* `Border`: if false, the section will not get the normal fieldset that appears around sections, and `Collapsible` and `Comments` will both be set to false. *Default: true*


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

* Pages, sections and questions will all be shown in the order that they are defined.
* Question IDs **must not** end in `-comments` or `-other` as these are used internally.
* Sections are designed primarily for dropdowns. If other AnswerTypes are used, it's recommended that `Border` is set to false.
* Sections **should** keep all dropdown questions before all non-dropdown questions. No data loss will occur if you don't, but the feedback won't look as neat.
