<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\RequestPasswordResetLink;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form.
     */
    public function show()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the password reset request.
     */
    public function store(Request $request)
    {
        $resetMethod = $request->input('reset_method', 'whatsapp');
        
        // Get identifier based on method
        if ($resetMethod === 'email') {
            $identifier = $request->input('email');
        } elseif ($resetMethod === 'sms') {
            $identifier = $request->input('sms_number');
        } else {
            $identifier = $request->input('whatsapp_number');
        }

        // Validate input
        $rules = [
            'reset_method' => ['required', 'in:email,whatsapp,sms'],
        ];

        if ($resetMethod === 'email') {
            $rules['email'] = ['required', 'email'];
        } elseif ($resetMethod === 'sms') {
            $rules['sms_number'] = ['required', 'string'];
        } else {
            $rules['whatsapp_number'] = ['required', 'string'];
        }

        $request->validate($rules);

        // Use custom action for all methods
        $action = new RequestPasswordResetLink();
        $message = $action([
            'reset_method' => $resetMethod,
            'identifier' => $identifier,
        ]);

        return back()->with('status', $message);
    }
}

