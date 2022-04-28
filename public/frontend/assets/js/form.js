(function($) {
    $.fn.donetyping = function(callback, x_timer){
      
        var _this = $(this);
        var x_timer = x_timer;    
        var x_timer_back = x_timer;    
        _this.keyup(function (){
            $("#result").html(
                '<div class="align-items-center d-flex justify-content-center renderArea w-100" style="min-height: 150px;"><div class="loader-circle"><div class="loader"><div class="loader-dot"></div></div></div></div>'
            );
            clearTimeout(x_timer);
            x_timer = setTimeout(clear_timer, x_timer_back);
        }); 
    
        function clear_timer(){
            clearTimeout(x_timer);
            callback.call(_this);
        }
    }
})(jQuery); 

function miniSubmitFile (par,par_return) {
    var fileCount = document.forms["mini_form"].files;
    if (fileCount > 0) {
        swal({
            title: "Lütfen Bekleyin",
            text: "Dosyalar kaydediliyor...",
            html:
            '<div class="progress progress-striped progress-bar-animated progressUpload" style="display:none;">' +
                '<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
                    '<span>80% Complete </span>'+
                '</div>'+
            '</div>',
            showConfirmButton: false,
        });
    }
    $('.progressUpload').show();
    //$('button#miniSubmitButton').prop('disabled', true);
    var formData = new FormData($("#mini_form")[0]);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", function(event){
        $(".progress-striped").show();
        var percent = (event.loaded / event.total) * 100;
        $(".progress-bar").width(Math.round(percent) +'%');
        $(".progress-bar").html(Math.round(percent) +'%');
        
        if (Math.round(percent) == 100) {
            swal({
                title: "Lütfen Bekleyin",
                text: "Dosyalar kaydediliyor...",
                showConfirmButton: false,
            });
        }

    }, false);
    ajax.onreadystatechange = function(){
        $(".progress-bar").width(0);
        if(ajax.readyState == 4){
            var response = JSON.parse(ajax.response);
            $('.progressUpload').hide();
            swal.close();
            if (response.error) {
                if (response.location) {
                    swal({
                        title: "Bilgilendirme!",
                        text: response.error,
                        type: "info",
                        confirmButtonText: "Tamam"
                    }).then(function () {
                        window.location.href = response.location;
                        }
                    );
                }else{
                    swal({
                        title: "Hata!",
                        text: response.error,
                        type: "error",
                        confirmButtonText: "Tamam"
                    });
                }
            }else{
                swal.close();
               // $('button#miniSubmitButton').prop('disabled', false);
                if (par_return == 'giveOffer') {
                    swal.close();
                    const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });
                    toast({
                        type: 'success',
                        title: response.success,
                        padding: '2em',
                    })
                    var file = response.arr.file;
                    var html = '' ;
                    if (file) {
                        $.each(file ,function(index, value){
                            html += `
                                <a href="${value.url}" download class="message-data-file">
                                    <svg width="1em" height="1.2em" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.4673e-05 6L6.00303 0H16.998C17.55 0 18 0.455 18 0.992V19.008C17.9998 19.2712 17.895 19.5235 17.7088 19.7095C17.5226 19.8955 17.2702 20 17.007 20H0.993025C0.861702 19.9991 0.731846 19.9723 0.61087 19.9212C0.489895 19.8701 0.38017 19.7957 0.287961 19.7022C0.195752 19.6087 0.122864 19.4979 0.0734597 19.3762C0.0240555 19.2545 -0.000897804 19.1243 2.4673e-05 18.993V6ZM7.00002 2V7H2.00002V18H16V2H7.00002Z" fill="currentColor"/>
                                    </svg>
                                    Dosyayı İndir
                                </a>
                            `;
                        });
                    }
                   
                    $('.chat-message-file-list').append(html);
                    $("#my-file").val(null);
                    $(".jFiler").remove();
                    $(".input-file-wrapper").html('<input type="file" name="messageFiles[]" class="input-file" id="my-file" accept=".pdf, .doc, .docx, .xlsx, .png, .jpg, .jpeg" multiple="multiple">');
                    $('#my-file').filer({
                        showThumbs: true,
                        addMore: true,
                        allowDuplicates: false
                    });
                }else if (par_return) {
                    swal({
                        title: "Başarılı!",
                        text: response.success,
                        type: "success",
                        confirmButtonText: "Tamam"
                    }).then(function () {
                            window.location.href = par_return;
                        }
                    );
                }else{
                    swal({
                        title: "Başarılı!",
                        text: response.success,
                        type: "success",
                        showConfirmButton: false,
                    });
                    setInterval(function(){
                        location.reload()
                    }, 2000);
                }
            }
        }
    };
    ajax.open("POST", par);
    ajax.send(formData);
}

function miniSubmit (par,par_return = 0, buttonShow = 0) {
    var formData = new FormData($("#mini_form")[0]);
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    $.ajax({
        type: "POST",
        url: par,
        async: true,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            if (response.error) {
                if (par_return == 'custom_reload_ajax') {
                    if (response.location) {
                        swal({
                            title: "Bilgilendirme!",
                            text: response.error,
                            type: "info",
                            confirmButtonText: "Tamam"
                        }).then(function () {
                            window.location.href = response.location;
                            }
                        );
                    }else{
                        swal({
                            title: "Hata!",
                            text: response.error,
                            type: "error",
                            confirmButtonText: "Tamam"
                        });
                    }
                }else{
                    if (response.location) {
                        swal({
                            title: "Bilgilendirme!",
                            text: response.error,
                            type: "info",
                            confirmButtonText: "Tamam"
                        }).then(function () {
                            window.location.href = response.location;
                            }
                        );
                    }else{
                        swal({
                            title: "Hata!",
                            text: response.error,
                            type: "error",
                            confirmButtonText: "Tamam"
                        });
                    }
                }
            } else {
                if (par_return == 'custom_noreload'){
                    const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });
                    toast({
                        type: 'success',
                        title: response.success,
                        padding: '2em',
                    })
                }else if (par_return == 'custom_reload_ajax') {
                    swal({
                        title: "Başarılı!",
                        text: response.success,
                        type: "success",
                        showConfirmButton: false,
                    });
                    setInterval(function(){
                        window.location.href = response.location;
                    }, 2000);
                }else if (par_return) {
                    if (buttonShow == '1') {
                        swal({
                            title: "Başarılı!",
                            text: response.success,
                            type: "success",
                            showConfirmButton: false,
                        });
                        setInterval(function(){
                            window.location.href = par_return;
                        }, 2000);
                    }else{
                        swal({
                            title: "Başarılı!",
                            text: response.success,
                            type: "success",
                            confirmButtonText: "Tamam"
                        }).then(function () {
                                window.location.href = par_return;
                            }
                        );
                    }
                }else{
                    swal({
                        title: "Başarılı!",
                        text: response.success,
                        type: "success",
                        showConfirmButton: false,
                    });
                    setInterval(function(){
                        location.reload()
                    }, 2000);
                }
            }
        }
    });
}

function miniSubmitForm (form, par,par_return = 0, buttonShow = 0) {
    var formData = new FormData($("#"+form+"")[0]);
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    $.ajax({
        type: "POST",
        url: par,
        async: true,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            if (response.error) {
                if (par_return == 'custom_reload_ajax') {
                    if (response.location) {
                        swal({
                            title: "Bilgilendirme!",
                            text: response.error,
                            type: "info",
                            confirmButtonText: "Tamam"
                        }).then(function () {
                            window.location.href = response.location;
                            }
                        );
                    }else{
                        swal({
                            title: "Hata!",
                            text: response.error,
                            type: "error",
                            confirmButtonText: "Tamam"
                        });
                    }
                }else{
                    if (response.location) {
                        swal({
                            title: "Bilgilendirme!",
                            text: response.error,
                            type: "info",
                            confirmButtonText: "Tamam"
                        }).then(function () {
                            window.location.href = response.location;
                            }
                        );
                    }else{
                        swal({
                            title: "Hata!",
                            text: response.error,
                            type: "error",
                            confirmButtonText: "Tamam"
                        });
                    }
                }
            } else {
                if (par_return == 'custom_noreload'){
                    const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });
                    toast({
                        type: 'success',
                        title: response.success,
                        padding: '2em',
                    })
                }else if (par_return == 'custom_reload_ajax') {
                    swal({
                        title: "Başarılı!",
                        text: response.success,
                        type: "success",
                        showConfirmButton: false,
                    });
                    setInterval(function(){
                        window.location.href = response.location;
                    }, 2000);
                }else if (par_return) {
                    if (buttonShow == '2') {
                        window.location.href = par_return;
                    }else if (buttonShow == '1') {
                        swal({
                            title: "Başarılı!",
                            text: response.success,
                            type: "success",
                            showConfirmButton: false,
                        });
                        setInterval(function(){
                            window.location.href = par_return;
                        }, 2000);
                    }else{
                        swal({
                            title: "Başarılı!",
                            text: response.success,
                            type: "success",
                            confirmButtonText: "Tamam"
                        }).then(function () {
                                window.location.href = par_return;
                            }
                        );
                    }
                }else{
                    swal({
                        title: "Başarılı!",
                        text: response.success,
                        type: "success",
                        showConfirmButton: false,
                    });
                    setInterval(function(){
                        location.reload()
                    }, 2000);
                }
            }
        }
    });
}

