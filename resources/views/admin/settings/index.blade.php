@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Settings</h1>
    <p style="color: #6b7280; font-size: 14px;">Manage system settings and preferences</p>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="admin-card">
            <div class="list-group list-group-flush">
                <a href="#general" class="list-group-item list-group-item-action active">General Settings</a>
                <a href="#email" class="list-group-item list-group-item-action">Email Configuration</a>
                <a href="#payment" class="list-group-item list-group-item-action">Payment Settings</a>
                <a href="#security" class="list-group-item list-group-item-action">Security</a>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="admin-card">
            <h3 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin-bottom: 24px;">General Settings</h3>
            
            <form>
                <div class="mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" class="form-control" value="Waste2Product">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Site Description</label>
                    <textarea class="form-control" rows="3">Transform waste into valuable products</textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" class="form-control" value="contact@waste2product.com">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Timezone</label>
                    <select class="form-select">
                        <option>UTC</option>
                        <option selected>Africa/Tunis</option>
                        <option>Europe/Paris</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="background: var(--admin-primary); border-color: var(--admin-primary);">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
