<?php
/**
 * @package Whois_Info
 * @version 1.0.1
 */
/*
Plugin Name: Whois Info
Plugin URI: https://github.com/shoyebzz/Whois-Search-WordPress-Plugin.git
Description: This is a plugin to add ip information lookup feature on your website. 
Author: Mashiur Rahman
Version: 1.0.1
Author URI: http://mashiurz.com
*/

/**
 * Include assests file for whoisApi.
 */
if(!function_exists('whoisApi_Assets_Calls')){

    function whoisApi_Assets_Calls(){
        wp_enqueue_style( 'whoisApi_style', plugin_dir_url( __FILE__ ) . 'assets/style.css',false,'1.1','all');
    }
    add_action( 'wp_enqueue_scripts', 'whoisApi_Assets_Calls' );

}

/**
 * API CALL
 */
if (!function_exists('whoisApi')) {

    function whoisApi($domainName){
        $api = "http://api.whoxy.com/?key=042f4d0d1d13d015ub0c501082256590f&whois={$domainName}";
        $apiCall =     file_get_contents($api);
        $domainData  = json_decode($apiCall, true);

        return $domainData;
    }

    //whoisApi("nikotei.com");
}


/**
 * Shortcode [whois_result]
 */
function whoisResult() {

    //dns provider list
    $dns_providers   = [
        'cloudns.net'           => 'Cloud DNS',
        'secureserver.net'      => 'GoDaddy',
        'domaincontrol.com'     => 'GoDaddy',
        'dnsmadeeasy.com'       => 'DNS Made Easy',
        'awsdns'                => 'Amazon',
        'worldnic.com'          => 'Network Solutions',
        'ultradns'              => 'Ultra DNS',
        'dns.cogentco'          => 'Cogent Communications',
        '1and1'                 => '1&1',
        'ui-dns'                => '1&1',
        'dynect'                => 'Dyn DNS',
        'registrar-servers.com' => 'NameCheap',
        'stabletransit.com'     => 'RackSpace',
        'gkg.net'               => 'GKG',
        'easydns.net'           => 'EasyDNS Technologies',
        'bluehost.com'          => 'BlueHost',
        'dreamhost'             => 'DreamHost',
        'hostgator'             => 'HostGator',
        'websitewelcome.com'    => 'HostGator',
        'gandi'                 => 'Gandi',
        'cloudflare'            => 'CloudFlare',
        'register.com'          => 'Register',
        'mediatemple.net'       => 'MediaTemple',
        'hostway'               => 'HostWay',
        'wixdns.net'            => 'Wix',
        'ipower'                => 'iPower',
        'digitalocean.com'      => 'Digital Ocean',
        'googledomains.com'     => 'Google',
        'whoisguard.com'        => 'NameCheap',
    ];
    $dnsPArry = array_keys($dns_providers);

    if( !empty($_POST['whois_domain']) || !empty($_GET['ws']) ){

        //get domain name
        $domainName = isset($_POST['whois_domain']) ? $_POST['whois_domain'] : $_GET['ws'];
        $whoisData  = whoisApi($domainName);
        
        //check if the domain is valid
        if( isset($whoisData["domain_registered"]) && $whoisData["domain_registered"] == 'yes'){
            // get data into vers
            //$domainStatus = $whoisData["domain_registered"];
            //isset() ? : ''

            $createdDate = isset($whoisData["create_date"]) ? $whoisData["create_date"] : '';
            $updateDate = isset($whoisData["update_date"]) ? $whoisData["update_date"] : '';
            $expireDate = isset($whoisData["expiry_date"]) ? $whoisData["expiry_date"] : '';

            $domainRegistrar = isset($whoisData["domain_registrar"]["registrar_name"]) ? $whoisData["domain_registrar"]["registrar_name"] : '';

            $ownerName = isset($whoisData["registrant_contact"]["full_name"]) ? $whoisData["registrant_contact"]["full_name"] : '';
            $CompanyName = isset($whoisData["registrant_contact"]["company_name"]) ? $whoisData["registrant_contact"]["company_name"] : '';
            $ownerAddress = isset($whoisData["registrant_contact"]["mailing_address"]) ? $whoisData["registrant_contact"]["mailing_address"] : '';
            $ownerCity = isset($whoisData["registrant_contact"]["city_name"]) ? $whoisData["registrant_contact"]["city_name"] : '';
            $ownerState = isset($whoisData["registrant_contact"]["state_name"]) ? $whoisData["registrant_contact"]["state_name"] : '';
            $ownerCountry = isset($whoisData["registrant_contact"]["country_name"]) ? $whoisData["registrant_contact"]["country_name"] : '';
            $ownerEmail = isset($whoisData["registrant_contact"]["email_address"]) ? $whoisData["registrant_contact"]["email_address"] : '';
            $ownerPhone = isset($whoisData["registrant_contact"]["phone_number"]) ? $whoisData["registrant_contact"]["phone_number"] : '';
            

            //$nameServers = isset($whoisData["name_servers"]) ? $whoisData["name_servers"] : '';
            $nameServer = isset($whoisData["name_servers"]) ? $whoisData["name_servers"] : '';
            
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $nameServer[0], $regs)) {
                $domain = substr($regs['domain'], 0, strpos($regs['domain'], '.'));
               
                $dnsDomain = preg_grep( "/^$domain/", $dnsPArry );
                $dnsName = array_values( array_intersect_key($dns_providers, array_flip($dnsDomain)) );
            }

            ob_start();
            ?>  
                <div class="whois-result">
                    <h2 class="title">Whois Record for <span><?php echo $domainName; ?></span></h2>
                    
                   <div class="quickurl">
                        <input id="wsUrl" type="text" value="<?php echo get_permalink(); ?>?ws=<?php echo $domainName; ?>">
                        <button onclick="clickToCopy()">Copy</button>
                   </div>
                   

                    <?php
                        if( isset($domainRegistrar) && isset($dnsName[0]) && strpos($domainRegistrar, $dnsName[0])!==false){
                            //matched
                            $dnsRegMatched = TRUE;
                        }else{
                            $dnsRegMatched = FALSE;
                        }
                    ?>

                    <div class="info-section">
                        <h3 class="title">Registrar</h3>
                        <p><?php echo $domainRegistrar; 
                            
                            if($dnsRegMatched === FALSE){
                                echo "<span title='Registrar & DNS Host Not Matched!' style='background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAADCElEQVR4nO2bz0sUYRjHP+4u5C1dvCjkSRT/AwPxr5CCQIQ6BCF4KfS+VCIiudBfUaEQRHQWKkLxJKLWKehQRriOnmQ6PDM0TrvOjDPvj53XL3wvi+7zfL/LvPO+z/O8oBY9wDBwF3gObAA7wA/gDDgPeBZ8thP8zTPgDnBLcX5KUAGmgCbwFfBz8hBYAyaD77YWdWAR+EZ+0ZeZsQD0a9KUCgPAMuChTnicJ8ASYrox1IB54A/6hMf5G5gDqoq1/odx4EuOxIvmJ2BMqeII7iOrtmnRcXrArELd1ICXFghNYhMFj0Qv8NYCcWm5EeRcmPgPFojKyvdFmFCju375ONfJ+Th0wzOfxBdXFf9AUUJJUBFzJqv4cdS96kwY4AGjacXXULvJMWGAD3wk5XowrzAJkwb4wKOk4AOo39ubNOCIhJPksuIETBvgA087Ba6j50hr2oAW0Ncu8KKG4DYY4ANP4kErqK3k2GbAAbHy2pSmwLYY4AO3o0GbDhqwGgbsoZjqbbcZsBcGHNYY1CYDfGAIpGnhqgHTIB0bVw1ogJSPXDXgDUg/zlUDtkGakq4a8B301/htMuAUpD3tqgHn1wbg9iPgwfUi6PRrcAvc3gi9BhlIctWABsg0lqsGTIOMorlqwGAY9FBzYBu4G3V9zYKEdHMlasCkBQnp5kTUgAp664KmuY/UQi9gQVPwJOjI4XG7wP3IBGbZDTgGbnYKvuSAAY3LgteR8dOyGvCTDo3RKOZKbMDDFPGpAp9LaMAmGe4bjCH1srIY0AJG0ooPMVsiA+5lFR9CZ9dYFS9sebOiiv6CSZF8RQH3jHqRwWPTYrLyHXAjr/ioCesWiMryyxcmPkQVGTw2LS6JKyi+QzSD3htiaXlCjtU+K0aR2VvTokNucoX3fF5UkdnboxyJ5+UvZHtr9EZpHRk/baFP+DFyqks82OhEHzKBeYA64ftIMaPjed4GVJAhxFVkFC2v6F1kZZ+gTRmrGzCENCAayEzONtKUPOXf9Xkv+GwLaVc1gv8ZbPN9heIvbWNmyK8ZbJoAAAAASUVORK5CYII=&quot;);'></span>"; 
                            }

                        ?></p>
                    </div>

                    <div class="info-section">
                        <h3 class="title">DNS Hosting provider</h3>

                        <p><?php
                        if(!empty($dnsName)){
                            echo $dnsName[0];
                        }else{
                            echo $domain;
                        }

                        if($dnsRegMatched === FALSE){
                            echo "<span title='Registrar & DNS Host Not Matched!' style='background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAADCElEQVR4nO2bz0sUYRjHP+4u5C1dvCjkSRT/AwPxr5CCQIQ6BCF4KfS+VCIiudBfUaEQRHQWKkLxJKLWKehQRriOnmQ6PDM0TrvOjDPvj53XL3wvi+7zfL/LvPO+z/O8oBY9wDBwF3gObAA7wA/gDDgPeBZ8thP8zTPgDnBLcX5KUAGmgCbwFfBz8hBYAyaD77YWdWAR+EZ+0ZeZsQD0a9KUCgPAMuChTnicJ8ASYrox1IB54A/6hMf5G5gDqoq1/odx4EuOxIvmJ2BMqeII7iOrtmnRcXrArELd1ICXFghNYhMFj0Qv8NYCcWm5EeRcmPgPFojKyvdFmFCju375ONfJ+Th0wzOfxBdXFf9AUUJJUBFzJqv4cdS96kwY4AGjacXXULvJMWGAD3wk5XowrzAJkwb4wKOk4AOo39ubNOCIhJPksuIETBvgA087Ba6j50hr2oAW0Ncu8KKG4DYY4ANP4kErqK3k2GbAAbHy2pSmwLYY4AO3o0GbDhqwGgbsoZjqbbcZsBcGHNYY1CYDfGAIpGnhqgHTIB0bVw1ogJSPXDXgDUg/zlUDtkGakq4a8B301/htMuAUpD3tqgHn1wbg9iPgwfUi6PRrcAvc3gi9BhlIctWABsg0lqsGTIOMorlqwGAY9FBzYBu4G3V9zYKEdHMlasCkBQnp5kTUgAp664KmuY/UQi9gQVPwJOjI4XG7wP3IBGbZDTgGbnYKvuSAAY3LgteR8dOyGvCTDo3RKOZKbMDDFPGpAp9LaMAmGe4bjCH1srIY0AJG0ooPMVsiA+5lFR9CZ9dYFS9sebOiiv6CSZF8RQH3jHqRwWPTYrLyHXAjr/ioCesWiMryyxcmPkQVGTw2LS6JKyi+QzSD3htiaXlCjtU+K0aR2VvTokNucoX3fF5UkdnboxyJ5+UvZHtr9EZpHRk/baFP+DFyqks82OhEHzKBeYA64ftIMaPjed4GVJAhxFVkFC2v6F1kZZ+gTRmrGzCENCAayEzONtKUPOXf9Xkv+GwLaVc1gv8ZbPN9heIvbWNmyK8ZbJoAAAAASUVORK5CYII=&quot;);'></span>"; 
                        }

                        ?></p>
                    </div>

                    <div class="info-section">
                        <h3 class="title">Domain info</h3>
                        <?php 
                            $today = strtotime(date("Y-m-d"));
                            $expireD = strtotime($expireDate);
                            $diff = ($expireD - $today)/60/60/24;
                        
                        ?>
                        <p>Created : <?php echo $createdDate; ?></p>
                        <p>Update : <?php echo $updateDate; ?></p>
                        <p>Expire : <?php echo $expireDate; if( $diff <= 300 && $diff > 0 ){ 
                            echo "<span title='Domain will expire soon!' style='background-image: url(&quot;data:image/svg+xml;base64,PHN2ZyBoZWlnaHQ9JzMwMHB4JyB3aWR0aD0nMzAwcHgnICBmaWxsPSIjMDAwMDAwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGRhdGEtbmFtZT0iTGF5ZXIgMSIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHg9IjBweCIgeT0iMHB4Ij48dGl0bGU+MTEyYWxsPC90aXRsZT48cGF0aCBkPSJNNDUuODcsMTUuNjlhMi41LDIuNSwwLDAsMC0yLjUsMi41VjQzLjY1SDI2LjQ5YTIuNSwyLjUsMCwxLDAsMCw1SDQ1Ljg3YTIuNSwyLjUsMCwwLDAsMi41LTIuNXYtMjhBMi41LDIuNSwwLDAsMCw0NS44NywxNS42OVoiPjwvcGF0aD48cGF0aCBkPSJNODcsNTMuMzlBNDIuMzcsNDIuMzcsMCwxLDAsNTMuMzksODcsMjQuMDYsMjQuMDYsMCwxLDAsODcsNTMuMzlaTTQ1LjQyLDgyLjc2QTM3LjM0LDM3LjM0LDAsMSwxLDgyLjc2LDQ1LjQyYTM3Ljc3LDM3Ljc3LDAsMCwxLS4zOSw1LjMyQTI0LjA3LDI0LjA3LDAsMCwwLDUwLjc0LDgyLjM3LDM3LjczLDM3LjczLDAsMCwxLDQ1LjQyLDgyLjc2Wm0yNy40Miw5LjE2QTE5LjA3LDE5LjA3LDAsMCwxLDU3LDgzLjM3YTIuNDcsMi40NywwLDAsMC0uNTQtLjg5LDE5LjA2LDE5LjA2LDAsMSwxLDE2LjQzLDkuNDRaIj48L3BhdGg+PHBhdGggZD0iTTcyLjg1LDU4LjQ3YTIuNSwyLjUsMCwwLDAtMi41LDIuNVY3NS43MmEyLjUsMi41LDAsMCwwLDUsMFY2MUEyLjUsMi41LDAsMCwwLDcyLjg1LDU4LjQ3WiI+PC9wYXRoPjxwYXRoIGQ9Ik03Mi44NSw4MC40N2EyLjUsMi41LDAsMCwwLTIuNSwyLjV2Mi43NWEyLjUsMi41LDAsMCwwLDUsMFY4M0EyLjUsMi41LDAsMCwwLDcyLjg1LDgwLjQ3WiI+PC9wYXRoPjwvc3ZnPg==&quot;);'></span>"; 
                        } ?></p>
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
                    
                </div>

                <script>
                function clickToCopy() {
                    var copyText = document.getElementById("wsUrl");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999)
                    document.execCommand("copy");
                    //alert("Copied the text: " + copyText.value);
                }
                </script>

            <?php

            return ob_get_clean();
            
        }else{
            return '<div class="invalid-domain">Domain is invalid or unregistered!</div>';
        }
    }
    
}
add_shortcode( 'whois_result', 'whoisResult' );


/**
 * shortcode for Form [whois_form rpage="/result-page"]
 */
function whoisForm( $atts ) {
    $atts = shortcode_atts( array(
        'rpage' => '',
    ), $atts );

    ob_start();
    ?>  
        <form id="whoisForm" action="<?php echo $atts['rpage']; ?>" method="post">
            <input type="text" name="whois_domain" placeholder="domain.com">
            <button type="submit">Search</button>
        </form>
    <?php
        return ob_get_clean();
}
add_shortcode( 'whois_form', 'whoisForm' );
