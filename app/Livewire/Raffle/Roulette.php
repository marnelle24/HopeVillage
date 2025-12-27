<?php

namespace App\Livewire\Raffle;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Roulette extends Component
{
    public string $source = 'range'; // range | members_fin | event_attendees

    public ?int $rangeStart = 1;
    public ?int $rangeEnd = 5;

    public ?int $selectedEventId = null;

    /** @var array<int, string> */
    public array $entries = [];

    public ?string $winner = null;
    public ?int $winnerIndex = null;

    public int $spinDegrees = 0;

    /** @var array<int, array{id:int,title:string}> */
    public array $events = [];

    public function mount(): void
    {
        $this->events = Event::query()
            ->orderByDesc('start_date')
            ->limit(200)
            ->get(['id', 'title'])
            ->map(fn (Event $e) => ['id' => $e->id, 'title' => $e->title])
            ->all();

        $this->loadEntries();
    }

    public function updatedSource(): void
    {
        $this->winner = null;
        $this->winnerIndex = null;
        $this->loadEntries();
    }

    public function updatedRangeStart(): void
    {
        if ($this->source === 'range' && $this->rangeStart !== null && $this->rangeEnd !== null) {
            // Only load if both values are set and valid
            if ($this->rangeStart >= 1 && $this->rangeEnd >= 1) {
                $this->loadEntries();
            }
        }
    }

    public function updatedRangeEnd(): void
    {
        if ($this->source === 'range' && $this->rangeStart !== null && $this->rangeEnd !== null) {
            // Only load if both values are set and valid
            if ($this->rangeStart >= 1 && $this->rangeEnd >= 1) {
                $this->loadEntries();
            }
        }
    }

    public function updatedSelectedEventId(): void
    {
        if ($this->source === 'event_attendees' && $this->selectedEventId) {
            $this->loadEntries();
        } elseif ($this->source === 'event_attendees' && !$this->selectedEventId) {
            // Clear entries if event is deselected
            $this->entries = [];
            $this->winner = null;
            $this->winnerIndex = null;
        }
    }

    public function loadEntries(): void
    {
        $this->resetValidation();
        $this->winner = null;
        $this->winnerIndex = null;
        $this->entries = [];

        if ($this->source === 'range') {
            $this->validate([
                'rangeStart' => ['required', 'integer', 'min:1'],
                'rangeEnd' => ['required', 'integer', 'min:1'],
            ]);

            if ($this->rangeStart > $this->rangeEnd) {
                $this->addError('rangeEnd', 'End must be greater than or equal to start.');
                return;
            }

            $count = ($this->rangeEnd - $this->rangeStart) + 1;
            if ($count > 500) {
                $this->addError('rangeEnd', 'Range is too large (max 500).');
                return;
            }

            $this->entries = collect(range($this->rangeStart, $this->rangeEnd))
                ->map(fn ($n) => (string) $n)
                ->values()
                ->all();

            return;
        }

        if ($this->source === 'members_fin') {
            $this->entries = User::query()
                ->where('user_type', 'member')
                ->whereNotNull('fin')
                ->where('fin', '!=', '')
                ->orderBy('fin')
                ->pluck('fin')
                ->map(fn ($v) => (string) $v)
                ->unique()
                ->values()
                ->all();

            return;
        }

        if ($this->source === 'event_attendees') {
            $this->validate([
                'selectedEventId' => ['required', 'integer'],
            ]);

            $fins = EventRegistration::query()
                ->where('event_id', $this->selectedEventId)
                ->where('status', 'attended')
                ->whereNotNull('user_id')
                ->with('user:id,fin')
                ->get()
                ->map(fn (EventRegistration $r) => $r->user?->fin)
                ->filter(fn ($fin) => is_string($fin) && trim($fin) !== '')
                ->map(fn ($fin) => strtoupper(trim($fin)))
                ->unique()
                ->values();

            $this->entries = $fins->all();

            return;
        }
    }

    public function spin(): void
    {
        $this->resetValidation();

        if (count($this->entries) === 0) {
            $this->addError('entries', 'No entries loaded.');
            return;
        }

        $count = count($this->entries);
        $index = random_int(0, $count - 1);

        $this->winnerIndex = $index;
        $this->winner = $this->entries[$index] ?? null;

        // Spin math: rotate multiple turns + land the selected segment under the pointer (top).
        $segment = 360 / $count;
        $centerOfSegment = ($index * $segment) + ($segment / 2);
        $target = 360 - $centerOfSegment;

        $fullTurns = random_int(5, 8) * 360;
        $this->spinDegrees = (int) round($fullTurns + $target);

        $this->dispatch('roulette-spun', degrees: $this->spinDegrees);
    }

    public function render()
    {
        return view('livewire.raffle.roulette')->layout('components.layouts.app');
    }
}
