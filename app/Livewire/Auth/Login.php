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
        
        $throttleKey = strtolower($this->email).'|'.request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', "برجاء المحاولة بعد {$seconds} ثانية.");
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => 1])) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            session()->regenerate();
            return redirect()->intended('/');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
        $this->addError('email', 'بيانات الدخول غير صحيحة أو الحساب غير نشط.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.pos');
    }
}
