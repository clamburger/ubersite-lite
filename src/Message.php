<?php
namespace Ubersite;

/*
 * Stores a simple message to display to the user.
 */
class Message
{

    public $type;
    public $message;

    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }
}