function miniSingle (value,par,par_return) {
    swal({
        title: 'Dikkat!',
        text: "İşlemi gerçekleştirmek istediğinizden emin misiniz ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Hayır',
        confirmButtonText: 'Evet, işlem gerçekleşsin!'
    }).then((result) => {
        if (result.value) {
            swal({
                title: "Lütfen Bekleyiniz...",
                onOpen: () => {
                    swal.showLoading()
                },
                showConfirmButton: false,
            });
            $.ajax({
                    url: par,
                    type: "POST",
                    data : {value : value},
                    dataType: "json",
                    success: function(response){
                        if (response.error) {
                            if (response.location) {
                                swal({
                                    title: "Bilgilendirme!",
                                    text: response.error,
                                    type: "info",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                    window.location.href = response.location;
                                    }
                                );
                            }else{
                                swal({
                                    title: "Hata!",
                                    text: response.error,
                                    type: "error",
                                    confirmButtonText: "Tamam"
                                });
                            }
                        }else{
                            if (par_return == 'custom_reload_ajax') {
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                        window.location.href = response.location;
                                    }
                                );
                            }else if (par_return == 'deleteItem') {
                                const toast = swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    padding: '2em'
                                });
                                toast({
                                    type: 'success',
                                    title: response.success,
                                    padding: '2em',
                                })
                                $('#deleteItemArea-'+value).remove();
                                $('#deleteItemAreaBasket-'+value).remove();
                               
                                if ($('#headerBasketArea li').length <= 0) {
                                    $('#headerBasketProduct').addClass('d-none');
                                    $('#headerBasketEmpty').removeClass('d-none');
                                }else{
                                    $('#headerBasketProduct').removeClass('d-none');
                                    $('#headerBasketEmpty').addClass('d-none');
                                    $('#headerBasketPrice').html(response.header_basket_price + 'TL');
                                }
                              
                                $('#shipping_price_area_header').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                                $('#shipping_price_area').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                                var vatRateHtml = '';
                                $.each(response.vatRate,function(index, value){
                                    vatRateHtml += `
                                        <li class="list-group-item d-flex">
                                            <span>KDV Tutarı (${index}%)</span> <span class="ml-auto font-size-sm">${value} TL</span>
                                        </li>
                                    `
                                });
                                $('#vatRateArea').html(vatRateHtml);

                                $('#headerBasketCount').html(response.basketCount); 
                                $('.couponBasketPrice').html(response.coupon_price + ' TL');
                                $('#disconce_price_area').html(response.disconce_price + ' TL');
                               
                                $('.basketCountArea').attr('data-cart-items', response.basketCount);
                                if ($('#basketProductArea li').length <= 0) {
                                    $('.basketProductArea').addClass('d-none');
                                    $('.basketPriceArea').addClass('d-none');
                                    $('.basketProductEmpty').removeClass('d-none');
                                }else{
                                    $('.basketProductArea').removeClass('d-none');
                                    $('.basketPriceArea').removeClass('d-none');
                                    $('.basketProductEmpty').addClass('d-none');
                                    $('#basketTotalPriceFirst').html(response.header_basket_price + ' TL');
                                    $('#basketTotalPriceTotal').html(response.basket_total_price + 'TL');
                                }
                                $('#disconce_price_area_header').html(response.disconce_price + ' TL');
                                $('#basketTotalPriceTotalHeader').html(response.basket_total_price_first + 'TL');
                                $('#basketTotalPriceFirstHeader').html(response.headerBasketPriceSesion + ' TL');

                                if (response.discount_min_error) {
                                    toast({
                                        type: 'info',
                                        title: response.discount_min_error,
                                        padding: '2em',
                                    })
                                }
                            }else if(par_return == 'deleteItemSlide') {
                                const toast = swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    padding: '2em'
                                });
                                toast({
                                    type: 'success',
                                    title: response.success,
                                    padding: '2em',
                                })
                                $('#deleteItemAreaSlider-'+value).remove();
                                $('#deleteItemAreaSliderSecend-'+value).remove();
                            }else if(par_return == 'deleteItemSlideMobile') {
                                const toast = swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    padding: '2em'
                                });
                                toast({
                                    type: 'success',
                                    title: response.success,
                                    padding: '2em',
                                })
                                $('#deleteItemAreaSliderMobile-'+value).remove();
                                $('#deleteItemAreaSliderMobileSecend-'+value).remove();
                            }else if(par_return == 'deleteItemGalery') {
                                const toast = swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    padding: '2em'
                                });
                                toast({
                                    type: 'success',
                                    title: response.success,
                                    padding: '2em',
                                })
                                $('#deleteItemGalery-'+value).remove();
                            }else if(par_return == 'deleteItemVideo') {
                                const toast = swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    padding: '2em'
                                });
                                toast({
                                    type: 'success',
                                    title: response.success,
                                    padding: '2em',
                                })
                                $('#deleteItemVideo-'+value).remove();
                            }else if(par_return){
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                        window.location.href = par_return;
                                    }
                                );
                            }else{
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    showConfirmButton: false,
                                });
                                setInterval(function(){
                                    location.reload()
                                }, 2000);
                            }
                        }
                    }
            });
        }
    });

}

function miniSubmitConfirmation (par,par_return) {
    swal({
        title: 'Dikkat!',
        text: "İşlemi gerçekleştirmek istediğinizden emin misiniz ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Hayır',
        confirmButtonText: 'Evet, işlem gerçekleşsin!'
    }).then((result) => {
        if (result.value) {
            var data = $("#mini_form").serialize();
            swal({
                title: "Lütfen Bekleyiniz...",
                onOpen: () => {
                    swal.showLoading()
                },
                showConfirmButton: false,
            });
            $.ajax({
                type: "POST",
                url: par,
                async: true,
                data: data,
                dataType: "json",
                success: function (response) {
                    if (response.error) {
                        if (par_return == 'custom_reload_ajax') {
                            if (response.location) {
                                swal({
                                    title: "Bilgilendirme!",
                                    text: response.error,
                                    type: "info",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                    window.location.href = response.location;
                                    }
                                );
                            }else{
                                swal({
                                    title: "Hata!",
                                    text: response.error,
                                    type: "error",
                                    confirmButtonText: "Tamam"
                                });
                            }
                           
                        }else{
                            swal({
                                title: "Hata!",
                                text: response.error,
                                type: "error",
                                confirmButtonText: "Tamam"
                            });
                        }
                    } else {
                        if (par_return == 'custom_noreload'){
                            const toast = swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                padding: '2em'
                            });
                            toast({
                                type: 'success',
                                title: response.success,
                                padding: '2em',
                            })
                        }else if (par_return == 'custom_reload_ajax') {
                            if (response.location) {
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    showConfirmButton: false,
                                });
                                setInterval(function(){
                                    window.location.href = response.location;
                                }, 2000);
                            }else{
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    showConfirmButton: false,
                                });
                                setInterval(function(){
                                    location.reload()
                                }, 2000);
                            }
                           
                        }else if (par_return) {
                            if (buttonShow == '1') {
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    showConfirmButton: false,
                                });
                                setInterval(function(){
                                    window.location.href = par_return;
                                }, 2000);
                            }else{
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                        window.location.href = par_return;
                                    }
                                );
                            }
                        }else{
                            swal({
                                title: "Başarılı!",
                                text: response.success,
                                type: "success",
                                showConfirmButton: false,
                            });
                            setInterval(function(){
                                location.reload()
                            }, 2000);
                        }
                    }
                }
            });
        }
    });

}

function miniSingleStatus (el,where,par) {
    var value ;
    if (el.checked) {
        value = '1'
    }else{
        value = '0'
    }
    $.ajax({
        type: "POST",
        url: par,
        async: true,
        data: { id: where, value: value },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
            }
        }
    });
}

function miniSingleForm (value,par,par_return) {
    var data = $("#mini_form").serialize();
    swal({
        title: 'Dikkat!',
        text: "İşlemi gerçekleştirmek istediğinizden emin misiniz ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Hayır',
        confirmButtonText: 'Evet, işlem gerçekleşsin!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                    url: par,
                    type: "POST",
                    data : data,
                    success: function(response){
                        response = response
                        if (response.error) {
                            swal({
                                title: "Hata!",
                                text: response.error,
                                type: "error",
                                confirmButtonText: "Tamam"
                            });
                        }else{
                            if (par_return == 'deleteItem') {
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                        $('#deleteArea-'+value+'').remove();
                                        $('.deleteArea-'+value+'').remove();
                                    }
                                );
                            }else if (par_return) {
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    confirmButtonText: "Tamam"
                                }).then(function () {
                                        window.location.href = par_return;
                                    }
                                );
                            }else{
                                swal({
                                    title: "Başarılı!",
                                    text: response.success,
                                    type: "success",
                                    showConfirmButton: false,
                                });
                                setInterval(function(){
                                    location.reload()
                                }, 2000);
                            }
                        }
                    }
            });
        }
    });

}

function miniSingleValue (el,where,par) {
    var value = el ;
    $.ajax({
        type: "POST",
        url: par,
        async: true,
        data: { id: where, value: value },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
               
                var total_price =  response.price.total_price;
                var discount_price = response.price.discount_price;
                var basket_price = response.price.basket_price;
                if (discount_price != '0.0' || basket_price != '0.0') {
                    if (basket_price != '0' && basket_price != '0.0') {
                        if (discount_price != '0' && discount_price != '0.0') {
                            $('#priceAreaOne'+where).html(total_price + ' TL');
                            $('#priceAreaOneHeader'+where).html(total_price + ' TL');
                        }else{
                            $('#priceAreaOne'+where).html(total_price + ' TL');
                            $('#priceAreaOneHeader'+where).html(total_price + ' TL');
                        }
                    }else{
                        $('#priceAreaOne'+where).html(total_price + ' TL');
                        $('#priceAreaOneHeader'+where).html(total_price + ' TL');
                    }

                    if (basket_price != 0) {
                        $('#priceAreaThree'+where).html('Sepete Özel <br>'+ basket_price + ' TL');
                        $('#priceAreaThreeHeader'+where).html('Sepete Özel '+ basket_price + ' TL');
                    }
                    if (discount_price != 0) {
                        $('#priceAreaTwo'+where).html(discount_price + ' TL');
                        $('#priceAreaTwoHeader'+where).html(discount_price + ' TL');
                    }else{
                        $('#priceAreaTwo'+where).html(total_price + ' TL');
                        $('#priceAreaTwoHeader'+where).html(total_price + ' TL');
                    }
                }
                $('#pieceArea'+where).html(response.piece);
                $('#shipping_price_area_header').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                $('#shipping_price_area').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                var vatRateHtml = '';
                $.each(response.vatRate,function(index, value){
                    vatRateHtml += `
                        <li class="list-group-item d-flex">
                            <span>KDV Tutarı (${index}%)</span> <span class="ml-auto font-size-sm">${value} TL</span>
                        </li>
                    `
                });
                $('#vatRateArea').html(vatRateHtml);
                $('#disconce_price_area').html(response.disconce_price + ' TL');
                $('#disconce_price_area_header').html(response.disconce_price + ' TL');
                $('#headerBasketPrice').html(response.header_basket_price + ' TL');
                $('#basketTotalPriceFirst').html(response.header_basket_price + ' TL');
                $('#basketTotalPriceFirstHeader').html(response.headerBasketPriceSesion + ' TL');
                $('#basketTotalPriceTotal').html(response.basket_total_price + ' TL');
                $('#basketTotalPriceTotalHeader').html(response.basket_total_price_first + ' TL');
                if (response.discount_min_error) {
                    toast({
                        type: 'info',
                        title: response.discount_min_error,
                        padding: '2em',
                    })
                }
                $('.couponBasketPrice').html(response.coupon_price + ' TL');
            }
        }
    });
}

function promationCodeUse () {
    var deger = $("form#promationCodeUse").serialize();
    $.ajax({
        type: "POST",
        url: "promationCodeUse",
        async: true,
        data: deger,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
                $('#shipping_price_area_header').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                $('#shipping_price_area').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                var vatRateHtml = '';
                $.each(response.vatRate,function(index, value){
                    vatRateHtml += `
                        <li class="list-group-item d-flex">
                            <span>KDV Tutarı (${index}%)</span> <span class="ml-auto font-size-sm">${value} TL</span>
                        </li>
                    `
                });
                $('#vatRateArea').html(vatRateHtml);
                $('#disconce_price_area').html(response.disconce_price + ' TL');
                $('#headerBasketPrice').html(response.header_basket_price + ' TL');
                $('#basketTotalPriceFirst').html(response.header_basket_price + ' TL');
                $('#basketTotalPriceTotal').html(response.basket_total_price + ' TL');

                $('.couponBasketArea').removeClass('d-none');
                $('.couponBasketArea').addClass('d-flex');
                $('.couponBasketCode').html(response.coupon_code);
                $('.couponBasketPrice').html(response.coupon_price + ' TL');

                $('#promationCodeUse').addClass('d-none');
                $('#promationCodeCanceled').removeClass('d-none');
                $('#couponCodName').html(response.coupon_code);
            }
        }
    });
}

