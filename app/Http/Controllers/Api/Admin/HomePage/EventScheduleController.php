<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Day;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventScheduleController extends Controller
{
    /**
     * 🟢 Get all days with their events
     */
    public function index(): JsonResponse
    {
        try {
            $days = Day::with('events')->get()->map(function (Day $day) {
                return $this->formatDay($day);
            })->values();

            return response()->json([
                'success' => true,
                'data' => $days,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error fetching all days', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching all event schedules.',
            ], 500);
        }
    }

    /**
     * 🟢 Get a single event by its ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $event = Event::with('day')->find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatEvent($event),
            ], 200);
        } catch (\Throwable $e) {
            Log::error("Error fetching event with ID: $id", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the event.',
            ], 500);
        }
    }

    /**
     * 🛠 Format Day data with its events
     */
    private function formatDay(Day $day): array
    {
        return [
            'id' => $day->id,
            'day_title' => $day->day_title,
            'date' => $day->date ? Carbon::parse($day->date)->format('d F Y') : null,
            'events' => $day->events->map(function ($event) {
                return $this->formatEvent($event);
            })->values(),
        ];
    }

    /**
     * 🛠 Format single Event data
     */
    private function formatEvent($event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'image' => (!empty($event->image) && file_exists(public_path('images/' . $event->image)))
                ? asset('images/' . $event->image)
                : asset('images/default-placeholder.jpg'),
            'time' => $event->time,
            'address' => $event->address,
            'day' => [
                'id' => $event->day?->id,
                'day_title' => $event->day?->day_title,
                'date' => $event->day?->date ? Carbon::parse($event->day->date)->format('d F Y') : null,
            ],
        ];
    }
}
