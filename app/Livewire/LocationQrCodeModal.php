<?php

namespace App\Livewire;

use App\Models\ActivityType;
use App\Models\Location;
use App\Models\MemberActivity;
use App\Models\Setting;
use App\Services\PointsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LocationQrCodeModal extends Component
{
    public $locationCode;
    public $open = false;
    public $location = null;
    public $memberFin = null;
    public $processing = false;
    public $error = null;
    public $success = false;
    public $successMessage = null;

    protected $listeners = [
        'openLocationQrModal' => 'open',
        'closeLocationQrModal' => 'close',
    ];

    public function mount($locationCode = null)
    {
        $this->locationCode = $locationCode;
        if ($this->locationCode) {
            $this->loadLocation();
        }
    }

    public function open($locationCode = null)
    {
        if ($locationCode) {
            $this->locationCode = $locationCode;
        }
        $this->open = true;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
        $this->location = null; // Reset location
        $this->memberFin = auth()->user()?->fin;
        
        if ($this->locationCode) {
            $this->loadLocation();
        }
    }

    public function close()
    {
        $this->open = false;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
    }

    public function loadLocation()
    {
        if ($this->locationCode) {
            // Normalize the location code (trim and uppercase)
            $normalizedCode = strtoupper(trim($this->locationCode));
            
            $this->location = Location::where('location_code', $normalizedCode)->first();
            
            if (!$this->location) {
                $this->error = 'Location not found.';
                return;
            }
            
            // Automatically call member-activity API for location entry
            $this->processLocationEntry();
        }
    }
    
    public function processLocationEntry()
    {
        $user = auth()->user();
        
        if (!$user || !$user->fin) {
            $this->error = 'Please login to scan QR codes.';
            return;
        }

        if (!$this->location) {
            $this->error = 'Location not found.';
            return;
        }

        $this->processing = true;
        $this->error = null;
        $this->success = false;
        $this->successMessage = null;

        try 
        {
            DB::transaction(function () use ($user) {
                // Check if user is a member
                if ($user->user_type !== 'member') {
                    $this->error = 'User is not a member.';
                    $this->processing = false;
                    return;
                }

                // Check if location is active
                if (!$this->location->is_active) {
                    $this->error = 'Location is not active.';
                    $this->processing = false;
                    return;
                }

                // Validate entry time gap for ENTRY activities
                // Get dynamic time gap from settings (default: 3600 seconds = 1 hour)
                $timeGapSeconds = (int) Setting::get('entry_time_gap', 3600);
                $timeGapAgo = now()->subSeconds($timeGapSeconds);
                
                // Check if member has a recent entry at this location within the time gap
                $recentEntry = MemberActivity::where('user_id', $user->id)
                    ->where('location_id', $this->location->id)
                    ->whereHas('activityType', function($query) {
                        $query->where('name', 'ENTRY');
                    })
                    ->where('activity_time', '>=', $timeGapAgo)
                    ->exists();
                
                if ($recentEntry) {
                    // $this->error = 'it requires ' . $timeGapSeconds . ' second(s) gap to scan again.';
                    $this->error = 'Sign in already recorded';
                    $this->processing = false;
                    return;
                }

                $activityType = ActivityType::where('name', 'ENTRY')->first();

                if ($activityType) 
                {
                    // Create member activity record
                    $memberActivity = MemberActivity::create([
                        'user_id' => $user->id,
                        'activity_type_id' => $activityType->id,
                        'location_id' => $this->location->id,
                        'amenity_id' => null,
                        'activity_time' => now(),
                        'description' => "Member ENTRY at {$this->location->name}",
                        'metadata' => [
                            'scanned_at' => now()->toIso8601String(),
                            'location_code' => $this->location->location_code,
                            'qr_code' => $user->qr_code,
                            'device_info' => request()->header('User-Agent'),
                            'access_type' => 'built_in_scanner',
                            'ip_address' => request()->ip(),
                        ],
                    ]);

                    // Award points for ENTRY activity
                    $pointsBefore = $user->total_points;
                    $pointsAwarded = 0;
                
                    app(PointsService::class)->award(
                        user: $user,
                        activityName: PointsService::ACTIVITY_LOCATION_ENTRY,
                        description: 'Member entry to location',
                        locationId: $this->location->id,
                        memberActivityId: $memberActivity->id,
                    );
                    
                    $user->refresh();
                    $pointsAwarded = $user->total_points - $pointsBefore;

                    Log::info('Member activity recorded', [
                        'member_fin' => $user->fin,
                        'location_code' => $this->location->location_code,
                        'activity_type' => 'ENTRY',
                        'points_awarded' => $pointsAwarded,
                    ]);

                    // Set success message
                    $this->success = true;
                    $this->successMessage = "SUCCESS";
                    
                    $this->dispatch('location-qr-processed', [
                        'code' => $this->locationCode,
                        'location_name' => $this->location->name,
                        'points_awarded' => $pointsAwarded,
                    ]);
                
                    // Dispatch event to update points header in real-time
                    $this->dispatch('points-updated');
                    
                    session()->flash('qr-scan-success', 'SUCCESS');
                    $this->processing = false;
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to record member activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error = 'Failed to process location entry: ' . ($e->getMessage() ?? 'Unknown error');
            $this->processing = false;
        }
    }

    public function processScan()
    {
        // This method is kept for backward compatibility but now calls processLocationEntry
        $this->processLocationEntry();
    }

    public function render()
    {
        return view('livewire.location-qr-code-modal');
    }
}
