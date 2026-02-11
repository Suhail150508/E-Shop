/**
 * Leaflet map for address create/edit. No API key (OpenStreetMap / Nominatim).
 * Click map to select location; address fields are auto-filled via reverse geocoding.
 */
(function () {
    'use strict';

    var defaultCenter = [20.5937, 78.9629];
    var defaultZoom = 5;

    function reverseGeocode(lat, lng, callback) {
        var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&zoom=18&addressdetails=1';
        var opts = { headers: { 'Accept': 'application/json', 'Accept-Language': 'en' } };
        fetch(url, opts)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (typeof callback === 'function') callback(data);
            })
            .catch(function () {
                if (typeof callback === 'function') callback(null);
            });
    }

    function fillAddressFields(data, opts, preferredLine1) {
        var line1El = opts.line1Id ? document.getElementById(opts.line1Id) : null;
        var postalCodeEl = opts.postalCodeId ? document.getElementById(opts.postalCodeId) : null;
        var cityEl = opts.cityId ? document.getElementById(opts.cityId) : null;
        var stateEl = opts.stateId ? document.getElementById(opts.stateId) : null;
        var countryEl = opts.countryId ? document.getElementById(opts.countryId) : null;

        if (preferredLine1 && line1El) {
            line1El.value = preferredLine1;
        }
        if (!data) return;
        var addr = data.address || {};
        var line1 = preferredLine1;
        if (!preferredLine1 && addr) {
            var parts = [];
            if (addr.road) parts.push(addr.road);
            if (addr.house_number) parts.push(addr.house_number);
            if (addr.suburb && parts.indexOf(addr.suburb) === -1) parts.push(addr.suburb);
            if (addr.village && parts.indexOf(addr.village) === -1) parts.push(addr.village);
            line1 = parts.length ? parts.join(', ') : (data.display_name || '');
            if (line1El && line1) line1El.value = line1;
        }
        if (postalCodeEl && addr.postcode) postalCodeEl.value = addr.postcode;
        if (cityEl && (addr.city || addr.town || addr.village)) cityEl.value = addr.city || addr.town || addr.village;
        if (stateEl && addr.state) stateEl.value = addr.state;
        if (countryEl && addr.country) countryEl.value = addr.country;
    }

    window.initAddressMap = function (opts) {
        opts = opts || {};
        var mapId = opts.mapId || 'address-map';
        var mapEl = document.getElementById(mapId);
        var latInput = document.getElementById(opts.latId || 'latitude');
        var lngInput = document.getElementById(opts.lngId || 'longitude');
        var searchEl = document.getElementById(opts.searchId || 'address-map-search');

        if (!mapEl || !latInput || !lngInput) return null;
        if (typeof L === 'undefined') return null;

        if (L.Icon && L.Icon.Default && !L.Icon.Default.imagePath) {
            L.Icon.Default.imagePath = 'https://unpkg.com/leaflet@1.9.4/dist/images/';
        }

        var initialLat = parseFloat(latInput.value) || opts.initialLat;
        var initialLng = parseFloat(lngInput.value) || opts.initialLng;
        var hasInitial = !isNaN(initialLat) && !isNaN(initialLng) && initialLat >= -90 && initialLat <= 90 && initialLng >= -180 && initialLng <= 180;

        var center = hasInitial ? [initialLat, initialLng] : defaultCenter;
        var zoom = hasInitial ? 15 : defaultZoom;

        mapEl.style.minHeight = '280px';
        var map = L.map(mapEl, { scrollWheelZoom: true }).setView(center, zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        var marker = null;

        function updateInputs(lat, lng) {
            latInput.value = lat === undefined || lat === null ? '' : lat;
            lngInput.value = lng === undefined || lng === null ? '' : lng;
        }

        function setMarkerAndGeocode(lat, lng, searchDisplayName) {
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function (e) {
                    var p = e.target.getLatLng();
                    updateInputs(p.lat, p.lng);
                    reverseGeocode(p.lat, p.lng, function (data) { fillAddressFields(data, opts, null); });
                });
            } else {
                marker.setLatLng([lat, lng]);
            }
            map.panTo([lat, lng]);
            updateInputs(lat, lng);
            if (searchDisplayName) {
                fillAddressFields(null, opts, searchDisplayName);
                reverseGeocode(lat, lng, function (data) {
                    fillAddressFields(data || null, opts, searchDisplayName);
                });
            } else {
                reverseGeocode(lat, lng, function (data) { fillAddressFields(data, opts, null); });
            }
        }

        if (hasInitial) {
            marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
            marker.on('dragend', function (e) {
                var p = e.target.getLatLng();
                updateInputs(p.lat, p.lng);
                reverseGeocode(p.lat, p.lng, function (data) { fillAddressFields(data, opts); });
            });
        }

        map.on('click', function (e) {
            setMarkerAndGeocode(e.latlng.lat, e.latlng.lng, null);
        });

        if (searchEl) {
            searchEl.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                var query = searchEl.value.trim();
                if (!query) return;
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=1&addressdetails=1', {
                    headers: { 'Accept': 'application/json', 'Accept-Language': 'en' }
                }).then(function (r) { return r.json(); }).then(function (data) {
                    if (data && data[0]) {
                        var item = data[0];
                        var lat = parseFloat(item.lat);
                        var lon = parseFloat(item.lon);
                        if (!isNaN(lat) && !isNaN(lon)) {
                            var displayName = (item.display_name || query).trim();
                            setMarkerAndGeocode(lat, lon, displayName);
                            var postalCodeEl = opts.postalCodeId ? document.getElementById(opts.postalCodeId) : null;
                            if (postalCodeEl && item.address && item.address.postcode) postalCodeEl.value = item.address.postcode;
                        }
                    }
                }).catch(function () {});
            });
        }

        setTimeout(function () {
            if (map && map.invalidateSize) map.invalidateSize();
        }, 100);
        return map;
    };
})();
