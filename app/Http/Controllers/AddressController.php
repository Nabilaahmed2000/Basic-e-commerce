<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all addresses for the authenticated user
        $addresses = Address::where('user_id', auth()->user()->id)->get();
        return response()->json(['data' => $addresses], 200);

    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        //store the address for the authenticated user
        $address = Address::create([
            'user_id' => auth()->user()->id,
            'address_text' => $request->address_text,
            'building_number' => $request->building_number,
            'notes' => $request->notes,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address, $id)
    {
        //get specific address for the authenticated user
        $address = Address::where('user_id', auth()->user()->id)->where('id', $address->id)->first();

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address , $id)
    {
    //update specific address for the authenticated user
    $address = Address::where('user_id', auth()->user()->id)->where('id', $address->id)->first();
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
        $address = Address::where('user_id', auth()->user()->id)->where('id', $address->id)->first();
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
