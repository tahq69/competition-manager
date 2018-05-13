<?php namespace App\Exceptions;

use Throwable;

/**
 * Class RouteBindingOverlapException
 *
 * @package App\Exceptions
 */
class RouteBindingOverlapException extends \Exception
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $repositories;

    /**
     * TeamOutOfCreditsException constructor.
     *
     * @param string          $tableName
     * @param array           $repositories
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $tableName,
        array $repositories,
        string $message = "",
        int $code = 0,
        Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->tableName = $tableName;
        $this->repositories = $repositories;
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report(): void
    {
        \Log::error('Route binding overlaps', [
            'table' => $this->tableName,
            'repositories' => $this->repositories,
        ]);
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
            'Found invalid count of repositories for request parameter. ' .
            'Please check parameter binding namings to avoid overlaps.', 500
        );
    }
}
