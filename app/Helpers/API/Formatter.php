<?php

namespace App\Helpers\API;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

trait Formatter
{
    private $http_status = [
        'unauthenticated_error' => 401,
        'with_error' => 417,
        'validation_error' => 422,
        'success' => 200,
        'created' => 201,
        'email_unverified' => 403,
        'sms_unverified' => 400,
    ];

    public function withSuccess($data)
    {
        return $this->response($data);
    }

    public function withCreated($data)
    {
        return $this->response($data, 'created');
    }

    public function withErrors($data)
    {
        return $this->response($data, 'with_error');
    }

    public function unauthenticatedError($data)
    {
        return $this->response($data, 'unauthenticated_error');
    }

    public function validationErrorResponse(ValidationException $exception)

    {

        $messages = array_map(function ($v) {
            return $v[0];
        }, $exception->errors());

        return $this->response($messages, 'validation_error');

    }

    private function response($data, $status = 'success')
    {
        $d = [];
        if ($data instanceof Collection) $data = $data->toArray();
        if (is_string($data)) {
            $d['string_data'] = $data;
        } elseif (is_object($data) || (is_array($data) && count(array_filter(array_keys($data), 'is_string')) > 0)) {
            $d['json_object'] = $data;
        } else {
            $d['json_array'] = $data;
        }
        return response([
            'status' => $this->http_status[$status],
            'data' => $d
        ], $this->http_status[$status]);
    }
}
