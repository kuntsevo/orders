async function getInternetAcquiringUrl(currentElement) {
  const url = currentElement.dataset.url;
  //   const elem = document.getElementById('spinner_' + currentElement.id);
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

const homepageFilterBtns = document.querySelectorAll(".homepage__filter-btn");

homepageFilterBtns.forEach((elem) => {
  elem.addEventListener("click", function () {
    const orderList = document.querySelectorAll(".order__list");
    const homepageFilterBtnActive = document.querySelector(
      ".homepage__filter-btn.is-active"
    );
    const filterType = "orderState";

    if (!(orderList && homepageFilterBtnActive && filterType in elem.dataset)) {
      return;
    }

    homepageFilterBtnActive.classList.remove("is-active");
    elem.classList.add("is-active");

    let filterValue = elem.dataset[filterType];
    const targetOrderList = document.getElementById(filterValue);
    if (!targetOrderList) {
      return;
    }

    orderList.forEach((orderItem) => {
      orderItem.setAttribute("hidden", "");
    });

    targetOrderList.removeAttribute('hidden');
  });
});
