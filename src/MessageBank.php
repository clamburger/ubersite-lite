<?php
namespace Ubersite;

/*
 * Stores a number of messages which will be displayed to the user.
 * It's not really a queue, since all messages get displayed at once.
 */
class MessageBank
{

  // Each type of message is stored as a subarray of $messages.
    private $messages = array();

    public function __construct()
    {
        if (isset($_SESSION['messages'])) {
            $this->messages = $_SESSION['messages'];
        }
    }

    public function addMessage(Message $message)
    {
        if (!isset($this->messages[$message->type])) {
            $this->messages[$message->type] = [];
        }
        $this->messages[$message->type][] = $message->message;
        $_SESSION['messages'] = $this->messages;
    }

    public function getMessages()
    {
        $messages = $this->messages;

      // Much like James Bond, we burn our messages after reading.
        $this->messages = [];
        unset($_SESSION['messages']);

        return $messages;
    }
}
