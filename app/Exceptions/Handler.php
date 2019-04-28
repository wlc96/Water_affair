<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $path = $request->path();

        if($e->getPrevious() && $e->getPrevious() instanceof \CustomException )
        {
            $e = $e->getPrevious();
        }

        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        $format = $request->input('format');

        $message = $e->getMessage();

        if($request->ajax() || in_array($format, ['json', 'jsonp'])){

            $data = [
                'result' => false,
                'message' => $message,
            ];

            if($e instanceof \CustomException)
            {
                $statusCode = 200;

                $data = $data + $e->data;
            }

            if($code = $e->getCode())
            {
                $data['error_code'] = $code;
            }

            if($format == 'jsonp')
            {
                return response()->jsonp($request->input('callback'), $data, $statusCode);                
            }

            return response()->json($data, $statusCode);
        }

        if(!config('app.debug') && $statusCode != 503)
        {
            if($statusCode == 404)
            {
                return response()->view('errors.404', array(), 404);
            }

            if($e instanceof \CustomException)
            {
                return response()->view('errors.custome', compact('statusCode', 'message'), $statusCode);
            }

            return response()->view('errors.500', compact('statusCode'), $statusCode);
        }

        return parent::render($request, $e);
    }
}
