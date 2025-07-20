<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Day;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class EventScheduleController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $days = Day::with('events')->get()->map(function (Day $day) {
                return [
                    'id' => $day->id,
                    'day_title' => $day->day_title,
                    'date' => $day->date ? Carbon::parse($day->date)->format('d F Y') : null,
                    'events' => $day->events->map(function ($event) {
                        return [
                            'id' => $event->id,
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
                'data' => $days,
            ]);
        } catch (\Throwable $e) {
            \Log::error('EventScheduleController@index Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching the event schedule.'
            ], 500);
        }
    }
}
