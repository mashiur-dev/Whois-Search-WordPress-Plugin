<?php
/**
 * @package Whois_Info
 * @version 1.0.0
 */
/*
Plugin Name: Whois Info
Plugin URI: http://mashiurz.com
Description: This is a plugin to add ip information lookup feature on your website. 
Author: Mashiur Rahman
Version: 1.0.0
Author URI: http://mashiurz.com
*/


// required headers
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (!function_exists('whoisApi')) {

    function whoisApi($domainName){
        $api = "http://api.whoxy.com/?key=042f4d0d1d13d015ub0c501082256590f&whois={$domainName}";
        $apiCall =     file_get_contents($api);
        $domainData  = json_decode($apiCall, true);

        return $domainData;
    }

    //whoisApi("nikotei.com");

}


// [whois_result api=""]
function whoisResult() {

    if( isset($_POST['apisearch']) && !empty($_POST['whois_domain']) ){

        $domainName = $_POST['whois_domain'];
        $whoisData = whoisApi($domainName);
        
        //check if the domain is valid
        if( isset($whoisData["domain_registered"]) && $whoisData["domain_registered"] == 'yes'){
            // get data into vers
            //$domainStatus = $whoisData["domain_registered"];
            $createdDate = $whoisData["create_date"];
            $updateDate = $whoisData["update_date"];
            $expireDate = $whoisData["expiry_date"];

            $domainRegistrar = $whoisData["domain_registrar"]["registrar_name"];

            $ownerName = $whoisData["registrant_contact"]["full_name"];
            $CompanyName = $whoisData["registrant_contact"]["company_name"];
            $ownerAddress = $whoisData["registrant_contact"]["mailing_address"];
            $ownerCity = $whoisData["registrant_contact"]["city_name"];
            $ownerState = $whoisData["registrant_contact"]["state_name"];
            $ownerCountry = $whoisData["registrant_contact"]["country_name"];
            $ownerEmail = $whoisData["registrant_contact"]["email_address"];
            $ownerPhone = $whoisData["registrant_contact"]["phone_number"] ;

            return "Domain Name: " .  $domainName . "</br>";
            exit;

        }else{
            return '<div class="invalid-domain">Domain is invalid or unregistered!</div>';
        }
    }
    
    
}
add_shortcode( 'whois_result', 'whoisResult' );


// shortcode for Form [whois_form rpage="/result-page"]
function whoisForm( $atts ) {
    $atts = shortcode_atts( array(
        'rpage' => '#',
    ), $atts );

    ob_start();
    ?>
        
        <form id="whoisForm" action="<?php echo $atts['rpage']; ?>" method="post">
            <input type="text" name="whois_domain" placeholder="domain.com">
            <button type="submit" name="apisearch">Search</button>
        </form>

        <script>
        
        jQuery("#whoisForm").submit(function(event) {

            /* stop form from submitting normally */
            event.preventDefault();

            /* get some values from elements on the page: */
            var $form = jQuery(this),
                domainName = $form.find('input[name="whois_domain"]').val();

            jQuery.ajax({
                type: "post",
                data: $form.serialize(),
                contentType: "application/x-www-form-urlencoded",
                success: function(responseData) {
                    console.log(responseData);
                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        </script>
        
    <?php
    return ob_get_clean();
    
}
add_shortcode( 'whois_form', 'whoisForm' );
