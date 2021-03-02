$(document).ready(function () {
    if($('.js-collect-pc').length > 0 || $('.js-close-panel-category').length > 0) {
        $('.js-collect-pc, .js-close-panel-category').click(function (e) {
           $('body').toggleClass('panel-categories-open');
        });
    }

    if($('.js-open-filter').length > 0) {
        $('.js-open-filter').click(function (e) {
            $('body').toggleClass('panel-filters-open');
        });
    }

    if($('.js-open-menu').length > 0) {
        $('.js-open-menu').click(function (e) {
            $('body').toggleClass('panel-categories-open');
        });
    }

    if($('.js-open-component').length > 0) {
        $('.js-open-component').click(function (e) {
            let id = $(this).attr('data-id');
            let data = 'id=' + id;

            ajax("POST", "/api/components/get-component", true, data)
                .then(
                    response => {
                        $('#panel-component .panel__body').html(response);
                        $('body').toggleClass('panel-product-open');
                    }
                ).catch(
                error => {
                    console.log(error.error);
                }
            );
        });
    }

    if($('.js-close-component').length > 0) {
        $('.js-close-component').click(function (e) {
            $('body').toggleClass('panel-product-open');
        });
    }

    if($('.js-choose-component').length > 0) {
        $('.js-choose-component').click(function (e) {
            $(this).next().prop('checked', true);
            document.forms['form-choose-component'].submit();
        });
    }

    if($('.js-remove-component').length > 0) {
        $('.js-remove-component').click(function (e) {
            $(this).next().prop('checked', true);
            document.forms['form-remove-component'].submit();
        });
    }

    if($('#form-set-filter').length > 0){
        let form = document.forms['form-set-filter'];
        form.onsubmit = function (e) {
            for(let a = 0; a < form.elements.length; ++a) {
                let element = form.elements[a];

                if((element.type == 'number' || element.type == 'text') && element.value == '') {
                    element.disabled = true;
                }
            }
        };
    }
});

function ajax(method, url, async, data) {

    return new Promise(function(resolve, reject) {

        let xhr = new XMLHttpRequest();
        xhr.open(method, url, async);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (this.status == 200) {
                resolve(this.response);
            } else {
                var error = {};
                error.error = new Error(this.statusText);
                error.response = this.response;
                error.code = this.status;
                reject(error);
            }
        };

        xhr.onerror = function() {
            reject(new Error("Network Error"));
        };

        xhr.send(data);
    });
}