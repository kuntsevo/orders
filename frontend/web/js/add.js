function getDocument(url, fileName) {
  const modalHandler = new LongOperationModalWindow();
  modalHandler.open();

  $.ajax({
    url: url,
  })
    .done(function(path) {
      let link = document.createElement("a");
      link.href = path;
      link.download = fileName;
      console.log(link);
      link.click();
    })
    .fail(function (err) {
      // TODO
      console.error(err);
    })
    .always(() => modalHandler.close());
}

async function getInternetAcquiringUrl(currentElement) {
  const url = currentElement.dataset.url;
  const modalHandler = new LongOperationModalWindow();
  modalHandler.open();

  let response = await fetch(url);

  if (response.ok) {
    let json = await response.json();
    if (json.url) {
      window.open(json.url);
    } else {
      location.reload();
    }
  } else {
    console.error("Ошибка HTTP: " + response.status);
  }

  modalHandler.close();
}

class LongOperationModalWindow {
  elementId = "#longOperationModal";
  element;

  constructor() {
    this.element = $(this.elementId).modal({
      backdrop: true,
      keyboard: false,
      show: false,
    });
  }

  open() {
    this.element.modal("show");
  }

  close() {
    this.element.modal("hide");
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

    targetOrderList.removeAttribute("hidden");
  });
});