function promationCodeCanceled () {
    var deger = $("form#promationCodeCanceled").serialize();
    $.ajax({
        type: "POST",
        url: "promationCodeCanceled",
        async: true,
        data: deger,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
                setInterval(function(){
                    location.reload()
                }, 2000);
            }
        }
    });
}

function citySelectd (el, userDist = 0) {
    var value = el.value; 
    var value = el.value; 
    if (!value) {
        var value = el; 
    }
    if (value == 0) {
        $('#townAreaUser').html('<option value="0">Lütfen Önce İl Seçiniz</option>');
        $('#townArea').html('<option value="0">Lütfen Önce İl Seçiniz</option>');
        $('#townAreaEdit').html('<option value="0">Lütfen Önce İl Seçiniz</option>');
        $('#neighborhoodAreaUser').html('<option value="0">Lütfen Önce İl ve ilçe Seçiniz</option>');
        $('#neighborhoodArea').html('<option value="0">Lütfen Önce İl ve ilçe Seçiniz</option>');
        $('#neighborhoodAreaEdit').html('<option value="0">Lütfen Önce İl ve ilçe Seçiniz</option>');
        return
    }
    $.ajax({
        type: "POST",
        url: 'citySelectd',
        async: true,
        data: { id: value, userDist: userDist },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                $('#townAreUsera').html('');
                $('#townArea').html('');
                $('#townAreEdit').html('');
                if (response.arr) {
                    $('#townAreaUser').html('<option value="0">Seçiniz</option>');
                    $('#townArea').html('<option value="0">Seçiniz</option>');
                    $('#townAreaEdit').html('<option value="0">Seçiniz</option>');
                    $('#neighborhoodAreaUser').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $('#neighborhoodArea').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $('#neighborhoodAreaEdit').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $.each(response.arr,function(index, value){
                        if (value.userDist == value.val) {
                            var selected = 'selected';
                        }else{
                            var selected = '';
                        }
                        $('#townAreaUser').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                        $('#townArea').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                        $('#townAreaEdit').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                    });
                }else{
                    $('#townAreaUser').append('<option value="0">Seçilen şehire ait ilçe bulunamadı.</option>');
                    $('#townArea').append('<option value="0">Seçilen şehire ait ilçe bulunamadı.</option>');
                    $('#townAreaEdit').append('<option value="0">Seçilen şehire ait ilçe bulunamadı.</option>');
                    $('#neighborhoodAreaUser').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $('#neighborhoodArea').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $('#neighborhoodAreaEdit').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                }
                
            }
        }
    });
}

function townSelectd (el, userNeigh = 0) {
    var value = el.value;  
    if (!value) {
        var value = el; 
    }
    if (value == 0) {
        $('#neighborhoodAreaUser').html('<option value="0">Lütfen Önce İl ve ilçe Seçiniz</option>');
        $('#neighborhoodArea').html('<option value="0">Lütfen Önce İl ve ilçe Seçiniz</option>');
        $('#neighborhoodAreaEdit').html('<option value="0">Lütfen Önce İl ve ilçe Seçiniz</option>');
        return
    }
    $.ajax({
        type: "POST",
        url: 'townSelectd',
        async: true,
        data: { id: value, userNeigh: userNeigh },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                $('#neighborhoodAreaUser').html('');
                $('#neighborhoodArea').html('');
                $('#neighborhoodAreaEdit').html('');
                if (response.arr) {
                    $('#neighborhoodAreaUser').html('<option value="0">Seçiniz</option>');
                    $('#neighborhoodArea').html('<option value="0">Seçiniz</option>');
                    $('#neighborhoodAreaEdit').html('<option value="0">Seçiniz</option>');
                    $.each(response.arr,function(index, value){
                        if (value.userNeigh == value.val) {
                            var selected = 'selected';
                        }else{
                            var selected = '';
                        }
                        $('#neighborhoodAreaUser').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                        $('#neighborhoodArea').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                        $('#neighborhoodAreaEdit').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                    });
                }else{
                    $('#neighborhoodAreaUser').append('<option value="0">Seçilen ilçeye ait mahalle bulunamadı.</option>');
                    $('#neighborhoodArea').append('<option value="0">Seçilen ilçeye ait mahalle bulunamadı.</option>');
                    $('#neighborhoodAreaEdit').append('<option value="0">Seçilen ilçeye ait mahalle bulunamadı.</option>');
                }
                
            }
        }
    });
}

function productQuickview (product_id, variant_barcode) {
    $.ajax({
        type: "POST",
        url: 'productQuickview',
        async: true,
        data: { product_id: product_id, variant_barcode: variant_barcode },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                if (response.arr) {
                    var product = response.arr;
                    var priceArea = '';
                    var sizeArea = '';
                    var sizeAreTitle = '';
                    var stockArea = '';

                    if (response.arr.stock > 0) {
                        for (let i = 1; i <= response.arr.stock; i++) {
                            stockArea += '<option value="'+i+'">'+i+'</option>';
                        }
                    }else{
                        stockArea += '<option value="0">Stok Bulunamadı</option>';
                    }

                    var variantArea = '';
                    if (product.discountBool) {
                        priceArea = `
                            <div class="priceItem discountItem text-danger border-danger">
                                <span class="font-size-sm font-weight-bold">%${product.discountRate}</span>
                                <span class="font-size-xs font-weight-normal">İndirim</span>
                            </div>
                            <div class="priceItem salesPriceItem">
                                <span class="font-size-lg text-gray-350 text-decoration-line-through">${product.totalPrice} TL</span>
                                <span class="font-size-h5 font-weight-bolder text-dark">${product.discountPrice} TL</span>
                            </div>
                        `;
                        if (product.basketPrice || product.basketBool) {
                            priceArea += `
                                <div class="priceItem bucketPriceItem">
                                    <span class="font-size-xs text-gray-500">Sepette ${product.basketRate}% İndirim</span>
                                    <span class="font-size-h5 font-weight-bolder text-success">${product.basketPrice} TL</span>
                                </div>
                            `;
                        }
                    }else{
                        priceArea = `
                            <div class="priceItem salesPriceItem">
                                <span class="font-size-h5 font-weight-bolder text-dark">${product.totalPrice} TL</span>
                            </div>
                        `;
                        if (product.basketPrice) {
                            priceArea += `
                                <div class="priceItem bucketPriceItem">
                                    <span class="font-size-xs text-gray-500">Sepette</span>
                                    <span class="font-size-h5 font-weight-bolder text-success">${product.basketPrice} TL</span>
                                </div>
                            `;
                        }
                    }
                    var combinationArea = '';
                    var variantID = '';
                    $.each(response.combinationGroup, function(index, value){
                        combinationArea += `
                            <p class="mb-5">
                                ${value['0'].group_title}: <strong><span id="${value['0'].group_slug}">${response.selectCombinationArrayTitle[index]}</span></strong>
                            </p>
                            <div class="mb-2">
                        `;
                        $.each(value, function(index, item){
                            var checked = '';
                            $.each(response.selectCombinationArray, function(indexSelect, itemSelect){
                                if (itemSelect == item.id ) {
                                    checked = 'checked';
                                }
                            });
                            if (item.is_color) {
                                combinationArea += `
                                    <div class="custom-control custom-control-inline custom-control-size mb-2">
                                        <input type="radio" class="custom-control-input" name="combination[${item.group_title}]" onclick="getPriceArea()"
                                            id="${item.group_slug}${item.id}" value="${item.id}" data-title="${item.title}" data-toggle="form-caption" ${checked ? 'checked' : ''} ${item.disabled ? 'disabled' : ''}
                                            data-target="#${item.group_slug}">
                                        <label class="custom-control-label" style="background:${item.color}" for="${item.group_slug}${item.id}">${item.title}</label>
                                    </div>
                                `;
                            }else{
                                combinationArea += `
                                    <div class="custom-control custom-control-inline custom-control-size mb-2">
                                        <input type="radio" class="custom-control-input" name="combination[${item.group_title}]" onclick="getPriceArea()"
                                            id="${item.group_slug}${item.id}" value="${item.id}" data-title="${item.title}" data-toggle="form-caption" ${jQuery.inArray( item.id, response.selectCombinationArray ) == 0 || jQuery.inArray( item.id, response.selectCombinationArray ) ? 'checked' : ''} ${item.disabled ? 'disabled' : ''}
                                            data-target="#${item.group_slug}">
                                        <label class="custom-control-label" for="${item.group_slug}${item.id}">${item.title}</label>
                                    </div>
                                `;
                            }
                            
                        });
                        combinationArea += `
                            </div>
                        `;
                    });
                    

                    var html = `
                        <div class="row align-items-center mx-xl-0">
                            <div class="col-12 col-lg-6 col-xl-5 py-4 py-xl-0 px-xl-0">

                                <!-- Image -->
                                <img class="img-fluid modalPreviewProductBanner" src="${product.image}"
                                    alt="...">

                                <!-- Button -->
                                <a class="btn btn-sm btn-block btn-primary" href="${product.link}">
                                    Detaylı İncele<i class="fe fe-shopping-bag ml-2"></i>
                                </a>

                            </div>
                            <div class="col-12 col-lg-6 col-xl-7 py-9 px-md-9">

                                <!-- Heading -->
                                <h4 class="mb-3">${product.title}</h4>

                                <!-- Price -->
                                <div class="mb-5">
                                    <div class="productPriceArea d-flex align-items-center">
                                       ${priceArea}
                                    </div>
                                </div>

                                <!-- Form -->
                                <form id="productBasketAdd" onsubmit="return false">
                                    <input type="hidden" name="variant_id" value="${variantID}">
                                    <input type="hidden" name="product_id" value="${product.productID}">
                                    <div class="form-group">
                                        ${combinationArea}
                                    </div>
                                    <div class="form-group mb-0">
                                        <div class="form-row">
                                            <div class="col-12 col-lg-auto productViewStock">
                                                <select class="custom-select mb-2 stockSelectArea" name="select_piece"> 
                                                    ${stockArea}
                                                </select>
                                            </div>
                                            <div class="col-9 col-lg productViewStock">
                                                <button type="submit" onclick="productBasketAdd()" id="productBasketAddQuick" class="btn btn-block btn-success mb-2">
                                                    Sepete Ekle <i class="fe fe-shopping-cart ml-2"></i>
                                                </button>
                                            </div>
                                            <div class="col-3 col-lg-auto">
                                                <button type="button" class="btn btn-outline-primary favoriteBtn likeButton ${product.favorite ? 'active' : ''}" data-product-id="${product.productID}" data-variant-id="${product.variantID}" data-toggle="button" onclick="${product.favorite ? `productFavoritesRemove(${product.productID}, ${product.variantID})` : `productFavoritesAdd(${product.productID}, ${product.variantID})`}" ${product.favorite ? 'aria-pressed="true"' : ''}">
                                                    <i class="fe fe-heart"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    `
                    $('#quickviewArea').html(html);
                    var e = document.querySelectorAll('[data-toggle="form-caption"]');
                    [].forEach.call(e, function (o) {
                        o.addEventListener("change", function () {
                            var e = document.querySelector(o.dataset.target),
                                t = o.dataset.title;
                            e.innerHTML = t
                        })
                    })
                    $('#modalProduct').modal('show');
                }
            }
        }
    });
}

