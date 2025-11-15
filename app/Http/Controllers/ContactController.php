<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Log;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $contacts = $user->contacts()
                        ->orderBy('id', 'desc')
                        ->paginate(5);

        Log::info($contacts);

        if ($contacts->isEmpty()) {
            return response()->json([
                'message' => 'Nenhum contato encontrado.',
                'data' => []
            ], 200);
        }

        return response()->json($contacts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('contacts', 'public');
        }

        $contact = Auth::user()->contacts()->create($data);

        return response()->json($contact, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $this->authorizeUser($contact);
        return response()->json($contact);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorizeUser($contact);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($contact->image) Storage::disk('public')->delete($contact->image);

            $data['image'] = $request->file('image')->store('contacts', 'public');
        }

        $contact->update($data);
        return response()->json($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $this->authorizeUser($contact);
        if ($contact->image) Storage::disk('public')->delete($contact->image);

        $contact->delete();
        return response()->noContent();
    }

    private function authorizeUser(Contact $contact)
    {
        if (!Auth::user()->contacts->contains($contact)) {
            abort(403, 'Cant Access.');
        }
    }
}
