<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use App\Helpers\API\Formatter;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController
{
    use Formatter;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $addresses = $user->addresses()->paginate();
        return $this->withSuccess($addresses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:shipping,billing',
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'phone' => 'required|max:20',
            'country' => 'required|max:10',
            'state' => 'required|max:191',
            'city' => 'required|max:191',
            'street_address_1' => 'required|max:191',
            'street_address_2' => 'nullable|max:191',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        try {
            $address = $user->addresses()->create($request->only(['type', 'name', 'email', 'phone', 'country', 'state', 'city', 'street_address_1', 'street_address_2', 'latitude', 'longitude']));
            if (!$address) throw new \Exception('Unable to create new address');
        } catch (\Exception $exception) {
            $this->withErrors($exception->getMessage());
        }
        return $this->withSuccess($address);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Address $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        if (!$address instanceof Address) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($user->id != $address->user_id) throw new AuthorizationException();
        return $this->withSuccess($address);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Address $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        if (!$address instanceof Address) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($user->id != $address->user_id) throw new AuthorizationException();
        $request->validate([
            'type' => 'required|in:shipping,billing',
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'phone' => 'required|max:20',
            'country' => 'required|max:10',
            'state' => 'required|max:191',
            'city' => 'required|max:191',
            'street_address_1' => 'required|max:191',
            'street_address_2' => 'nullable|max:191',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        try {
            $res = $address->update($request->only(['type', 'name', 'email', 'phone', 'country', 'state', 'city', 'street_address_1', 'street_address_2', 'latitude', 'longitude']));
            if (!$res) throw new \Exception('Unable to update the address');
        } catch (\Exception $exception) {
            $this->withErrors($exception->getMessage());
        }
        return $this->withSuccess($address);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Address $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        if (!$address instanceof Address) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($user->id != $address->user_id) throw new AuthorizationException();
        try {
            $res = $address->delete();
            if (!$res) throw new \Exception('Unable to delete address');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        return $this->withSuccess('Address deleted successfully');
    }
}
