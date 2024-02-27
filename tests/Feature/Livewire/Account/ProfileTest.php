<?php

namespace Tests\Feature\Livewire\Account;

use App\Livewire\Account\Profile;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Profile::class)
            ->assertStatus(200);
    }
}
