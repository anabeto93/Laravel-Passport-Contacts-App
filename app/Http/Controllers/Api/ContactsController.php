<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Resources\Contact as ContactResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        Log::info('Retrieving all contacts belonging to this person');

        if(in_array($request->user()->id,[1,2])) {
            $contacts = Contact::all();
        }else {
            Log::info('Getting only contacts belonging to this User');
            //only return users belonging to this person
            $contacts = Contact::where('user_id',$request->user()->id)->get();
        }


        return  ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        Log::info('Request to create a new contact');
        Log::debug($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['status' => 'Declined', 'code' => 422, 'errors' => $validator->errors()],422);
        }

        //$contact = $request->user()->contacts()->create($request->all());
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $contact = Contact::create($data);
        Log::info('New Created Contact'); Log::debug($contact);

        return new ContactResource($contact);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return mixed
     */
    public function show($id)
    {
        $contact = Contact::find($id);

        if(!$contact instanceof Contact) {
            return response()->json(errorResponse([],'Error',404,'Contact not found.'));
        }

        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        Log::info('Request to Update contact');
        Log::debug($request->all());
        $contact = Contact::find($id);

        if(!$contact instanceof Contact) {
            return response()->json(errorResponse([],'Error',404,'Contact not found.'));
        }

        if($request->user()->id !== $contact->user_id) {
            return response()->json(errorResponse([],'Declined',401,'Unauthorized action'),401);
        }

        //$contact = $request->user()->contacts()->update($request->all());
        $contact->update($request->all());
        Log::info('Updated contact is ',['contact' => $contact->toArray()]);

        return new ContactResource($contact);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);
        Log::info('Request to delete contact');
        Log::debug($contact);

        if(!$contact instanceof Contact) {
            return response()->json(errorResponse([],'Error',404,'Contact not found.'));
        }

        try {
            $contact->delete();
        }catch(\Exception $e) {
            Log::info('Error while attempting to delete Contact ID: '.$id);
            Log::debug($e->getMessage());
        }

        return response()->json(successResponse([],'Success',200,'Contact deleted.'));
    }
}
