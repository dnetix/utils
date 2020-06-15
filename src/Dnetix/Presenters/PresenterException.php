<?php

namespace Dnetix\Presenters;

use Exception;

/**
 * Class PresenterException.
 * @author Diego Calle
 */
class PresenterException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
