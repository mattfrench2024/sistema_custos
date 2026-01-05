<?php

namespace App\Http\Controllers;

use App\Models\NotificationInternal;
use Illuminate\Http\Request;

class NotificationInternalController extends Controller
{
    public function index()
    {
        return NotificationInternal::with('user')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
        ]);

        $data['user_id'] = auth()->id();

        return NotificationInternal::create($data);
    }
    public function destroy($id)
    {
        $notification = NotificationInternal::findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully.']);
    }
    public function markAsRead($id)
    {
        $notification = NotificationInternal::findOrFail($id);
        $notification->read_at = now();
        $notification->save();

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
