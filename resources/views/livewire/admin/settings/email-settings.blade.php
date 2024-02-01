<div>
    <div role="tablist" class="tabs tabs-bordered my-4">
        <a role="tab" class="tab @if($tab == 'welcome') tab-active @endif"
           wire:click="$set('tab', 'welcome')"><i class="icon-hand pr-2"></i>
            <span class="md:block hidden">{{ __('pages/admin/settings/email_settings.tabs.welcome') }}</span>
        </a>

        <a role="tab" class="tab @if($tab == 'login') tab-active @endif"
           wire:click="$set('tab', 'login')"><i class="icon-log-in pr-2"></i>
            <span class="md:block hidden">{{ __('pages/admin/settings/email_settings.tabs.login') }}</span>
        </a>

        <a role="tab" class="tab @if($tab == 'forgotPassword') tab-active @endif"
           wire:click="$set('tab', 'forgotPassword')"><i class="icon-help-circle pr-2"></i>
            <span class="md:block hidden">{{ __('pages/admin/settings/email_settings.tabs.forgot_password') }}</span>
        </a>
    </div>

    <x-form wire:submit="updateEmailSettings">
        @csrf
        @if($tab == 'welcome')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-5 mb-5">
                <x-input label="{{ __('pages/admin/settings/email_settings.welcome.title') }}"
                         class="input-bordered" wire:model="welcomeEmailTitle"
                         hint="{{ __('pages/admin/settings/email_settings.welcome.hints.title') }}" required/>

                <x-input label="{{ __('pages/admin/settings/email_settings.welcome.subject') }}"
                         class="input-bordered" wire:model="welcomeEmailSubject"
                         hint="{{ __('pages/admin/settings/email_settings.welcome.hints.subject') }}" required/>
            </div>

            {{ $this->getForm('welcomeEmailContent') }}
            <div
                class="label-text-alt text-gray-400 p-1 pb-0">{{ __('pages/admin/settings/email_settings.welcome.hints.content') }}</div>
        @endif

        @if($tab == 'login')
            <div class="pt-5">
                <x-select label="{{ __('pages/admin/settings/email_settings.login.enable_login') }}"
                          wire:model="enableLoginEmail"
                          class="select select-bordered mb-4 "
                          :options="[
                            ['id' => '1', 'name' => __('messages.yes')],
                            ['id' => '0', 'name' => __('messages.no')]
                        ]" required></x-select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <x-input label="{{ __('pages/admin/settings/email_settings.login.title') }}"
                         class="input-bordered" wire:model="loginEmailTitle"
                         hint="{{ __('pages/admin/settings/email_settings.login.hints.title') }}" required/>

                <x-input label="{{ __('pages/admin/settings/email_settings.login.subject') }}"
                         class="input-bordered" wire:model="loginEmailSubject"
                         hint="{{ __('pages/admin/settings/email_settings.login.hints.subject') }}" required/>
            </div>

            {{ $this->getForm('loginEmailContent') }}
            <div
                class="label-text-alt text-gray-400 p-1 pb-0">{{ __('pages/admin/settings/email_settings.login.hints.content') }}</div>
        @endif

        @if($tab == 'forgotPassword')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-5 mb-5">
                <x-input label="{{ __('pages/admin/settings/email_settings.forgot_password.title') }}"
                         class="input-bordered" wire:model="forgotPasswordEmailTitle"
                         hint="{{ __('pages/admin/settings/email_settings.forgot_password.hints.title') }}" required/>

                <x-input label="{{ __('pages/admin/settings/email_settings.forgot_password.subject') }}"
                         class="input-bordered" wire:model="forgotPasswordEmailSubject"
                         hint="{{ __('pages/admin/settings/email_settings.forgot_password.hints.subject') }}" required/>
            </div>

            {{ $this->getForm('forgotPasswordEmailContent') }}
            <div
                class="label-text-alt text-gray-400 p-1 pb-0">{{ __('pages/admin/settings/email_settings.forgot_password.hints.content') }}</div>
        @endif

        <div class="divider"></div>

        <div class="mt-2 flex justify-start gap-3">
            <x-button class="btn btn-success"
                      type="submit" spinner="updateEmailSettings">
                {{ __('messages.buttons.update') }}
            </x-button>
        </div>
    </x-form>
</div>
