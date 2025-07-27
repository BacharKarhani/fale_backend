<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Day;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EventScheduleController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $days = Day::with('events')->get()->map(function ($day) {
                return $this->formatDay($day);
            });

            return response()->json([
                'success' => true,
                'data' => $days,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch days/events', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch events.'], 500);
        }
    }

    public function show($id): JsonResponse
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
            Log::error('Failed to fetch event', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch event.'], 500);
        }
    }

   public function store(Request $request): JsonResponse
{
    try {
        $validated = $request->validate([
            'day_id' => 'required|exists:days,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $data = $request->only(['day_id', 'title', 'description', 'time', 'address']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/events', $imageName);
            $data['image'] = $imageName;
        } else {
            $data['image'] = '';
        }

        $event = Event::create($data);

        return response()->json([
            'success' => true,
            'data' => $this->formatEvent($event->load('day')),
        ], 201);
    } catch (\Throwable $e) {
        Log::error('Failed to create event', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to create event.'], 500);
    }
}


   public function update(Request $request, $id): JsonResponse
{
    try {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        $validated = $request->validate([
            'day_id' => 'sometimes|required|exists:days,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'time' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $data = $request->only(['day_id', 'title', 'description', 'time', 'address']);

        if ($request->hasFile('image')) {
            if ($event->image && Storage::exists('public/events/' . $event->image)) {
                Storage::delete('public/events/' . $event->image);
            }

            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/events', $imageName);
            $data['image'] = $imageName;
        }

        $event->update($data);

        return response()->json([
            'success' => true,
            'data' => $this->formatEvent($event->fresh('day')),
        ], 200);
    } catch (\Throwable $e) {
        Log::error('Failed to update event', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to update event.'], 500);
    }
}


    public function destroy($id): JsonResponse
    {
        try {
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.',
                ], 404);
            }

            if ($event->image && file_exists(public_path('images/' . $event->image))) {
                unlink(public_path('images/' . $event->image));
            }

            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully.',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to delete event', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete event.'], 500);
        }
    }

    public function publicSchedule(): JsonResponse
    {
        try {
            $days = Day::with('events')->get()->map(function ($day) {
                return $this->formatDay($day);
            });

            return response()->json([
                'success' => true,
                'data' => $days,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch public schedule', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch schedule.'], 500);
        }
    }

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

    private function formatEvent($event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'image' => (!empty($event->image) && file_exists(public_path('images/' . $event->image)))
                ? 'images/' . $event->image
                : null,
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

