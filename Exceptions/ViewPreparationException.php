<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Exceptions;

use Exception;
use Throwable;

class ViewPreparationException extends Exception
{
    /**
     * ViewPreparationException constructor.
     *
     * @param Throwable $previous
     */
    public function __construct(Throwable $previous)
    {
        parent::__construct('There was an error during environment notice view preparation', 1, $previous);
    }
}
