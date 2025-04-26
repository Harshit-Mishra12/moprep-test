// load products
function loadproduct(_pricerange, _tags, _skip, _isappend) {

    var modeldata = new FormData();
    modeldata.append("pricerange", _pricerange);
    modeldata.append("tags", _tags);
    modeldata.append("skip", _skip);
    //data.append("pricerange", tags);

    //jsondata = JSON.stringify({ pricerange: _pricerange, tags: _tags, skip: _skip });
    $.ajax({
        //async: false,
        contentType: false,
        processData: false,
        url: "/front/home/loadproducts",
        type: 'POST',
        data: modeldata,
        beforeSend: function () {
            $("#LoadProduct").show();
        },
        success: function (result) {

            if (_isappend) {
                $('#divloadproduct').append(result);
            } else {
                $('#divloadproduct').html('');
                $('#divloadproduct').html(result);
            }

            console.log(" hdn skip: " + $("#hdn_skip").val())
            console.log($(".hb_product_div").length + " product load");

            if ($("#hdn_skip").val() >= $(".hb_product_div").length) {
                console.log("Hide Load BUtton");
                $("#btnLoadMore").hide();
            }

            // inetialize total Products after filter
            $("#hdn_skip").val($(".hb_product_div").length);

            //alert($("#hdn_skip").val());
            //alert();

        },
        complete: function () {
            $("#LoadProduct").hide();
        }
    });
}

// load products
function loadtopproduct(_pricerange, _tags, _skip, _isappend) {

    var modeldata = new FormData();
    modeldata.append("pricerange", _pricerange);
    modeldata.append("tags", _tags);
    modeldata.append("skip", _skip);
    //data.append("pricerange", tags);

    //jsondata = JSON.stringify({ pricerange: _pricerange, tags: _tags, skip: _skip });
    $.ajax({
        //async: false,
        contentType: false,
        processData: false,
        url: "/front/home/loadTopProducts",
        type: 'POST',
        data: modeldata,
        beforeSend: function () {
            $("#LoadProduct").show();
        },
        success: function (result) {

            if (_isappend) {
                $('#divloadproduct').append(result);
            } else {
                $('#divloadproduct').html('');
                $('#divloadproduct').html(result);
            }
            // inetialize total Products after filter
            $("#hdn_skip").val($(".hb_product_div").length);

            //alert($("#hdn_skip").val());
            //alert();

        },
        complete: function () {
            $("#LoadProduct").hide();
        }
    });
}

function cartcount() {
    $.ajax({
        url: "/User/dashboard/GetCartCount",
        type: 'POST',
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        beforeSend: function () {
            this.disabled = true;
            $("#loaderImage").show();
        },
        success: function (result) {
            if (result.Result == "Ok") {
                $("#cart_count").text(result.Count);
                console.log("couting is " + result.Count)
            }
            if (result.Result == "logout") {
                swal({
                    title: 'warning',
                    text: result.Message,
                    type: 'warning',
                    timer: 2000,
                    showCancelButton: false,
                    showConfirmButton: false
                })
            }
            else if (result.Result == "error") {
                $("#loaderImage").hide();
                this.disabled = false;
                sweetAlert("Oops...", result.Message, "error");
            }
        },
        complete: function () {
        }
    });
}

function AddToFavorite(_ProductId) {

    if (_ProductId == 'login') {
        sweetAlert("Oops...", "Please Login to Buy", "error");
    }
    else {
        json = JSON.stringify({ productId: _ProductId });
        $.ajax({
            url: "/User/shopping/addfavorite",
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: json,
            beforeSend: function () {
                this.disabled = true;
                $("#loaderImage").show();
            },
            success: function (result) {
                if (result.Result == "Ok") {
                    // udpate cart count on layout page
                    //$('#cart_count').load('@Url.Action("GetCount", "dashboard")');
                    // $("#cart_count").text(parseInt($("#cart_count").text()) + 1);
                    $("#heart_" + _ProductId).css("color", "red");
                    //filterProducts();

                    swal({
                        title: 'Success',
                        text: result.Message,
                        type: 'success',
                        timer: 800,
                        showCancelButton: false,
                        showConfirmButton: false
                    })
                }
                else if (result.Result == "error") {
                    $("#loaderImage").hide();
                    this.disabled = false;
                    sweetAlert("Oops...", result.Message, "error");
                }
            },
            complete: function () {
            }
        });
    }
}

function AddToCart(_ProductId) {

    if (_ProductId == 'login') {
        sweetAlert("Oops...", "Please Login to Buy", "error");
    }
    else {
        json = JSON.stringify({ productId: _ProductId });
        $.ajax({
            url: "/User/shopping/add",
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: json,
            beforeSend: function () {
                this.disabled = true;
                $("#loaderImage").show();
            },
            success: function (result) {
                if (result.Result == "Ok") {
                    // udpate cart count on layout page
                    //$('#cart_count').load('@Url.Action("GetCount", "dashboard")');
                    // $("#cart_count").text(parseInt($("#cart_count").text()) + 1);
                    $("#cart_count").text(result.Count);
                    //filterProducts();

                    swal({
                        title: 'Success',
                        text: result.Message,
                        type: 'success',
                        timer: 800,
                        showCancelButton: false,
                        showConfirmButton: false
                    })

                }
                else if (result.Result == "error") {
                    $("#loaderImage").hide();
                    this.disabled = false;
                    sweetAlert("Oops...", result.Message, "error");
                }
            },
            complete: function () {
            }
        });
    }
}

function AddToCart2(_ProductId, _Quantity) {

    if (_ProductId == 'login') {
        sweetAlert("Oops...", "Please Login to Buy", "error");
    }
    else {
        json = JSON.stringify({ productId: _ProductId, quantity: _Quantity });
        $.ajax({
            url: "/User/shopping/add2",
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: json,
            beforeSend: function () {
                this.disabled = true;
                $("#loaderImage").show();
            },
            success: function (result) {
                if (result.Result == "Ok") {
                    // udpate cart count on layout page
                    //$('#cart_count').load('@Url.Action("GetCount", "dashboard")');
                    // $("#cart_count").text(parseInt($("#cart_count").text()) + 1);
                    $("#cart_count").text(result.Count);
                    //filterProducts();

                    swal({
                        title: 'Success',
                        text: result.Message,
                        type: 'success',
                        timer: 800,
                        showCancelButton: false,
                        showConfirmButton: false
                    })

                }
                else if (result.Result == "error") {
                    $("#loaderImage").hide();
                    this.disabled = false;
                    sweetAlert("Oops...", result.Message, "error");
                }
            },
            complete: function () {
            }
        });
    }
}