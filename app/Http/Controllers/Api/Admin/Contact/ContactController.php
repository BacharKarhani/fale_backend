<?php

namespace App\Http\Controllers\Api\Admin\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactSetting;
use App\Models\ReceivedEmail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * 🔓 Public API: Get Contact Information
     */
public function getContactInfo()
{
    $contact = ContactSetting::first();

    if (!$contact) {
        return response()->json([
            'success' => false,
            'message' => 'Contact information not found.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'location' => $contact->location,
            'emails' => json_decode($contact->emails),
            'phones' => json_decode($contact->phones),
            'services' => json_decode($contact->services),
            'is_shown' => $contact->is_shown, // <-- Add this line
        ]
    ]);
}

public function getAdminContactInfo()
{
    $contact = ContactSetting::first();

    if (!$contact) {
        return response()->json([
            'success' => false,
            'message' => 'Contact information not found.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $contact->id,
            'location' => $contact->location,
            'emails' => json_decode($contact->emails),
            'phones' => json_decode($contact->phones),
            'services' => json_decode($contact->services),
            'is_shown' => $contact->is_shown,
        ]
    ]);
}


    /**
     * 🔓 Public API: Save Contact Form Submission
     */
    public function saveContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        // Send email to your desired address
        $toEmail = 'Info@lafeleb.com'; // <-- Change to your desired email

        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\nPhone: {$validated['phone']}\nService: {$validated['service']}\nMessage: {$validated['message']}",
            function ($message) use ($toEmail, $validated) {
                $message->to($toEmail)
                        ->subject('New Contact Form Submission');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully!'
        ], 201);
    }

    /**
     * 🔒 Admin API: Get All Received Emails
     */
    public function getAllReceivedEmails()
    {
        $emails = ReceivedEmail::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $emails
        ]);
    }

    /**
     * 🔒 Admin API: Delete a Received Email
     */
    public function deleteReceivedEmail($id)
    {
        $email = ReceivedEmail::find($id);

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found.'
            ], 404);
        }

        $email->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email deleted successfully.'
        ]);
    }

    /**
     * 🔒 Admin API: Add Contact Info
     */
    public function createContactInfo(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:500',
            'emails' => 'required|array|min:1',
            'emails.*' => 'email',
            'phones' => 'required|array|min:1',
            'phones.*' => 'string|max:20',
            'services' => 'required|array|min:1',
            'services.*' => 'string|max:255'
        ]);

        $contact = ContactSetting::create([
            'location' => $validated['location'],
            'emails' => json_encode($validated['emails']),
            'phones' => json_encode($validated['phones']),
            'services' => json_encode($validated['services']),
            'is_shown' => $request->has('is_shown') ? $request->is_shown : true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact information added successfully!',
            'data' => $contact
        ], 201);
    }

    /**
     * 🔒 Admin API: Update Contact Info
     */
    public function updateContactInfo(Request $request, $id)
    {
        $contact = ContactSetting::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found.'
            ], 404);
        }

        $validated = $request->validate([
            'location' => 'nullable|string|max:500',
            'emails' => 'nullable|array|min:1',
            'emails.*' => 'email',
            'phones' => 'nullable|array|min:1',
            'phones.*' => 'string|max:20',
            'services' => 'nullable|array|min:1',
            'services.*' => 'string|max:255'
        ]);

        $contact->update([
            'location' => $validated['location'] ?? $contact->location,
            'emails' => isset($validated['emails']) ? json_encode($validated['emails']) : $contact->emails,
            'phones' => isset($validated['phones']) ? json_encode($validated['phones']) : $contact->phones,
            'services' => isset($validated['services']) ? json_encode($validated['services']) : $contact->services,
            'is_shown' => $request->has('is_shown') ? $request->is_shown : $contact->is_shown,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact information updated successfully!',
            'data' => $contact
        ]);
    }

    /**
     * 🔒 Admin API: Delete Contact Info
     */
    public function deleteContactInfo($id)
    {
        $contact = ContactSetting::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact information not found.'
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact information deleted successfully.'
        ]);
    }
}
