<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller for all application controllers.
 *
 * All controllers extend this to get the authorize() and validate() helpers.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
