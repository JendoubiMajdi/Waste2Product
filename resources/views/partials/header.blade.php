<header id="header" class="header fixed-top">

  @unless(request()->is('login') || request()->is('register'))
    <div class="topbar d-flex align-items-center dark-background">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@example.com">support@waste2product.com</a></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"><span>+216 98 765 432</span></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">
          <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
  </div><!-- End Top Bar -->
    @endunless

    <div class="branding d-flex align-items-cente" style="background-color: rgba(0, 0, 0, 1); backdrop-filter: blur(10px);">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="/" class="logo d-flex align-items-center">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="assets/img/logo.webp" alt=""> -->
          <h1 class="sitename" style="color: #ffffffff;">Waste2Product</h1>
        </a>

    @unless(request()->is('login') || request()->is('register'))
  <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Home</a></li>
            @guest
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <!-- @endguest -->
            <!-- <li><a href="#collectionpoints">Collection Points</a></li> -->
            @guest
            <li><a href="#team">Team</a></li>
            @endguest
            

            <!-- Megamenu 2 -->
            <li class="megamenu-2"><a href="#"><span>Features</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>

              <!-- Mobile Megamenu -->
              <ul class="mobile-megamenu">

                <li class="dropdown"><a href="#"><span>Collection Points</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('collection_points.index') }}">Available Collection Points</a></li>
                    <li><a href="{{ route('collection_points.map') }}">Nearest Collection Point</a></li>
                    <li><a href="{{ route('collection_points.dashboard') }}">Collection Points Dashboard</a></li>
                    <li><a href="{{ route('collection_points.dashboard') }}">Collection Points Statistics</a></li>
                  </ul>
                </li>

                <li class="dropdown"><a href="#"><span>Products</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('products.index') }}">Available products</a></li>
                    <li><a href="{{ route('products.create') }}">Add reusable products</a></li>
                    @auth
                    <li><a href="{{ route('orders.create') }}">Order Products</a></li>
                    <li><a href="{{ route('orders.my-orders') }}">My Orders</a></li>
                    @endauth
                  </ul>
                </li>

                <li class="dropdown"><a href="#"><span>Waste Management</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('wastes.index') }}">All Wastes</a></li>
                    <li><a href="{{ route('wastes.create') }}">Deposit Waste</a></li>
                  </ul>
                </li>

                @auth
                @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                <li class="dropdown"><a href="#"><span>Deliveries</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('orders.index') }}">All Orders</a></li>
                    <li><a href="{{ route('orders.index') }}?status=pending">Pending Orders</a></li>
                    <li><a href="{{ route('orders.index') }}?status=in_delivery">In Delivery</a></li>
                    <li><a href="{{ route('orders.index') }}?status=delivered">Completed</a></li>
                  </ul>
                </li>
                @endif
                @endauth

                <li class="dropdown"><a href="#"><span>Challenges</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('challenges.index') }}">View Challenges</a></li>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('challenges.create') }}">Create Challenge</a></li>
                    @endif
                    @endauth
                  </ul>
                </li>

                <li class="dropdown"><a href="#"><span>Events</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('events.index') }}">View Events</a></li>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('events.create') }}">Create Event</a></li>
                    @endif
                    @endauth
                  </ul>
                </li>

                <li class="dropdown"><a href="#"><span>Forum</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('forum.index') }}">View Posts</a></li>
                    <li><a href="{{ route('forum.create') }}">Create Post</a></li>
                  </ul>
                </li>

                <li class="dropdown"><a href="#"><span>Donations</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('donations.index') }}">View Donations</a></li>
                    <li><a href="{{ route('donations.create') }}">Make Donation</a></li>
                    @auth
                    <li><a href="{{ route('donations.my-donations') }}">My Donations</a></li>
                    @endauth
                  </ul>
                </li>

              </ul><!-- End Mobile Megamenu -->

              <!-- Desktop Megamenu -->
              <div class="desktop-megamenu">

                <div class="tab-navigation">
                  <ul class="nav nav-tabs flex-column" id="2190-megamenu-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="2190-tab-1-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-1" type="button" role="tab" aria-controls="2190-tab-1" aria-selected="true">
                        <i class="bi bi-building-gear"></i>
                        <span>Collection Points</span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="2190-tab-2-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-2" type="button" role="tab" aria-controls="2190-tab-2" aria-selected="false">
                        <i class="bi bi-code-slash"></i>
                        <span>Products</span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="2190-tab-6-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-6" type="button" role="tab" aria-controls="2190-tab-6" aria-selected="false">
                        <i class="bi bi-trash"></i>
                        <span>Waste Management</span>
                      </button>
                    </li>
                    @auth
                    @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="2190-tab-7-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-7" type="button" role="tab" aria-controls="2190-tab-7" aria-selected="false">
                        <i class="bi bi-truck"></i>
                        <span>Deliveries</span>
                      </button>
                    </li>
                    @endif
                    @endauth
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="2190-tab-3-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-3" type="button" role="tab" aria-controls="2190-tab-3" aria-selected="false">
                        <i class="bi bi-palette"></i>
                        <span>Challenges</span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="2190-tab-4-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-4" type="button" role="tab" aria-controls="2190-tab-4" aria-selected="false">
                        <i class="bi bi-journal-text"></i>
                        <span>Events</span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="2190-tab-5-tab" data-bs-toggle="tab" data-bs-target="#2190-tab-5" type="button" role="tab" aria-controls="2190-tab-5" aria-selected="false">
                        <i class="bi bi-journal-text"></i>
                        <span>Forums</span>
                      </button>
                    </li>
                  </ul>
                </div>

                <div class="tab-content">

                  <!-- Collection Points tab -->
                  <div class="tab-pane fade show active" id="2190-tab-1" role="tabpanel" aria-labelledby="2190-tab-1-tab">
                    <div class="content-grid">
                      <div class="product-section">
                        <h4>General Actions</h4>
                        <div class="product-list">
                          <a href="{{ route('collection_points.index') }}" class="product-link">
                            <i class="bi bi-people"></i>
                            <div>
                              <span>Available Collection Points</span>
                              <small>See all the available collection points using our platform</small>
                            </div>
                          </a>
                          <a href="{{ route('collection_points.map') }}" class="product-link">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                              <span>Nearest Collection Point</span>
                              <small>Find the closest collection points based on your location</small>
                            </div>
                          </a>
                        </div>
                      </div>

                      @auth
                        @if(auth()->user()->role === 'collector' || auth()->user()->role === 'admin')
                      <div class="product-section">
                        <h4>Collectors Side</h4>
                        <div class="product-list">
                          <a href="{{ route('collection_points.dashboard') }}" class="product-link">
                            <i class="bi bi-file-earmark-text"></i>
                            <div>
                              <span>Collection Points Dashboard</span>
                              <small>Manage your registered collection points</small>
                            </div>
                          </a>
                          <a href="{{ route('collection_points.dashboard') }}" class="product-link">
                            <i class="bi bi-bar-chart"></i>
                            <div>
                              <span>Collection Points Statistics</span>
                              <small>Keep on track your Collection Points</small>
                            </div>
                          </a>
                        </div>
                      </div>
                        @endif
                      @endauth
                    </div>

                    @auth
                      @if(auth()->user()->role !== 'collector')
                    <div class="featured-banner">
                      <div class="banner-content">
                        <img src="/assets/img/misc/misc-7.webp" alt="Enterprise Solutions" class="banner-image">
                        <div class="banner-info">
                          <h5>Become a Collector?</h5>
                          <p>If you have a Collection Point and want to become a Collector just press the button below</p>
                          <a href="#" class="cta-btn">Get Started <i class="bi bi-arrow-right"></i></a>
                        </div>
                      </div>
                    </div>
                      @endif
                    @else
                    <div class="featured-banner">
                      <div class="banner-content">
                        <img src="/assets/img/misc/misc-7.webp" alt="Enterprise Solutions" class="banner-image">
                        <div class="banner-info">
                          <h5>Become a Collector?</h5>
                          <p>If you have a Collection Point and want to become a Collector just press the button below</p>
                          <a href="#" class="cta-btn">Get Started <i class="bi bi-arrow-right"></i></a>
                        </div>
                      </div>
                    </div>
                    @endauth
                  </div>

                  <!-- Products Tab -->
                  <div class="tab-pane fade" id="2190-tab-2" role="tabpanel" aria-labelledby="2190-tab-2-tab">
                    <div class="content-grid">
                      <div class="product-section">
                        <h4>Browse Products</h4>
                        <div class="product-list">
                          <a href="{{ route('products.index') }}" class="product-link">
                            <i class="bi bi-code-square"></i>
                            <div>
                              <span>Available products</span>
                              <small>A list of the available products</small>
                            </div>
                          </a>
                        </div>
                      </div>

                      @auth
                      <div class="product-section">
                        <h4>Order Products</h4>
                        <div class="product-list">
                          <a href="{{ route('orders.create') }}" class="product-link">
                            <i class="bi bi-cart-plus"></i>
                            <div>
                              <span>Order Products</span>
                              <small>Place an order for recycled or reusable products</small>
                            </div>
                          </a>
                          <a href="{{ route('orders.my-orders') }}" class="product-link">
                            <i class="bi bi-box-seam"></i>
                            <div>
                              <span>My Orders</span>
                              <small>Track your product orders and delivery status</small>
                            </div>
                          </a>
                        </div>
                      </div>

                      <div class="product-section">
                        <h4>Contribute</h4>
                        <div class="product-list">
                          <a href="{{ route('products.create') }}" class="product-link">
                            <i class="bi bi-git"></i>
                            <div>
                              <span>Add reusable products</span>
                              <small>Add your reusable products for other people to make them in use, again!</small>
                            </div>
                          </a>
                        </div>
                      </div>
                      @endauth
                    </div>

                    
                  </div>

                  <!-- Waste Management Tab -->
                  <div class="tab-pane fade" id="2190-tab-6" role="tabpanel" aria-labelledby="2190-tab-6-tab">
                    <div class="content-grid">
                      <div class="product-section">
                        <h4>Waste Tracking</h4>
                        <div class="product-list">
                          <a href="{{ route('wastes.index') }}" class="product-link">
                            <i class="bi bi-trash"></i>
                            <div>
                              <span>All Wastes</span>
                              <small>View all waste deposits and track recycling progress</small>
                            </div>
                          </a>
                          <a href="{{ route('wastes.create') }}" class="product-link">
                            <i class="bi bi-plus-circle"></i>
                            <div>
                              <span>Deposit Waste</span>
                              <small>Register new waste deposit at a collection point</small>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Transporter Deliveries Tab -->
                  @auth
                  @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                  <div class="tab-pane fade" id="2190-tab-7" role="tabpanel" aria-labelledby="2190-tab-7-tab">
                    <div class="content-grid">
                      <div class="product-section">
                        <h4>Delivery Management</h4>
                        <div class="product-list">
                          <a href="{{ route('orders.index') }}" class="product-link">
                            <i class="bi bi-box-seam"></i>
                            <div>
                              <span>All Orders</span>
                              <small>View all orders that need delivery</small>
                            </div>
                          </a>
                          <a href="{{ route('orders.index') }}?status=pending" class="product-link">
                            <i class="bi bi-clock-history"></i>
                            <div>
                              <span>Pending Orders</span>
                              <small>Orders waiting to be accepted for delivery</small>
                            </div>
                          </a>
                          <a href="{{ route('orders.index') }}?status=in_delivery" class="product-link">
                            <i class="bi bi-truck"></i>
                            <div>
                              <span>In Delivery</span>
                              <small>Orders currently being delivered by you</small>
                            </div>
                          </a>
                          <a href="{{ route('orders.index') }}?status=delivered" class="product-link">
                            <i class="bi bi-check-circle"></i>
                            <div>
                              <span>Completed Deliveries</span>
                              <small>View your delivery history</small>
                            </div>
                          </a>
                        </div>
                      </div>

                      <div class="featured-banner">
                        <div class="banner-content">
                          <img src="/assets/img/misc/misc-7.webp" alt="Delivery Info" class="banner-image">
                          <div class="banner-info">
                            <h5>Delivery Dashboard</h5>
                            <p>Manage your deliveries efficiently and track your delivery statistics</p>
                            <a href="{{ route('orders.index') }}" class="cta-btn">View Dashboard <i class="bi bi-arrow-right"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
                  @endauth

                  <!-- Challenges Tab -->
                  <div class="tab-pane fade" id="2190-tab-3" role="tabpanel" aria-labelledby="2190-tab-3-tab">
                    <div class="content-grid">
                      <div class="product-section">
                        <h4>Challenges</h4>
                        <div class="product-list">
                          <a href="{{ route('challenges.index') }}" class="product-link">
                            <i class="bi bi-trophy"></i>
                            <div>
                              <span>Daily Challenge</span>
                              <small>Browse and participate in environmental challenges</small>
                            </div>
                          </a>
                          @if(Auth::check() && Auth::user()->isAdmin())
                          <a href="{{ route('challenges.create') }}" class="product-link">
                            <i class="bi bi-plus-circle"></i>
                            <div>
                              <span>Create Challenge</span>
                              <small>Start a new community challenge</small>
                            </div>
                          </a>
                          @endif
                        </div>
                      </div>

                      <div class="product-section">
                        <h4>Participation</h4>
                        <div class="product-list">
                        @if(Auth::check() && Auth::user()->isAdmin())
                          <a href="{{ route('challenges.index') }}" class="product-link">
                            <i class="bi bi-award"></i>
                            <div>
                              <span>Browse Challenges</span>
                              <small>View all available challenges</small>
                            </div>
                          </a>
                          @endif
                          <a href="{{ route('progress') }}" class="product-link">
                            <i class="bi bi-person-badge"></i>
                            <div>
                              <span>My Progress</span>
                              <small>Track your challenge submissions</small>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Events Tab -->
                  <div class="tab-pane fade" id="2190-tab-4" role="tabpanel" aria-labelledby="2190-tab-4-tab">
                    <div class="resources-layout">
                      <div class="resource-categories">
                        <div class="resource-category">
                          <i class="bi bi-calendar-event"></i>
                          <h5>All Events</h5>
                          <p>Discover upcoming environmental events and community gatherings.</p>
                          <a href="{{ route('events.index') }}" class="resource-link">Browse Events <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="resource-category">
                          <i class="bi bi-calendar-check"></i>
                          <h5>My Events</h5>
                          <p>View and manage events you've registered for.</p>
                          <a href="{{ route('events.my-events') }}" class="resource-link">My Registrations <i class="bi bi-arrow-right"></i></a>
                        </div>
                        @if(Auth::check() && Auth::user()->isAdmin())
                        <div class="resource-category">
                          <i class="bi bi-calendar-plus"></i>
                          <h5>Create Event</h5>
                          <p>Organize a new community event or environmental initiative.</p>
                          <a href="{{ route('events.create') }}" class="resource-link">Create Event <i class="bi bi-arrow-right"></i></a>
                        </div>
                        @endif
                        
                      </div>
                    </div>
                  </div>

                  <!-- Forums Tab -->
                  <div class="tab-pane fade" id="2190-tab-5" role="tabpanel" aria-labelledby="2190-tab-5-tab">
                    <div class="resources-layout">
                      <div class="resource-categories">
                        <div class="resource-category">
                          <i class="bi bi-people-fill"></i>
                          <h5>Social Feed</h5>
                          <p>Share posts and connect with friends in the community.</p>
                          <a href="{{ route('posts.index') }}" class="resource-link">View Feed <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="resource-category">
                          <i class="bi bi-chat-dots"></i>
                          <h5>Discussion Feed</h5>
                          <p>Browse and participate in community discussions about sustainability.</p>
                          <a href="{{ route('forum.index') }}" class="resource-link">View Posts <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="resource-category">
                          <i class="bi bi-pencil-square"></i>
                          <h5>Create Post</h5>
                          <p>Start a new discussion or share your environmental ideas.</p>
                          <a href="{{ route('forum.create') }}" class="resource-link">New Post <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="resource-category">
                          <i class="bi bi-heart"></i>
                          <h5>My Activity</h5>
                          <p>View your posts, comments, and liked discussions.</p>
                          <a href="{{ route('forum.index') }}" class="resource-link">My Posts <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="resource-category">
                          <i class="bi bi-gift"></i>
                          <h5>Donations</h5>
                          <p>Browse donation opportunities and contribute to the community.</p>
                          <a href="{{ route('donations.index') }}" class="resource-link">View Donations <i class="bi bi-arrow-right"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

              </div><!-- End Desktop Megamenu -->

            </li><!-- End Megamenu 2 -->

            <li><a href="#contact">Contact</a></li>

          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
  </nav>
    @endunless

        <!-- Conditional CTA: show Register on login page, Login on register page, Login on other pages -->
        <div class="d-none d-md-block ms-3">
          @auth
            <div class="d-flex align-items-center gap-3">
            <!-- Social Icons (Friends, Messages, Notifications) -->
            <div class="d-flex align-items-center gap-2">
              <!-- Friends -->
              <a href="{{ route('friends.index') }}" class="social-icon-btn position-relative" title="Friends">
                <i class="bi bi-people-fill"></i>
                @if(Auth::user()->pendingFriendRequests()->count() > 0)
                <span class="badge-notification">{{ Auth::user()->pendingFriendRequests()->count() }}</span>
                @endif
              </a>

              <!-- Messages -->
              <a href="{{ route('messages.index') }}" class="social-icon-btn position-relative" title="Messages">
                <i class="bi bi-chat-dots-fill"></i>
                @if(Auth::user()->unreadMessagesCount() > 0)
                <span class="badge-notification">{{ Auth::user()->unreadMessagesCount() }}</span>
                @endif
              </a>

              <!-- Notifications -->
              <div class="dropdown">
                <button class="social-icon-btn position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                  <i class="bi bi-bell-fill"></i>
                  @if(Auth::user()->unreadNotificationsCount() > 0)
                  <span class="badge-notification">{{ Auth::user()->unreadNotificationsCount() }}</span>
                  @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropdown" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                  <li class="dropdown-header d-flex justify-content-between align-items-center">
                    <span>Notifications</span>
                    @if(Auth::user()->unreadNotificationsCount() > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-link btn-sm p-0" style="font-size: 12px;">Mark all read</button>
                    </form>
                    @endif
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  @php
                    $recentNotifications = Auth::user()->notifications()->take(5)->get();
                  @endphp
                  @forelse($recentNotifications as $notification)
                    @php
                      $data = json_decode($notification->data, true);
                    @endphp
                    <li>
                      <a class="dropdown-item {{ $notification->read_at ? '' : 'unread-notification' }}" href="#" 
                         onclick="markNotificationRead({{ $notification->id }}); return false;">
                        <div class="d-flex align-items-start gap-2">
                          <i class="bi 
                            @if($notification->type == 'friend_request') bi-person-plus-fill text-primary
                            @elseif($notification->type == 'friend_accepted') bi-check-circle-fill text-success
                            @elseif($notification->type == 'message') bi-chat-fill text-info
                            @elseif($notification->type == 'ban') bi-x-circle-fill text-danger
                            @else bi-bell-fill text-secondary
                            @endif"></i>
                          <div class="flex-grow-1">
                            <small class="d-block text-muted" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                            <p class="mb-0" style="font-size: 13px;">{{ $data['message'] ?? 'New notification' }}</p>
                          </div>
                        </div>
                      </a>
                    </li>
                  @empty
                    <li class="text-center py-3 text-muted">
                      <i class="bi bi-bell-slash"></i>
                      <p class="mb-0">No notifications</p>
                    </li>
                  @endforelse
                  @if($recentNotifications->count() > 0)
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-center small" href="{{ route('notifications.index') }}">View all notifications</a></li>
                  @endif
                </ul>
              </div>
            </div><!-- End Social Icons -->

            <style>
              .social-icon-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #374151;
                font-size: 18px;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
              }
              
              .social-icon-btn:hover {
                background: white;
                color: #00927E;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
                transform: translateY(-2px);
              }
              
              .badge-notification {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ef4444;
                color: white;
                border-radius: 10px;
                padding: 2px 6px;
                font-size: 10px;
                font-weight: 600;
                min-width: 18px;
                text-align: center;
                border: 2px solid #000;
              }

              .notifications-dropdown .dropdown-item {
                padding: 12px 16px;
                white-space: normal;
              }

              .notifications-dropdown .unread-notification {
                background-color: rgba(0, 146, 126, 0.05);
                border-left: 3px solid #00927E;
              }

              .notifications-dropdown .dropdown-item:hover {
                background-color: rgba(0, 146, 126, 0.1);
              }
              
              .user-dropdown-btn {
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-radius: 50px;
                padding: 8px 16px;
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
              }
              
              .user-dropdown-btn:hover {
                background: white;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
                transform: translateY(-1px);
              }
              
              .user-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #00927E 0%, #00a88f 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 15px;
                text-transform: uppercase;
              }
              
              .user-info {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
              }
              
              .user-name {
                font-size: 14px;
                font-weight: 600;
                color: #1a1a1a;
                line-height: 1.2;
              }
              
              .user-role {
                font-size: 11px;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
              }
              
              .user-dropdown-icon {
                font-size: 18px;
                color: #6b7280;
                transition: transform 0.3s ease;
              }
              
              .user-dropdown-btn[aria-expanded="true"] .user-dropdown-icon {
                transform: rotate(180deg);
              }
              
              .modern-dropdown-menu {
                border-radius: 16px;
                border: 1px solid rgba(0, 0, 0, 0.08);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                padding: 8px;
                min-width: 240px;
                margin-top: 8px;
              }
              
              .modern-dropdown-menu .dropdown-item {
                border-radius: 10px;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                gap: 12px;
                transition: all 0.2s ease;
                color: #374151;
                font-size: 14px;
              }
              
              .modern-dropdown-menu .dropdown-item:hover {
                background: rgba(0, 146, 126, 0.08);
                color: #00927E;
              }
              
              .modern-dropdown-menu .dropdown-item i {
                font-size: 18px;
                width: 20px;
                text-align: center;
              }
              
              .modern-dropdown-menu .dropdown-divider {
                margin: 8px 0;
                border-color: rgba(0, 0, 0, 0.06);
              }
              
              .modern-dropdown-menu .dropdown-item.text-danger:hover {
                background: rgba(239, 68, 68, 0.08);
                color: #ef4444;
              }
              
              .user-dropdown-header {
                padding: 12px 16px;
                border-bottom: 1px solid rgba(0, 0, 0, 0.06);
                margin-bottom: 8px;
              }
              
              .user-dropdown-header .user-email {
                font-size: 13px;
                color: #6b7280;
                margin-top: 2px;
              }
            </style>
            
            <div class="dropdown">
              <button class="user-dropdown-btn" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">
                  {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="user-info">
                  <span class="user-name">{{ Auth::user()->name }}</span>
                  <span class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</span>
                </div>
                <i class="bi bi-chevron-down user-dropdown-icon"></i>
              </button>
              
              <ul class="dropdown-menu dropdown-menu-end modern-dropdown-menu" aria-labelledby="userDropdown">
                <li class="user-dropdown-header">
                  <div class="user-name">{{ Auth::user()->name }}</div>
                  <div class="user-email">{{ Auth::user()->email }}</div>
                </li>
                
                <li><a class="dropdown-item" href="{{ route('home') }}">
                  <i class="bi bi-house-door"></i>
                  Dashboard
                </a></li>
                
                <li><a class="dropdown-item" href="{{ route('progress') }}">
                  <i class="bi bi-trophy"></i>
                  My Progress
                </a></li>
                
                <li><a class="dropdown-item" href="{{ route('profile.show', Auth::user()->id) }}">
                  <i class="bi bi-person"></i>
                  Profile
                </a></li>
                
                <li><a class="dropdown-item" href="#">
                  <i class="bi bi-gear"></i>
                  Settings
                </a></li>
                
                @if(Auth::user()->role === 'admin')
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                  <i class="bi bi-speedometer2"></i>
                  Switch to Backoffice
                </a></li>
                @endif
                
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger w-100 text-start">
                      <i class="bi bi-box-arrow-right"></i>
                      Sign Out
                    </button>
                  </form>
                </li>
              </ul>
            </div><!-- End User Dropdown -->
            </div><!-- End Auth Wrapper -->
          @else
            @if(request()->is('login'))
              <a href="{{ route('register') }}" class="cta-btn">Register <i class="bi bi-arrow-right"></i></a>
            @elseif(request()->is('register'))
              <a href="{{ route('login') }}" class="cta-btn">Login <i class="bi bi-arrow-right"></i></a>
            @else
              <a href="{{ route('login') }}" class="cta-btn">Login <i class="bi bi-arrow-right"></i></a>
            @endif
          @endauth
        </div>

      </div>

    </div>

  </header>

  <!-- Notification marking script -->
  @auth
  <script>
    function markNotificationRead(notificationId) {
      fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Reload to update counts
          location.reload();
        }
      })
      .catch(error => console.error('Error:', error));
    }
  </script>
  @endauth