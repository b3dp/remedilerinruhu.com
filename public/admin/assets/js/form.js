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
                    var file = response.data.file;
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
        title: "Lütfen Bekleyiniz...!",
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
        title: "Lütfen Bekleyiniz...!",
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
                    $('#emailArea').html(response.email);
                    if (response.email) {
                        $('.changeEmailArea').addClass('d-none');
                        $('.emailArea').removeClass('d-none')
                    }
                    if (response.passwordChange) {
                        $('.changePasswordArea').addClass('d-none');
                        $('.passwordArea').removeClass('d-none');
                    }
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
                        setInterval(function(){
                            window.location.href = par_return;
                        }, 2000);
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
                title: "Lütfen Bekleyiniz...!",
                onOpen: () => {
                    swal.showLoading()
                },
                showConfirmButton: false,
            });
            $.ajax({
                    url: par,
                    type: "POST",
                    data : {value:value},
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
                            }else if (par_return == 'deleteItemValue') {
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
                                $('#deleteItemAreaTwo'+value).remove();
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
                title: "Lütfen Bekleyiniz...!",
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

function miniSingleRank (value,id,par) {
    $.ajax({
        type: "POST",
        url: par,
        async: true,
        data: { id: id, value: value },
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

function productCombinationEdit(formId, par){
    var formData = new FormData($("#combination_form_"+formId+"")[0]);
    swal({
        title: "Lütfen Bekleyiniz...!",
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
                swal({
                    title: "Hata!",
                    text: response.error,
                    type: "error",
                    confirmButtonText: "Tamam"
                });
            } else {
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
        $('#townArea').html('<option value="0">Lütfen Önce İl Seçiniz</option>');
        $('#townAreaEdit').html('<option value="0">Lütfen Önce İl Seçiniz</option>');
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
                $('#townArea').html('');
                $('#townAreEdit').html('');
                if (response.arr) {
                    $('#townArea').html('<option value="0">Seçiniz</option>');
                    $('#townAreaEdit').html('<option value="0">Seçiniz</option>');
                    $('#neighborhoodArea').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $('#neighborhoodAreaEdit').html('<option value="0">Lütfen Önce ilçe Seçiniz</option>');
                    $.each(response.arr,function(index, value){
                        if (value.userDist == value.val) {
                            var selected = 'selected';
                        }else{
                            var selected = '';
                        }
                        $('#townArea').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                        $('#townAreaEdit').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                    });
                }else{
                    $('#townArea').append('<option value="0">Seçilen şehire ait ilçe bulunamadı.</option>');
                    $('#townAreaEdit').append('<option value="0">Seçilen şehire ait ilçe bulunamadı.</option>');
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
                $('#neighborhoodArea').html('');
                $('#neighborhoodAreaEdit').html('');
                if (response.arr) {
                    $('#neighborhoodArea').html('<option value="0">Seçiniz</option>');
                    $('#neighborhoodAreaEdit').html('<option value="0">Seçiniz</option>');
                    $.each(response.arr,function(index, value){
                        if (value.userNeigh == value.val) {
                            var selected = 'selected';
                        }else{
                            var selected = '';
                        }
                        $('#neighborhoodArea').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                        $('#neighborhoodAreaEdit').append('<option '+selected+' value="'+value.val+'">'+value.title+'</option>');
                    });
                }else{
                    $('#neighborhoodArea').append('<option value="0">Seçilen ilçeye ait mahalle bulunamadı.</option>');
                    $('#neighborhoodAreaEdit').append('<option value="0">Seçilen ilçeye ait mahalle bulunamadı.</option>');
                }
                
            }
        }
    });
}

function getCheckoutEditAddressForm (id, par) {
    $.ajax({
        type: "POST",
        url: par,
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

                    <div class="d-flex flex-column mb-4">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="required">Adres Başlığı</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder=""
                            name="title" value="${response.getAddress.title}" />
                    </div>

                    <div class="d-flex flex-column mb-4">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="required">Alıcı Ad Soyad</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder=""
                            name="receiver_name" value="${response.getAddress.receiver_name}" />
                    </div>

                    <div class="d-flex flex-column mb-4">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="">E-posta</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder=""
                            name="email" value="${response.getAddress.email}" />
                    </div>

                    <div class="d-flex flex-column mb-4">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="">Cep Telefonu</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder=""
                            name="phone" value="${response.getAddress.phone}" />
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-12 fv-row">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold form-label mb-2">Adres</label>
                            <div class="row fv-row">
                                <div class="col-4">
                                    <select name="user_city" id="cityAreaEdit" onchange="citySelectd(this)"
                                        data-control="select2" data-placeholder="İl"
                                        class="form-select form-select-solid">
                                        <option value="0" selected>İl Seçiniz</option>
                                        ${cityArea}
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="user_town" id="townAreaEdit"
                                        class="form-select form-select-solid" onchange="townSelectd(this)"
                                        data-hide-search="true" data-placeholder="İlçe">
                                        <option value="0" selected>Seçiniz.</option>
                                        ${townArea}
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-4">
                                    <select name="user_neighborhood" id="neighborhoodAreaEdit"
                                        class="form-select form-select-solid" data-control="select2"
                                        data-hide-search="true" data-placeholder="Mahalle">
                                        <option value="0" selected>Seçiniz</option>
                                        ${neighborhoodArea}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-4">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="">Posta Kodu</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder=""
                            name="post_code" value="${response.getAddress.post_code}" />
                    </div>

                    <div class="d-flex flex-column mb-4">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="">Adres</span>
                        </label>
                        <textarea name="address" id="" cols="30" rows="4"
                            class="form-control form-control-solid resize-none">${response.getAddress.address}</textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="reset" data-bs-dismiss="modal" class="btn btn-danger me-3">Kapat</button>
                        <button type="submit" id="kt_modal_new_card_submit" class="btn btn-primary"> <span class="indicator-label">Kaydet</span> </button>
                    </div>
                `;
                $('#checkout_edit_address').html(addressContent);
                $('#modalEditAddress').modal('show');
            }
        }
    });
}

function checkoutEditAddressForm  () {
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
                location.reload();
            }
        }
    });
}

function getCargoReceiptPrint (par) {
    var data = $("#mini_form").serialize();
    $.ajax({
        type: "POST",
        url: par,
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
                var html = `
                    <h5>Alıcı Bilgileri</h5>
                    <div class="fv-row mb-7">	
                        <table class="table" style="font-size:1.3em">
                            <tr>
                                <td class="w-225px">Alıcı Ad Soyad :</td>
                                <td>${response.receiver_name}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">Adres Bilgisi :</td>
                                <td>${response.address}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">Telefon Numarası :</td>
                                <td>${response.phone}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">E-Posta Adresi :</td>
                                <td>${response.email}</td>
                            </tr>
                        </table>										
                    </div>

                    <h5>Gönderici Bilgileri</h5>
                    <div class="fv-row mb-7">	
                        <table class="table" style="font-size:1.3em">
                            <tr>
                                <td class="w-225px">Gönderen İsim :</td>
                                <td>${response.receiver_name}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">Gönderen Adres :</td>
                                <td>${response.sender_address}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">Gönderen Telefon :</td>
                                <td>${response.sender_phone}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">E-Posta Adresi :</td>
                                <td>${response.sender_email}</td>
                            </tr>
                        </table>										
                    </div>
                    
                    <h5>Kargo Bilgileri</h5>
                    <div class="fv-row mb-7">	
                        <table class="table" style="font-size:1.3em">
                            <tr>
                                <td class="w-225px">Ödeme Türü :</td>
                                <td>${response.order_type}</td>
                            </tr>
                            <tr>
                                <td class="w-225px">Kargo Firması :</td>
                                <td>${response.cargo_company}</td>
                            </tr>
                        </table>										
                    </div>

                    <div class="d-flex fv-row justify-content-center mb-1 mt-5">	
                        <span>
                            ${response.barcode}
                        </span>						
                    </div>
                    <div class="d-flex fv-row justify-content-center mb-1">	
                        <span style=" letter-spacing: 10px; font-size: 15px; margin-left: 10px; ">
                            ${response.barcode_no}
                        </span>									
                    </div>
                `;
                $('#cargoReceiptPrintBody').html(html);
                $('#cargoReceiptPrintArea').modal('show');
                $('#invonceNo').attr('disabled','disabled');
                $('#cargoCount').attr('disabled','disabled');
            }
        }
    });
}


function filter_add (orderQuery) {
    var deger = $("form#filter_add").serialize();
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
