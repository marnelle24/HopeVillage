<?php

namespace App\Livewire\Member\EventsV2;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\PointsService;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // 'all' or 'upcoming'

    protected $paginationTheme = 'tailwind';

    public function mount(?string $filter = 'all'): void
    {
        $this->filter = $filter ?? 'all';
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function updateFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function addToFavorites(int $eventId): void
    {
        $user = auth()->user();

        $event = Event::query()
            ->where('status', 'published')
            ->where('end_date', '>=', now())
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
            ->where('end_date', '>=', now())
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
                // Update status if already exists
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
            ->where('end_date', '>=', now())
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            ->findOrFail($eventId);

        if ($event->max_participants && $event->max_participants > 0) {
            $current = $event->registrations()->count();
            if ($current >= $event->max_participants) {
                session()->flash('message', 'This event is already full.');
                session()->flash('message_type', 'error');
                $this->dispatch('scroll-to-top');
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
                // Update status if already exists
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
            // In case of a race condition on the unique(user_id,event_id) constraint.
            session()->flash('message', 'Unable to join event.');
            session()->flash('message_type', 'error');
        }

        sleep(1); // Wait for 1 second to allow the flash message to be displayed

        // Redirect to member events page with type=my-events parameter
        $this->redirect(route('member.events', ['type' => 'my-events']));
    }

    public function render()
    {
        $userId = auth()->id();

        $events = Event::query()
            ->where('status', 'published')
            ->where('end_date', '>=', now())
            // Only show events from locations that are not soft deleted
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'))
            // Filter by date range based on filter type
            ->when($this->filter === 'upcoming', function ($q) {
                // Show events within the next 7 days
                $q->whereBetween('start_date', [now(), now()->addWeek()]);
            })
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('venue', 'like', '%' . $this->search . '%')
                        ->orWhereHas('location', function ($lq) {
                            $lq->whereNull('deleted_at')
                                ->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->with(['location', 'media'])
            ->withCount('registrations')
            ->withExists([
                'registrations as is_registered' => fn ($rq) => $rq->where('user_id', $userId),
            ])
            ->orderBy('start_date')
            ->paginate(12);

        // Get user's registration status for each event
        $userRegistrations = EventRegistration::where('user_id', $userId)
            ->whereIn('event_id', $events->pluck('id'))
            ->get()
            ->keyBy('event_id');

        // Transform events for Alpine.js - format data properly
        $events->getCollection()->transform(function ($event) use ($userRegistrations) {
            // Get thumbnail URL - if no thumbnail, leave as null to show date placeholder
            $thumbnailUrl = $event->thumbnail_url;
            
            // Get user's registration status for this event
            $userRegistration = $userRegistrations->get($event->id);
            $registrationStatus = $userRegistration ? $userRegistration->status : null;
            
            return [
                'id' => $event->id,
                'event_code' => $event->event_code,
                'title' => $event->title,
                'description' => $event->description,
                'venue' => $event->venue,
                'start_date' => $event->start_date?->toIso8601String(),
                'end_date' => $event->end_date?->toIso8601String(),
                'thumbnail_url' => $thumbnailUrl,
                'is_registered' => $event->is_registered,
                'registration_status' => $registrationStatus, // Add registration status
                'location' => $event->location ? [
                    'name' => $event->location->name,
                    'address' => $event->location->address,
                    'city' => $event->location->city,
                    'latitude' => $event->location->latitude ?? null,
                    'longitude' => $event->location->longitude ?? null,
                ] : null,
            ];
        });

        return view('livewire.member.eventsV2.browse', [
            'events' => $events,
        ]);
    }
}

