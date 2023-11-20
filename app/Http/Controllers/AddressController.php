<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\User;

class AddressController extends Controller
{


    //display all addresses api
    public function index()
    {
        $addresses = auth()->user()->addresses()->get();
        return response()->json(['addresses' => $addresses], 200);
    }

    public function store(StoreAddressRequest $request)
    {

        $address = auth()->user()->addresses()->create($request->validated());
        return response()->json(['message' => 'Address created successfully' , 'address'=> $address], 200);
    }

    public function show(Address $address)
    {
        $address = auth()->user()->addresses()->where('id', $address->id)->first();
        return response()->json(['address' => $address], 200);
    }

    public function update(UpdateAddressRequest $request, Address $address, $id)
    {
        //update specific address for the authenticated user
        $address = auth()->user()->addresses()->update($request->validated());
        return response()->json(['message' => 'Address updated successfully'], 200);
    }


    public function destroy(Address $address)
    {
        //delete specific address for the authenticated user
        $address = auth()->user()->addresses()->where('id', $address->id)->first();
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
