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

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* get invitation */
        $limit = 10;
        $invites = Invite::where('email', Auth::user()->email)
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit)
                    ->through(function ($invite) {

            $room = Rooms::find($invite->room_id);
            return [
                'id' => $invite->id,
                'roomid' => $invite->room_id,
                'cleandate' => date('d M Y, H:ia', strtotime($invite->created_at)),
                'person' => User::find($room->owner)->name,
                'id_encrypt' => Crypt::encryptString($invite->id),
                'roomname' => $room->name,
            ];
        });

        /* public chat room */
        $joined_public = Member::where('member_id', Auth::user()->id)
                            ->join('rooms', 'rooms.id', '=', 'member.rooms_id')
                            ->where('private', 0)
                            ->count();
        $total_public = Rooms::where('private', 0)
                            ->count();

        /* private chat room */
        $joined_private = Member::where('member_id', Auth::user()->id)
                            ->join('rooms', 'rooms.id', '=', 'member.rooms_id')
                            ->where('private', 1)
                            ->count();
        $total_private = Rooms::where('private', 1)
                            ->count();

        /* my chat room */
        $limit = 10;
        $myrooms = Rooms::where('owner', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit)
                    ->through(function ($room) {

            $count_member = Member::where('rooms_id', $room->id)->count();
            $count_message = Message::where('room_id', $room->id)->count();
            return [
                'name' => $room->name,
                'count_member' => $count_member,
                'count_message' => $count_message,
            ];
        });

        return Inertia::render('Dashboard', [
            'invites' => $invites, 
            'joined_public' => $joined_public, 
            'total_public' => $total_public,
            'joined_private' => $joined_private, 
            'total_private' => $total_private,
            'myrooms' => $myrooms
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
        //
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
    public function destroy($id)
    {
        //
    }
}
