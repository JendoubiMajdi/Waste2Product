<div>
    <!-- Invites -->
    @if($invitations->count() > 0)
        <div class="alert alert-info mb-4">
            <h6><i class="bi bi-envelope"></i> Friend Invitations</h6>
            @foreach ($invitations as $invite)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><strong>{{ $invite->sender->name }}</strong> sent you a friend invitation</span>
                    <div>
                        <button wire:click="acceptInvite({{ $invite->id }})" class="btn btn-sm btn-success me-2">Accept</button>
                        <button wire:click="refuseInvite({{ $invite->id }})" class="btn btn-sm btn-outline-secondary">Decline</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Post Creation -->
    <div class="card mb-4" id="create-post">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Post</h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="createPost">
                <div class="mb-3">
                    <textarea wire:model="newContent" placeholder="Share your thoughts..." class="form-control" rows="3"></textarea>
                    @error('newContent') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Post Type</label>
                            <select wire:model.live="newMediaType" class="form-select">
                                <option value="text">Text Only</option>
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                                <option value="link">Link</option>
                            </select>
                            @error('newMediaType') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    @if ($newMediaType === 'image' || $newMediaType === 'video')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Upload {{ ucfirst($newMediaType) }}</label>
                                <input type="file" wire:model="newMedia" class="form-control" 
                                       accept="{{ $newMediaType === 'image' ? 'image/*' : 'video/*' }}">
                                @if($newMedia)
                                    <div class="form-text text-success">
                                        <i class="bi bi-check-circle"></i> File selected: {{ $newMedia->getClientOriginalName() }}
                                    </div>
                                @endif
                                @error('newMedia') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    @endif
                </div>
                
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-send"></i> Post</span>
                    <span wire:loading><i class="bi bi-hourglass-split"></i> Posting...</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Feed -->
    @forelse ($posts as $post)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ $post->user->name }}</strong>
                        <small class="text-muted d-block">{{ $post->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @if($post->user_id !== Auth::id())
                    @if($post->is_friend)
                        <span class="btn btn-sm btn-success disabled">
                            <i class="bi bi-people-fill"></i> Friends
                        </span>
                    @else
                        <button wire:click="openInviteModal({{ $post->user_id }})" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person-plus"></i> Add Friend
                        </button>
                    @endif
                @endif
            </div>
            
            <div class="card-body">
                @if($post->content)
                    <p class="card-text">{{ $post->content }}</p>
                @endif
                
                @if ($post->media_url)
                    @if ($post->media_type === 'image')
                        <img src="{{ Storage::url($post->media_url) }}" class="img-fluid rounded mb-3" style="max-height: 400px;">
                    @elseif ($post->media_type === 'video')
                        <video src="{{ Storage::url($post->media_url) }}" controls class="w-100 mb-3" style="max-height: 400px;"></video>
                    @elseif ($post->media_type === 'link')
                        <a href="{{ $post->media_url }}" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-link-45deg"></i> View Link
                        </a>
                    @endif
                @endif
                
                <!-- Reaction Buttons -->
                <div class="d-flex gap-2 mb-3 border-top pt-3">
                    <button wire:click="like({{ $post->id }})" class="btn btn-sm {{ $post->user_reaction === 'like' ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="bi bi-hand-thumbs-up"></i> {{ $post->likes_count }} Like{{ $post->likes_count !== 1 ? 's' : '' }}
                    </button>
                    <button wire:click="dislike({{ $post->id }})" class="btn btn-sm {{ $post->user_reaction === 'dislike' ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="bi bi-hand-thumbs-down"></i> {{ $post->dislikes_count }} Dislike{{ $post->dislikes_count !== 1 ? 's' : '' }}
                    </button>
                    <button wire:click="share({{ $post->id }})" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-share"></i> Share ({{ $post->share_count }})
                    </button>
                    <button wire:click="openReportModal({{ $post->id }})" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-flag"></i> Report
                    </button>
                </div>

                <!-- Comments Section -->
                @if($post->comments->where('parent_id', null)->count() > 0)
                    <div class="border-top pt-3">
                        <h6 class="mb-3">Comments ({{ $post->comments->where('parent_id', null)->count() }})</h6>
                        @foreach ($post->comments->where('parent_id', null) as $comment)
                            <div class="mb-4">
                                <!-- Main Comment -->
                                <div class="d-flex mb-2">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="bg-light p-3 rounded">
                                            <strong class="d-block">{{ $comment->user->name }}</strong>
                                            <span>{{ $comment->content }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 mt-2">
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                            <button wire:click="likeComment({{ $comment->id }})" class="btn btn-sm {{ $comment->user_liked ? 'btn-primary' : 'btn-outline-primary' }} py-0 px-2">
                                                <i class="bi bi-heart"></i> {{ $comment->likes_count }}
                                            </button>
                                            <button onclick="toggleReply({{ $comment->id }})" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                                <i class="bi bi-reply"></i> Reply
                                            </button>
                                        </div>
                                        
                                        <!-- Reply Form -->
                                        <div id="reply-form-{{ $comment->id }}" class="mt-2" style="display: none;">
                                            <form wire:submit.prevent="replyToComment({{ $comment->id }})">
                                                <div class="input-group input-group-sm">
                                                    <input wire:model="replyTo.{{ $comment->id }}" placeholder="Write a reply..." class="form-control">
                                                    <button type="submit" class="btn btn-outline-primary">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Replies -->
                                @if($comment->replies->count() > 0)
                                    <div class="ms-5">
                                        @foreach($comment->replies as $reply)
                                            <div class="d-flex mb-2">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="bg-light p-2 rounded">
                                                        <strong class="d-block" style="font-size: 0.9em;">{{ $reply->user->name }}</strong>
                                                        <span style="font-size: 0.9em;">{{ $reply->content }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <small class="text-muted" style="font-size: 0.8em;">{{ $reply->created_at->diffForHumans() }}</small>
                                                        <button wire:click="likeComment({{ $reply->id }})" class="btn btn-sm {{ $reply->user_liked ? 'btn-primary' : 'btn-outline-primary' }} py-0 px-1" style="font-size: 0.8em;">
                                                            <i class="bi bi-heart"></i> {{ $reply->likes_count }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Add Comment Form -->
                <form wire:submit.prevent="comment({{ $post->id }})" class="mt-3">
                    <div class="input-group">
                        <input wire:model="newComment.{{ $post->id }}" placeholder="Write a comment..." class="form-control">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-chat-dots display-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No posts yet</h5>
            <p class="text-muted">Be the first to share something with the community!</p>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>

    <!-- Report Modal -->
    @if($showReportModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Report Post</h5>
                        <button type="button" class="btn-close" wire:click="closeReportModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="submitReport">
                            <div class="mb-3">
                                <label class="form-label">Reason for reporting:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="reportReason" value="spam" id="spam">
                                    <label class="form-check-label" for="spam">Spam</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="reportReason" value="harassment" id="harassment">
                                    <label class="form-check-label" for="harassment">Harassment or bullying</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="reportReason" value="inappropriate" id="inappropriate">
                                    <label class="form-check-label" for="inappropriate">Inappropriate content</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="reportReason" value="misinformation" id="misinformation">
                                    <label class="form-check-label" for="misinformation">False information</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="reportReason" value="violence" id="violence">
                                    <label class="form-check-label" for="violence">Violence or dangerous content</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="reportReason" value="other" id="other">
                                    <label class="form-check-label" for="other">Other</label>
                                </div>
                                @error('reportReason') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                            
                            @if($reportReason === 'other')
                                <div class="mb-3">
                                    <label class="form-label">Please specify:</label>
                                    <textarea wire:model="customReason" class="form-control" rows="3" placeholder="Describe the issue..."></textarea>
                                    @error('customReason') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeReportModal">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="submitReport">Submit Report</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Friend Invite Modal -->
    @if($showInviteModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Friend Invitation</h5>
                        <button type="button" class="btn-close" wire:click="closeInviteModal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="bi bi-person-plus display-4 text-primary mb-3"></i>
                        <p>Send a friend invitation to this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeInviteModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="sendFriendInvite">
                            <i class="bi bi-send"></i> Send Invitation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleReply(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm.style.display === 'none') {
        replyForm.style.display = 'block';
        replyForm.querySelector('input').focus();
    } else {
        replyForm.style.display = 'none';
    }
}
</script>