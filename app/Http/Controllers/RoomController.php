<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Rooms;
use App\Models\Member;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = 10;
        
        /* public rooms */
        $public_rooms = Rooms::where('private', 0)->orderBy('created_at', 'desc')->paginate($limit)->through(function ($room) {
            $count = Member::where('member_id', Auth::user()->id)->where('rooms_id', $room->id)->get()->count();
            return [
                'name' => $room->name,
                'desc' => $room->desc,
                // 'private' => $room->private,
                'created_at' => date('d M Y, H:ia', strtotime($room->created_at)),
                'owner_name' => User::find($room->owner)->name,
                'room_id' => Crypt::encryptString($room->id),
                'is_owner' => ($room->owner == Auth::user()->id ? true : false),
                'is_join' => ($count > 0 ? 'yes' : 'no'),
            ];
        });

        /* private rooms */
        $private_rooms = Rooms::join('member', 'rooms.id', '=', 'member.rooms_id')
        ->where('private', 1)
        ->where('member.member_id', '=', Auth::user()->id)
        ->orderBy('created_at', 'desc')->paginate($limit)->through(function ($room) {
            
            return [
                'name' => $room->name,
                'desc' => $room->desc,
                'created_at' => date('d M Y, H:ia', strtotime($room->created_at)),
                'owner_name' => User::find($room->owner)->name,
                'room_id' => Crypt::encryptString($room->rooms_id),
                'is_owner' => ($room->owner == Auth::user()->id ? true : false),
                'is_join' => 'yes',
            ];
        });

        return Inertia::render('Rooms', ['public_rooms' => $public_rooms, 'private_rooms' => $private_rooms]);
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
        $customAttr = [
            'name' => 'Room Name', 
            'desc' => 'Description',
            'private' => 'Private',
        ];

        $validated = $request->validate([
            'name' => 'required|min:5',
            'desc' => 'required|max:120',
            'private' => 'boolean',
        ], [], $customAttr);

        $room = Rooms::create([
            'name' => $request->name, 
            'desc' => $request->desc, 
            'private' => $request->private, 
            'owner' => Auth::user()->id
        ]);

        Member::create([
            'rooms_id' => $room->id,
            'member_id' => Auth::user()->id
        ]);

        return redirect(route('room.index'));
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
        Rooms::destroy($id);
        Message::where('room_id', $id)->delete();
        Member::where('rooms_id', $id)->delete();
        return redirect(route('room.index'));
    }

    /**
     * Member join room
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request)
    {

        $roomid = Crypt::decryptString($request->id);

        Member::create([
            'rooms_id' => $roomid, 
            'member_id' => Auth::user()->id
        ]);

        return redirect(route('message.index', $request->id));
    }

    /**
     * Member lear room
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leave(Request $request)
    {

        $roomid = Crypt::decryptString($request->id);
        Member::where('rooms_id', $roomid)->where('member_id', Auth::user()->id)->delete();

        return redirect(route('room.index'));
    }
}
