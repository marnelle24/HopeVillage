<?php

namespace App\Livewire\Member\EventsV2;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\PointsService;
use Illuminate\Database\QueryException;
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

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($this->event['id']);

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
                    session()->flash('message', 'Event added to favorites.');
                    session()->flash('message_type', 'success');
                    $this->dispatch('notify', type: 'success', message: 'Event added to favorites.');
                } else {
                    session()->flash('message', 'Event is already in your favorites.');
                    session()->flash('message_type', 'info');
                    $this->dispatch('notify', type: 'info', message: 'Event is already in your favorites.');
                }
            } else {
                session()->flash('message', 'Event added to favorites.');
                session()->flash('message_type', 'success');
                $this->dispatch('notify', type: 'success', message: 'Event added to favorites.');
            }
        } catch (QueryException) {
            session()->flash('message', 'Unable to add event to favorites.');
            session()->flash('message_type', 'error');
            $this->dispatch('notify', type: 'error', message: 'Unable to add event to favorites.');
        }

        // Refresh the event data for this card
        $this->refreshEventData();

        $this->dispatch('event-updated');
        
        // Refresh parent component if in my-events
        if ($this->isMyEvents) {
            $this->dispatch('refresh-my-events');
        }
    }

    public function markInterested(): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($this->event['id']);

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

            // If registration already exists, update the status to interested
            if (!$registration->wasRecentlyCreated) {
                if ($registration->status !== 'interested') {
                    $registration->update([
                        'status' => 'interested',
                        'registered_at' => now(),
                    ]);
                    session()->flash('message', 'Marked as interested.');
                    session()->flash('message_type', 'success');
                    $this->dispatch('notify', type: 'success', message: 'Marked as interested.');
                } else {
                    session()->flash('message', 'You are already marked as interested.');
                    session()->flash('message_type', 'info');
                    $this->dispatch('notify', type: 'info', message: 'You are already marked as interested.');
                }
            } else {
                session()->flash('message', 'Marked as interested.');
                session()->flash('message_type', 'success');
                $this->dispatch('notify', type: 'success', message: 'Marked as interested.');
            }
        } catch (QueryException) {
            session()->flash('message', 'Unable to mark event as interested.');
            session()->flash('message_type', 'error');
            $this->dispatch('notify', type: 'error', message: 'Unable to mark event as interested.');
        }

        // Refresh the event data for this card
        $this->refreshEventData();

        $this->dispatch('event-updated');
        
        // Refresh parent component if in my-events
        if ($this->isMyEvents) {
            $this->dispatch('refresh-my-events');
        }
    }

    public function join(): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($this->event['id']);

        if ($event->max_participants && $event->max_participants > 0) {
            $current = $event->registrations()->where('status', 'registered')->count();
            if ($current >= $event->max_participants) {
                session()->flash('message', 'This event is already full.');
                session()->flash('message_type', 'error');
                $this->dispatch('notify', type: 'error', message: 'This event is already full.');
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

            // If registration already exists, update the status to registered
            if (!$registration->wasRecentlyCreated) {
                $oldStatus = $registration->status;
                if ($oldStatus !== 'registered') {
                    $registration->update([
                        'status' => 'registered',
                        'registered_at' => now(),
                    ]);
                    // Award points only when changing to registered status (not if already attended)
                    if ($oldStatus !== 'attended') {
                        app(PointsService::class)->awardEventJoin($user, $event);
                    }
                    session()->flash('message', 'You have successfully joined the event.');
                    session()->flash('message_type', 'success');
                    $this->dispatch('notify', type: 'success', message: 'You have successfully joined the event.');
                } else {
                    session()->flash('message', 'You are already registered for this event.');
                    session()->flash('message_type', 'info');
                    $this->dispatch('notify', type: 'info', message: 'You are already registered for this event.');
                }
            } else {
                // Award points for new registration
                app(PointsService::class)->awardEventJoin($user, $event);
                session()->flash('message', 'You have successfully joined the event.');
                session()->flash('message_type', 'success');
                $this->dispatch('notify', type: 'success', message: 'You have successfully joined the event.');
            }
        } catch (QueryException) {
            session()->flash('message', 'Unable to join event.');
            session()->flash('message_type', 'error');
            $this->dispatch('notify', type: 'error', message: 'Unable to join event.');
        }

        // Refresh the event data for this card
        $this->refreshEventData();

        $this->dispatch('event-updated');
        
        // Refresh parent component if in my-events
        if ($this->isMyEvents) {
            $this->dispatch('refresh-my-events');
        }

        // For Browse component, redirect after join
        if (request()->routeIs('member.events') && !$this->isMyEvents) {
            sleep(1);
            $this->redirect(route('member.events', ['type' => 'my-events']));
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

