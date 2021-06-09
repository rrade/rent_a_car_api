<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class ValidationException extends Exception
{
    private $validator;
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->validator->errors()->first(),
            'code' => 'E999'
        ], $this->code);
    }
}
