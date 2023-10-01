<?php

namespace App\Livewire\Auth;

use App\Http\Controllers\Auth\AuthController;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    use WithRateLimiting;

    public $user;
    public $username;
    public $password;
    public $remember_me = null;
    public $two_factor_enabled = false;
    public $two_factor_code = null;

    public $rateLimitTime;

    public function setLanguage($language)
    {
        cookie()->queue(cookie()->forget('language'));
        cookie()->queue(cookie()->forever('language', $language));

        Notification::make()
            ->title(__('messages.language_changed'))
            ->success()
            ->send();
        return redirect()->route('login');
    }

    public function setRateLimit()
    {
        try {
            $this->rateLimit(10);
        } catch (TooManyRequestsException $exception) {
            $this->rateLimitTime = $exception->secondsUntilAvailable;
            return true;
        }
        return false;
    }

    public function checkIfUserExits($username)
    {
        if ($this->setRateLimit()) {
            return;
        }
        if ($username == null || $username == '') {
            return;
        }
        $this->user = User::where('username', $username)->first();
        if ($this->user == null) {
            $this->user = false;
        }
        $this->dispatch('userExists');
    }

    public function checkTwoFactorCode()
    {
        if ($this->setRateLimit()) {
            return;
        }

        if (AuthController::checkTwoFactorKey($this->user, $this->two_factor_code)) {
            Auth::login($this->user);
            return redirect('/');
        }

        session()->flash('error', __('pages/login.invalid_two_factor'));
    }

    public function attemptLogin()
    {
        if ($this->setRateLimit()) {
            return;
        }

        $credentials = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        $this->checkIfUserExits($this->username);

        if (Auth::attempt($credentials, $this->remember_me)) {
            if ($this->user->two_factor_enabled) {
                Auth::logout();
                $this->two_factor_enabled = true;
            } else {
                $this->redirect('/');
            }
        } else {
            session()->flash('error', __('messages.invalid_password'));
        }
    }

    #[Layout('components.layouts.guest'), Title('Login')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
