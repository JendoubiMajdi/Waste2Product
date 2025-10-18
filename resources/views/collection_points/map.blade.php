@extends('layouts.app')

@section('title', 'Nearest Collection Points')

@section('content')
<!-- Page Title -->
<div class="page-title dark-background" style="background: linear-gradient(135deg, #00927E 0%, #006928 100%); padding: 60px 0 40px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <div class="container">
        <h1 class="mb-2" style="color: #FFFFFF !important; font-weight: 700; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); mix-blend-mode: normal; opacity: 1;">
            <i class="bi bi-geo-alt-fill me-3" style="color: #FFFFFF !important;"></i>Find Nearest Collection Point
        </h1>
        <p class="mb-0" style="color: #FFFFFF !important; font-size: 1.1rem; text-shadow: 1px 1px 6px rgba(0,0,0,0.4); mix-blend-mode: normal; opacity: 0.95;">
            Discover the closest waste collection point based on your real-time location
        </p>
    </div>
</div>

<div class="container-fluid px-0" style="position: relative; height: 600px; min-height: 600px;">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.9); z-index: 1000; display: flex; align-items: center; justify-content: center; flex-direction: column;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Getting your location...</p>
    </div>

    <!-- Info Card -->
    <div id="infoCard" class="position-absolute top-0 start-0" style="z-index: 999; max-width: 400px; display: none; margin-top: 1rem; margin-left: 4rem;">
        <div class="card shadow-lg border-0" style="overflow: hidden;">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #00927E 0%, #006928 100%); border: none; padding: 1rem 1.25rem;">
                <h5 class="mb-0" style="color: #FFFFFF !important; font-weight: 600;">
                    <i class="bi bi-geo-alt-fill me-2"></i>Nearest Collection Point
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('infoCard').style.display='none'"></button>
            </div>
            <div class="card-body" style="background: #ffffff;">
                <h6 class="card-title fw-bold" id="nearestName" style="color: #00927E;">-</h6>
                <p class="card-text mb-2">
                    <i class="bi bi-geo-alt me-2" style="color: #00927E;"></i>
                    <span id="nearestAddress">-</span>
                </p>
                <p class="card-text mb-2">
                    <i class="bi bi-clock me-2" style="color: #00927E;"></i>
                    <span id="nearestHours">-</span>
                </p>
                <p class="card-text mb-2">
                    <i class="bi bi-telephone me-2" style="color: #00927E;"></i>
                    <span id="nearestPhone">-</span>
                </p>
                <div class="alert mb-0 mt-3" style="background: rgba(0, 146, 126, 0.1); border-left: 4px solid #00927E; border-radius: 4px; color: #006928;">
                    <i class="bi bi-info-circle me-2" style="color: #00927E;"></i>
                    Distance: <strong style="color: #00927E;"><span id="nearestDistance">-</span> km</strong>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a id="directionsLink" href="#" target="_blank" class="btn" style="background: linear-gradient(135deg, #00927E 0%, #006928 100%); border: none; color: white; font-weight: 500; padding: 0.5rem 1rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,146,126,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="bi bi-compass me-2"></i>Get Directions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="position-absolute bottom-0 start-0 m-3" style="z-index: 999;">
        <div class="card shadow">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 30px; height: 30px; background: #28a745; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>
                    <span class="ms-2 small fw-bold">Nearest Point</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 24px; height: 24px; background: #007bff; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>
                    <span class="ms-2 small">Other Points</span>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #ff6b6b; border-radius: 50%; border: 2px solid white;"></div>
                    <span class="ms-2 small">Your Location</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div id="map" style="width: 100%; height: 100%;"></div>
</div>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="mb-3">How It Works</h2>
                <p class="text-muted">Finding your nearest collection point is easy and fast</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-geo-alt-fill text-primary fs-3"></i>
                        </div>
                        <h5 class="card-title">Share Location</h5>
                        <p class="card-text text-muted small">Allow your browser to access your current location</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-search text-success fs-3"></i>
                        </div>
                        <h5 class="card-title">We Calculate</h5>
                        <p class="card-text text-muted small">Our system finds all collection points near you</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-pin-map-fill text-warning fs-3"></i>
                        </div>
                        <h5 class="card-title">View Results</h5>
                        <p class="card-text text-muted small">See the nearest point highlighted on the map</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-compass text-info fs-3"></i>
                        </div>
                        <h5 class="card-title">Get Directions</h5>
                        <p class="card-text text-muted small">Navigate to the location using Google Maps</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row mt-5">
            <div class="col-md-4 text-center">
                <div class="mb-2">
                    <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                </div>
                <h3 class="fw-bold text-primary">{{ count($collectionPoints) }}</h3>
                <p class="text-muted mb-0">Active Collection Points</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-2">
                    <i class="bi bi-clock text-success" style="font-size: 2rem;"></i>
                </div>
                <h3 class="fw-bold text-success">24/7</h3>
                <p class="text-muted mb-0">Various Opening Hours</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-2">
                    <i class="bi bi-geo text-info" style="font-size: 2rem;"></i>
                </div>
                <h3 class="fw-bold text-info">GPS</h3>
                <p class="text-muted mb-0">Accurate Location Tracking</p>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Collection points data from Laravel
