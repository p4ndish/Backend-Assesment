<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResponse extends JsonResource
{
    public bool $success;
    public string $message;
    public $object;
    public ?array $errors;

    public function __construct($resource, bool $success = true, string $message = '', $object = null, ?array $errors = null)
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
        $this->object = $object;
        $this->errors = $errors;
    }

    public static function success($resource, string $message = 'Success', $object = null): self
    {
        return new self($resource, true, $message, $object);
    }

    public static function error($resource, string $message = 'Error', ?array $errors = null): self
    {
        return new self($resource, false, $message, null, $errors);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'object' => $this->object,
            'errors' => $this->errors,
        ];
    }
}
