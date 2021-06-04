<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Rooms;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = Crypt::decryptString($request->id);

        $room = Rooms::where('id', $id)->get();
        if(count($room) <= 0) {
            echo "invalid room id";
            exit;
        }

        /* get messages record */
        $limit = 10;
        $messages = Message::where('room_id', $id)->orderBy('created_at', 'desc')->paginate($limit)->through(function ($message) {
            return [
                'memberid' => $message->member_id,
                'roomid' => $message->room_id,
                'text' => nl2br($message->text),
                'cleandate' => date('d M Y, H:ia', strtotime($message->created_at)),
                'person' => User::find($message->member_id)->name,
                'id_encrypt' => Crypt::encryptString($message->id),
                'isowner' => ($message->member_id == Auth::user()->id ? true : false)
            ];
        });

        /* get invitation record */
        $limit = 5;
        $invites = Invite::where('room_id', $id)->orderBy('created_at', 'desc')->paginate($limit)->through(function ($invite) {
            return [
                'email' => $invite->email,
                'cleandate' => date('d M Y, H:ia', strtotime($invite->created_at)),
                'id_encrypt' => Crypt::encryptString($invite->id),
            ];
        });

        /* get member associate with this room */
        $limit = 5;
        $members = Member::where('rooms_id', $id)->orderBy('created_at', 'desc')->paginate($limit)->through(function ($member) {
            
            $room = Rooms::find($member->rooms_id);
            return [
                'memberid' => $member->member_id,
                'person' => User::find($member->member_id)->name,
                'cleandate' => date('d M Y, H:ia', strtotime($member->created_at)),
                'id_encrypt' => Crypt::encryptString($member->id)
            ];
        });

        $my_id = Crypt::encryptString(Auth::user()->id);

        return Inertia::render('Message', [
            'room' => $room,
            'room_owner' => ($room[0]->owner == Auth::user()->id ? true : false), 
            'messages' => $messages, 
            'invites' => $invites, 
            'members' => $members, 
            'id_encrypt' => $request->id,
            'my_id' => $my_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $room_id = Crypt::decryptString($request->id_encrypt);

        $validated = $request->validate([
            'text' => 'required|min:5'
        ]);

        Message::create(['text' => strip_tags($request->text), 'member_id' => Auth::user()->id, 'room_id' => $room_id]);

        return redirect(route('message.index', $request->id_encrypt));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id = Crypt::decryptString($request->id);

        $rooms = Message::where('id', $id)->get();
        if(count($rooms) > 0) {
            foreach($rooms as $key => $room) {
                $room_id = Crypt::encryptString($room->room_id);
            }
        }

        Message::where('id', $id)->delete();

        return redirect(route('message.index', $room_id));
    }
}
