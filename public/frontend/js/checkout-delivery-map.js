/**
 * Checkout shipping: Leaflet map for delivery location.
 * If selected address has lat/lng, show it on map; else let user pick on map. No API key.
 */
(function () {
    'use strict';

    var defaultCenter = [20.5937, 78.9629];
    var defaultZoom = 5;
    var map = null;
    var marker = null;

    function getSelectedAddressEl() {
        var radio = document.querySelector('input[name="address_id"]:checked');
        if (!radio) return null;
        var listGroup = document.getElementById('address-list');
        if (!listGroup) return null;
        return listGroup.querySelector('label[data-address-id="' + radio.value + '"]') || null;
    }

    function getLatLngFromAddressEl(el) {
        if (!el) return null;
        var lat = parseFloat(el.getAttribute('data-lat'));
        var lng = parseFloat(el.getAttribute('data-lng'));
        if (isNaN(lat) || isNaN(lng)) return null;
        return [lat, lng];
    }

    function updateInputs(lat, lng) {
        var latIn = document.getElementById('shipping_latitude');
        var lngIn = document.getElementById('shipping_longitude');
        if (latIn) latIn.value = lat === undefined || lat === null ? '' : lat;
        if (lngIn) lngIn.value = lng === undefined || lng === null ? '' : lng;
    }

    function setMarker(latLng) {
        if (!map) return;
        var lat = latLng.lat != null ? latLng.lat : latLng[0];
        var lng = latLng.lng != null ? latLng.lng : latLng[1];
        if (!marker) {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function (e) {
                var pos = e.target.getLatLng();
                updateInputs(pos.lat, pos.lng);
            });
        } else {
            marker.setLatLng([lat, lng]);
        }
        map.panTo([lat, lng]);
        updateInputs(lat, lng);
    }

    function removeMarker() {
        if (marker && map) {
            map.removeLayer(marker);
            marker = null;
        }
        updateInputs('', '');
    }

    function syncMapToSelectedAddress() {
        var mapEl = document.getElementById('checkout-delivery-map');
        var mapSection = document.getElementById('map-section');
        if (!mapEl || !mapSection || mapSection.classList.contains('d-none')) return;

        var addressEl = getSelectedAddressEl();
        var latLng = addressEl ? getLatLngFromAddressEl(addressEl) : null;
        var savedLat = parseFloat(document.getElementById('shipping_latitude').value);
        var savedLng = parseFloat(document.getElementById('shipping_longitude').value);
        var hasSaved = !isNaN(savedLat) && !isNaN(savedLng);

        if (latLng) {
            if (!map) initMap(latLng[0], latLng[1], 15);
            else map.setView(latLng, 15);
            setMarker(latLng);
        } else if (hasSaved) {
            if (!map) initMap(savedLat, savedLng, 15);
            else map.setView([savedLat, savedLng], 15);
            setMarker([savedLat, savedLng]);
        } else {
            if (!map) initMap(defaultCenter[0], defaultCenter[1], defaultZoom);
            else map.setView(defaultCenter, defaultZoom);
            removeMarker();
        }
    }

    function initMap(centerLat, centerLng, zoom) {
        var mapEl = document.getElementById('checkout-delivery-map');
        if (!mapEl || typeof L === 'undefined') return;
        if (L.Icon && L.Icon.Default && !L.Icon.Default.imagePath) {
            L.Icon.Default.imagePath = 'https://unpkg.com/leaflet@1.9.4/dist/images/';
        }
        var lat = centerLat != null ? centerLat : defaultCenter[0];
        var lng = centerLng != null ? centerLng : defaultCenter[1];
        var z = zoom != null ? zoom : defaultZoom;
        map = L.map(mapEl, { scrollWheelZoom: true }).setView([lat, lng], z);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);
        map.on('click', function (e) {
            setMarker(e.latlng);
        });
    }

    function onDeliveryTypeChange() {
        var homeDelivery = document.getElementById('delivery_home');
        var mapSection = document.getElementById('map-section');
        if (mapSection) {
            if (homeDelivery && homeDelivery.checked) {
                mapSection.classList.remove('d-none');
                syncMapToSelectedAddress();
            } else {
                mapSection.classList.add('d-none');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var mapSection = document.getElementById('map-section');
        if (!mapSection || mapSection.classList.contains('d-none')) return;

        syncMapToSelectedAddress();

        document.querySelectorAll('input[name="address_id"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                syncMapToSelectedAddress();
            });
        });

        document.querySelectorAll('input[name="delivery_type"]').forEach(function (radio) {
            radio.addEventListener('change', onDeliveryTypeChange);
        });
    });
})();
