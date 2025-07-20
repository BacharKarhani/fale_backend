<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Day;
use Illuminate\Http\JsonResponse;

class EventScheduleController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Day::with('events')->get()->map(function (Day $day) {
            return [
                'day' => $day->day_title,
                'date' => $day->date ? $day->date->format('d F Y') : null,
                'events' => $day->events->map(function ($event) {
                    return [
                        'title' => $event->title,
                        'description' => $event->description,
                        'image' => (!empty($event->image) && file_exists(public_path('images/' . $event->image)))
                            ? asset('images/' . $event->image)
                            : asset('images/default-placeholder.jpg'),
                        'time' => $event->time,
                        'address' => $event->address,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
