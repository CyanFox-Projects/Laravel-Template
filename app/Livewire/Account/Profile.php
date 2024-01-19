<?php

namespace App\Livewire\Account;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Profile extends Component
{

    /* Profile */
    public $first_name;
    public $last_name;
    public $username;
    public $email;

    /* Tabs */
    #[Url]
    public $tab = 'overview';

    public function changeTab($tab)
    {
        $this->tab = $tab;
    }

    public function updateProfile()
    {

        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users,username,' . Auth::user()->getAuthIdentifier() . ',id',
            'email' => 'required|email|unique:users,email,' . Auth::user()->getAuthIdentifier() . ',id'
        ]);


        Auth::user()->first_name = $this->first_name;
        Auth::user()->last_name = $this->last_name;
        Auth::user()->username = $this->username;
        Auth::user()->email = $this->email;

        if (Auth::user()->save()) {
            Notification::make()
                ->title(__('pages/account/messages.notifications.profile_updated'))
                ->success()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.profile_update_success');

            $this->redirect(route('profile'));
        } else {
            Notification::make()
                ->title(__('messages.something_went_wrong'))
                ->danger()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.profile_update_failed');
        }
    }

    /* Theme & Language */
    public function changeLanguage($lang)
    {

        if ($lang == Auth::user()->language) {
            return;
        }

        Auth::user()->language = $lang;

        if (Auth::user()->save()) {
            Notification::make()
                ->title(__('messages.notifications.language_changed'))
                ->success()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.language_change_success');

            return redirect()->route('profile');
        } else {
            Notification::make()
                ->title(__('messages.something_went_wrong'))
                ->danger()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.language_change_failed');
        }
    }

    public function changeTheme($theme)
    {
        if ($theme == Auth::user()->theme) {
            return;
        }

        Auth::user()->theme = $theme;

        try {
            Auth::user()->save();
        }catch (Exception $e) {
            Notification::make()
                ->title(__('messages.something_went_wrong'))
                ->danger()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.theme_change_failed');

            $this->dispatch('sendToConsole', $e->getMessage());
            return;
        }

        Notification::make()
            ->title(__('pages/account/messages.notifications.theme_changed'))
            ->success()
            ->send();

        activity('system')
            ->performedOn(auth()->user())
            ->causedBy(auth()->user())
            ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
            ->withProperty('ip', request()->ip())
            ->log('account.theme_change_success');

        return redirect()->route('profile');
    }

    /* Sessions */
    public $sessionData = [];

    public function getSessionData()
    {
        $userSessions = DB::table('sessions')->where('user_id', Auth::user()->getAuthIdentifier())->get();

        foreach ($userSessions as $session) {

            if (!$session->user_id === Auth::user()->getAuthIdentifier()) {
                continue;
            }

            $agent = new Agent();
            $agent->setUserAgent($session->user_agent);

            $platform = $agent->browser() . " " . $agent->platform();

            $isCurrentSession = (session()->getId() === $session->id);

            $deviceType = $agent->isDesktop() ? 'Desktop' : ($agent->isPhone() ? 'Phone' : 'Tablet');

            $ip = $session->ip_address;

            $this->sessionData[] = [
                'id' => $session->id,
                'ip' => $ip,
                'agent' => $agent->getUserAgent(),
                'platform' => $platform,
                'deviceType' => $deviceType,
                'isCurrentSession' => $isCurrentSession
            ];
        }

        usort($this->sessionData, fn($a, $b) => $b['isCurrentSession'] <=> $a['isCurrentSession']);
    }


    /* Password */
    public $current_password = '';
    public $new_password = '';
    public $confirm_password = '';

    public function updatePassword()
    {

        if (!Auth::validate(['email' => Auth::user()->email, 'password' => $this->current_password])) {

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.change_password_failed');

            throw ValidationException::withMessages([
                'current_password' => __('validation.current_password')
            ]);
        }

        $this->validate([
            'new_password' => 'required',
            'confirm_password' => 'required'
        ]);


        if ($this->new_password !== $this->confirm_password) {

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.change_password_failed');

            throw ValidationException::withMessages([
                'new_password' => __('validation.custom.passwords_not_match'),
                'confirm_password' => __('validation.custom.passwords_not_match')
            ]);
        }

        Auth::user()->password = bcrypt($this->new_password);

        if (Auth::user()->save()) {
            Notification::make()
                ->title(__('pages/account/messages.notifications.password_changed'))
                ->success()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.change_password_success');

            $this->redirect(route('profile'));
        } else {
            Notification::make()
                ->title(__('messages.something_went_wrong'))
                ->danger()
                ->send();

            activity('system')
                ->performedOn(auth()->user())
                ->causedBy(auth()->user())
                ->withProperty('name', auth()->user()->username . ' (' . auth()->user()->email . ')')
                ->withProperty('ip', request()->ip())
                ->log('account.change_password_failed');
        }
    }


    /* Activity Log */
    public $activityLog = [];
    public function getActivityLog()
    {
        $userActivity = Activity::where('subject_id', Auth::user()->getAuthIdentifier())->get();

        foreach ($userActivity as $activity) {

            $this->activityLog[] = [
                'id' => $activity->id,
                'subject_id' => $activity->subject_id,
                'causer_id' => $activity->causer_id,
                'causer_ip' => $activity->getExtraProperty('ip'),
                'description' => $activity->description,
            ];
        }

        return $this->activityLog;
    }


    public function mount()
    {
        $this->first_name = Auth::user()->first_name;
        $this->last_name = Auth::user()->last_name;
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
        $this->getSessionData();
        $this->getActivityLog();
    }

    #[Title('Profile')]
    public function render()
    {
        return view('livewire.account.profile')
            ->layout('components.layouts.app', [
                'title' => __('navigation/messages.profile')
            ]);
    }
}