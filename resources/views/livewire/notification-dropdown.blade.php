<div class="d-flex align-items-center">
    <!-- Friend Invitations -->
    <div class="dropdown me-2">
        <button class="btn btn-outline-light position-relative" wire:click="toggleInvitations">
            <i class="bi bi-people"></i>
            @if($invitations->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $invitations->count() }}
                </span>
            @endif
        </button>
        
        @if($showInvitations)
            <div class="dropdown-menu show position-absolute" style="right: 0; left: auto; min-width: 300px;">
                <h6 class="dropdown-header">Friend Invitations</h6>
                @forelse($invitations as $invitation)
                    <div class="dropdown-item-text p-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                {{ strtoupper(substr($invitation->sender->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <strong>{{ $invitation->sender->name }}</strong>
                                <small class="d-block text-muted">wants to be friends</small>
                                <div class="mt-2">
                                    <button wire:click="acceptInvite({{ $invitation->id }})" class="btn btn-sm btn-success me-2">Accept</button>
                                    <button wire:click="declineInvite({{ $invitation->id }})" class="btn btn-sm btn-outline-secondary">Decline</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="dropdown-item-text text-center text-muted py-3">
                        No friend invitations
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <!-- Notifications -->
    <div class="dropdown">
        <button class="btn btn-outline-light position-relative" wire:click="toggleNotifications">
            <i class="bi bi-bell"></i>
            @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadCount }}
                </span>
            @endif
        </button>
        
        @if($showNotifications)
            <div class="dropdown-menu show position-absolute" style="right: 0; left: auto; min-width: 350px;">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <h6 class="mb-0">Notifications</h6>
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead" class="btn btn-sm btn-link p-0">Mark all as read</button>
                    @endif
                </div>
                
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($notifications as $notification)
                        <div class="dropdown-item-text p-3 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}" 
                             wire:click="markAsRead({{ $notification->id }})">
                            <div class="d-flex">
                                <div class="me-3">
                                    @if($notification->type === 'ban')
                                        <i class="bi bi-exclamation-triangle-fill text-muted fs-5"></i>
                                    @elseif($notification->type === 'unban')
                                        <i class="bi bi-check-circle-fill text-muted fs-5"></i>
                                    @elseif($notification->type === 'post_like')
                                        <i class="bi bi-heart-fill text-muted"></i>
                                    @elseif($notification->type === 'post_comment')
                                        <i class="bi bi-chat-fill text-muted"></i>
                                    @elseif($notification->type === 'friend_invite')
                                        <i class="bi bi-person-plus-fill text-muted"></i>
                                    @else
                                        <i class="bi bi-bell-fill text-muted"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    @if($notification->type === 'ban')
                                        <div class="fw-bold">
                                            <i class="bi bi-shield-exclamation me-1"></i>Account Suspended
                                        </div>
                                        <div class="small">{{ $notification->message }}</div>
                                    @elseif($notification->type === 'unban')
                                        <div class="fw-bold">
                                            <i class="bi bi-shield-check me-1"></i>Account Restored
                                        </div>
                                        <div class="small">{{ $notification->message }}</div>
                                    @else
                                        <div class="fw-bold">{{ $notification->message }}</div>
                                    @endif
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if(!$notification->is_read)
                                    <div class="ms-2">
                                        <span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px;"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="dropdown-item-text text-center text-muted py-3">
                            No notifications
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
