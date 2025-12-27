<?php

namespace App\Livewire\Member\EventsV2;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\PointsService;
use Illuminate\Database\QueryException;
use Livewire\Attributes\On;
use Livewire\Component;

class MyEvents extends Component
{
    #[On('event-joined')]
    public function refreshOnJoinedEvent(): void
    {
        // Intentionally empty: calling this method triggers a re-render.
    }

    #[On('refresh-my-events')]
    public function refreshMyEvents(): void
    {
        // Intentionally empty: calling this method triggers a re-render.
    }

    public function addToFavorites(int $eventId): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($eventId);

        try {
            $registration = EventRegistration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    'type' => 'app',
                    'status' => 'favorited',
                    'registered_at' => now(),
                ]
            );

            if ($registration->wasRecentlyCreated) {
                session()->flash('message', 'Event added to favorites.');
                session()->flash('message_type', 'success');
            } else {
                session()->flash('message', 'Event is already in your favorites.');
                session()->flash('message_type', 'info');
            }
        } catch (QueryException) {
            session()->flash('message', 'Unable to add event to favorites.');
            session()->flash('message_type', 'error');
        }
    }

    public function markInterested(int $eventId): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($eventId);

        try {
            $registration = EventRegistration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    'type' => 'app',
                    'status' => 'interested',
                    'registered_at' => now(),
                ]
            );

            if ($registration->wasRecentlyCreated) {
                session()->flash('message', 'Marked as interested.');
                session()->flash('message_type', 'success');
            } else {
                if ($registration->status !== 'interested') {
                    $registration->update(['status' => 'interested']);
                    session()->flash('message', 'Marked as interested.');
                    session()->flash('message_type', 'success');
                } else {
                    session()->flash('message', 'You are already marked as interested.');
                    session()->flash('message_type', 'info');
                }
            }
        } catch (QueryException) {
            session()->flash('message', 'Unable to mark event as interested.');
            session()->flash('message_type', 'error');
        }
    }

    public function join(int $eventId): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($eventId);

        if ($event->max_participants && $event->max_participants > 0) {
            $current = $event->registrations()->count();
            if ($current >= $event->max_participants) {
                session()->flash('message', 'This event is already full.');
                session()->flash('message_type', 'error');
                return;
            }
        }

        try {
            $registration = EventRegistration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    'type' => 'app',
                    'status' => 'registered',
                    'registered_at' => now(),
                ]
            );

            if ($registration->wasRecentlyCreated) {
                app(PointsService::class)->awardEventJoin($user, $event);
                session()->flash('message', 'You have successfully joined the event.');
                session()->flash('message_type', 'success');
            } else {
                if ($registration->status !== 'registered') {
                    $registration->update(['status' => 'registered']);
                    session()->flash('message', 'You have successfully joined the event.');
                    session()->flash('message_type', 'success');
                } else {
                    session()->flash('message', 'You are already registered for this event.');
                    session()->flash('message_type', 'info');
                }
            }
        } catch (QueryException) {
            session()->flash('message', 'Unable to join event.');
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        $allRegistrations = auth()->user()
            ->eventRegistrations()
            ->whereNotNull('event_id')
            ->with('event.location', 'event.media')
            ->get();

        // Helper function to format event data
        $formatEvent = function ($event, $registration) {
            // Get thumbnail URL - if no thumbnail, leave as null to show date placeholder
            $thumbnailUrl = $event->thumbnail_url;
            
            return [
                'id' => $event->id,
                'event_code' => $event->event_code,
                'title' => $event->title,
                'description' => $event->description,
                'venue' => $event->venue,
                'start_date' => $event->start_date?->toIso8601String(),
                'end_date' => $event->end_date?->toIso8601String(),
                'thumbnail_url' => $thumbnailUrl,
                'status' => $registration->status, // Include registration status
                'registered_at' => $registration->registered_at?->toIso8601String(),
                'attended_at' => $registration->attended_at?->toIso8601String() ?? null,
                'location' => $event->location ? [
                    'name' => $event->location->name,
                    'address' => $event->location->address,
                    'city' => $event->location->city,
                    'latitude' => $event->location->latitude ?? null,
                    'longitude' => $event->location->longitude ?? null,
                ] : null,
            ];
        };

        // Categorize events by status
        $favoritedEvents = $allRegistrations
            ->filter(function ($registration) {
                return $registration->status === 'favorited' && $registration->event !== null;
            })
            ->sortByDesc('registered_at')
            ->map(function ($registration) use ($formatEvent) {
                return $formatEvent($registration->event, $registration);
            });

        $interestedEvents = $allRegistrations
            ->filter(function ($registration) {
                return $registration->status === 'interested' && $registration->event !== null;
            })
            ->sortByDesc('registered_at')
            ->map(function ($registration) use ($formatEvent) {
                return $formatEvent($registration->event, $registration);
            });

        $joinedEvents = $allRegistrations
            ->filter(function ($registration) {
                return $registration->status === 'registered' && $registration->event !== null;
            })
            ->sortByDesc('registered_at')
            ->map(function ($registration) use ($formatEvent) {
                return $formatEvent($registration->event, $registration);
            });

        $attendedEvents = $allRegistrations
            ->filter(function ($registration) {
                return $registration->status === 'attended' && $registration->event !== null;
            })
            ->sortByDesc('attended_at')
            ->map(function ($registration) use ($formatEvent) {
                return $formatEvent($registration->event, $registration);
            });

        return view('livewire.member.eventsV2.my-events', [
            'favoritedEvents' => $favoritedEvents,
            'interestedEvents' => $interestedEvents,
            'joinedEvents' => $joinedEvents,
            'attendedEvents' => $attendedEvents,
        ]);
    }
}

