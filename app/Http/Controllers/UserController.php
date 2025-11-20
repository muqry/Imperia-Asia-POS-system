<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Show the user index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all(); // ✅ Includes Admins, Cashiers, and any others
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {

        $users = new User;
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = $request->password; // Plain text UNTUK TGK RAW DATA
        $users->is_admin = $request->is_admin;
        $users->save();
        if ($users) {
            return redirect()->back()->with('Success', 'User Created Successfully');
        }
        return redirect()->back()->with('Error', 'User Fail Created');
    }


    // ORIGINAL UPDATE
    //public function update(Request $request, $id)
    //{
    //$users = User::find($id);
    //if (!$users){

    //return back()->with('Error', 'User not found');
    //}
    //$users->update($request->all());
    // return back()->with('Success', 'User Updated Successfully');
    //}


    //NI UPDATE YG NAK TGK RAW DATA NNT BOLE DELETE PAKAI ORIGINA BALIK
    public function update(Request $request, $id)
    {
        $users = User::find($id);
        if (!$users) {
            return back()->with('Error', 'User not found');
        }

        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = $request->password; // ✅ Plain text
        $users->is_admin = $request->is_admin;
        $users->save();

        return back()->with('Success', 'User Updated Successfully');
    }


    public function destroy($id)
    {
        $users = User::find($id);
        if (!$users) {

            return back()->with('Error', 'User not found');
        }
        $users->delete();
        return back()->with('Success', 'User Deleted Successfully');
    }
}