function productStock (size_id, variant_id, product_id,) {
    $.ajax({
        type: "POST",
        url: 'productStock',
        async: true,
        data: { size_id: size_id, variant_id: variant_id, product_id: product_id },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                if (response.arr) {
                    var product = response.arr;
                    var stockArea = '';
                    if (product.stock > 0) {
                        for (let i = 1; i <= product.stock; i++) {
                            if (i == '11'){
                                break;
                            }
                            stockArea += '<option value="'+i+'">'+i+'</option>';
                        }
                        $('.stockSelectArea').html(stockArea);
                        $('.productViewStock').removeClass('d-none');
                    }else{
                        $('.productViewStock').addClass('d-none');
                    }
                    $('#quickviewArea').html(html);
                }
            }
        }
    });
}

function getPriceArea (size_id, variant_id, product_id, id) {
    var deger = $("form#productBasketAdd").serialize();
    $.ajax({
        type: "POST",
        url: 'getPriceArea',
        async: true,
        data: deger,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                if (response.arr) {
                    var product = response.arr;
                    var priceArea = '';
                    var stockArea = '';
                    if (product.discountBool) {
                        priceArea = `
                            <div class="priceItem discountItem text-danger border-danger">
                                <span class="font-size-sm font-weight-bold">%${product.discountRate}</span>
                                <span class="font-size-xs font-weight-normal">İndirim</span>
                            </div>
                            <div class="priceItem salesPriceItem">
                                <span class="font-size-lg text-gray-350 text-decoration-line-through">${product.totalPrice} TL</span>
                                <span class="font-size-h5 font-weight-bolder text-dark">${product.discountPrice} TL</span>
                            </div>
                        `;
                        if (product.basketPrice) {
                            priceArea += `
                                <div class="priceItem bucketPriceItem">
                                    <span class="font-size-xs text-gray-500">Sepette</span>
                                    <span class="font-size-h5 font-weight-bolder text-success">${product.basketPrice} TL</span>
                                </div>
                            `;
                        }
                    }else{
                        priceArea = `
                            <div class="priceItem salesPriceItem">
                                <span class="font-size-h5 font-weight-bolder text-dark">${product.totalPrice} TL</span>
                            </div>
                        `;
                        if (product.basketPrice) {
                            priceArea += `
                                <div class="priceItem bucketPriceItem">
                                    <span class="font-size-xs text-gray-500">Sepette</span>
                                    <span class="font-size-h5 font-weight-bolder text-success">${product.basketPrice} TL</span>
                                </div>
                            `;
                        }
                    }

                    if (product.discountBool) {
                        priceAreaMobile = `
                            <div class="priceItem discountItem text-danger border-danger">
                                <span class="font-size-sm font-weight-bold">%${product.discountRate}</span>
                                <span class="font-size-xs font-weight-normal">İndirim</span>
                            </div>
                            <div class="priceItem salesPriceItem">
                                <span class="font-size-xxs text-gray-350 text-decoration-line-through">${product.totalPrice} TL</span>
                                <span class="font-size-xs font-weight-bolder text-dark">${product.discountPrice} TL</span>
                            </div>
                        `;
                        if (product.basketPrice) {
                            priceAreaMobile += `
                                div class="priceItem bucketPriceItem">
                                    <span class="font-size-xxs text-success">Sepette</span>
                                    <span class="font-size-xs font-weight-bolder text-success">${product.basketPrice} TL</span>
                                </div>
                            `;
                        }
                    }else{
                        priceAreaMobile = `
                            <div class="priceItem salesPriceItem">
                                <span class="font-size-xs font-weight-bolder text-dark">${product.totalPrice} TL</span>
                            </div>
                        `;
                        if (product.basketPrice) {
                            priceAreaMobile += `
                                div class="priceItem bucketPriceItem">
                                    <span class="font-size-xxs text-success">Sepette</span>
                                    <span class="font-size-xs font-weight-bolder text-success">${product.basketPrice} TL</span>
                                </div>
                            `;
                        }
                    }

                    if (product.stock > 0) {
                        for (let i = 1; i <= product.stock; i++) {
                            if (i == '11'){
                                break;
                            }
                            stockArea += '<option value="'+i+'">'+i+'</option>';
                        }
                    }else{
                        stockArea += '<option value="0">Stok Bulunamadı</option>';
                    }

                    $('.productPriceArea').html(priceArea);
                    $('.productPriceAreaMobileRender').html(priceAreaMobile);
                    $('.stockSelectArea').html(stockArea);
                    var pictureArea = '<div class="flickity-buttons-offset dotsBannerInline mb-md-0" id="productSlider">';
                    $.each(response.arr.productPicture, function(index, value){
                        pictureArea += `
                            <div class="w-100">
                                <a href="uploads/products/${value.image}" data-fancybox="gallery">
                                    <img class="stop productDetailTopBanner"  src="uploads/products/min/${value.image}">
                                </a>
                            </div>
                        `;
                    });
                    pictureArea += `</div>`;
                    $('#productPictureRenderArea').html(pictureArea);
                    $('#productSlider').flickity({
                        "pageDots": true,
                        "prevNextButtons": true,
                    });
                   
                    if (response.arr.variantID) {
                        $('#productBasketAdd input[name="variant_id"]').val(response.arr.variantID);
                        $('#productBasketAddDestop').text('Sepete Ekle');
                        $('#productBasketAddMobile').text('Sepete Ekle');
                        $('#productBasketAddQuick').text('Sepete Ekle');
                        $('#productBasketAddDestop').removeAttr('disabled', 'disabled');
                        $('#productBasketAddMobile').removeAttr('disabled', 'disabled');
                        $('#productBasketAddQuick').removeAttr('disabled', 'disabled');
                    }else{
                        $('#productBasketAddDestop').text('Kullanım Dışı');
                        $('#productBasketAddMobile').text('Kullanım Dışı');
                        $('#productBasketAddQuick').text('Kullanım Dışı');
                        $('#productBasketAddDestop').attr('disabled', 'disabled');
                        $('#productBasketAddMobile').attr('disabled', 'disabled');
                        $('#productBasketAddQuick').attr('disabled', 'disabled');
                    }
                }
            }
        }
    });
}

function productFavoritesAdd (product_id, variant_id = '') {
    $.ajax({
        type: "POST",
        url: 'productFavoritesAdd',
        async: true,
        data: {product_id : product_id, variant_id: variant_id },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            }else if (response.location) {
                window.location.href = response.location;
            }else{
                if (variant_id) {
                    $(".likeButton[data-variant-id="+variant_id+"]").attr('onclick', `productFavoritesRemove('${product_id}', '${variant_id}')`);
                    $(".likeButton[data-variant-id="+variant_id+"]").addClass('active');
                    toast({
                        type: 'success',
                        title: 'Ürünü favorilerinize eklediniz.',
                        padding: '2em',
                    })
                }else{
                    $(".likeButton[data-product-id="+product_id+"]").attr('onclick', `productFavoritesRemove('${product_id}', '${variant_id}')`);
                    $(".likeButton[data-product-id="+product_id+"]").addClass('active');
                    toast({
                        type: 'success',
                        title: 'Ürünü favorilerinize eklediniz.',
                        padding: '2em',
                    })
                }
            }
        }
    });
}

function productFavoritesRemove (product_id, variant_id = '') {
    $.ajax({
        type: "POST",
        url: 'productFavoritesRemove',
        async: true,
        data: {product_id : product_id, variant_id: variant_id },
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            }else if (response.location) {
                window.location.href = response.location;
            }else{
                if (variant_id) {
                    $(".likeButton[data-variant-id="+variant_id+"]").attr('onclick', `productFavoritesAdd('${product_id}', '${variant_id}')`);
                    $(".likeButton[data-variant-id="+variant_id+"]").removeClass('active');
                    toast({
                        type: 'success',
                        title: 'Ürünü favorilerinizden çıkarttınız.',
                        padding: '2em',
                    })
                }else{
                    $(".likeButton[data-product-id="+product_id+"]").attr('onclick', `productFavoritesAdd('${product_id}', '${variant_id}')`);
                    $(".likeButton[data-product-id="+product_id+"]").removeClass('active');
                    toast({
                        type: 'success',
                        title: 'Ürünü favorilerinizden çıkarttınız.',
                        padding: '2em',
                    })
                }
            }
        }
    });
}

function productFavoritesRemoveAccount (product_id, variant_id = '') {

    swal({
        title: 'Dikkat!',
        text: "İşlemi gerçekleştirmek istediğinizden emin misiniz ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Hayır',
        confirmButtonText: 'Evet, işlem gerçekleşsin!'
    }).then((result) => {
        if (result.value) {
            swal({
                title: "Lütfen Bekleyiniz...",
                onOpen: () => {
                    swal.showLoading()
                },
                showConfirmButton: false,
            });
            $.ajax({
                type: "POST",
                url: 'productFavoritesRemove',
                async: true,
                data: {product_id : product_id, variant_id: variant_id },
                dataType: "json",
                success: function (response) {
                    const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });
                    if (response.error) {
                        toast({
                            type: 'error',
                            title: response.error,
                            padding: '2em',
                        })
                    }else if (response.location) {
                        window.location.href = response.location;
                    }else{
                       $('#favoriyeID-'+ variant_id +'').remove();
                       swal.close();
                    }
                }
            });
        }
    });

}

function filter_add (orderQuery) {
    var deger = $("form#filter_add").serialize();
    var rating = $("#ratingDestop").val();
    if (rating) {
        if (deger) {
            deger = deger + '&rating[]=' + rating;
        }else{
            deger = 'rating[]=' + rating;
        }
    }else{
        if (deger) {
            deger = deger;
        }
    }
    $.ajax({
        url: "filter_add",
        type: "post",
        data:deger,
        dataType: "json",
        success: function(response) {
            var getUrl = window.location;
            if (orderQuery) {
                if (response.link) {
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '&' + orderQuery ;
                }else{
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '?' + orderQuery ;
                }
            }else{
                var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link ;
            }
            location.replace(baseUrl);
        }
    });
}

