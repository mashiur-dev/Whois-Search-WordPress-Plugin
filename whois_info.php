<?php
/**
 * @package Whois_Info
 * @version 1.0.0
 */
/*
Plugin Name: Whois Info
Plugin URI: https://github.com/shoyebzz/Whois-Search-WordPress-Plugin.git
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


// [whois_result]
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
            
            ob_start();
            ?>  
                <div class="whois-result">
                    <h2 class="title">Whois Record for <span><?php echo $domainName; ?></span></h2>

                    <div class="info-section">
                        <h3 class="title">Domain info</h3>
                        <p>Created : <?php echo $createdDate; ?></p>
                        <p>Update : <?php echo $updateDate; ?></p>
                        <p>Expire : <?php echo $expireDate; ?></p>
                    </div>

                    <div class="info-section">
                        <h3 class="title">Owner info</h3>
                        <p>Owner Name : <?php echo $ownerName; ?></p>
                        <p>Company Name : <?php echo $CompanyName; ?></p>
                        <p>Mailing Address : <?php echo $ownerAddress; ?></p>
                        <p>City : <?php echo $ownerCity; ?></p>
                        <p>State : <?php echo $ownerState; ?></p>
                        <p>Country : <?php echo $ownerCountry; ?></p>
                        <p>Email : <?php echo $ownerEmail; ?></p>
                        <p>Phone : <?php echo $ownerEmail; ?></p>
                    </div>

                    <div class="info-section">
                        <h3 class="title">Registrar info</h3>
                        <p>Registrar : <?php echo $domainRegistrar; ?></p>
                    </div>


                </div>


            <?php
            return ob_get_clean();

        }else{
            return '<div class="invalid-domain">Domain is invalid or unregistered!</div>';
        }
    }
    
    
}
add_shortcode( 'whois_result', 'whoisResult' );


// shortcode for Form [whois_form rpage="/result-page"]
function whoisForm( $atts ) {
    $atts = shortcode_atts( array(
        'rpage' => '',
    ), $atts );

    ob_start();
    ?>  
        <style>
            #whoisForm {
                margin: 30px 0px;
            }
            #whoisForm input[type="text"] {
                border: 1px solid #dcdcdc;
                padding: 10px 10px;
                width: 70%;
            }
            #whoisForm button {
                padding: 10px 15px;
                font-size: 18px;
                background: #000000;
                color: #fff;
                width: 25%;
                border: 0px;
            }
            #whoisForm button, #whoisForm input {
                border-radius: 0px;
            }

            .whois-result h2.title {
                font-size: 25px;
                line-height: 35px;
                margin-bottom: 20px;
            }
            .whois-result h2.title span {
                color: #ff3d00;
            }
            .whois-result .info-section {
                background: #f4f4f4;
                margin-bottom: 40px;
            }
            .whois-result .info-section > * {
                padding: 0px 20px;
                margin: 0px;
            }
            .whois-result .info-section h3.title {
                background: #000000;
                color: #fff;
                padding: 5px 10px;
                font-size: 16px;
                text-transform: uppercase;
                font-weight: bold;
            }
            .whois-result .info-section p {
                font-size: 14px;
                margin-top: 5px;
                color: #000;
                border-bottom: 1px solid #dcdcdc;
                padding-bottom: 5px;
            }
        </style>

        <form id="whoisForm" action="<?php echo $atts['rpage']; ?>" method="post">
            <input type="text" name="whois_domain" placeholder="domain.com">
            <button type="submit" name="apisearch">Search</button>
        </form>

    <?php
        return ob_get_clean();
}
add_shortcode( 'whois_form', 'whoisForm' );
