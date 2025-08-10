<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'applicant_id' => $this->applicant_id,
            'job_id' => $this->job_id,
            'resume_link' => $this->resume_link,
            'cover_letter' => $this->cover_letter,
            'status' => $this->status,
            'applied_at' => $this->applied_at,
            'applicant' => new UserResource($this->whenLoaded('applicant')),
            'job' => new JobListingResource($this->whenLoaded('job')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
