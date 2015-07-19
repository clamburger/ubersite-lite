<?php
namespace Ubersite\Questionnaire;

class Question implements \JsonSerializable
{
    // Constants relating to the order of colours in dropdown boxes
    const BEST_FIRST = 1;
    const BEST_LAST = 2;
    const BEST_IN_MIDDLE = 3;

    public $id;
    public $question;
    public $questionShort;
    private $answerType;
    public $answerOptions;
    public $answerOther;
    public $colouredDropdown;

    private static $dropdownColours = [
        [99, 190, 123],     // Green    #63BE7B
        [255, 235, 132],    // Yellow   #FFEB84
        [248, 105, 107]     // Red      #F8696B
    ];

    const OTHER_RESPONSE = 42;
    const DEFAULT_COLOUR = "white";

    public static $answerTypes = [
        'Text',
        'Radio',
        'Dropdown',
        '1-5',
        'Length'
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
        if (isset($details['ColouredDropdown'])) {
            $this->colouredDropdown = $details['ColouredDropdown'];
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
        $output = "<h4>{$this->question}</h4>\n";
        $output .= "<ul>";
        if (!isset($allResponses[$this->id])) {
            $output .= "<li><span style='color: silver;'>No responses for this question</span></li>";
        } else {
            foreach ($allResponses[$this->id] as $response) {
                $sig = "- <em>" . $users[$response['Username']]->name . "</em>";
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

    /**
     * Get the list of colours used for the dropdown responses.
     * @return array An array of colours in hexadecimal format (e.g. #000000)
     */
    public function getColourScale()
    {
        if ($this->getAnswerType() !== 'Dropdown') {
            throw new \RuntimeException('Colour scales are only supported with dropdown questions.');
        }

        // In the unlikely event that this function is called with only one response, return green
        if (count($this->answerOptions) <= 1) {
            return $this->getGradientColourAtPosition(0.0);
        }

        $colours = [];

        $answerCount = count($this->answerOptions);
        if ($this->colouredDropdown == self::BEST_IN_MIDDLE) {
            $denominator = ($answerCount - 1) / 2;
        } else {
            $denominator = $answerCount - 1;
        }

        $fraction = 1 / $denominator;

        // If we have n responses, we need multiples of 1 / (n-1) to get a smooth scale between 0.0 and 1.0.
        // The reason it's n-1 and not just n is because we start with 0.0.
        // Example for five responses: 0.0, 0.25, 0.5, 0.75, 1.0 (note the multiples of 1/4, starting from 0.0)
        $count = -1;
        while ($count < $answerCount - 1) {
            $count++;
            if ($this->colouredDropdown == self::BEST_IN_MIDDLE) {
                $distanceFromHalfway = abs($denominator - $count);
                $percent = 1 - $fraction * $distanceFromHalfway;
            } elseif ($this->colouredDropdown == self::BEST_LAST) {
                $percent = 1 - $fraction * $count;
            } else {
                $percent = $fraction * $count;
            }
            $colours[] = $this->getGradientColourAtPosition($percent);
        }

        return $colours;
    }

    /**
     * A helper function to get the colour at a particular point in the three colour gradient we use
     * @param float $percent A number between 0.0 and 1.0, indicating how far through the gradient you are
     * @return string A colour in hexadecimal format (e.g. #000000)
     */
    private function getGradientColourAtPosition($percent)
    {
        list($start, $middle, $end) = self::$dropdownColours;

        if ($percent <= 0.5) {
            // At 50% or less, look between green and yellow
            $percent *= 2;
            $red = round($start[0] + $percent * ($middle[0] - $start[0]));
            $green = round($start[1] + $percent * ($middle[1] - $start[1]));
            $blue = round($start[2] + $percent * ($middle[2] - $start[2]));
        } else {
            // At more than 50%, look between yellow and red
            $percent *= 2 - 1;
            $red = round($middle[0] + $percent * ($end[0] - $middle[0]));
            $green = round($middle[1] + $percent * ($end[1] - $middle[1]));
            $blue = round($middle[2] + $percent * ($end[2] - $middle[2]));
        }

        return sprintf('#%02x%02x%02x', $red, $green, $blue);
    }
}
