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

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/chat/{id}', function (Request $request) {

        $id = Crypt::decryptString($request->id);

        $room = Rooms::where('id', $id)->get();
        if(count($room) <= 0) {
            echo "invalid room id";
            exit;
        }

        $messages = Message::where('room_id', $id)->orderBy('created_at', 'desc')->get();
        if(count($messages) > 0) {
            foreach($messages as $key => $message) {
                $messages[$key]->cleandate = date('d M Y, H:ia', strtotime($message->created_at));
                $messages[$key]->person = User::find($message->memberid)->name;
                $messages[$key]->text = nl2br($message->text);
                $messages[$key]->id_encrypt = Crypt::encryptString($message->id);
            }
        }
        
        return Inertia::render('Chat', ['messages' => $messages, 'room' => $room, 'id_encrypt' => $request->id]);
    })->name('chat');

    Route::post('/messages', function(Request $request) {

        $room_id = Crypt::decryptString($request->id_encrypt);

        $validated = $request->validate([
            'text' => 'required|min:5'
        ]);

        Message::create(['text' => strip_tags($request->text), 'memberid' => Auth::user()->id, 'room_id' => $room_id]);
        return redirect('/chat/'.$request->id_encrypt);
    })->name('message.store');

    Route::delete('/messages/{id}', function(Request $request) {

        $id = Crypt::decryptString($request->id);

        $rooms = Message::where('id', $id)->get();
        if(count($rooms) > 0) {
            foreach($rooms as $key => $room) {
                $room_id = Crypt::encryptString($room->room_id);
            }
        }

        Message::where('id', $id)->delete();
        return redirect('/chat/'.$room_id);
    })->name('message.delete');

    Route::get('/rooms', function(Request $request) {

        $rooms = Rooms::orderBy('created_at', 'desc')->get();
        if(count($rooms) > 0) {
            foreach($rooms as $key => $room) {
                $rooms[$key]->cleandate = date('d M Y, H:ia', strtotime($room->created_at));
                $rooms[$key]->person = User::find($room->owner)->name;
                $rooms[$key]->id_encrypt = Crypt::encryptString($room->id);
            }
        }

        return Inertia::render('Rooms', ['rooms' => $rooms]);
    })->name('rooms.index');

    Route::post('/rooms', function(Request $request) {

        $customAttr = ['name' => 'Room Name', 'desc' => 'Description'];
        $validated = $request->validate([
            'name' => 'required|min:5',
            'desc' => 'required|max:120',
        ], [], $customAttr);

        Rooms::create(['name' => $request->name, 'desc' => $request->desc, 'owner' => Auth::user()->id]);
        return redirect('/rooms');
    })->name('rooms.store');

    Route::delete('/rooms/{id}', function(Request $request) {

        $id = Crypt::decryptString($request->id);
        Rooms::destroy($id);
        Message::where('room_id', $id)->delete();
        return redirect('/rooms');
    })->name('rooms.delete');
});

