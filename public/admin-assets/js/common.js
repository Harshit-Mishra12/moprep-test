$(".input_capital").on('keypress', function(evt) {
  $(this).val(function (_, val) {
    return val + String.fromCharCode(evt.which).toUpperCase();
  });
  
  return false;
});

$("form#form").submit(function(e) {

    e.preventDefault();

    var formId = $(this).attr('id');
    var formAction = $(this).attr('action');

    $.ajax({
        url: formAction,
        data: new FormData(this),
        dataType: 'json',
        type: 'post',
        beforeSend: function() {
            $('#preloader').css('display', 'block');
        },
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
            } else {
                sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
            }

            $('#preloader').css('display', 'none');
        },
        success: function(data) {
            if (data.error) {
                sweetAlertMsg('error', data.message);
            } else {

                if (data.reset) {
                    $('#' + formId)[0].reset();
                    $('.fSelect').fSelect('destroy');

                    $('.fSelect').fSelect('create');
                }
                if (data.script) {
                    resetFormData();
                }

                sweetAlertMsg('success', data.message);
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
            $('#preloader').css('display', 'none');
        },
        cache: false,
        contentType: false,
        processData: false,
    });

});


function sweetAlertMsg(type, msg) {
    if (type == 'success') {
        swal({
            title: 'Success !',
            text: msg,
            icon: "success",
            button: "OK",
            confirmButtonColor: 'red',
            closeOnClickOutside: false
        });
    } else {
        swal({
            title: "Error!",
            text: msg,
            icon: "error",
            button: "Ok",
            dangerMode: true,
            closeOnClickOutside: false
        });
    }
}


$(document).ready(function() {
    $('#example2').DataTable();
});


$(function() {   
    $(".image").change(function() {      
        var uploadType = $(this).data('type');        
        var dvPreview = $("#" + $(this).data('image-preview'));        
        var isUpdate = $(this).data('isupdate');

                 
        if (typeof(FileReader) != "undefined") {            
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp|.xlsx)$/;             
            $($(this)[0].files).each(function() {               
                var file = $(this);               
                if (regex.test(file[0].name.toLowerCase())) {                  
                    var reader = new FileReader();                  
                    reader.onload = function(e) {                     
                        var img = $("<img />");                     
                        img.attr("style", "width: 100px;border:1px solid #222;margin-right: 13px");                     
                        img.attr("src", e.target.result);                                          
                        if (uploadType == 'multiple') {                         dvPreview.append(img);                      } else {                         dvPreview.html(img);                      }                  
                    }                  
                    reader.readAsDataURL(file[0]);               
                } else {                   alert(file[0].name + " is not a valid file.");                   return false;                }            
            });         
        } else {             alert("This browser does not support HTML5 FileReader.");          }      
    });   
});

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 &&
        (charCode < 48 || charCode > 57))
        return false;

    return true;
}

var stateId = 0;
var cityId = 0;
var countryId = 0;

$('.country').change(function() {

    stateId = parseInt($(this).data('state_id'));
    cityId = parseInt($(this).data('city_id'));
    countryId = $(this).val();

    $.ajax({
        url: baseUrl + '/get-state?country_id=' + countryId,
        dataType: 'json',
        type: 'get',
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
            } else {
                showMsg('error', xhr.status + ': ' + xhr.statusText);
            }
        },
        success: function(data) {
            $('.statehtml').fSelect('destroy')
            $('.statehtml').html(data.html);

            if (stateId > 0) {

                $('.statehtml option').each(function() {
                    if (this.value == stateId)
                        $('.statehtml').val(stateId);
                });

            }

            $('.statehtml').fSelect('create');

            if (countryId == 101) {

                $('.pincodesssss').attr('minlength', '6');
                $('.pincodesssss').attr('maxlength', '6');

            } else {

                $('.pincodesssss').attr('minlength', '5');
                $('.pincodesssss').attr('maxlength', '5');

            }
        },
        cache: false,
        timeout: 5000
    });

});



$('.statehtml').change(function() {

    $.ajax({
        url: baseUrl + '/get-city?state_id=' + $(this).val(),
        dataType: 'json',
        type: 'get',
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
            } else {
                showMsg('error', xhr.status + ': ' + xhr.statusText);
            }
        },
        success: function(data) {

            $('.cityHtml').fSelect('destroy');
            $('.cityHtml').html(data.html);

            if (cityId > 0) {
                $('.cityHtml option').each(function() {
                    if (this.value == cityId)
                        $('.cityHtml').val(cityId);
                });

            }

            $('.cityHtml').fSelect('create', {
                placeholder: '--Country--',
                overflowText: '{n} selected',
                noResultsText: 'No results found',
                searchText: 'Search',
                showSearch: true
            });
        },
        cache: false,
        timeout: 5000
    });
});


const firebaseConfig = {
    apiKey: "AIzaSyByyTgY3etoAGZPVGiGLEXwR558SEqlP-g", 
    authDomain: "mrcem-53c6b.firebaseapp.com", 
    projectId: "mrcem-53c6b", 
    storageBucket: "mrcem-53c6b.appspot.com", 
    messagingSenderId: "820216490372", 
    appId: "1:820216490372:web:9f70b1c681c1e353a595d1", 
    measurementId: "G-SDPBLFY84M"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.firestore(); 

const chatCollectionRef = db.collection('Chat');

const displayedAlerts = new Set();

chatCollectionRef.onSnapshot((chatSnapshot) => {
  
    chatSnapshot.forEach((chatDoc) => {
        const groupId = chatDoc.id;
        const groupRef = chatDoc.ref.collection('message');

        $('.notificationList').html('');

        groupRef.where('adminstatus', '==', 'Unseen').onSnapshot((snapshot) => {
            
            const numberOfNewMessages = snapshot.size;
            if (!displayedAlerts.has(groupId) && numberOfNewMessages > 0) {

                let notificationItem = `
                    <li id="${groupId}" class="text-center w-100" style="background: #353c48;height: 50px;border-bottom: 2px solid white;display: flex;align-items: center;justify-content: center;">
                    <a href="#" onClick="return false;" class="text-white" style="border-bottom: 0">
                        <span class="table-img msg-user">
                            <img src="{{ asset('admin-assets/images/user1.jpg') }}" alt="">
                        </span>
                        <span class="menu-info">
                            <span class="menu-title">${groupId}</span>
                            <span class="menu-desc">You have ${numberOfNewMessages} new messages.</span>
                        </span>
                    </a>
                    </li>
                `;
                $('#'+groupId).remove();
                $('.notificationList').append(notificationItem);
                
                $('#notificationBell').addClass('notify');
                $('#notificationBell1').addClass('heartbeat');
                
                playNotificationSound();
            }else{
                $('#notificationBell').removeClass('notify');
                $('#notificationBell1').removeClass('heartbeat');
            }
        });
    });
});

function playNotificationSound() { 
    var notificationSound = document.getElementById('notificationSound');
    notificationSound.play();
  
}