<?php

use App\Livewire\Account\ActivateTwoFactor;
use App\Livewire\Account\ChangePassword;
use App\Livewire\Account\ForgotPassword;
use App\Livewire\Admin\Admin;
use App\Livewire\Admin\Groups\GroupList;
use App\Livewire\Admin\Roles\RoleCreate;
use App\Livewire\Admin\Roles\RoleEdit;
use App\Livewire\Admin\Roles\RoleList;
use App\Livewire\Admin\Users\UserCreate;
use App\Livewire\Admin\Users\UserEdit;
use App\Livewire\Admin\Users\UserList;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Home;
use App\Livewire\Profile;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['setLanguage'])->group(function () {
    Route::middleware('auth')->group(function () {
        Route::middleware(['mustActivateTwoFactor', 'mustChangePassword'])->group(callback: function () {
            Route::get('/', Home::class)->name('home');
            Route::get('/profile', Profile::class)->name('profile');

            Route::group(['prefix' => 'admin', 'middleware' => ['role:Super Admin']], function () {
                Route::get('/', Admin::class)->name('admin');

                Route::group(['prefix' => '/users'], function () {
                    Route::get('/', UserList::class)->name('admin-user-list');
                    Route::get('/create', UserCreate::class)->name('admin-user-create');
                    Route::get('/edit/{userId}', UserEdit::class)->name('admin-user-edit');

                });
                Route::group(['prefix' => '/roles'], function () {
                    Route::get('/', RoleList::class)->name('admin-role-list');
                    Route::get('/create', RoleCreate::class)->name('admin-role-create');
                    Route::get('/edit/{roleId}', RoleEdit::class)->name('admin-role-edit');
                });
            });

            Route::get('/logout', function () {
                auth()->logout();
                return redirect()->route('login');
            })->name('logout');
        });

        Route::prefix('account')->group(function () {
            Route::get('/change-password', ChangePassword::class)->name('account.change-password');
            Route::get('/activate-two-factor', ActivateTwoFactor::class)->name('account.activate-two-factor');
        });
    });


    Route::middleware(['web', 'throttle:30,1', 'redirectIfAuthenticated'])->group(function () {
        Route::get('/login', Login::class)->name('login');

        if (env('ENABLE_REGISTRATION')) {
            Route::get('/register', Register::class)->name('register');
        }

        if (env('ENABLE_FORGOT_PASSWORD')) {
            Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
            Route::get('/forgot-password/{resetToken}', ForgotPassword::class)->name('forgot-password');
        }
    });

});
