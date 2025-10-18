@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">My Profile</h1>
    <p style="color: #6b7280; font-size: 14px;">Manage your account settings</p>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="admin-card" style="text-align: center;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--admin-primary) 0%, #008a74 100%); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: 700;">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 4px;">{{ Auth::user()->name }}</h3>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 16px;">{{ Auth::user()->email }}</p>
            <span class="badge bg-danger">Admin</span>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="admin-card">
            <h3 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin-bottom: 24px;">Profile Information</h3>
            
            <form>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="{{ Auth::user()->email }}">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" placeholder="Enter phone number">
                </div>
                
                <hr style="margin: 32px 0;">
                
                <h3 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin-bottom: 24px;">Change Password</h3>
                
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-control">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary" style="background: var(--admin-primary); border-color: var(--admin-primary);">
                    Update Profile
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
