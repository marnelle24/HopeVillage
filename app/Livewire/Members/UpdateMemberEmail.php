<?php

namespace App\Livewire\Members;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateMemberEmail extends Component
{
    /** @var int|null The ID of the member whose email is being updated (passed from parent). */
    public ?int $userId = null;

    public string $email = '';

    public bool $open = true;

    protected $listeners = [
        'closeUpdateMemberEmailModal' => 'close',
    ];

    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
        $this->open = $userId !== null;

        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $this->email = $user->email ?? '';
            }
        }
    }

    public function close(): void
    {
        $this->open = false;
        $this->email = '';
        $this->userId = null;
        $this->dispatch('updateEmailModalClosed');
    }

    public function save(): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'You do not have permission to update member email.');
            $this->dispatch('updateEmailModalClosed');
            return;
        }

        $user = User::find($this->userId);
        if (! $user) {
            session()->flash('error', 'Member not found.');
            $this->dispatch('updateEmailModalClosed');
            return;
        }

        Validator::make(
            ['email' => $this->email],
            [
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
            ],
            [
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already in use by another account.',
            ]
        )->validate();

        $previousEmail = $user->email;
        $user->forceFill(['email' => $this->email])->save();

        Log::info('Member email updated by administrator', [
            'admin_user' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'member_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'previous_email' => $previousEmail,
                'new_email' => $this->email,
            ],
            'updated_at' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $message = 'Member email updated successfully.';
        $this->close();
        $this->dispatch('updateEmailModalClosed');
        $this->dispatch('hv-toast', type: 'success', message: $message);
    }

    public function getUserProperty(): ?User
    {
        if (! $this->userId) {
            return null;
        }

        return User::find($this->userId);
    }

    public function render()
    {
        return view('livewire.members.update-member-email');
    }
}
