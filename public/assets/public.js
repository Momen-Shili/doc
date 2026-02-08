(function () {
  const form = document.getElementById('public-search-form');
  const geoToggle = document.getElementById('geo-toggle');
  const geoRadius = document.getElementById('geo-radius');
  const geoLat = document.getElementById('geo-lat');
  const geoLng = document.getElementById('geo-lng');
  const geoAround = document.getElementById('geo-around');
  const geoCity = document.getElementById('geo-city');
  const sortSelect = document.getElementById('sort-select');

  const GEO_KEY = 'geo_coords';
  const GEO_CITY_KEY = 'geo_city';
  const GEO_AUTO_KEY = 'geo_auto_applied';

  function setCoords(lat, lng) {
    if (geoLat) geoLat.value = lat.toFixed(6);
    if (geoLng) geoLng.value = lng.toFixed(6);
  }

  function loadCoords() {
    const raw = sessionStorage.getItem(GEO_KEY);
    if (!raw) return null;
    try {
      return JSON.parse(raw);
    } catch (_) {
      return null;
    }
  }

  function saveCoords(lat, lng) {
    sessionStorage.setItem(GEO_KEY, JSON.stringify({ lat, lng }));
  }

  function updateCityLabel() {
    if (!geoCity) return;
    const cached = sessionStorage.getItem(GEO_CITY_KEY);
    if (cached) {
      geoCity.textContent = cached;
    }
  }

  function fetchCity(lat, lng) {
    fetch(`?r=public-geocode-reverse&lat=${encodeURIComponent(lat)}&lng=${encodeURIComponent(lng)}`)
      .then((r) => r.json())
      .then((data) => {
        if (data && data.city) {
          sessionStorage.setItem(GEO_CITY_KEY, data.city);
          updateCityLabel();
        }
      })
      .catch(() => {});
  }

  function requestGeo(onDone) {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        saveCoords(lat, lng);
        setCoords(lat, lng);
        fetchCity(lat, lng);
        if (onDone) onDone(true);
      },
      () => {
        if (onDone) onDone(false);
      },
      { enableHighAccuracy: false, timeout: 8000, maximumAge: 300000 }
    );
  }

  function enableAround(enabled) {
    if (!geoAround) return;
    geoAround.value = enabled ? '1' : '0';
    if (geoRadius) {
      geoRadius.disabled = !enabled;
    }
  }

  const isDoctorsPage = window.location.search.includes('r=public-doctors');

  if (form && geoToggle) {
    geoToggle.addEventListener('click', function () {
      const enabled = geoAround && geoAround.value === '1';
      if (enabled) {
        enableAround(false);
        if (isDoctorsPage) form.submit();
        return;
      }
      const coords = loadCoords();
      if (coords) {
        setCoords(coords.lat, coords.lng);
        enableAround(true);
        if (isDoctorsPage) form.submit();
        return;
      }
      requestGeo(function (ok) {
        if (ok) {
          enableAround(true);
          if (isDoctorsPage) form.submit();
        }
      });
    });
  }

  if (sortSelect && form) {
    sortSelect.addEventListener('change', function () {
      form.submit();
    });
  }

  if (geoRadius && form) {
    geoRadius.addEventListener('change', function () {
      if (geoAround && geoAround.value === '1') {
        form.submit();
      }
    });
  }

  const coords = loadCoords();
  if (coords) {
    setCoords(coords.lat, coords.lng);
    updateCityLabel();
    if (geoAround && geoAround.value === '1') {
      if (geoRadius) geoRadius.disabled = false;
    }
  } else if (form && !sessionStorage.getItem(GEO_AUTO_KEY)) {
    requestGeo(function (ok) {
      if (!ok) return;
      sessionStorage.setItem(GEO_AUTO_KEY, '1');
      if (isDoctorsPage) {
        if (geoAround) geoAround.value = '1';
        if (geoRadius) geoRadius.disabled = false;
        if (sortSelect) sortSelect.value = 'distance';
        form.submit();
      }
    });
  }

  // Doctor booking slots
  const serviceSelect = document.getElementById('service_id');
  const dateInput = document.getElementById('slot_date_input');
  const slotsContainer = document.getElementById('slots');
  const slotDate = document.getElementById('slot_date');
  const slotTime = document.getElementById('slot_time');
  const slugInput = document.querySelector('input[name="slug"]');

  function clearSlots() {
    if (slotsContainer) slotsContainer.innerHTML = '';
    if (slotDate) slotDate.value = '';
    if (slotTime) slotTime.value = '';
  }

  function renderSlots(slots) {
    if (!slotsContainer) return;
    slotsContainer.innerHTML = '';
    if (!slots.length) {
      slotsContainer.innerHTML = '<span class="muted">Aucun creneau disponible.</span>';
      return;
    }
    slots.forEach((time) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'public-slot';
      btn.textContent = time;
      btn.addEventListener('click', () => {
        slotsContainer.querySelectorAll('.public-slot').forEach((el) => el.classList.remove('selected'));
        btn.classList.add('selected');
        if (slotDate) slotDate.value = dateInput.value;
        if (slotTime) slotTime.value = time;
      });
      slotsContainer.appendChild(btn);
    });
  }

  function loadSlots() {
    if (!serviceSelect || !dateInput || !slugInput) return;
    const serviceId = serviceSelect.value;
    const date = dateInput.value;
    if (!serviceId || !date) {
      clearSlots();
      return;
    }
    fetch(`?r=public-slots-json&slug=${encodeURIComponent(slugInput.value)}&service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`)
      .then((r) => r.json())
      .then((data) => {
        renderSlots(data.slots || []);
      })
      .catch(() => {
        renderSlots([]);
      });
  }

  if (serviceSelect) serviceSelect.addEventListener('change', loadSlots);
  if (dateInput) dateInput.addEventListener('change', loadSlots);
})();
