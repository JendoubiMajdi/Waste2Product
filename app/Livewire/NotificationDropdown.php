<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $showNotifications = false;
    public $showInvitations = false;

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
        $this->showInvitations = false;
    }

    public function toggleInvitations()
    {
        $this->showInvitations = !$this->showInvitations;
        $this->showNotifications = false;
    }

    public function markAsRead($notificationId)
    {
        Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function acceptInvite($inviteId)
    {
        $invite = Invitation::find($inviteId);
        if ($invite->receiver_id !== Auth::id()) return;
        
        $invite->status = 'accepted';
        $invite->save();
        
        // Add friendship both ways
        Auth::user()->friends()->attach($invite->sender_id);
        $invite->sender->friends()->attach(Auth::id());
        
        session()->flash('success', 'Friend invitation accepted!');
    }

    public function declineInvite($inviteId)
    {
        $invite = Invitation::find($inviteId);
        if ($invite->receiver_id !== Auth::id()) return;
        
        $invite->status = 'refused';
        $invite->save();
        
        session()->flash('success', 'Friend invitation declined.');
    }

    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $invitations = Invitation::where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('livewire.notification-dropdown', compact('notifications', 'invitations', 'unreadCount'));
    }
}
