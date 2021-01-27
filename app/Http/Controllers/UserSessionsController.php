<?php

namespace App\Http\Controllers;

use App\Models\SessionData;
use Illuminate\Http\Request;

class UserSessionsController extends Controller
{
    // get all the sessions details of the user
    public function index(Request $request)
    {
        $sessions = SessionData::where('user_id', $request->user()->id)->get();
        $data = ['sessions' => $sessions, 'current' => $request->session()->getId()];
        if ($request->isJson() || $request->ajax())
            return $data;
        else {
            return view('user.active-sessions', $data);
        }
    }

    // get all details of a particular session
    public function show(Request $request, $id)
    {
        $session_data = SessionData::findOrFail($id);
        $geo_data = geoip($session_data->ip_address);
        $data = ['session_data' => $session_data, 'geo_data' => $geo_data];
        if ($request->isJson() || $request->ajax())
            return $data;
        else {
            return view('user.session-info', $data);
        }
    }

    // get all details of a particular session
    public function destroy(Request $request, $id)
    {
        $session_data = SessionData::findOrFail($id);
        $session_data->isValid = false;
        $session_data->save;

        $changed = $session_data->wasChanged('isValid');

        $data = [$changed ? "success" : "error" => $changed ? "Session was revoked successfully" : "Unexpected error: session was not revoked"];

        if ($request->isJson() || $request->ajax())
            return $data;
        else {
            return view('user.session-info', $data);
        }
    }
}
