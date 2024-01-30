<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Filament\Notifications\Notification;
use Livewire\Component;

class SystemSettings extends Component
{
    public $timeZones = [];

    public $appName;
    public $appUrl;
    public $appLang;
    public $appTimezone;

    public $unsplashUtm;
    public $unsplashApiKey;

    public $projectVersionUrl;
    public $templateVersionUrl;


    public function mount()
    {

        /* Timezones */
        $timeZones = timezone_identifiers_list();
        $results = [];
        foreach ($timeZones as $timeZone) {
            if ($timeZone === setting('app_timezone')) {
                $results[] = ['id' => $timeZone, 'name' => $timeZone, 'selected' => true];
            } else {
                $results[] = ['id' => $timeZone, 'name' => $timeZone];
            }
        }

        $this->timeZones = $results;

        /* App Settings */
        $this->appName = setting('app_name');
        $this->appUrl = setting('app_url');
        $this->appLang = setting('app_lang');
        $this->appTimezone = setting('app_timezone');

        /* Unsplash Settings */
        $this->unsplashUtm = setting('unsplash_utm');
        $this->unsplashApiKey = decrypt(setting('unsplash_api_key'));

        /* Version Settings */
        $this->projectVersionUrl = setting('project_version_url');
        $this->templateVersionUrl = setting('template_version_url');
    }

    public function updateSystemSettings()
    {

        $this->validate([
            'appName' => 'nullable',
            'appUrl' => 'nullable|url',
            'appLang' => 'nullable',
            'appTimezone' => 'nullable',
            'unsplashUtm' => 'nullable',
            'unsplashApiKey' => 'nullable',
            'projectVersionUrl' => 'nullable|url',
            'templateVersionUrl' => 'nullable|url',
        ]);


        $settings = [
            'app_name' => $this->appName,
            'app_url' => $this->appUrl,
            'app_lang' => $this->appLang,
            'app_timezone' => $this->appTimezone,
            'unsplash_utm' => $this->unsplashUtm,
            'unsplash_api_key' => $this->unsplashApiKey ? encrypt($this->unsplashApiKey) : null,
            'project_version_url' => $this->projectVersionUrl,
            'template_version_url' => $this->templateVersionUrl
        ];

        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        Notification::make()
            ->success()
            ->title(__('pages/admin/settings/settings.notifications.settings_updated'))
            ->send();

        return redirect()->route('admin.settings');
    }

    public function render()
    {
        return view('livewire.admin.settings.system-settings');
    }
}
