<?php namespace App\Exceptions;

use App\Team;
use Throwable;

/**
 * Class TeamOutOfCreditsException
 *
 * @package App\Exceptions
 */
class TeamOutOfCreditsException extends \Exception
{
    /**
     * @var \App\Team
     */
    private $team;

    /**
     * TeamOutOfCreditsException constructor.
     *
     * @param \App\Team       $team
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        Team $team,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->team = $team;
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report(): void
    {
        //
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
        return response()->json(
            'Team insufficient credit amount does not allow proceed with' .
            ' current action.', 422
        );
    }
}
