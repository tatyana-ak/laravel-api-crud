<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data, $status = 200) {
            return Response::json([
                'error'  => false,
                'data' => $data,
            ], $status);
        });

        Response::macro('error', function ($message, $status = 400) {
            $errors = null;
            if (!is_string($message)) {
                $errors = $message;
            }
            return Response::json(array_filter([
                'error' => true,
                'message' => is_null($errors) ? $message : sprintf('%d %s', $status, BaseResponse::$statusTexts[$status]),
                'errors' => $errors
            ]), $status);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
