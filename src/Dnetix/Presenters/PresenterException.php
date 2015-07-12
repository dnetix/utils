<?php  namespace Dnetix\Presenters;

use Exception;

/**
 * Class PresenterException
 * @author Diego Calle
 * @package Dnetix\Presenters
 */
class PresenterException extends Exception {

    function __construct($message) {
        parent::__construct($message);
    }

}