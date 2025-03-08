<?php

namespace app\core\exception;

use Exception;

class ForbiddenException extends Exception
{
    protected $message = 'You dont\'t have permission to access this page';
    protected $code = 403;
}