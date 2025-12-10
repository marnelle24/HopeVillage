<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Location;
use Livewire\Component;

class Form extends Component
{
    public $locationCode;
    public $eventId;
    public $location;
    public $title = '';
    public $description = '';
    public $start_date = '';
    public $start_time = '';
    public $end_date = '';
    public $end_time = '';
    public $venue = '';
    public $max_participants = '';
    public $status = 'draft';
    public $event_code = '';
    public $showMessage = false;

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'venue' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published,cancelled,completed',
        ];

        if ($this->eventId) {
            $rules['event_code'] = 'nullable|string|max:255|unique:events,event_code,' . $this->eventId;
        } else {
            $rules['event_code'] = 'nullable|string|max:255|unique:events,event_code';
        }

        return $rules;
    }

    public function mount($location_code, $id = null)
    {
        $this->locationCode = $location_code;
        $this->location = Location::where('location_code', $location_code)->firstOrFail();
        $this->eventId = $id;
        $this->showMessage = session()->has('message');

        if ($this->eventId) {
            $event = Event::where('location_id', $this->location->id)
                ->findOrFail($this->eventId);
            
            $this->title = $event->title;
            $this->description = $event->description;
            $this->start_date = $event->start_date->format('Y-m-d');
            $this->start_time = $event->start_date->format('H:i');
            $this->end_date = $event->end_date->format('Y-m-d');
            $this->end_time = $event->end_date->format('H:i');
            $this->venue = $event->venue;
            $this->max_participants = $event->max_participants;
            $this->status = $event->status;
            $this->event_code = $event->event_code;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate($this->rules());

        $startDateTime = $this->start_date . ' ' . $this->start_time;
        $endDateTime = $this->end_date . ' ' . $this->end_time;

        if ($this->eventId) {
            $event = Event::where('location_id', $this->location->id)
                ->findOrFail($this->eventId);
            
            // Only update event_code if it's different and provided
            $updateData = [
                'title' => $this->title,
                'description' => $this->description,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'venue' => $this->venue,
                'max_participants' => $this->max_participants ? (int)$this->max_participants : null,
                'status' => $this->status,
            ];

            if ($this->event_code && $this->event_code !== $event->event_code) {
                $updateData['event_code'] = $this->event_code;
            }

            $event->update($updateData);
            $message = 'Event updated successfully.';
        } else {
            $event = Event::create([
                'location_id' => $this->location->id,
                'created_by' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'venue' => $this->venue,
                'max_participants' => $this->max_participants ? (int)$this->max_participants : null,
                'status' => $this->status,
                'event_code' => $this->event_code ?: null, // Will be auto-generated if empty
            ]);
            $message = 'Event created successfully.';
        }

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.locations.events.index', $this->locationCode);
    }

    public function render()
    {
        return view('livewire.events.form', [
            'location' => $this->location,
        ])->layout('components.layouts.app');
    }
}
