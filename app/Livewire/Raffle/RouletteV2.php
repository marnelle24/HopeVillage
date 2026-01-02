<?php

namespace App\Livewire\Raffle;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Livewire\Component;

class RouletteV2 extends Component
{
    public string $source = 'range'; // range | members_fin | members_qr_code | event_attendees

    public ?int $rangeStart = 1;
    public ?int $rangeEnd = 10;

    public ?int $selectedEventId = null;

    /** @var array<int, string> */
    public array $entries = [];

    public ?string $winner = null;
    public ?int $winnerIndex = null;
    public ?array $latestWinner = null; // Store latest winner with member details

    /** @var array<int, array{place:int,value:string,member:array|null}> */
    public array $winners = [];

    /** @var array<int, array{id:int,title:string,attendee_count:int}> */
    public array $events = [];

    public function mount(): void
    {
        $this->events = Event::query()
            ->orderByDesc('start_date')
            ->limit(200)
            ->get(['id', 'title'])
            ->map(function (Event $e) {
                $attendeeCount = EventRegistration::query()
                    ->where('event_id', $e->id)
                    ->where('status', 'attended')
                    ->whereNotNull('user_id')
                    ->count();
                
                return [
                    'id' => $e->id,
                    'title' => $e->title,
                    'attendee_count' => $attendeeCount,
                ];
            })
            ->all();

        $this->loadEntries();
    }

    public function updatedSource(): void
    {
        $this->resetWinners();
        $this->loadEntries();
    }

    public function updatedRangeStart(): void
    {
        if ($this->source === 'range' && $this->rangeStart !== null && $this->rangeEnd !== null) {
            if ($this->rangeStart >= 1 && $this->rangeEnd >= 1) {
                $this->resetWinners();
                $this->loadEntries();
            }
        }
    }

    public function updatedRangeEnd(): void
    {
        if ($this->source === 'range' && $this->rangeStart !== null && $this->rangeEnd !== null) {
            if ($this->rangeStart >= 1 && $this->rangeEnd >= 1) {
                $this->resetWinners();
                $this->loadEntries();
            }
        }
    }

    public function updatedSelectedEventId(): void
    {
        if ($this->source === 'event_attendees' && $this->selectedEventId) {
            $this->resetWinners();
            $this->loadEntries();
        } elseif ($this->source === 'event_attendees' && !$this->selectedEventId) {
            $this->entries = [];
            $this->resetWinners();
        }
    }

    public function loadEntries(): void
    {
        $this->resetValidation();
        $this->resetWinners();
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

            $this->dispatch('entries-loaded');
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

            $this->dispatch('entries-loaded');
            return;
        }

        if ($this->source === 'members_qr_code') {
            $this->entries = User::query()
                ->where('user_type', 'member')
                ->whereNotNull('qr_code')
                ->where('qr_code', '!=', '')
                ->orderBy('qr_code')
                ->pluck('qr_code')
                ->map(fn ($v) => (string) $v)
                ->unique()
                ->values()
                ->all();

            $this->dispatch('entries-loaded');
            return;
        }

