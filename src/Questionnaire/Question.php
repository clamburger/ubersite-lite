<?php
namespace Ubersite\Questionnaire;

class Question implements \JsonSerializable
{
    public $id;
    public $question;
    public $questionShort;
    private $answerType;
    public $answerOptions = null;
    public $answerOther = null;

    const OTHER_RESPONSE = 42;
    const DEFAULT_COLOUR = "white";

    public static $answerTypes = [
        'Text',
        'Radio',
        'Dropdown',
        '1-5',
        'Length'
    ];

    // TODO: we shouldn't have any special cases for things like this (not here, anyway)
    public static $coloursFive = [
        "white",
        "#F8696B",
        "#FBAA77",
        "#FFEB84",
        "#B1D580",
        "#63BE7B"
    ];

    public static $coloursFiveTwo = [
        "white",
        "#F8696B",
        "#FFEB84",
        "#63BE7B",
        "#FFEB84",
        "#F8696B"
    ];

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function populateFromDetails($details)
    {
        $this->question = $details['Question'];
        $this->setAnswerType($details['AnswerType']);
        if (isset($details['AnswerOptions'])) {
            $this->answerOptions = $details['AnswerOptions'];
        }
        if (isset($details['AnswerOther'])) {
            $this->answerOther = $details['AnswerOther'];
        }
        if (isset($details['QuestionShort'])) {
            $this->questionShort = $details['QuestionShort'];
        } else {
            $this->questionShort = $details['Question'];
        }
    }

    public function getAnswerType()
    {
        return $this->answerType;
    }

    public function setAnswerType($answerType)
    {
        if ($answerType == '1-5') {
            $answerType = 'Dropdown';
            $this->answerOptions = [5, 4, 3, 2, 1];
        } elseif ($answerType == 'Length') {
            $answerType = 'Dropdown';
            $this->answerOptions = [
                'Far too long', 'A little too long', 'Just right', 'A little too short', 'Far too short'
            ];
        }
        $this->answerType = $answerType;
    }

    public function getAnswerString($response)
    {
        // Either an empty string or no answer selected
        if (!$response) {
            return false;
        }

        // For text, return as-is.
        if ($this->answerType == "Text") {
            return $response;
        }

        // Look up the correct key in the answerOptions array.
        // Subtract 1 from the response since response values go from 1 to [x] and our arrays go from 0 to [x-1]
        if ($this->answerType == "Dropdown" || $this->answerType == "Radio") {
            if ($this->answerOther && $response == count($this->answerOptions)) {
                return self::OTHER_RESPONSE;
            }
            return $this->answerOptions[$response - 1];
        }

        throw new \Exception('Answer type not handled: ' . $this->answerType);
    }

    // TODO: not entirely pleased with having to include $users, perhaps there's a better way
    public function renderFeedback($allResponses, $users)
    {
        $output = "<h3>{$this->question}</h3>\n";
        $output .= "<ul>";
        if (!isset($allResponses[$this->id])) {
            $output .= "<li><em style='color: silver;'>No responses for this question</em></li>";
        } else {
            foreach ($allResponses[$this->id] as $response) {
                $sig = "- <em>" . $users[$response['Username']]->Name . "</em>";
                $stringResponse = $this->getAnswerString($response['Answer']);
                if ($stringResponse === self::OTHER_RESPONSE) {
                    $output .= "<li>Other: ".$allResponses[$this->id."-other"]
                                                [$response['Username']]['Answer']." $sig</li>";
                } elseif ($stringResponse) {
                    $output .= "<li>$stringResponse $sig</li>\n";
                }
            }
        }
        $output .= "</ul>";
        return $output;
    }

    public function getColour($response)
    {
        return self::DEFAULT_COLOUR;
    }

    public function jsonSerialize()
    {
        $return = ['Question' => $this->question];
        if ($this->question != $this->questionShort) {
            $return['QuestionShort'] = $this->questionShort;
        }
        $return['AnswerType'] = $this->answerType;
        if ($this->answerOptions !== null) {
            $return['AnswerOptions'] = $this->answerOptions;
        }
        if ($this->answerOther !== null) {
            $return['AnswerOther'] = $this->answerOther;
        }
        return $return;
    }
}
