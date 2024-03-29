<x-modal class="modal-bottom sm:modal-middle">

    <x-form wire:submit="activateTwoFactor">
        @csrf
        <div class="md:flex justify-center mb-3">

            <div class="flex flex-col items-center mr-4">
                <img src="data:image/svg+xml;base64,{{ user()->getUser($this->user)->getTwoFactorManager()->getTwoFactorImage() }}" alt="Two Factor Image"
                     class="border-4 border-white mb-2">
                <p>{{ decrypt($user->two_factor_secret) }}</p>
            </div>

            <div class="space-y-4 md:mt-2 mt-6">
                <x-input label="{{ __('messages.password') }}"
                         type="password" class="input input-bordered"
                         wire:model="password" required/>

                <x-input label="{{ __('messages.two_factor_code') }}"
                         class="input input-bordered"
                         wire:model="twoFactorCode" required/>
            </div>

        </div>

        <div class="divider"></div>

        <div class="mt-2 flex justify-between gap-3">
            <button class="btn btn-neutral flex-grow" type="button"
                    wire:click="$dispatch('closeModal')">{{ __('messages.buttons.cancel') }}</button>

            <x-button class="btn btn-success flex-grow"
                      type="submit" spinner="activateTwoFactor">
                {{ __('account/profile.modals.activate_two_factor.buttons.activate_two_factor') }}
            </x-button>
        </div>
    </x-form>
</x-modal>