const collectionPoints = @json($collectionPoints);

console.log('Collection Points loaded:', collectionPoints.length, 'points');

let map;
let userMarker;
let nearestPoint = null;
let markers = [];

// Initialize the map
function initMap() {
    console.log('Initializing map...');
    // Default center (will be updated when we get user location)
    map = L.map('map').setView([33.5731, -7.5898], 12); // Default to Casablanca

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Add all collection points to the map
    collectionPoints.forEach(point => {
        addMarker(point, false);
    });

    // Get user's location
    getUserLocation();
}

// Add a marker to the map
function addMarker(point, isNearest) {
    const icon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            width: ${isNearest ? '30px' : '24px'}; 
            height: ${isNearest ? '30px' : '24px'}; 
            background: ${isNearest ? '#28a745' : '#007bff'}; 
            border-radius: 50%; 
            border: ${isNearest ? '3px' : '2px'} solid white; 
            box-shadow: 0 2px ${isNearest ? '8px' : '5px'} rgba(0,0,0,0.3);
            ${isNearest ? 'animation: pulse 2s infinite;' : ''}
        "></div>
        <style>
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        </style>`,
        iconSize: [isNearest ? 30 : 24, isNearest ? 30 : 24],
        iconAnchor: [isNearest ? 15 : 12, isNearest ? 15 : 12]
    });

    const marker = L.marker([point.latitude, point.longitude], { icon: icon })
        .addTo(map)
        .bindPopup(`
            <div style="min-width: 200px;">
                <h6 class="fw-bold mb-2">${point.name}</h6>
                <p class="mb-1 small"><i class="bi bi-geo-alt"></i> ${point.address}</p>
                <p class="mb-1 small"><i class="bi bi-clock"></i> ${point.working_hours || 'Not specified'}</p>
                ${point.contact_phone ? `<p class="mb-1 small"><i class="bi bi-telephone"></i> ${point.contact_phone}</p>` : ''}
                ${point.distance ? `<p class="mb-0 small"><strong>Distance: ${point.distance} km</strong></p>` : ''}
            </div>
        `);

    markers.push({ marker, point });

    // Open popup for nearest point
    if (isNearest) {
        marker.openPopup();
    }

    return marker;
}

// Get user's current location
function getUserLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        document.getElementById('loadingOverlay').style.display = 'none';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        position => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            // Add user location marker
            const userIcon = L.divIcon({
                className: 'user-marker',
                html: '<div style="width: 20px; height: 20px; background: #ff6b6b; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            userMarker = L.marker([userLat, userLng], { icon: userIcon })
                .addTo(map)
                .bindPopup('<strong>Your Location</strong>');

            // Center map on user location
            map.setView([userLat, userLng], 13);

            // Find nearest collection point
            findNearestPoint(userLat, userLng);
        },
        error => {
            console.error('Error getting location:', error);
            alert('Unable to get your location. Please enable location services.');
            document.getElementById('loadingOverlay').style.display = 'none';
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Find the nearest collection point
function findNearestPoint(userLat, userLng) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Security token missing. Please refresh the page.');
        document.getElementById('loadingOverlay').style.display = 'none';
        return;
    }
    
    console.log('Finding nearest point for location:', userLat, userLng);
    
    fetch('{{ route("collection_points.findNearest") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content
        },
        body: JSON.stringify({
            latitude: userLat,
            longitude: userLng
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.nearest) {
            nearestPoint = data.nearest;
            
            // Remove old markers
            markers.forEach(m => map.removeLayer(m.marker));
            markers = [];

            // Add markers with distance info
            data.all_points.forEach(point => {
                addMarker(point, point.id === nearestPoint.id);
            });

            // Update info card
            document.getElementById('nearestName').textContent = nearestPoint.name;
            document.getElementById('nearestAddress').textContent = nearestPoint.address;
            document.getElementById('nearestHours').textContent = nearestPoint.working_hours || 'Not specified';
            document.getElementById('nearestPhone').textContent = nearestPoint.contact_phone || 'Not available';
            document.getElementById('nearestDistance').textContent = nearestPoint.distance;
            
            // Update directions link
            const directionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${nearestPoint.latitude},${nearestPoint.longitude}`;
            document.getElementById('directionsLink').href = directionsUrl;
            
            // Show info card
            document.getElementById('infoCard').style.display = 'block';

            // Fit map to show user and nearest point
            const bounds = L.latLngBounds([
                [userLat, userLng],
                [nearestPoint.latitude, nearestPoint.longitude]
            ]);
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        // Hide loading overlay
        document.getElementById('loadingOverlay').style.display = 'none';
        console.log('Nearest point found:', nearestPoint.name, 'Distance:', nearestPoint.distance, 'km');
    })
    .catch(error => {
        console.error('Error finding nearest point:', error);
        alert('Error finding nearest collection point. Please try again.');
        document.getElementById('loadingOverlay').style.display = 'none';
    });
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush
