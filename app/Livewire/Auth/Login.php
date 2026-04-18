<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => 1])) {
            session()->regenerate();
            return redirect()->intended('/');
        }

        $this->addError('email', 'The provided credentials do not match our records or your account is inactive.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.pos');
    }
}
