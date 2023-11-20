<?php

namespace App\Http\Controllers\Website;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Controllers\Controller;

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

    public function show($id)
    {
        $address = auth()->user()->addresses()->find($id)->first();
        return response()->json(['address' => $address], 200);
    }

    public function update(UpdateAddressRequest $request, $id)
    {
        //update specific address for the authenticated user
        $address = auth()->user()->addresses()->find($id)->update($request->validated());
        return response()->json(['message' => 'Address updated successfully'], 200);
    }


    public function destroy($id)
    {
        //delete specific address for the authenticated user
        $address = auth()->user()->addresses()->find($id)->first();
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
