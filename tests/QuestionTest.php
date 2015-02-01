<?php
namespace Ubersite\Tests;

use Ubersite\Questionnaire\Question;

class QuestionTest extends \PHPUnit_Framework_TestCase
{

    public function testThreeAnswerGradient()
    {
        $question = new Question('test');
        $question->setAnswerType('Dropdown');
        $question->answerOptions = range(1, 3);
        $question->colouredDropdown = Question::BEST_FIRST;

        $colours = $question->getColourScale();
        $expectedColours = ['#63be7b', '#ffeb84', '#f8696b'];
        $this->assertEquals($expectedColours, $colours);
    }

    public function testFourAnswerGradient()
    {
        $question = new Question('test');
        $question->setAnswerType('Dropdown');
        $question->answerOptions = range(1, 4);
        $question->colouredDropdown = Question::BEST_FIRST;

        $colours = $question->getColourScale();
        $expectedColours = ['#63be7b', '#cbdc81', '#fa9473', '#f8696b'];
        $this->assertEquals($expectedColours, $colours);
    }

    public function testFiveAnswerGradient()
    {
        $question = new Question('test');
        $question->setAnswerType('Dropdown');
        $question->answerOptions = range(1, 5);
        $question->colouredDropdown = Question::BEST_FIRST;

        $colours = $question->getColourScale();
        $expectedColours = ['#63be7b', '#b1d580', '#ffeb84', '#fa8a71', '#f8696b'];
        $this->assertEquals($expectedColours, $colours);
    }

    public function testFiveAnswerGradientBackwards()
    {
        $question = new Question('test');
        $question->setAnswerType('Dropdown');
        $question->answerOptions = range(1, 5);
        $question->colouredDropdown = Question::BEST_LAST;

        $colours = $question->getColourScale();
        $expectedColours = ['#f8696b', '#fa8a71', '#ffeb84', '#b1d580', '#63be7b'];
        $this->assertEquals($expectedColours, $colours);
    }

    public function testFiveAnswerGradientFromMiddle()
    {
        $question = new Question('test');
        $question->setAnswerType('Dropdown');
        $question->answerOptions = range(1, 5);
        $question->colouredDropdown = Question::BEST_IN_MIDDLE;

        $colours = $question->getColourScale();
        $expectedColours = ['#63be7b', '#ffeb84', '#f8696b', '#ffeb84', '#63be7b'];
        $this->assertEquals($expectedColours, $colours);
    }

    public function testTextResponse()
    {
        $question = new Question('test');
        $question->setAnswerType('Text');
        $response = 'It was a good activity.';
        $responseGiven = $question->getAnswerString($response);
        $this->assertEquals($response, $responseGiven);
    }

    public function testDropdownResponse()
    {
        $question = new Question('test');
        $question->setAnswerType('Dropdown');
        $question->answerOptions = ['Excellent', 'Okay', 'Average', 'Bad', 'Terrible'];
        $this->assertEquals('Excellent', $question->getAnswerString('1'));
        $this->assertEquals('Bad', $question->getAnswerString(4));
    }

    public function test1To5AnswerType()
    {
        $question = new Question('test');
        $question->setAnswerType('1-5');
        $this->assertEquals('Dropdown', $question->getAnswerType());
        $this->assertEquals([5, 4, 3, 2, 1], $question->answerOptions);
    }
}
