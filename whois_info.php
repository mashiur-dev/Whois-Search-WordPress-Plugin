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
                    <h2 class="title">Domain information for <span><?php echo $domainName; ?></span></h2>
                    
                   <div class="quickurl">
                        <input id="wsUrl" type="text" value="<?php echo get_permalink(); ?>?ws=<?php echo $domainName; ?>">
                        <button style='background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAYAAAD0eNT6AAAWmklEQVR4nO3d+cvveVnH8dfkMlYuLTaikDKaUhpZJqholuVASkGalkJQ+ENYJCT4gxllCkmhtGgqBpUhTJJKu2G5ZYvZ5kAiLRoJRo055YKOOs7YD3eGTp6ZM877+l73+/o8HvD6B67j+zPP8Zy5TwIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABzeXZM8PMlTkvx4kp9J8uIkv57kVUlebWaH3EMDjHH7JI9M8uwkb0pyTZJPmZl9jj0hwNa+MMmTk/xBko+k/6NiZntMAMCmHprkV5N8MP0fEjPbbwIANvPoJG9M/8fDzPaeAIBNPDLJX6b/o2FmMyYA4Jy7LMkr0v+xMLNZEwBwjv1gkv9O/4fCzOZNAMA5dJec/Xe63R8IM5s7AQDnzIOTvDv9Hwczmz0BAOfIE5J8LP0fBjObPwEA58QPJbk+/R8FMzvGBACcAz+V/o+BmR1rAgCaPTP9HwIzO94EADR6Svo/AmZ2zAkAaPKdST6Z/o+AmR1zAgAa3DfJh9L/ATCz404AwIndIcnb0//4zezYEwBwYi9N/8M3MxMAcEKPSf+jNzP7VAQAnMwd4kf8mtn5mQCAE3lO+h+8mdmnJwDgBO4dP+PfzM7XBACcwK+k/7GbmX3mBAAUu2eST6T/sZuZfeYEABR7UfofupnZjScAoNCXJvlo+h+6mdmNJwCg0FPT/8jNzD7XBAAU+ov0P3Izs881AQBFvir9D9zM7EITAFDkWel/4GZmF5oAgCJvTP8DNzO70AQAFLg0ybXpf+BmZheaAIACj0r/4zYzu6kJACjwE+l/3GZmNzUBAAWuTP/jNjO7qQkAKPC36X/cZmY3NQEABT6c/sdtZnZTEwCw2N3T/7DNzG5uAgAWe0D6H7aZ2c1NAMBiD03/wzYzu7kJAFjsivQ/bDOzm5sAgMUen/6HbWZ2cxMAsNiT0v+wfQAA4MQEAAAckAAAgAMSAABwQAIAAA5IAADAAQkAADggAQAAByQAAOCABAAAHJAAAIADEgAAcEACAAAOSAAAwAEJAAA4IAEAAAckAADggAQAAByQAACAAxIAAHBAAgAADkgAAMABCQAAOCABAAAHJAAA4IAEAAAckAAAgAMSAABwQAIAAA5IAADAAQkAADggAQAAByQAAOCABAAAHJAAAIADEgAAcEACAAAOSAAAwAEJAAA4IAEAAAckAADggAQAAByQADiO2yW5PMnXJ3lEksckeWLO/jdgZud735PkcUm+PclDktwnyaWBW+FJ6f8HuwBY77Ik35vkBUl+L8k/Jbku/b+GZrZuNyT5tySvT/JzSZ6c5J6BiyQAZviCJN+W5EVJ3pH+Xysz69u7k7w4yRVJbhO4AAGwt/sn+dkk703/r4+Znb+9L8kvJHlA4EYEwJ6+Nckb0v9rYmb77E1JHhv4XwJgL49N8tb0/1qY2b7765z9YUIOTgDs4T5JXpv+XwMzm7PXJfmacFgC4Hy7NMmzk1yb/vub2bx9PMlzktw+HI4AOL/ul+Sq9N/dzObvqpz9oWIORACcT09O8qH039zMjrOPJvn+cBgC4Hz5giS/lP5bm9lx96L4+QGHIADOj0uTvCb9dzYz+/0kXxRGEwDnw51z9t/odt/YzOzT+9OcfZsYSgD0u2OSt6X/vmZmN97bktwpjCQAet0uyR+l/7ZmZhfanyS5QxhHAPS5JMmV6b+rmdnN7Tdz9s1iEAHQ57npv6mZ2cXuuWEUAdDjiiTXp/+mZmYXuxuSfEcYQwCc3t2TXJ3+e5qZ3dK9P8k9wggC4PT+OP23NDP7fPe6MIIAOK3vS/8dzcxu7X4gbE8AnM5dkvxH+u9oZnZrd02SLwtbEwCn88L039DMbNVeGLYmAE7j8iTXpf+GZmardl2Se4dtCYDTeGn672dmtnovD9sSAPXukeRj6b+fmdnqXZfkK8OWBEC9F6T/dmZmVXt+2JIAqHW7nP3gjO7bmZlV7f1JLg3bEQC1viv9dzMzq96TwnYEQK3XpP9uZmbV+52wHQFQ50uSfDz9dzMzq97HktwpbEUA1PF//5vZkfa4sBUBUMdP/jOzI+1lYSsCoM7fp/9mZman2j+ErQiAGndNckP6b2ZmdsrdNWxDANR4VPrvZWZ26j06bEMA1Hhq+u9lZnbqPT1sQwDU+Pn038vM7NR7SdiGAKjx2vTfy8zs1PvDsA0BUOPt6b+Xmdmpd1XYhgCo8a7038vM7NR7b9iGAKhxdfrvZWZ26n04bEMA1PhI+u9lZnbqfTxsQwDU6L7Vxe7PcnZXMzvfe2v6vxcXOzYhAGp03+pi98qqAwBLvTr93wsBMIwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBYTgDU6L6VAIBZBADLCYAa3bcSADCLAGA5AVCj+1YCAGYRACwnAGp030oAwCwCgOUEQI3uWwkAmEUAsJwAqNF9KwEAswgAlhMANbpvJQBgFgHAcgKgRvetBADMIgBY7mE5+4fADntI0Q0qdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATXQ/QAEAswgA2ET3AxQAMIsAgE10P0ABALMIANhE9wMUADCLAIBNdD9AAQCzCADYRPcDFAAwiwCATVyf/kdoZnbqfTJwcNem/yGamZ16Hwkc3PvT/xDNzE699wUO7h/T/xDNzE69dwYO7i3pf4hmZqfemwMH94r0P0Qzs1Pv5YGDe076H6KZ2an3k4GDe2L6H6KZ2an3+MDB3S/9D9HM7NS7T+DgLklyTfofo5nZqXZ1gCTJ76b/QZqZnWqvCZAkeVr6H6SZ2an21ABJzn4vrPtBmpmdavcK8H+uSv+jNDOr3t8E+CzPTP/DNDOr3jMCfJZ75Oyvx+x+nGZmVbsuyd0C/D+/nf4HamZWNX/6Hy7gm9P/QM3MqvaIABf0tvQ/UjOz1fvzADfpMel/qGZmq/foADfrLel/rGZmq/aGABflQUmuT/+jNTO7tftkkq8LcNFekv6Ha2Z2a/fCALfInZO8N/2P18zs8917ktwxwC12RZIb0v+Izcxu6a5P8i0BPm/PT/9DNjO7pXtegFvltknenP7HbGZ2sXt9ktsEuNUuS/Iv6X/UZmY3t39O8uUBlvnqJNek/3GbmV1o/5nkvgGWe3CSD6b/kZuZ3XgfSPINAco8LGcPrfuxm5l9ev+V5CEByj0oydXpf/RmZv+e5IEBTubyJO9M/+M3s+PuHUnuGeDk7pzkt9L/ETCz4+1V8VP+oNUlSZ6e5GPp/yCY2fxdm+RpAc6NByb5u/R/HMxs7v4qydcGOHdum+QZST6U/g+Fmc3ZB5L8aPx0Pzj37pbkZUk+kf4Ph5ntu48neXGSrwiwlcuT/HLOfs+u+0NiZvvso0lemuReAbZ2WZJnxd8nYGY3vXcl+bH4N34Y55Ik35TkRUnek/6PjZn171+T/GKSh+fsGwEcwP2T/EiS30jy7iQ3pP9jZGZ1uyFn/5Z/ZZIfztlfMgaQOyb5xiRPyNnPFnhezn4f8Ndy9sF4pZmd+12Zszf70iQ/nbM/vf/dOfsR4l8cAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACA1f4HHHkOyKM3GXkAAAAASUVORK5CYII=");' onclick="clickToCopy()">Copy</button>
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
                        <h3 class="title">Domain Information</h3>
                        <p>Registrar: <?php echo $domainRegistrar; 
                            
                            if($dnsRegMatched === FALSE){
                                echo "<span title='Registrar & DNS Host Not Matched!' style='background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAADCElEQVR4nO2bz0sUYRjHP+4u5C1dvCjkSRT/AwPxr5CCQIQ6BCF4KfS+VCIiudBfUaEQRHQWKkLxJKLWKehQRriOnmQ6PDM0TrvOjDPvj53XL3wvi+7zfL/LvPO+z/O8oBY9wDBwF3gObAA7wA/gDDgPeBZ8thP8zTPgDnBLcX5KUAGmgCbwFfBz8hBYAyaD77YWdWAR+EZ+0ZeZsQD0a9KUCgPAMuChTnicJ8ASYrox1IB54A/6hMf5G5gDqoq1/odx4EuOxIvmJ2BMqeII7iOrtmnRcXrArELd1ICXFghNYhMFj0Qv8NYCcWm5EeRcmPgPFojKyvdFmFCju375ONfJ+Th0wzOfxBdXFf9AUUJJUBFzJqv4cdS96kwY4AGjacXXULvJMWGAD3wk5XowrzAJkwb4wKOk4AOo39ubNOCIhJPksuIETBvgA087Ba6j50hr2oAW0Ncu8KKG4DYY4ANP4kErqK3k2GbAAbHy2pSmwLYY4AO3o0GbDhqwGgbsoZjqbbcZsBcGHNYY1CYDfGAIpGnhqgHTIB0bVw1ogJSPXDXgDUg/zlUDtkGakq4a8B301/htMuAUpD3tqgHn1wbg9iPgwfUi6PRrcAvc3gi9BhlIctWABsg0lqsGTIOMorlqwGAY9FBzYBu4G3V9zYKEdHMlasCkBQnp5kTUgAp664KmuY/UQi9gQVPwJOjI4XG7wP3IBGbZDTgGbnYKvuSAAY3LgteR8dOyGvCTDo3RKOZKbMDDFPGpAp9LaMAmGe4bjCH1srIY0AJG0ooPMVsiA+5lFR9CZ9dYFS9sebOiiv6CSZF8RQH3jHqRwWPTYrLyHXAjr/ioCesWiMryyxcmPkQVGTw2LS6JKyi+QzSD3htiaXlCjtU+K0aR2VvTokNucoX3fF5UkdnboxyJ5+UvZHtr9EZpHRk/baFP+DFyqks82OhEHzKBeYA64ftIMaPjed4GVJAhxFVkFC2v6F1kZZ+gTRmrGzCENCAayEzONtKUPOXf9Xkv+GwLaVc1gv8ZbPN9heIvbWNmyK8ZbJoAAAAASUVORK5CYII=&quot;);'></span>"; 
                            }

                        ?></p>

                        <p>DNS Host: <?php
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
            <button type="submit">Lookup Domain</button>
        </form>
    <?php
        return ob_get_clean();
}
add_shortcode( 'whois_form', 'whoisForm' );
