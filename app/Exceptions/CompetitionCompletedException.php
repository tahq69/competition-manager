<?php namespace App\Exceptions;

use App\Competition;
use Exception;
use Throwable;

/**
 * Class CompetitionCompletedException
 *
 * @package App\Exceptions
 */
class CompetitionCompletedException extends Exception
{
    /**
     * @var \App\Competition
     */
    private $cm;

    /**
     * CompetitionCompletedException constructor.
     *
     * @param \App\Competition $cm
     * @param string           $message
     * @param int              $code
     * @param \Throwable|null  $previous
     */
    public function __construct(
        Competition $cm, $message = "", $code = 0, Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->cm = $cm;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(
            'Competition already completed registration and can`t be modified' .
            ' anymore.', 422
        );
    }
}
