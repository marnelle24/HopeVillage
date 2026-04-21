<?php

namespace App\Livewire\Member\EventsV2;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\PointsService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EventCard extends Component
{
    public array $event;
    public int $index;
    public bool $isMyEvents = false;

    public function mount(array $event, int $index = 0, bool $isMyEvents = false): void
    {
        $this->event = $event;
        $this->index = $index;
        $this->isMyEvents = $isMyEvents;
    }

    public function addToFavorites(): void
    {
        $user = auth()->user();

        $event = $this->resolvePublishedEvent();

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

            // If registration already exists, update the status to favorited
            if (!$registration->wasRecentlyCreated) {
                if ($registration->status !== 'favorited') {
                    $registration->update([
                        'status' => 'favorited',
                        'registered_at' => now(),
                    ]);
                    $this->notify('success', 'Event added to favorites.');
                } else {
                    $this->notify('info', 'Event is already in your favorites.');
                }
            } else {
                $this->notify('success', 'Event added to favorites.');
            }
        } catch (QueryException) {
            $this->notify('error', 'Unable to add event to favorites.');
        }

        $this->dispatchCardRefreshEvents();
    }

    public function markInterested(): void
    {
        $user = auth()->user();

        $event = $this->resolvePublishedEvent();

        try {
            $liked = DB::transaction(function () use ($user, $event): bool {
                $registration = EventRegistration::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->lockForUpdate()
                    ->first();

                // Toggle: if already interested, remove it (delete registration)
                if ($registration?->status === 'interested') {
                    $registration->delete();
                    return false;
                }

                EventRegistration::updateOrCreate(
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

                return true;
            });

            $message = $liked ? 'Event liked.' : 'Removed from liked events.';
            $this->notify('success', $message);
        } catch (QueryException) {
            $this->notify('error', 'Unable to update like status.');
        }

        $this->dispatchCardRefreshEvents();
    }

    public function join(): void
    {
        $user = auth()->user();

        $event = $this->resolvePublishedEvent();

        try {
            $registration = EventRegistration::where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();

            // Toggle: if already registered, unregister (delete registration)
            if ($registration && $registration->status === 'registered') {
                $registration->delete();
                session()->flash('message', 'Registration cancelled.');
                session()->flash('message_type', 'success');
                $this->dispatch('notify', type: 'success', message: 'Registration cancelled.');
            } else {
                // Check if event is full before registering
                if ($event->max_participants && $event->max_participants > 0) {
                    $current = $event->registrations()->where('status', 'registered')->count();
                    if ($current >= $event->max_participants) {
                        $this->notify('error', 'This event is already full.');
                        return;
                    }
                }

                // Create or update to registered status
                $oldStatus = $registration?->status;
                if ($registration) {
                    $registration->update([
                        'status' => 'registered',
                        'registered_at' => now(),
                    ]);
                } else {
                    EventRegistration::create([
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'type' => 'app',
                        'status' => 'registered',
                        'registered_at' => now(),
                    ]);
                }

                // Award points only when changing to registered status (not if already attended)
                if ($oldStatus !== 'attended') {
                    app(PointsService::class)->awardEventJoin($user, $event);
                }

                $this->notify('success', 'Successfully registered for the event.');
            }
        } catch (QueryException) {
            $this->notify('error', 'Unable to update registration.');
        }

        $this->dispatchCardRefreshEvents();
    }

    protected function resolvePublishedEvent(): Event
    {
        return Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($this->event['id']);
    }

    protected function notify(string $type, string $message): void
    {
        session()->flash('message', $message);
        session()->flash('message_type', $type);
        $this->dispatch('notify', type: $type, message: $message);
    }

    protected function dispatchCardRefreshEvents(): void
    {
        // Refresh the event data for this card
        $this->refreshEventData();

        $this->dispatch('event-updated', status: $this->event['registration_status'] ?? null);

        // Refresh parent component if in my-events
        if ($this->isMyEvents) {
            $this->dispatch('refresh-my-events');
        }
    }

    protected function refreshEventData(): void
    {
        $userId = auth()->id();
        $eventId = $this->event['id'];

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->with(['location', 'media'])
            ->withCount('registrations')
            ->withExists([
                'registrations as is_registered' => fn ($rq) => $rq->where('user_id', $userId),
            ])
            ->findOrFail($eventId);

        // Get user's registration status
        $userRegistration = EventRegistration::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->first();

        $thumbnailUrl = $event->thumbnail_url;

        $this->event = [
            'id' => $event->id,
            'event_code' => $event->event_code,
            'title' => $event->title,
            'description' => $event->description,
            'venue' => $event->venue,
            'start_date' => $event->start_date?->toIso8601String(),
            'end_date' => $event->end_date?->toIso8601String(),
            'thumbnail_url' => $thumbnailUrl,
            'is_registered' => $event->is_registered,
            'registration_status' => $userRegistration ? $userRegistration->status : null,
            'location' => $event->location ? [
                'name' => $event->location->name,
                'address' => $event->location->address,
                'city' => $event->location->city,
            ] : null,
        ];
    }

    public function formatDateForThumbnail(?string $dateString): array
    {
        if (!$dateString) {
            return ['month' => 'TBA', 'day' => '', 'year' => ''];
        }
        
        try {
            $date = new \DateTime($dateString);
            return [
                'month' => $date->format('M'),
                'day' => $date->format('j'),
                'year' => $date->format('Y'),
            ];
        } catch (\Exception) {
            return ['month' => 'TBA', 'day' => '', 'year' => ''];
        }
    }

    public function render()
    {
        return view('livewire.member.eventsV2.event-card');
    }
}

