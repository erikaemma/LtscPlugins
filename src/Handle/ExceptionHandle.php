<?php


namespace LTSC\Plugin\Handle;


use Throwable;

abstract class ExceptionHandle extends \RuntimeException
{
    private $what;
    private $why;

    public function __construct($what, $why, $message = "", $code = 0, Throwable $previous = null) {
        $this->what = $what;
        $this->why = $why;
        parent::__construct("$what happen an error because $why and with message $message.", $code, $previous);
    }

    public function getWhat() :string {
        return $this->what;
    }

    public function getWhy() :string {
        return $this->why;
    }
}