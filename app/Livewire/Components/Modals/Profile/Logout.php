<?php

namespace App\Livewire\Components\Modals\Profile;

use LivewireUI\Modal\ModalComponent;

class Logout extends ModalComponent
{
    public function render()
    {
        return view('livewire.components.modals.profile.logout');
    }
}
