$(document).ready(function () {

    //add product btn
    $('.add-subject-btn').on('click', function (e) {

        e.preventDefault();
        var name = $(this).data('name');
        var id = $(this).data('id');

        $(this).removeClass('btn-success').addClass('btn-default disabled');
        // <input type="hidden" name="product_ids[]" value="${id}">

        var html =
                `<tr>
                <td>${name}</td>
                <td><input type="hidden" name="subjects[${id}][quantity]"  class="form-control input-sm subject-quantity" min="1" value="1"></td>
                <td><button class="btn btn-danger btn-sm remove-subject-btn" data-id="${id}"><span class="fa fa-trash"></span></button></td>
            </tr>`;

        $('.order-list').append(html);

        (function myFunction() {
            //var list = document.getElementById('#order-list').hasChildNodes();
            if ($('.order-list').children.length > 0) {
                $('#add-order-form-btn').removeClass('disabled');
            } else {
                $('#add-order-form-btn').addClass('disabled');
            }
        }());
    });

    //fuction to change department based on levels
    var departids = $('#hiddeparts').data('id');
    var departnames = $('#hiddeparts').data('name');
    var departlevels = $('#hiddeparts').data('level');

    var levelids = $('#hidlevels').data('id');

    for (let index = 0; index < 5; index++) {
        if (departlevels == levelids) {
            //var html = `<option value="${departids}">${departnames}</option>`

            //console.log(levelids);
        }
    }


    $('#levelselect').on('change', function () {
        //$('#departselect').children().remove();
        //$('#departselect').append(html);
        if ($('.departoption').data('level') == levelids) {
            $('.departoption').css('display', 'block');
        }
    });

    //prevent disabled
    $('body').on('click', '.disabled', function (e) {
        e.preventDefault();
    });

    //remove product
    $('body').on('click', '.remove-subject-btn', function (e) {

        e.preventDefault();
        var id = $(this).data('id');

        $(this).closest('tr').remove();
        $('#subject-' + id).removeClass('btn-default disabled').addClass('btn-success');


    });




    //product quantity
    $('body').on('keyup change', '.product-quantity', function () {
        var quantity = parseInt($(this).val());
        var unitPrice = parseFloat($(this).data('price').replace(/,/g, ''));

        $(this).closest('tr').find('.product-price').html($.number(quantity * unitPrice, 2));
        calcTotalPrice();
    });

    //list all products
    $('.order-subjects').on('click', function (e) {
        e.preventDefault();

        $('#loading').css('display', 'flex');

        var url = $(this).data('url');
        var method = $(this).data('method');

        $.ajax({
            url: url,
            method: method,
            success: function (data) {
                $('#loading').css('display', 'none');
                $('#order-product-list').empty();
                $('#order-product-list').append(data);
            }
        }
        )
    });

    //print order
    $(document).on('click', '.print-btn', function () {
        $('#print-area').printThis();
    });


    //set the date is today
    //$('.datePicker').val(new Date().toDateInputValue());

    //document.getElementsByClassName('datePicker').value = new Date();
    document.getElementsByClassName('datePicker').valueAsDate = new Date();


});//end of document ready

/*function calcTotalPrice(){
 var price = 0;
 
 $('.order-list .product-price').each(function(index){
 price += parseFloat($(this).html().replace(/,/g, ''));
 });
 
 $('.total-price').html($.number(price,2));
 
 //check if price > 0
 if (price > 0) {
 $('#add-order-form-btn').removeClass('disabled');
 } else {
 $('#add-order-form-btn').addClass('disabled');
 }
 }
 
 */

/**
 * audio object 
 * notification sound
 * 
 * @type Audio
 */
var p = new Audio('/audio/not2.mp3');
p.load();


function loadView(url, selector) {
    var htmlLoadding = "<div class='text-center w3-large w3-margin' ><i class='fa fa-spin fa-spinner' ></i></div>";
    $(selector).html(htmlLoadding);
    $.get(url, function (r) {
        $(selector).html(r);
    });
}

function success(msg) {
    p.play();
    iziToast.success({title: msg, position: 'topCenter'});
}

function error(msg) {
    p.play();
    iziToast.error({title: msg, position: 'topCenter'});
}