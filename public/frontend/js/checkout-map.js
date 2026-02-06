function initMap() {
    const defaultLocation = { lat: 23.8103, lng: 90.4125 }; // Default to Dhaka or configurable
    
    let savedLat = parseFloat(document.getElementById('shipping_latitude').value);
    let savedLng = parseFloat(document.getElementById('shipping_longitude').value);
    
    let initialPos = defaultLocation;
    let hasSavedLocation = !isNaN(savedLat) && !isNaN(savedLng);
    
    if (hasSavedLocation) {
        initialPos = { lat: savedLat, lng: savedLng };
    }

    const map = new google.maps.Map(document.getElementById("google-map"), {
        zoom: 15,
        center: initialPos,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true
    });
    
    // Get title from global variable or default
    const markerTitle = window.deliveryLocationTitle || 'Delivery Location';

    const marker = new google.maps.Marker({
        position: initialPos,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: markerTitle
    });

    // Try HTML5 geolocation if no saved position
    if (!hasSavedLocation) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    map.setCenter(pos);
                    marker.setPosition(pos);
                    updateInputs(pos);
                },
                () => {
                    // Handle location error (optional)
                }
            );
        }
    }

    map.addListener("click", (e) => {
        placeMarkerAndPanTo(e.latLng, map);
    });
    
    marker.addListener("dragend", (e) => {
        updateInputs(e.latLng);
    });

    function placeMarkerAndPanTo(latLng, map) {
        marker.setPosition(latLng);
        map.panTo(latLng);
        updateInputs(latLng);
    }
    
    function updateInputs(latLng) {
        const lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
        const lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;
        
        document.getElementById('shipping_latitude').value = lat;
        document.getElementById('shipping_longitude').value = lng;
    }
}

// Toggle map visibility based on delivery type
document.addEventListener('DOMContentLoaded', function() {
    const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
    const mapSection = document.getElementById('map-section');
    
    if (deliveryRadios.length > 0) {
        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (mapSection) {
                    if (this.value === 'pickup') {
                        mapSection.classList.add('d-none');
                    } else {
                        mapSection.classList.remove('d-none');
                    }
                }
            });
        });
    }
});
