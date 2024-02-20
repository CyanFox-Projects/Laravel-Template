<?php

namespace Tests\Feature\Livewire\Components\Modals\Admin\Users;

use App\Livewire\Components\Modals\Admin\Users\DeleteUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(DeleteUser::class)
            ->assertStatus(200);
    }
}