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
        
        $rooms = Member::where('memberid', Auth::user()->id)->orderBy('created_at', 'desc')->paginate($limit)->through(function ($member) {

            $room = Rooms::find($member->roomid);
            return [
                'name' => $room->name,
                'desc' => $room->desc,
                'private' => $room->private,
                'cleandate' => date('d M Y, H:ia', strtotime($room->created_at)),
                'person' => User::find($room->owner)->name,
                'id_encrypt' => Crypt::encryptString($room->id),
                'isowner' => ($room->owner == Auth::user()->id ? true : false)
            ];
        });
        return Inertia::render('Rooms', ['rooms' => $rooms]);
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
            'roomid' => $room->id,
            'memberid' => Auth::user()->id
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
        Message::where('roomid', $id)->delete();
        Member::where('roomid', $id)->delete();
        return redirect(route('room.index'));
    }
}
