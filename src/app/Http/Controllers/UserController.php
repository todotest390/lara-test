<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Create;
use App\User;
use MongoDB\BSON\ObjectID;
use App\UserContact;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::with('user_contacts')->get();
        return view('users.index',compact('users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $user = User::create($request->all());
        $userContact = UserContact::create([
            'user_id' => new ObjectID($user->id),
            'address' => $request->get('address'),
            'country'=> $request->get('country')
        ]);
        return redirect('/users')->with('success', 'User has been added');
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
        $user = User::with('user_contacts')->find($id);
//        echo "<pre>";
//        print_R($user->toArray());die;
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Create $request, $id)
    {
        $user = User::with('user_contacts')->find($id);
        if($user){
            $user->update($request->all());
            $userContact = UserContact::where('user_id', $id)->first();
            if(!$userContact){
                $userContactSave = UserContact::create([
                    'user_id' => new ObjectID($id),
                    'address' => $request->get('address'),
                    'country'=> $request->get('country')
                ]);
            }else{
                $userContact->address = $request->get('address');
                $userContact->country = $request->get('country');
                $userContact->update();
            }
        }
        return redirect('/users')->with('success', 'User has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if($user){
            $userContact = UserContact::where('user_id', $id)->first();
            if($userContact){
                $userContact->delete();
            }
            $user->delete();
        }

        return redirect('/users')->with('success', 'User has been deleted Successfully');
    }
}
