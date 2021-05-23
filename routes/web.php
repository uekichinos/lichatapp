<?php

use App\Models\User;
use Inertia\Inertia;
use App\Models\Rooms;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    /* dashboard */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* message */
    Route::get('/message/{id}', [MessageController::class, 'index'])->name('message.index');
    Route::post('/message', [MessageController::class, 'store'])->name('message.store');
    Route::delete('/message/{id}', [MessageController::class, 'destroy'])->name('message.destroy');

    /* room */
    Route::get('/room', [RoomController::class, 'index'])->name('room.index');
    Route::post('/room', [RoomController::class, 'store'])->name('room.store');
    Route::delete('/room/{id}', [RoomController::class, 'destroy'])->name('room.destroy');

    /* member */
    Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');
    Route::post('/invite', [MemberController::class, 'invite'])->name('member.invite');
    Route::delete('/invite/{id}', [MemberController::class, 'invitedestroy'])->name('member.invitedestroy');
    Route::post('/invite/accept/{id}', [MemberController::class, 'inviteaccept'])->name('member.inviteaccept');
    Route::post('/invite/decline/{id}', [MemberController::class, 'invitedecline'])->name('member.invitedecline');
});

