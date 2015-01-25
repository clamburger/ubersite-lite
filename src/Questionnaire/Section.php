<?php
namespace Ubersite\Questionnaire;

class Section implements \JsonSerializable
{
    public $title;
    /** @var Question[] */
    public $questions = [];
    public $collapsible = false;
    public $border = true;
  
    public function __construct($details)
    {
        $this->title = $details->Title;
        foreach ($details->Questions as $id => $question) {
            $this->questions[] = new Question($id, $question);
        }
        if (isset($details->Collapsible)) {
            $this->collapsible = $details->Collapsible;
        }
        if (isset($details->Border)) {
            $this->border = $details->Border;
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
  
    public function renderHTML()
    {
        $out = "";

        if ($this->border) {
            $extraClass = $this->collapsible ? "optquest" : "";
            $out .= "<fieldset class='question-group $extraClass'>";
            if ($this->collapsible) {
                $out .= "<legend>{$this->title} <span class='help'>click to view questions</span></legend>";
                $out .= "<div class='hide-container'>";
            } else {
                $out .= "<legend>{$this->title}</legend>";
            }

            foreach ($this->questions as $key => $question) {
                $out .= $question->renderHTML(($key + 1) . ". ");
            }

            if ($this->collapsible) {
                $out .= "</div>";
            }
            $out .= "</fieldset>";
        } else {
            foreach ($this->questions as $key => $question) {
                $out .= $question->renderHTML();
            }
        }
        return $out;
    }

    public function renderFeedback($allResponses, $users)
    {
        $output = "<fieldset class='question-group feedback'>";
        $output .= "<legend>{$this->title}</legend>";

        $dropdowns = 0;
        $acceptedTypes = ["1-5", "Length", "Dropdown"];
        $responders = [];
        foreach ($this->questions as $question) {
            if (in_array($question->answerType, $acceptedTypes)) {
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
        if (!$this->border) {
            $return['Border'] = false;
        }
        return $return;
    }
}
