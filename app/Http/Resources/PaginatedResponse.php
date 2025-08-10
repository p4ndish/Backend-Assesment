<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResponse extends JsonResource
{
    public bool $success;
    public string $message;
    public array $object;
    public int $pageNumber;
    public int $pageSize;
    public int $totalSize;
    public ?array $errors;

    public function __construct(
        $resource,
        bool $success = true,
        string $message = '',
        array $object = [],
        int $pageNumber = 1,
        int $pageSize = 10,
        int $totalSize = 0,
        ?array $errors = null
    ) {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
        $this->object = $object;
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
        $this->totalSize = $totalSize;
        $this->errors = $errors;
    }

    public static function success(
        $resource,
        string $message = 'Success',
        array $object = [],
        int $pageNumber = 1,
        int $pageSize = 10,
        int $totalSize = 0
    ): self {
        return new self($resource, true, $message, $object, $pageNumber, $pageSize, $totalSize);
    }

    public static function error($resource, string $message = 'Error', ?array $errors = null): self
    {
        return new self($resource, false, $message, [], 0, 0, 0, $errors);
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
            'pageNumber' => $this->pageNumber,
            'pageSize' => $this->pageSize,
            'totalSize' => $this->totalSize,
            'errors' => $this->errors,
        ];
    }
}