function productRatingDestop (orderQuery) {
    var deger = $("form#filter_add").serialize();
    var rating = $("#ratingDestop").val();
    if (rating) {
        if (deger) {
            deger = deger + '&rating[]=' + rating;
        }else{
            deger = 'rating[]=' + rating;
        }
    }else{
        if (deger) {
            deger = deger;
        }
    }
    $.ajax({
        url: "filter_add",
        type: "post",
        data:deger,
        dataType: "json",
        success: function(response) {
            var getUrl = window.location;
            if (orderQuery) {
                if (response.link) {
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '&' + orderQuery ;
                }else{
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '?' + orderQuery ;
                }
            }else{
                var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link ;
            }
            location.replace(baseUrl);
        }
    });
}

function productRatingMobile (orderQuery) {
    var deger = $("form#filter_add_mobile").serialize();
    var rating = $("#ratingMobile").val();
    if (rating) {
        if (deger) {
            deger = deger + '&rating[]=' + rating;
        }else{
            deger = 'rating[]=' + rating;
        }
    }else{
        if (deger) {
            deger = deger;
        }
    }
    $.ajax({
        url: "filter_add",
        type: "post",
        data:deger,
        dataType: "json",
        success: function(response) {
            var getUrl = window.location;
            if (orderQuery) {
                if (response.link) {
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '&' + orderQuery ;
                }else{
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '?' + orderQuery ;
                }
            }else{
                var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link ;
            }
            location.replace(baseUrl);
        }
    });
}

function filter_add_mobile (orderQuery) {
    var deger = $("form#filter_add_mobile").serialize();
    var rating = $("#ratingDestopMobile").val();
    if (rating) {
        if (deger) {
            deger = deger + '&rating[]=' + rating;
        }else{
            deger = 'rating[]=' + rating;
        }
    }else{
        if (deger) {
            deger = deger;
        }
    }
    $.ajax({
        url: "filter_add",
        type: "post",
        data:deger,
        dataType: "json",
        success: function(response) {
            var getUrl = window.location;
            if (orderQuery) {
                var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link + '&' + orderQuery ;
            }else{
                var baseUrl = getUrl .protocol + "//" + getUrl.host + "" + getUrl.pathname + response.link ;
            }
            location.replace(baseUrl);
        }
    });
}

function productBasketAdd () {
    var data = $("#productBasketAdd").serialize();
    $.ajax({
        type: "POST",
        url: 'productBasketAdd',
        async: true,
        data: data,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
                $('.basketCountArea').attr('data-cart-items', response.basketCount);
                var basketHtml = '';
                var basketSizeHtml = '';
                var thisProductAreaSize = $('.productItem-'+`${response.basketProduct.variant_id ? response.basketProduct.variant_id : response.basketProduct.product_id}`).length;
                for (let index = 1; index <= response.basketProduct.max_stock; index++) {
                    basketSizeHtml += `<option ${index == response.basketProduct.piece ? 'selected' : ''} value="${index}">${index}</option>`;
                }
                if (thisProductAreaSize < 1) {
                    basketHtml += `<li class="list-group-item productItem-${response.basketProduct.variant_id ? response.basketProduct.variant_id : response.basketProduct.product_id}" id="deleteItemArea-${response.basketProduct.variant_id ? response.basketProduct.variant_id : response.basketProduct.product_id}">`
                }
                var priceArea;
                if (response.basketProduct.header_basket_price == response.basketProduct.last_price) {
                    priceArea = `<span class="font-size-xs font-weight-bolder text-dark"  id="priceAreaOneHeader${response.basketProduct.id}">${response.basketProduct.header_basket_price} TL</span>`;
                }else {
                    priceArea = `
                        <span class="font-size-xxs text-gray-350 text-decoration-line-through" id="priceAreaOneHeader${response.basketProduct.id}">${response.basketProduct.header_basket_price} TL</span>
                        <span class="font-size-xs font-weight-bolder text-dark" id="priceAreaTwoHeader${response.basketProduct.id}">${response.basketProduct.last_price} TL</span>
                    `;
                    
                }
                if (response.basketProduct.basket_price && response.basketProduct.basketRate) {
                    priceArea += `
                        <br>
                        <span class="font-size-xxs font-weight-bolder text-success" id="priceAreaThreeHeader${response.basketProduct.id}">Sepete Özel ${response.basketProduct.basket_price} TL</span>
                    `;
                       
                }
                var combinationArea = '';
                $.each(response.basketProduct.combanition ,function(index, value){
                    combinationArea += ''+ value.group_title +': '+ value.title +' <br>';
                });
                basketHtml += `
                        <div class="row align-items-center">
                            <div class="col-4">

                                <!-- Image -->
                                <a href="${response.basketProduct.link}">
                                    <img class="img-fluid" src="${response.basketProduct.image}" alt="${response.basketProduct.title}">
                                </a>

                            </div>
                            <div class="col-8">

                                <!-- Title -->
                                <p class="font-size-sm font-weight-bold mb-6">
                                    <a class="text-body" href="${response.basketProduct.link}">${response.basketProduct.title}</a> <br>
                                    ${priceArea}
                                </p>
                                <p class="mb-7 font-size-sm text-muted">
                                    ${combinationArea}
                                </p>
                                <!--Footer -->
                                <div class="d-flex align-items-center">

                                    <!-- Select -->
                                    <select class="custom-select custom-select-xxs w-auto" onchange="miniSingleValue(this.value, ${response.basketProduct.variant_id ? response.basketProduct.variant_id : response.basketProduct.product_id}, 'basketProductPiece');">
                                       ${basketSizeHtml}
                                    </select>

                                    <!-- Remove -->
                                    <a class="font-size-xs text-gray-400 ml-auto" href="javascript:void(0)" onclick="miniSingle(${response.basketProduct.variant_id ? response.basketProduct.variant_id : response.basketProduct.product_id}, 'basketProductDelete', 'deleteItem')">
                                        <i class="fe fe-x"></i> Sepetimden Çıkart
                                    </a>

                                </div>

                            </div>
                        </div>`;
                if (thisProductAreaSize < 1) {
                    basketHtml += `</li>`
                    $('#headerBasketArea').append(basketHtml);
                }else{
                    $('.productItem-'+`${response.basketProduct.variant_id ? response.basketProduct.variant_id : response.basketProduct.product_id}`).html(basketHtml);
                }
                
                $('#headerBasketPrice').html(response.header_basket_price + ' TL');
                $('#headerBasketCount').html($('#headerBasketArea li').length);

                if ($('#headerBasketArea li').length <= 0) {
                    $('#headerBasketProduct').addClass('d-none');
                    $('#headerBasketEmpty').removeClass('d-none');
                }else{
                    $('#headerBasketProduct').removeClass('d-none');
                    $('#headerBasketEmpty').addClass('d-none');
                }
                $('#disconce_price_area_header').html(response.disconce_price + ' TL');
                $('#basketTotalPriceTotalHeader').html(response.basket_total_price_first + 'TL');
                $('#shipping_price_area_header').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                $('#shipping_price_area').html(response.free_shipping ? response.free_shipping : 'Ücretsiz Kargo');
                $('#basketTotalPriceFirstHeader').html(response.headerBasketPriceSesion + ' TL');
            }
        }
    });
}

$.fn.isInViewport = function() {
    var elementTop = $(this).offset().top + ( $(this).height() / 2 );
    var elementBottom = elementTop + $(this).outerHeight();
    
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();
    
    return elementBottom > viewportTop && elementTop < viewportBottom;
};

$(window).on('resize scroll', function() {
    var area = $('#sizeSelectedArea');
    if (area.isInViewport()) { 
        $('body').addClass('scroleActive');
    } else { 
        $('body').removeClass('scroleActive');
    } 
});

function scrollSizeAreaGo() {
    $('html, body').animate({
        scrollTop: $("#sizeSelectedArea").offset().top - 280
    }, 1000);
}

function checkoutNewAddressForm  () {
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    var data = $("#checkout_new_address").serialize();
    $.ajax({
        type: "POST",
        url: 'userNewAddress',
        async: true,
        data: data,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
                var addressContent = `
                    <tr deleteItem${response.returnData.id}>
                        <td>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" id="userAdress${response.returnData.id}" checked name="address" value="${response.returnData.id}" type="radio">
                                <label class="custom-control-label text-body text-nowrap" for="userAdress${response.returnData.id}">
                                    ${response.returnData.title}
                                </label>
                            </div>
                        </td>
                        <td>
                            ${response.returnData.address}
                            ${response.returnData.town} /  ${response.returnData.city}
                        </td>
                        <td>
                            <button type="button" class="btn btn-xs btn-circle btn-pinterest" onclick="getCheckoutEditAddressForm('${response.returnData.id}')">
                                <i class="fe fe-edit-2"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('.checkoutAddressContent').append(addressContent);
                $('.checkoutAddressArea').removeClass('d-none');
                $('.checkoutEmpytAddress').addClass('d-none');
                $('#modalNewAddress').modal('hide');
                $("#checkout_new_address").trigger("reset");
            }
        }
    });
}

function checkoutNewBillingAddressForm  () {
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    var data = $("#checkout_new_billing_address").serialize();
    $.ajax({
        type: "POST",
        url: 'userNewAddress',
        async: true,
        data: data,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
                if (response.returnData.billing_type == '1'){
                    var billingTitle = '<b>T.C Kimlik :</b>';
                    var billingValue =  response.returnData.identification_number;
                    var billingContent = `
                        <td>
                            <small>${billingTitle} ${billingValue}</small><br>
                            <small><b>Vergi Dairesi :</b> ${response.returnData.tax_administration}</small>
                        </td>
                    `;
                }else if (response.returnData.billing_type == '2') {
                    var billingTitle = '<b>Vergi Numarası :</b>';
                    var billingValue =  response.returnData.tax_number;
                    var billingContent = `
                        <td>
                            <small>${billingTitle} ${billingValue}</small><br>
                            <small><b>Vergi Dairesi :</b> ${response.returnData.tax_administration}</small>
                        </td>
                    `;
                }
                var addressContent = `
                    <tr deleteItem${response.returnData.id}>
                        <td>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" id="userAdress${response.returnData.id}" checked name="billing_address" value="${response.returnData.id}" type="radio">
                                <label class="custom-control-label text-body text-nowrap" for="userAdress${response.returnData.id}">
                                    ${response.returnData.title}
                                </label>
                            </div>
                        </td>
                        <td>
                            ${response.returnData.address}
                            ${response.returnData.town} /  ${response.returnData.city}
                        </td>
                        ${billingContent}
                        <td>
                            <button type="button" class="btn btn-xs btn-circle btn-pinterest" onclick="getCheckoutEditBillingAddressForm('${response.returnData.id}')">
                                <i class="fe fe-edit-2"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('.checkoutBillingAddressContent').append(addressContent);
                $('.checkoutBillingAddressArea').removeClass('d-none');
                $('.checkoutEmpytBillingAddress').addClass('d-none');
                $('#modalNewBillingAddress').modal('hide');
                $("#checkout_new_billing_address").trigger("reset");
            }
        }
    });
}

