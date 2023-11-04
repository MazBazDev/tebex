// Learn Template literals: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
// Learn about Modal: https://getbootstrap.com/docs/5.0/components/modal/
var modalWrap = null;
/**
 *
 * @param {string} title
 * @param {string} description content of modal body
 * @param {string} yesBtnLabel label of Yes button
 * @param {string} noBtnLabel label of No button
 * @param {string} product_id product id for callback
 * @param {string} price product price
 */

const showModal = (title, description, product_id, price) => {
  if (modalWrap !== null) {
    modalWrap.remove();
  }

  modalWrap = document.createElement('div');
  modalWrap.innerHTML = `
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${title}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ${description}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancel}</button>
            <button type="button" class="btn btn-primary modal-success-btn" data-bs-dismiss="modal">${buy} (${price})</button>
          </div>
        </div>
      </div>
    </div>
  `;

  modalWrap.querySelector('.modal-success-btn').onclick = function() {
      Swal.fire({
        title: title,
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
            min: 3,
        },
        inputPlaceholder: 'Steve',
        showCancelButton: true,
        reverseButtons: true,
        inputValue: pseudo,
        confirmButtonText: buy,
        cacnelButtonText: cancel,
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (value.length < 3) {
                return errorUser
            }
        },
        preConfirm: (username) => {
            return fetch(api, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        package_id: product_id
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Oups ! : ${error}`
                    )
                })
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            setTimeout(() => {
                window.open(result.value.checkout_url, '_blank').focus();
            })
        }
    });
  };

  document.body.append(modalWrap);

  var modal = new bootstrap.Modal(modalWrap.querySelector('.modal'));
  modal.show();
}
