<x-modal class="modal-bottom sm:modal-middle">

    <div class="text-center">
        <h2 class="text-2xl font-bold mb-4">{{ __('account/profile.modals.change_avatar.title') }}</h2>
        <p class="mb-3">{{ __('account/profile.modals.change_avatar.description') }}</p>
    </div>

    <x-form wire:submit="updateAvatar" novalidate>
        <div class="flex justify-center my-6">

            <x-file wire:model="avatar" accept="image/png"
                    hide-progress=""
                    change-text="{{ __('messages.image_upload.hover') }}"
                    crop-title-text="{{ __('messages.image_upload.crop.title') }}"
                    crop-cancel-text="{{ __('messages.buttons.cancel') }}"
                    crop-save-text="{{ __('messages.buttons.save') }}"
                    crop-button-class="btn-success"
                    crop-after-change
                    required>
                <img src="{{ user()->getUser($user)->getAvatarURL() }}" class="h-30 rounded-lg" alt="Avatar"/>
            </x-file>

        </div>

        <div class="divider mt-4">{{ strtoupper(__('messages.or')) }}</div>

        <div class="my-4">
            <x-input label="{{ __('account/profile.modals.change_avatar.custom_avatar_url') }}" wire:model="customAvatarUrl"
                     class="input-bordered"/>
        </div>

        <div class="divider mt-4"></div>

        <div class="mt-2 flex justify-between gap-3">
            <button class="btn btn-neutral flex-grow" type="button"
                    wire:click="$dispatch('closeModal')">{{ __('messages.buttons.cancel') }}</button>

            <button class="btn btn-warning flex-grow" type="button"
                    wire:click="resetAvatar">{{ __('account/profile.modals.change_avatar.buttons.reset_avatar') }}</button>

            <x-button class="btn btn-success flex-grow"
                      type="submit" spinner="updateAvatar">
                {{ __('account/profile.modals.change_avatar.buttons.save_avatar') }}
            </x-button>
        </div>
    </x-form>
</x-modal>