function getCheckoutEditAddressForm (id) {
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    $.ajax({
        type: "POST",
        url: 'getCheckoutEditAddressForm',
        async: true,
        data: {id : id},
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                var cityArea = '';
                $.each(response.city ,function(index, value){
                    if (response.getAddress.user_city == value.CityID) {
                        var selected = 'selected';
                    }else{
                        var selected = '';
                    }
                    cityArea += `
                        <option ${selected} value="${value.CityID}">${value.CityName}</option>
                    `;
                });

                var townArea = '';
                $.each(response.town ,function(index, value){
                    if (response.getAddress.user_town == value.TownID) {
                        var selected = 'selected';
                    }else{
                        var selected = '';
                    }
                    townArea += `
                        <option ${selected} value="${value.TownID}">${value.TownName}</option>
                    `;
                });

                var neighborhoodArea = '';
                $.each(response.neighborhood ,function(index, value){
                    if (response.getAddress.user_neighborhood == value.NeighborhoodID) {
                        var selected = 'selected';
                    }else{
                        var selected = '';
                    }
                    neighborhoodArea += `
                        <option ${selected} value="${value.NeighborhoodID}">${value.NeighborhoodName}</option>
                    `;
                });

                var addressContent = `
                    <input type="hidden" value="${response.getAddress.id}" name="userAddressID">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="title">Adres Başlığı <i class="text-danger">*</i></label>
                                <input class="form-control" id="title" name="title" type="text" value="${response.getAddress.title}" placeholder="Adres Başlığı*" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="receiver_name">Teslim Alacak Kişi <i class="text-danger">*</i></label>
                                <input class="form-control" id="receiver_name" name="receiver_name" value="${response.getAddress.receiver_name}" type="text" placeholder="Teslim Alacak Kişi*" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="emailAddress">E-posta <i class="text-danger">*</i></label>
                                <input class="form-control" id="emailAddress" name="email" value="${response.getAddress.email}" type="email" placeholder="E-posta Adresi" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="phoneNumberEdit2">Cep Telefonu <i class="text-danger">*</i></label>
                                <input class="form-control" id="phoneNumberEdit2" name="phone" value="${response.getAddress.phone}" type="text" placeholder="" required data-inputmask-clearincomplete="true">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cityArea">İl <i class="text-danger">*</i></label>
                                <select class="custom-select" id="cityAreaEdit" name="user_city" onchange="citySelectd(this)" name="city_id">
                                    <option value="0" selected>İl Seçiniz</option>
                                    ${cityArea}
                                </select>
                            </div>

                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="townArea">İlçe <i class="text-danger">*</i></label>
                                <select class="custom-select" id="townAreaEdit" name="user_town" onchange="townSelectd(this)" name="town_id">
                                    <option value="0" selected>Seçiniz.</option>
                                    ${townArea}
                                </select>
                            </div>

                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="neighborhoodArea">Mahalle <i class="text-danger">*</i></label>
                                <select class="custom-select" id="neighborhoodAreaEdit" name="user_neighborhood">
                                    <option value="0" selected>Seçiniz</option>
                                    ${neighborhoodArea}
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="post_code">Posta Kodu</label>
                                <input class="form-control" id="post_code" name="post_code" value="${response.getAddress.post_code}" type="tel" placeholder="">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="identification_number_2">T.C Kimlik No <i class="text-danger">*</i></label>
                                <input class="form-control" id="identification_number_2" data-inputmask-clearincomplete="true" value="${response.getAddress.identification_number ? response.getAddress.identification_number : ''}" data-inputmask="'mask': '99999999999'" name="identification_number" value="" type="tel" inputmode="text">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="country">Adres <i class="text-danger">*</i></label>
                                <textarea class="form-control" name="address" id="country" type="text" required>${response.getAddress.address}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="defaultDeliveryAddress" value="1" ${response.getAddress.address_default == '1' ? 'checked' : ''} name="address_default">
                                    <label class="custom-control-label" for="defaultDeliveryAddress">Bu adresi varsayılan adres olarak kullanmak istiyorum.</label>
                                </div> 
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                            <span class="font-size-xs text-gray-500"><i class="text-danger">*</i> Doldurulması zorunlu alanlar</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary " type="submit">
                        Kaydet
                    </button>
                `;
                $('#checkout_edit_address').html(addressContent);
                $('#modalEditAddress').modal('show');
                $("#phoneNumberEdit2").inputmask({'mask': '+\\90(999)-999-99-99'});
                $("#identification_number_2").inputmask({'mask': '99999999999'});
                swal.close();
            }
        }
    });
}

function getCheckoutEditBillingAddressForm (id) {
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    $.ajax({
        type: "POST",
        url: 'getCheckoutEditAddressForm',
        async: true,
        data: {id : id},
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                var cityArea = '';
                $.each(response.city ,function(index, value){
                    if (response.getAddress.user_city == value.CityID) {
                        var selected = 'selected';
                    }else{
                        var selected = '';
                    }
                    cityArea += `
                        <option ${selected} value="${value.CityID}">${value.CityName}</option>
                    `;
                });

                var townArea = '';
                $.each(response.town ,function(index, value){
                    if (response.getAddress.user_town == value.TownID) {
                        var selected = 'selected';
                    }else{
                        var selected = '';
                    }
                    townArea += `
                        <option ${selected} value="${value.TownID}">${value.TownName}</option>
                    `;
                });

                var neighborhoodArea = '';
                $.each(response.neighborhood ,function(index, value){
                    if (response.getAddress.user_neighborhood == value.NeighborhoodID) {
                        var selected = 'selected';
                    }else{
                        var selected = '';
                    }
                    neighborhoodArea += `
                        <option ${selected} value="${value.NeighborhoodID}">${value.NeighborhoodName}</option>
                    `;
                });
                
                var addressContent = `
                    <input type="hidden" value="${response.getAddress.id}" name="userAddressID">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="title">Fatura Başlığı <i class="text-danger">*</i></label>
                                <input class="form-control" id="title" name="title" type="text" value="${response.getAddress.title}" placeholder="Fatura Başlığı*" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="receiver_name">Ad Soyad / Firma <i class="text-danger">*</i></label>
                                <input class="form-control" id="receiver_name" name="receiver_name" value="${response.getAddress.receiver_name}" type="text" placeholder="Ad Soyad / Firma*" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="emailAddress">E-posta <i class="text-danger">*</i></label>
                                <input class="form-control" id="emailAddress" name="email" value="${response.getAddress.email}" type="email" placeholder="E-posta Adresi" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="phoneNumberEdit2">Cep Telefonu <i class="text-danger">*</i></label>
                                <input class="form-control" id="phoneNumberEdit2" name="phone" value="${response.getAddress.phone}" type="text" placeholder="" required data-inputmask-clearincomplete="true" >
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cityArea">İl <i class="text-danger">*</i></label>
                                <select class="custom-select" id="cityAreaEdit" name="user_city" onchange="citySelectd(this)" name="city_id">
                                    <option value="0" selected>İl Seçiniz</option>
                                    ${cityArea}
                                </select>
                            </div>

                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="townArea">İlçe <i class="text-danger">*</i></label>
                                <select class="custom-select" id="townAreaEdit" name="user_town" onchange="townSelectd(this)" name="town_id">
                                    <option value="0" selected>Seçiniz.</option>
                                    ${townArea}
                                </select>
                            </div>

                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="neighborhoodArea">Mahalle <i class="text-danger">*</i></label>
                                <select class="custom-select" id="neighborhoodAreaEdit" name="user_neighborhood">
                                    <option value="0" selected>Seçiniz</option>
                                    ${neighborhoodArea}
                                </select>
                            </div>

                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="post_code">Posta Kodu</label>
                                <input class="form-control" id="post_code" name="post_code" value="${response.getAddress.post_code}" type="tel" placeholder="">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="country">Adres <i class="text-danger">*</i></label>
                                <textarea class="form-control" name="address" id="country" type="text" required>${response.getAddress.address}</textarea>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-around">
                            <div class="custom-control custom-radio">
                                <input ${response.getAddress.billing_type == '1' ? 'checked' : ''} class="custom-control-input billingTypeEdit" id="checkoutShippingStandard6" value="1" name="billingTypeEdit" type="radio">
                                <label class="custom-control-label text-body text-nowrap" for="checkoutShippingStandard6">
                                    Bireysel
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input ${response.getAddress.billing_type == '2' ? 'checked' : ''} class="custom-control-input billingTypeEdit" id="checkoutShippingStandard7" value="2" name="billingTypeEdit" type="radio">
                                <label class="custom-control-label text-body text-nowrap" for="checkoutShippingStandard7">
                                    Kurumsal
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 ${response.getAddress.billing_type == '2' ? 'd-none' : ''}" id="individualAreaEdit">
                            <div class="form-group">
                                <label for="identification_number_edit">T.C Kimlik No <i class="text-danger">*</i></label>
                                <input ${response.getAddress.billing_type == '2' ? 'disabled' : ''} class="form-control" id="identification_number_edit" name="identification_number" value="${response.getAddress.identification_number}" type="text" placeholder="T.C Kimlik No*" data-inputmask-clearincomplete="true" data-inputmask="'mask': '99999999999'" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 ${response.getAddress.billing_type == '1' ? 'd-none' : ''}" id="corporateAreaEdit">
                            <div class="form-group">
                                <label for="tax_number">Vergi Numarası <i class="text-danger">*</i></label>
                                <input ${response.getAddress.billing_type == '1' ? 'disabled' : ''} class="form-control" id="tax_number" name="tax_number" value="${response.getAddress.tax_number}" type="text" placeholder="Vergi Numarası*" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="tax_administration">Vergi Dairesi <i class="text-danger">*</i></label>
                                <input class="form-control" id="tax_administration" name="tax_administration" value="${response.getAddress.tax_administration}" type="text" placeholder="Vergi Dairesi*" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                            <span class="font-size-xs text-gray-500"><i class="text-danger">*</i> Doldurulması zorunlu alanlar</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary " type="submit">
                        Kaydet
                    </button>
                `;
                $('#checkout_edit_address').html(addressContent);
                $('#modalEditAddress').modal('show');
                $('.billingTypeEdit').on('change', function() {
                    if (this.value == '1') {
                        $('#corporateAreaEdit').addClass('d-none');
                        $('#individualAreaEdit').removeClass('d-none');
                        $('#corporateAreaEdit input').attr('disabled', 'disabled');
                        $('#individualAreaEdit input').removeAttr('disabled', 'disabled');
                    }else if (this.value == '2') {
                        $('#individualAreaEdit').addClass('d-none');
                        $('#corporateAreaEdit').removeClass('d-none');
                        $('#individualAreaEdit input').attr('disabled', 'disabled');
                        $('#corporateAreaEdit input').removeAttr('disabled', 'disabled');
                    }
                });
                $("#identification_number_edit").inputmask({"mask": "99999999999"});
                $("#tax_number").inputmask({"mask": "9999999999"});
                $("#phoneNumberEdit2").inputmask({'mask': '+\\90(999)-999-99-99'});
                swal.close();
            }
        }
    });
}

