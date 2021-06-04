<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Rooms;
use App\Models\Invite;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function destroy(Request $request, $id)
    {
        $id = Crypt::decryptString($request->id);

        $members = Member::where('id', $id)->get();
        if(count($members) > 0) {
            foreach($members as $key => $member) {
                $roomid = Crypt::encryptString($member->rooms_id);
            }
        }

        Member::where('id', $id)->delete();

        return redirect(route('message.index', $roomid));
    }

    /**
     * Send invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function invite(Request $request)
    {
        $roomid = Crypt::decryptString($request->id_encrypt);

        $customAttr = [
            'email' => 'Email', 
            'id_encrypt' => 'Room',
        ];

        $validated = $request->validate([
            'email' => 'required|email',
            'id_encrypt' => 'required',
        ]);

        $count = Invite::where('email', $request->email)->where('room_id', $roomid)->count();
        if($count > 0) {
            throw ValidationException::withMessages(['email' => $request->email.' existed']);
        }
        else {
            Invite::create([
                'email' => $request->email, 
                'room_id' => $roomid
            ]);
        }
        
        return redirect(route('message.index', $request->id_encrypt));
    }

    /**
     * Remove invitation by admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invitedestroy(Request $request, $id)
    {
        $id = Crypt::decryptString($request->id);

        $invites = Invite::where('id', $id)->get();
        if(count($invites) > 0) {
            foreach($invites as $key => $invite) {
                $roomid = Crypt::encryptString($invite->room_id);
            }
        }

        Invite::where('id', $id)->delete();

        return redirect(route('message.index', $roomid));
    }

    /**
     * Decline invitation by member
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invitedecline(Request $request, $id)
    {
        $id = Crypt::decryptString($request->id);

        Invite::where('id', $id)->delete();

        return redirect(route('dashboard'));
    }

    /**
     * Accept invitation by member
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inviteaccept(Request $request, $id)
    {
        $id = Crypt::decryptString($request->id);

        $invites = Invite::where('id', $id)->get();
        if(count($invites) > 0) {
            foreach($invites as $key => $invite) {
                $roomid = $invite->room_id;
                $roomid_enc = Crypt::encryptString($roomid);

                Member::create([
                    'rooms_id' => $roomid, 
                    'member_id' => Auth::user()->id
                ]);
            }
        }

        Invite::where('id', $id)->delete();

        return redirect(route('dashboard'));
    }
}
