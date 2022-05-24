function addMarker(position = { lat: 47.85848098154215, lng: 35.10464944543838 }, draggable = true) {
  return new google.maps.Marker({
    position,
    draggable,
    animation: google.maps.Animation.DROP,
    map: map,
  });
}

// передаем маркер для editform или пусто для addform
function edit(position = null)
{
  const confAdress = document.querySelector('#confAdress');
  const latitide = document.querySelector('#latitude');
  const longitude = document.querySelector('#longitude');
  const select = document.querySelector('#countrySelect');
  select.addEventListener('change', selectChangeHandler);

  function selectChangeHandler(event) {
    // id select.value
    // КАКАЯТО ХРЕНЬ!!!!!
    const option = Array.from(select.options).find(item => item.value === select.value);
    const lat = parseFloat(option.dataset['lat']);
    const lng = parseFloat(option.dataset['lon']);
    const zoom = parseFloat(option.dataset['zoom']);
    map.setCenter({lat, lng});
    map.setZoom(zoom);
    // очищаем маркер, адресс и координаты
    if (marker) {
      clearMarker();
    }
  }

  let marker = null;
  if (position) {
    marker = addMarker(position, true);
    marker.addListener('dragend', markerHandler);
    map.setCenter(marker.getPosition());
    map.setZoom(15);
  }

  function clearMarker() {
    marker.setMap(null);
    marker = null;
    latitide.value = 0;
    longitude.value = 0;
  }

  function markerHandler(event){
    if (marker) {
      map.setCenter(marker.getPosition());
      latitide.value = marker.getPosition().lat();
      longitude.value = marker.getPosition().lng();
    }
  }
  
  function setMarkerDraggable(event){
    if (event.target.value) {
      // ставим маркер
      if (!marker) {
        marker = addMarker(map.center, true);
        marker.addListener('dragend', markerHandler);
      }
      latitide.value = map.center.lat();
      longitude.value = map.center.lng();
    } else {
      // очищаем маркер
      clearMarker();
    }
  }

  confAdress.addEventListener('change', setMarkerDraggable);
}

// передаем маркер для detail
function detail(position = {}) {
  if (position) {
    const marker = addMarker(position, false);
    map.setCenter(position);
    map.setZoom(15);
  }
}

function country() {
  const countryForm = document.querySelector('#countryForm');
  const lat = document.querySelector('#lat');
  const lon = document.querySelector('#lon');
  const zoom = document.querySelector('#zoom');
  countryForm.addEventListener('submit', submitHandler);

  // переключение между странами
  const countries = document.querySelectorAll('.country');
  countries.forEach(item => {
    item.addEventListener('click', clickHandler)
  });

  function clickHandler(event) {
    const lat = parseFloat(event.target.dataset['lat']);
    const lng = parseFloat(event.target.dataset['lon']);
    const zoom = parseFloat(event.target.dataset['zoom']);
    map.setCenter({lat, lng});
    map.setZoom(zoom);
  }

  function submitHandler(event) {
    event.preventDefault();
    lat.value = map.center.lat();
    lon.value = map.center.lng();
    zoom.value = map.zoom;
    countryForm.submit();
  }
}

let map;

function initMap() {
  map = new google.maps.Map(document.getElementById("googleMap"), {
    center: { lat: 47.85848098154215, lng: 35.10464944543838 },
    zoom: 5,
  });
}

window.initMap = initMap;