function checkoutEditAddressForm  () {
    swal({
        title: "Lütfen Bekleyiniz...",
        onOpen: () => {
            swal.showLoading()
        },
        showConfirmButton: false,
    });
    var data = $("#checkout_edit_address").serialize();
    $.ajax({
        type: "POST",
        url: 'userEditAddress',
        async: true,
        data: data,
        dataType: "json",
        success: function (response) {
            const toast = swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                padding: '2em'
            });
            if (response.error) {
                toast({
                    type: 'error',
                    title: response.error,
                    padding: '2em',
                })
            } else {
                toast({
                    type: 'success',
                    title: response.success,
                    padding: '2em',
                })
                if (response.returnData.billing_type == '1'){
                    var billingTitle = '<b>T.C Kimlik :</b>';
                    var billingValue =  response.returnData.identification_number;
                    var billingContent = `
                        <td>
                            <small>${billingTitle} ${billingValue}</small><br>
                            <small><b>Vergi Dairesi :</b> ${response.returnData.tax_administration}</small>
                        </td>
                    `;
                    var onclick = 'getCheckoutEditBillingAddressForm';
                    var name = 'billing_address';
                }else if (response.returnData.billing_type == '2') {
                    var billingTitle = '<b>Vergi Numarası :</b>';
                    var billingValue =  response.returnData.tax_number;
                    var billingContent = `
                        <td>
                            <small>${billingTitle} ${billingValue}</small><br>
                            <small><b>Vergi Dairesi :</b> ${response.returnData.tax_administration}</small>
                        </td>
                    `;
                    var onclick = 'getCheckoutEditBillingAddressForm';
                    var name = 'billing_address';
                }else {
                    var onclick = 'getCheckoutEditAddressForm';
                    var name = 'address';
                }
                var addressContent = `
                    <td>
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" id="userAdress${response.returnData.id}" checked name="${name}" value="${response.returnData.id}" type="radio">
                            <label class="custom-control-label text-body text-nowrap" for="userAdress${response.returnData.id}">
                                ${response.returnData.title}
                            </label>
                        </div>
                    </td>
                    <td>
                        ${response.returnData.address}
                        ${response.returnData.town} /  ${response.returnData.city}
                    </td>
                    ${billingContent}
                    <td>
                        <button type="button" class="btn btn-xs btn-circle btn-pinterest" onclick="${onclick}('${response.returnData.id}')">
                            <i class="fe fe-edit-2"></i>
                        </button>
                    </td>
                `;
                $('#userAddressArea-'+response.returnData.id+'').html(addressContent);
                $('.checkoutAddressArea').removeClass('d-none');
                $('.checkoutEmpytAddress').addClass('d-none');
                $('#modalEditAddress').modal('hide');
                $("#checkout_new_address").trigger("reset");
            }
        }
    });
}

function orderStart () {
    $.ajax({
        url: "orderStart",
        type: "post",
        dataType: "json",
        success: function(response) {
            window.location.href = 'siparis';
        }
    });
}

$(".searchForm").click(function(event){
    $('#desktopSearchSide').removeClass('d-none');
    lastVisitedSearchHeader();
});


function lastVisitedSearchHeader () {
    const lastVisited = JSON.parse(window.localStorage.getItem('lastVisited'));
    if (lastVisited) {
        $.ajax({
            type: "POST",
            url: 'lastVisited',
            data: { lastVisited: lastVisited},
            dataType: "json",
            success: function (response) {
                $('#lastVisitedHeaderSearchArea').html('');
                var htmlHeaderSearch = '' ;

                if (response.arr) {
                    $.each(response.arr,function(index, value){
                        if (index < 4) {
                            var imageArea = '' ;
                            var priceArea = '' ;
                            if (value.discountBool) {
                                priceArea = `
                                    <span class="font-size-xs text-gray-350 text-decoration-line-through">${value.totalPrice} TL</span>
                                    <span class="text-primary">${value.discountPrice} TL</span>
                                `;
                            }else{
                                priceArea = `
                                    <span class="text-primary">${value.totalPrice} TL</span>
                                `;
                            }
    
                            $.each(value.image,function(index, item){
                        
                                if (index == '0') {
                                    imageArea += `
                                        <img class="card-img-top " src="${item}" alt="${value.title}">
                                    `
                                }
                            });
    
                            htmlHeaderSearch += `
                                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                    <div class="card mb-1">
                                        
                                        ${value.discountBool ? '<div class="badge badge-danger card-badge card-badge-left text-uppercase">'+value.discountRate +' </div>' : ''}
                                        
                                        <div class="card-img">
    
                                            <a class="" href="${value.link}">
                                                ${imageArea}
                                            </a>
                                        </div>
    
                                        <div class="card-body pb-1 pt-1 px-0">
                                            <div class="font-size-xs">
                                                <a class="text-muted" href="${value.link}">${value.brand}</a>
                                            </div>
                                            <div class="font-weight-bold headerSearchProductTitle">
                                                <a class="text-body doubleLineElement" href="${value.link}">
                                                    ${value.title}
                                                </a>
                                            </div>
    
                                            <div class="font-weight-bold">
                                                ${priceArea}
                                            </div>
    
                                        </div>
    
                                    </div>
    
                                </div>
                            `;
                        }
                    });
                    $('#lastVisitedHeaderSearchArea').html(htmlHeaderSearch);
                    $('.lastVisitedHeaderSearchContent').removeClass('d-none');
                }
            }
        });
    }
}

function lastVisitedSearchHeaderMobile () {
    const lastVisited = JSON.parse(window.localStorage.getItem('lastVisited'));
    if (lastVisited) {
        $.ajax({
            type: "POST",
            url: 'lastVisited',
            data: { lastVisited: lastVisited},
            dataType: "json",
            success: function (response) {
                $('#lastVisitedHeaderSearchAreaMobile').html('');
                var htmlHeaderSearch = '' ;

                if (response.arr) {
                    $.each(response.arr,function(index, value){
                        if (index < 4) {
                            var imageArea = '' ;
                            var priceArea = '' ;
                            if (value.discountBool) {
                                priceArea = `
                                    <span class="font-size-xs text-gray-350 text-decoration-line-through">${value.totalPrice} TL</span>
                                    <span class="text-primary">${value.discountPrice} TL</span>
                                `;
                            }else{
                                priceArea = `
                                    <span class="text-primary">${value.totalPrice} TL</span>
                                `;
                            }
    
                            $.each(value.image,function(index, item){
                        
                                if (index == '0') {
                                    imageArea += `
                                        <img class="card-img-top " src="${item}" alt="${value.title}">
                                    `
                                }
                            });
    
                            htmlHeaderSearch += `
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card mb-1">
                                        
                                        ${value.discountBool ? '<div class="badge badge-danger card-badge card-badge-left text-uppercase">'+value.discountRate +' </div>' : ''}
                                        
                                        <div class="card-img">
    
                                            <a class="" href="${value.link}">
                                                ${imageArea}
                                            </a>
                                        </div>
    
                                        <div class="card-body pb-1 pt-1 px-0">
                                            <div class="font-size-xs">
                                                <a class="text-muted" href="${value.link}">${value.brand}</a>
                                            </div>
                                            <div class="font-weight-bold headerSearchProductTitle">
                                                <a class="text-body doubleLineElement" href="${value.link}">
                                                    ${value.title}
                                                </a>
                                            </div>
    
                                            <div class="font-weight-bold">
                                                ${priceArea}
                                            </div>
    
                                        </div>
    
                                    </div>
    
                                </div>
                            `;
                        }
                    });
                    $('#lastVisitedHeaderSearchAreaMobile').html(htmlHeaderSearch);
                    $('.lastVisitedHeaderSearchContentMobile').removeClass('d-none');
                }
            }
        });
    }
}

$('#searchForm').donetyping(function(){
    var search = $(this).val();
    desktopSearch(search);
}, 500);

$('#searchFormMobile').donetyping(function(){
    var search = $(this).val();
    mobileSearch(search);
}, 500);

function desktopSearch (value = '') {
    if (value) {
        $('#desktopSearchArea').removeClass('d-none');
        $('#desktopSearchNavArea').addClass('d-none');
        $('#desktopSearchLinkArea').addClass('opacity-10');
        $('.productHeaderSearchContent').addClass('opacity-10');
        $('.headerSearchSpiner').removeClass('d-none');
        $.ajax({
            type: "POST",
            url: 'desktopSearch',
            data: { searchKey: value},
            dataType: "json",
            success: function (response) {
                var searchResponseArea = '';
                $('#desktopSearchLinkArea').html('');
                $('#productHeaderSearchArea').html('');
                console.log(response);
                if (response.data || response.arr) {
                    if (response.data) {
                        searchResponseArea += `
                            <div class="searchLinkArea">
                                <ul>
                        `;
                        $.each(response.data, function(index, variable){
                            $.each(variable, function(key, item){
                                if (index == 'brand') {
                                    searchResponseArea += `
                                        <li>
                                            <a href="${item.slug}-b-${item.id}?searchKey=${value}" class="linkSearchTitle">${item.title}
                                                <span class="linkSearchType">Marka</span>    
                                            </a>
                                        </li>
                                    `;  
                                }else if (index == 'categories') {
                                    searchResponseArea += `
                                        <li>
                                            <a href="${item.slug}-c-${item.id}?searchKey=${value}" class="linkSearchTitle">${item.title}
                                                <span class="linkSearchType">Kategori</span>    
                                            </a>
                                        </li>
                                    `;  
                                }else if (index == 'camping') {
                                    searchResponseArea += `
                                        <li>
                                            <a href="kampanya/${item.slug}-c-${item.id}?searchKey=${value}" class="linkSearchTitle">${item.title}
                                                <span class="linkSearchType">Kampanya</span>    
                                            </a>
                                        </li>
                                    `;  
                                }
                            });
                        });
                        searchResponseArea += `
                                </ul>
                            </div>  
                        `;  
                    }
                    if (response.arr) {
                        var htmlHeaderSearch = '';
                        $.each(response.arr,function(index, value){
                            if (index < 4) {
                                var imageArea = '' ;
                                var priceArea = '' ;
                                if (value.discountBool) {
                                    priceArea = `
                                        <span class="font-size-xs text-gray-350 text-decoration-line-through">${value.totalPrice} TL</span>
                                        <span class="text-primary">${value.discountPrice} TL</span>
                                    `;
                                }else{
                                    priceArea = `
                                        <span class="text-primary">${value.totalPrice} TL</span>
                                    `;
                                }
        
                                $.each(value.image,function(index, item){
                            
                                    if (index == '0') {
                                        imageArea += `
                                            <img class="card-img-top " src="${item}" alt="${value.title}">
                                        `
                                    }
                                });
        
                                htmlHeaderSearch += `
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                                        <div class="card mb-1">
                                            
                                            ${value.discountBool ? '<div class="badge badge-danger card-badge card-badge-left text-uppercase">'+value.discountRate +' </div>' : ''}
                                            
                                            <div class="card-img">
        
                                                <a class="" href="${value.link}">
                                                    ${imageArea}
                                                </a>
                                            </div>
        
                                            <div class="card-body pb-1 pt-1 px-0">
                                                <div class="font-size-xs">
                                                    <a class="text-muted" href="${value.link}">${value.brand}</a>
                                                </div>
                                                <div class="font-weight-bold headerSearchProductTitle">
                                                    <a class="text-body doubleLineElement" href="${value.link}">
                                                        ${value.title}
                                                    </a>
                                                </div>
        
                                                <div class="font-weight-bold">
                                                    ${priceArea}
                                                </div>
        
                                            </div>
        
                                        </div>
        
                                    </div>
                                `;
                            }
                        });
                        $('#productHeaderSearchArea').html(htmlHeaderSearch);
                        $('.productHeaderSearchContent').removeClass('d-none');
                    }else{
                        $('.productHeaderSearchContent').addClass('d-none');
                    }
                }else{
                    searchResponseArea = `
                        <div class="p-6">

                            <!-- Text -->
                            <p class="mb-3 font-size-sm text-center">
                                Üzgünüz... Sonuç Bulunamadı!
                            </p>
                            <p class="mb-0 font-size-sm text-center">
                                😞
                            </p>

                        </div>
                    `;
                    $('.productHeaderSearchContent').addClass('d-none'); 
                }
                $('#desktopSearchLinkArea').html(searchResponseArea);
                $('#desktopSearchLinkArea').removeClass('opacity-10');
                $('.productHeaderSearchContent').removeClass('opacity-10');
                $('.headerSearchSpiner').addClass('d-none');
            }
        });
        
    }else{
        $('#desktopSearchNavArea').removeClass('d-none');
        $('#desktopSearchArea').addClass('d-none');
    }
}

