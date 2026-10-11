<?php

namespace App\Http\Resources\Public;

use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Batch */
class BatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date?->toDateString(),
            'enroll_last_date' => $this->lastEnrollDate()?->toDateString(),
            'seat_limit' => $this->seat_limit,
            'seats_left' => $this->seatsLeft(),   // null = unlimited
            // Only on the course details page:
            $this->mergeWhen($this->relationLoaded('instructor'), fn () => [
                'approx_end_date' => $this->approx_end_date?->toDateString(),
                'instructor' => $this->instructor ? [
                    'name' => $this->instructor->name,
                    'designation' => $this->instructor->designation,
                    'avatar' => Media::url($this->instructor->avatar),
                ] : null,
            ]),
        ];
    }
}
