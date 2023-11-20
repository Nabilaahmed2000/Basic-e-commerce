<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\User;

class AddressController extends Controller
{
    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        //store the address for the authenticated user
        $user_id = auth()->user()->id;
        $address = Address::create([
            'user_id' =>  $user_id,
            'address_text' => $request->address_text,
            'building_number' => $request->building_number,
            'notes' => $request->notes,
        ]);
        return response()->json(['message' => 'Address created successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address,$user_id)
    {
        //get specific address for the authenticated user
        $addresses = User::find($user_id)->addresses;
        return response()->json(['addresses' => $addresses], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address , $id)
    {
    //update specific address for the authenticated user
    $user_id = auth()->user()->id;
    $address =User::find($user_id)->addresses->where('id', $address->id)->first();
    $address->update([
        'address_text' => $request->address_text,
        'building_number' => $request->building_number,
        'notes' => $request->notes,
    ]);
    return response()->json(['message' => 'Address updated successfully'], 200);
}
   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        //delete specific address for the authenticated user
        $user_id = auth()->user()->id;
        $address =User::find($user_id)->addresses->where('id', $address->id)->first();
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