function mobileSearch (value = '') {
    if (value) {
        $('#mobileSearchArea').removeClass('d-none');
        $('#mobileSearchNavArea').addClass('d-none');
        $('#mobileSearchLinkArea').addClass('opacity-10');
        $('.productHeaderSearchContentMobile').addClass('opacity-10');
        $('.headerSearchSpiner').removeClass('d-none');
        $.ajax({
            type: "POST",
            url: 'desktopSearch',
            data: { searchKey: value},
            dataType: "json",
            success: function (response) {
                var searchResponseArea = '';
                $('#mobileSearchLinkArea').html('');
                $('#productHeaderSearchAreaMobile').html('');
                
                if (response.data || response.arr) {
                    if (response.data) {
                        searchResponseArea += `
                            <div class="searchLinkArea">
                                <ul>
                        `;
                        $.each(response.data, function(index, variable){
                            $.each(variable, function(key, item){
                                if (index == 'brand') {
                                    searchResponseArea += `
                                        <li>
                                            <a href="${item.slug}-b-${item.id}?searchKey=${value}" class="linkSearchTitle">${item.title}
                                                <span class="linkSearchType">Marka</span>    
                                            </a>
                                        </li>
                                    `;  
                                }else if (index == 'categories') {
                                    searchResponseArea += `
                                        <li>
                                            <a href="${item.slug}-c-${item.id}?searchKey=${value}" class="linkSearchTitle">${item.title}
                                                <span class="linkSearchType">Kategori</span>    
                                            </a>
                                        </li>
                                    `;  
                                }else if (index == 'camping') {
                                    searchResponseArea += `
                                        <li>
                                            <a href="kampanya/${item.slug}-c-${item.id}?searchKey=${value}" class="linkSearchTitle">${item.title}
                                                <span class="linkSearchType">Kampanya</span>    
                                            </a>
                                        </li>
                                    `;  
                                }
                            });
                        });
                        searchResponseArea += `
                                </ul>
                            </div>  
                        `;  
                    }
                    if (response.arr) {
                        var htmlHeaderSearch = '';
                        $.each(response.arr,function(index, value){
                            if (index < 4) {
                                var imageArea = '' ;
                                var priceArea = '' ;
                                if (value.discountBool) {
                                    priceArea = `
                                        <span class="font-size-xs text-gray-350 text-decoration-line-through">${value.totalPrice} TL</span>
                                        <span class="text-primary">${value.discountPrice} TL</span>
                                    `;
                                }else{
                                    priceArea = `
                                        <span class="text-primary">${value.totalPrice} TL</span>
                                    `;
                                }
        
                                $.each(value.image,function(index, item){
                            
                                    if (index == '0') {
                                        imageArea += `
                                            <img class="card-img-top " src="${item}" alt="${value.title}">
                                        `
                                    }
                                });
        
                                htmlHeaderSearch += `
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="card mb-1">
                                            
                                            ${value.discountBool ? '<div class="badge badge-danger card-badge card-badge-left text-uppercase">'+value.discountRate +' </div>' : ''}
                                            
                                            <div class="card-img">
        
                                                <a class="" href="${value.link}">
                                                    ${imageArea}
                                                </a>
                                            </div>
        
                                            <div class="card-body pb-1 pt-1 px-0">
                                                <div class="font-size-xs">
                                                    <a class="text-muted" href="${value.link}">${value.brand}</a>
                                                </div>
                                                <div class="font-weight-bold headerSearchProductTitle">
                                                    <a class="text-body doubleLineElement" href="${value.link}">
                                                        ${value.title}
                                                    </a>
                                                </div>
        
                                                <div class="font-weight-bold">
                                                    ${priceArea}
                                                </div>
        
                                            </div>
        
                                        </div>
        
                                    </div>
                                `;
                            }
                        });
                        $('#productHeaderSearchAreaMobile').html(htmlHeaderSearch);
                        $('.productHeaderSearchContentMobile').removeClass('d-none');
                    }else{
                        $('.productHeaderSearchContentMobile').addClass('d-none');
                    }
                }else{
                    searchResponseArea = `
                        <div class="p-6">

                            <!-- Text -->
                            <p class="mb-3 font-size-sm text-center">
                                Üzgünüz... Sonuç Bulunamadı!
                            </p>
                            <p class="mb-0 font-size-sm text-center">
                                😞
                            </p>

                        </div>
                    `;
                    $('.productHeaderSearchContentMobile').addClass('d-none'); 
                }
                $('#mobileSearchLinkArea').html(searchResponseArea);
                $('.headerSearchSpiner').addClass('d-none');
                $('#mobileSearchLinkArea').removeClass('opacity-10');
                $('.productHeaderSearchContentMobile').removeClass('opacity-10');
            }
        });
        
    }else{
        $('#mobileSearchNavArea').removeClass('d-none');
        $('#mobileSearchArea').addClass('d-none');
    }
}

var searchKeyLocale = JSON.parse(window.localStorage.getItem('searchKeyLocale'));

if (searchKeyLocale != '' && searchKeyLocale != null ) {
    var lastSearchKeyArea = '';
    $.each(searchKeyLocale, function(index, value){
        lastSearchKeyArea += `
            <div class="custom-control custom-control-inline custom-control-size mb-2 lastSearchKey-${value.searchKeySlug}">
                <span class="custom-control-label font-size-xxxs"> <a class="text-body" href="${value.searchLink}"> ${value.searchKey} </a>
                    <a class="text-reset ml-2" onclick="lastSearchKeyremove('${value.searchKeySlug}')" href="javascript:void(0)" role="button">
                        <i class="fe fe-x"></i>
                    </a>
                </span>
            </div>
        `;
    });
    $('.lastSearchKeyContent').html(lastSearchKeyArea);
    $('.lastSearchKeyArea').removeClass('d-none');
    $('.lastSearchKeyContentArea').removeClass('d-none');
}else{
    $('.lastSearchKeyArea').html('');
    $('.lastSearchKeyArea').addClass('d-none');
    $('.lastSearchKeyContentArea').addClass('d-none');
}

function lastSearchKeyremove (item) {
    var newSearchKeyLocale = [];
    var searchKeyLocale = JSON.parse(window.localStorage.getItem('searchKeyLocale'));
    var searchKeyLocaleLength =  searchKeyLocale.length;
    if (searchKeyLocaleLength == 1) {
        $('.lastSearchKeyArea').addClass('d-none');
    }
    $.each(searchKeyLocale, function(index, value){
        if (value.searchKeySlug != item) {
            const newData = {
                searchKey : value.searchKey,
                searchKeySlug : value.searchKeySlug
            }
            if (newSearchKeyLocale.length < 7) {
                newSearchKeyLocale.push(newData);
            }
        }
    });
    window.localStorage.setItem('searchKeyLocale', JSON.stringify(newSearchKeyLocale));
    $('.lastSearchKey-'+item+'').html('');
}

function headerSearchKeyReset() {
    var newSearchKeyLocale = [];
    window.localStorage.setItem('searchKeyLocale', JSON.stringify(newSearchKeyLocale));
    $('.lastSearchKeyArea').addClass('d-none');
}


var formHeaderSearch = $('#headerSearchForm');
formHeaderSearch.validate({
    errorPlacement: function(label, element) {
        label.addClass('arrow');
        label.insertAfter(element);
    },
    wrapper: 'span',
    rules: {
        searchKey: {
            required: true,
        }
    },
    // Specify validation error messages
    messages: {
        searchKey: {
            required: "Lütfen aramak istediğiniz kelimeyi yazınız."
            
        }
    },  
    submitHandler: function() {
        var searchKey = $('#searchForm').val();
        var searchKeyClear = searchKey.replaceAll(" ", "+");
        window.location.href = 'arama?q='+searchKeyClear;
    }
});

var formHeaderSearchMobile = $('#headerSearchFormMobile');
formHeaderSearchMobile.validate({
    errorPlacement: function(label, element) {
        label.addClass('arrow');
        label.insertAfter(element);
    },
    wrapper: 'span',
    rules: {
        searchKey: {
            required: true,
        }
    },
    // Specify validation error messages
    messages: {
        searchKey: {
            required: "Lütfen aramak istediğiniz kelimeyi yazınız."
            
        }
    },  
    submitHandler: function() {
        var searchKey = $('#searchFormMobile').val();
        var searchKeyClear = searchKey.replaceAll(" ", "+");
        window.location.href = 'arama?q='+searchKeyClear;
    }
});


$(document).on("click", function (event) {
    if ($(event.target).closest("#desktopSearchArea").length === 0 && $(event.target).closest("#headerSearchForm").length === 0 && $(event.target).closest("#desktopSearchNavArea").length === 0) {
        $('#desktopSearchArea').addClass('d-none');
        $('#desktopSearchNavArea').addClass('d-none');
        $('#productHeaderSearchContentMobile').addClass('d-none');
        $('#productHeaderSearchContent').addClass('d-none');
        $('#productHeaderSearchContentMobile').addClass('d-none');
    }
    if ($(event.target).closest("#headerSearchForm").length === 1) {
        if ($('#searchForm').val()) {
            $('#desktopSearchArea').removeClass('d-none');
        }else{
            $('#desktopSearchNavArea').removeClass('d-none');
        }
        $('#productHeaderSearchContent').addClass('d-none');
        $('#productHeaderSearchContentMobile').addClass('d-none');
    }
});

