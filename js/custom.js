jQuery(document).ready(function(){
   
    // Cloning of Comfirmation page
    jQuery('#seatid-clone').cloneya();
    
    // Google Geolocation api initialization
    google.maps.event.addDomListener(window, 'load', seatid_initialize);
    
    // Form Validation
    seatid_validate();

});

/* Function To validate Form before submitting */
function seatid_validate()
{
    jQuery("form#seatid").submit(function(event) {

            jQuery(".seat_errors").empty();

            appid = jQuery("#seatID_appID");
            account = jQuery("#seatID_account");
            addr = jQuery("#seatID_address");

            jQuery(".seatid_error").removeClass("seatid_error");

            valid = 1;

            if (appid.val() == "") {

                appid.addClass("seatid_error");

                jQuery(".seat_errors").append("<div class='error'>Please enter valid App ID</div>");

                valid = 0;

            }

            if (account.val() == "") {

                account.addClass("seatid_error");

                jQuery(".seat_errors").append("<div class='error'>Please enter valid Account name</div>");

                valid = 0;

            }

            if (addr.val() == "") {

                addr.addClass("seatid_error");

                jQuery(".seat_errors").append("<div class='error'>Please insert valid Address</div>");

                valid = 0;

            }

            else {

                lat = jQuery("#seatID_lat").val();

                long = jQuery("#seatID_long").val();



                if (lat == "" || long == "") {

                    addr.addClass("seatid_error");

                    jQuery(".seat_errors").append("<div class='error'>Address entered is not Valid.</div>");

                    valid = 0;

                }

            }

            count = 0;

            jQuery("#seatid-clone select").each(function($) {

                if (jQuery(this).val() != "") {

                    count = 1;

                }

            });
            
            if (count == 0) {

                jQuery("#seatid-clone select").addClass("seatid_error");

                jQuery(".seat_errors").append("<div class='error'>Please select confirmation page</div>");

                valid = 0;

            }

            if (valid != 0) {

                console.log("IH");

                return;

            }
            else {

                jQuery('.seat_errors').focus();

                event.preventDefault();

            }

        });
}

/* Function for google geolocation API */
function seatid_initialize() {
    var address = (document.getElementById('seatID_address'));

    var autocomplete = new google.maps.places.Autocomplete(address);

    autocomplete.setTypes(['geocode']);

    google.maps.event.addListener(autocomplete, 'place_changed', function() {

        var place = autocomplete.getPlace();

        if (!place.geometry) {

            return;

        }

        var address = '';

        if (place.address_components) {

            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')

            ].join(' ');

        }

        /*********************************************************************/

        /* var address contain your autocomplete address *********************/

        /* place.geometry.location.lat() && place.geometry.location.lat() ****/

        /* will be used for current address latitude and longitude************/

        /*********************************************************************/

        jQuery('#seatID_lat').val(place.geometry.location.lat());

        jQuery('#seatID_long').val(place.geometry.location.lng());

    });
    google.maps.event.addDomListener(address, 'keydown', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });
}