<div class="flex flex-col justify-between min-h-screen relative">
    <div wire:ignore id="bg_image" class="absolute inset-0 z-[-1]">
    </div>
    <script src="{{ asset("js/sites/background.js") }}"></script>

    <div class="flex flex-col items-center justify-center px-6 mx-auto py-6">
        <p class="flex items-center mb-6 text-2xl font-semibold">
            <img class="w-32 h-32 mr-2" src="{{ asset("img/Logo.png") }}" alt="logo">
            <span
                class="text-4xl font-bold brand-text lg:block hidden" id="logo_text"
                wire:ignore>{{ env('APP_NAME') }}</span>
        </p>
        <div class="bg-neutral rounded-box sm:w-full w-auto">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">

                <form class="space-y-4 md:space-y-6" onsubmit="event.preventDefault()">
                    @csrf

                    <div class="form-control w-full">
                        <label class="label" for="current_password">
                            <span class="label-text">{{ __('pages/account/change-password.current_password') }}</span>
                        </label>
                        <input id="current_password" type="password"
                               class="input input-bordered w-full" wire:model="current_password"/>
                    </div>


                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="form-control w-full">
                            <label class="label" for="new_password">
                                <span class="label-text">{{ __('pages/account/change-password.new_password') }}</span>
                            </label>
                            <input id="new_password" type="password"
                                   class="input input-bordered w-full" wire:model="new_password"/>
                        </div>
                        <div class="form-control w-full">
                            <label class="label" for="new_password_confirm">
                                <span class="label-text">{{ __('pages/account/change-password.new_password_confirm') }}</span>
                            </label>
                            <input id="new_password_confirm" type="password"
                                   class="input input-bordered w-full" wire:model="new_password_confirm"/>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit"
                                class="flex-1 mr-2 btn btn-primary"
                                wire:click="changePassword" wire:ignore>
                            {{ __('pages/account/change-password.change_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="pl-6 pb-4" id="unsplashCredits" wire:ignore>
        <span class="text-sm" id="credits" wire:ignore><a id="photo">{{ __('messages.photo') }}</a>, <a
                id="author"></a>, <a
                href="https://unaplash.com/utm_source=CyanFox&utm_medium=referral">Unsplash</a></span>
    </div>
</div>
