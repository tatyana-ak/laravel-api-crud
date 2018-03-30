<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * @SWG\Swagger(
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Users API documentation",
 *         description=""
 *     ),
 *     @SWG\Definition(
 *         definition="errorModel",
 *         required={"error", "message"},
 *         @SWG\Property(
 *             property="error",
 *             type="boolean",
 *             default=true
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *        )
 *     )
 * )
 */

abstract class ApiController extends BaseController
{

}
