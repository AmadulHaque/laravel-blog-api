<?php


function successResponse($message, $data = [], $statusCode = 200)
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}



function errorResponse($message, $errors = [], $statusCode = 400)
{
    return response()->json([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $statusCode);
}