        if ($this->source === 'event_attendees') {
            $this->validate([
                'selectedEventId' => ['required', 'integer'],
            ]);

            $qrCodes = EventRegistration::query()
                ->where('event_id', $this->selectedEventId)
                ->where('status', 'attended')
                ->whereNotNull('user_id')
                ->with('user:id,qr_code')
                ->get()
                ->map(fn (EventRegistration $r) => $r->user?->qr_code)
                ->filter(fn ($qrCode) => is_string($qrCode) && trim($qrCode) !== '')
                ->map(fn ($qrCode) => strtoupper(trim($qrCode)))
                ->unique()
                ->values();

            $this->entries = $qrCodes->all();

            $this->dispatch('entries-loaded');
            return;
        }
    }

    public function spin(): void
    {
        $this->resetValidation();

        // Get available entries (excluding already selected winners)
        $availableEntries = $this->getAvailableEntries();

        if (count($availableEntries) === 0) {
            $this->addError('entries', 'No entries available.');
            return;
        }

        // Select random winner from available entries
        $count = count($availableEntries);
        $availableIndex = random_int(0, $count - 1);
        $this->winner = $availableEntries[$availableIndex] ?? null;

        if ($this->winner === null) {
            $this->addError('entries', 'Failed to select winner.');
            return;
        }

        // Find the index in the original entries array for the wheel
        // The wheel is initialized with filtered entries, so we use the available index
        $this->winnerIndex = $availableIndex;

        // Dispatch event to trigger spin-wheel spin
        $this->dispatch('spin-wheel', index: $this->winnerIndex);
    }

    private function getAvailableEntries(): array
    {
        // Get winner values
        $winnerValues = collect($this->winners)->pluck('value')->toArray();
        
        // Filter out entries that are already winners
        return collect($this->entries)
            ->filter(fn ($entry) => !in_array((string) $entry, $winnerValues, true))
            ->values()
            ->all();
    }

    public function onSpinComplete($winnerIndex): void
    {
        $this->winnerIndex = $winnerIndex;
        
        // Use the winner value that was already determined in spin() method
        // If not set, fallback to getting from entries array (shouldn't happen normally)
        if ($this->winner === null) {
            $this->winner = $this->entries[$winnerIndex] ?? null;
        }
        
        // Validate winner is not null
        if ($this->winner === null) {
            return;
        }
        
        // Ensure winner is a string
        $winnerValue = is_string($this->winner) ? $this->winner : (string) $this->winner;
        
        // Validate winner value is not empty
        if (trim($winnerValue) === '') {
            return;
        }
        
        // Check if this winner already exists in the winners array to prevent duplicates
        $existingWinner = collect($this->winners)->firstWhere('value', $winnerValue);
        if ($existingWinner !== null) {
            // Winner already exists, don't add duplicate
            return;
        }
        
        // Fetch member details based on entry source
        $memberDetails = $this->getMemberDetails($winnerValue);
        
        // Add winner to winners array
        $place = count($this->winners) + 1;
        
        $winnerData = [
            'place' => $place,
            'value' => $winnerValue,
            'member' => $memberDetails,
        ];
        
        $this->winners[] = $winnerData;
        $this->latestWinner = $winnerData; // Store for modal display
        
        // Dispatch event to open winner modal
        // Don't update wheel yet - wait for modal to close
        $this->dispatch('show-winner-modal');
        
        // Also dispatch browser event as fallback
        $this->dispatch('show-winner-modal-browser');
    }

    public function updateWheelAfterModalClose(): void
    {
        // Update entries to exclude winners
        $availableEntries = $this->getAvailableEntries();
        $this->entries = $availableEntries;
        
        // Dispatch event to reinitialize wheel with updated entries
        $this->dispatch('entries-loaded');
    }

    private function getMemberDetails(string $value): ?array
    {
        // For range source, there's no member associated
        if ($this->source === 'range') {
            return null;
        }

        $user = null;

        // Find user based on source type
        if ($this->source === 'members_qr_code') {
            $user = User::where('qr_code', $value)
                ->where('user_type', 'member')
                ->first();
        } elseif ($this->source === 'members_fin') {
            $user = User::where('fin', $value)
                ->where('user_type', 'member')
                ->first();
        } elseif ($this->source === 'event_attendees') {
            // For event attendees, find by QR code through event registration
            $user = User::where('qr_code', strtoupper(trim($value)))
                ->where('user_type', 'member')
                ->first();
        }

        if (!$user) {
            return null;
        }

        // Return member details
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'whatsapp_number' => $user->whatsapp_number,
            'fin' => $user->fin,
            'qr_code' => $user->qr_code,
            'age' => $user->age,
            'gender' => $user->gender,
            'type_of_work' => $user->type_of_work,
            'is_verified' => $user->is_verified,
            'total_points' => $user->total_points,
        ];
    }

    private function getEntryType(): string
    {
        return match($this->source) {
            'members_qr_code' => 'qr_code',
            'members_fin' => 'fin',
            'event_attendees' => 'qr_code',
            'range' => 'number',
            default => 'unknown',
        };
    }

    public function clearWinners(): void
    {
        $this->resetWinners();
    }

    private function resetWinners(): void
    {
        $this->winners = [];
        $this->winner = null;
        $this->winnerIndex = null;
        $this->latestWinner = null;
    }

    public function render()
    {
        return view('livewire.raffle.roulette-v2')->layout('components.layouts.app');
    }
}

