async function getInternetAcquiringUrl(currentElement) {
  const url = currentElement.dataset.url;
  //   const elem = document.getElementById("spinner_" + currentElement.id);
  const spinnerElem = "spinner-border";
  //   elem.classList.toggle(spinnerElem);
  let response = await fetch(url);
  if (response.ok) {
    let json = await response.json();
    if (json.url) {
      window.open(json.url);
    } else {
      location.reload();
    }
    // elem.classList.toggle(spinnerElem);
  } else {
    console.error("Ошибка HTTP: " + response.status);
  }
}
