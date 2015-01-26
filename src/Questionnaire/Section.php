<?php
namespace Ubersite\Questionnaire;

class Section implements \JsonSerializable
{
    public $title = 'Untitled Section';
    /** @var Question[] */
    public $questions = [];
    public $collapsible = false;

    public function populateFromDetails($details)
    {
        $this->title = $details['Title'];
        foreach ($details['Questions'] as $id => $questionDetails) {
            $question = new Question($id);
            $question->populateFromDetails($questionDetails);
            $this->questions[] = $question;
        }
        if (isset($details['Collapsible'])) {
            $this->collapsible = $details['Collapsible'];
        }
    }

    public function deleteQuestion($questionId)
    {
        foreach ($this->questions as $index => $question) {
            if ($question->id === $questionId) {
                array_splice($this->questions, $index, 1);
                break;
            }
        }
    }

    public function addQuestion($question)
    {
        $this->questions[] = $question;
    }

    public function renderFeedback($allResponses, $users)
    {
        $output = "<fieldset class='question-group feedback'>";
        $output .= "<legend>{$this->title}</legend>";

        $dropdowns = 0;
        $acceptedTypes = ["Dropdown"];
        $responders = [];
        foreach ($this->questions as $question) {
            if (in_array($question->getAnswerType(), $acceptedTypes)) {
                $dropdowns++;

                if (!isset($allResponses[$question->id])) {
                    continue;
                }
                foreach ($allResponses[$question->id] as $response) {
                    $responders[$response['Username']] = $users[$response['Username']]->Name;
                }
            } else {
                break;
            }
        }

        asort($responders);

        if ($dropdowns && $responders) {
            $output .= "<table>\n";
            $output .= "<tr>\n";
            $output .= "  <th>Person</th>\n";
            for ($i = 0; $i < $dropdowns; $i++) {
                $output .= "  <th style='width: 100px;'>{$this->questions[$i]->questionShort}</th>\n";
            }
            $output .= "</tr>\n";
            foreach ($responders as $username => $person) {
                $output .= "<tr>\n";
                $output .= "  <td>$person</td>";
                for ($i = 0; $i < $dropdowns; $i++) {
                    /** @var Question $question */
                    $question = $this->questions[$i];

                    if (isset($allResponses[$question->id][$username])) {
                        $response = $allResponses[$question->id][$username]['Answer'];
                        $bgColour = $question->getColour($response);
                        $response = $question->getAnswerString($response);
                        $output .= "  <td style='background-color: $bgColour;'>$response</td>\n";
                    } else {
                        $output .= "  <td style='background-color: ".Question::DEFAULT_COLOUR.";'>--</td>";
                    }
                }
                $output .= "</tr>\n";
            }
            $output .= "</table>\n";
        }

        foreach ($this->questions as $key => $question) {
            if ($key < $dropdowns) {
                continue;
            }
            $output .= $question->renderFeedback($allResponses, $users);
        }
        $output .= "</fieldset>";
        return $output;
    }

    public function jsonSerialize()
    {
        $return = ['Title' => $this->title];

        $questions = [];
        foreach ($this->questions as $question) {
            $questions[$question->id] = $question;
        }
        $return['Questions'] = $questions;

        if ($this->collapsible) {
            $return['Collapsible'] = true;
        }
        return $return;
    }
}
