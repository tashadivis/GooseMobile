<?php
/*
 * Action.php
 * 
 * Copyright 2014 Ortus IT <contact@ortusit.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

class OrtusAction
{
    public $stateSend = false;
    public function __construct($data = '')
    {
        $this->data = !empty($data) && isset($data) ? $data : false;
        
        if ($this->data) {
			//No automate sending 
			$this->stateSend = false;
			//Automate sending 
			/*
            if ($this->sendCurl($this->data)) {
                $this->stateSend = true;
            } else {
                $this->stateSend = false;
            }
            */
        }
    }
    
    public function sendCurl($data = '')
    {
		//if you want to use curl
       /* 
        if ($curl = curl_init()) {
            //$data['method'] = 'curl';
            $data           = array(
               // 'action' => 'setsettings',
                'logmagento' => $this->prepareString($data)
            );
            $headers = array(
				'Content-type: application/x-www-form-urlencoded'
			);			
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_URL, 'http://errorreport.providesupport.com/cgi-bin/providesupport/logmagento.cgi');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $out = curl_exec($curl);
            //echo $out; //output answer
            if(!curl_errno($curl))
			{
				//answer info 
				//$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				//$url = curl_getinfo($curl,CURLINFO_EFFECTIVE_URL);
				curl_close($curl);
				return true;
			}else{
			    curl_close($curl);
				return false;
			}
		}else{
				return false;
		}
			//curl debug info 
			/*
			extract(curl_getinfo($curl));
			$metrics = <<<EOD
			URL....: $url <br>
			Code...: $http_code ($redirect_count redirect(s) in $redirect_time secs)<br>
			Content: $content_type Size: $download_content_length (Own: $size_download) Filetime: $filetime<br>
			Time...: $total_time Start @ $starttransfer_time (DNS: $namelookup_time Connect: $connect_time Request: $pretransfer_time)<br>
			Speed..: Down: $speed_download (avg.) Up: $speed_upload (avg.)<br>
			Curl...: v{$curl['version']}
			EOD;
			print_r($metrics);
			*/
			
		
		$array           = array(
               'logwordpress' => $this->prepareString($data)
            );
		
		$result = $this->post_request('http://errorreport.providesupport.com/cgi-bin/providesupport/logwordpress.cgi', $array);

		if ('ok' == $result['status']){
 
			return true;
			
			//debug info
			// Print headers 
			//echo $result['header']; 
			//echo '<hr />';
			// print the result of the whole request:
			//echo $result['content'];
		}else {
			return false;
			
			//debug info
			//echo 'A error occured: ' . $result['error']; 
		}
        
    }
    
    
   public function post_request($url, $data, $referer='') {
 
    // Convert the data array into URL Parameters like a=b&foo=bar etc.
    $data = http_build_query($data);
 
    // parse the given URL
    $url = parse_url($url);
 
    if ($url['scheme'] != 'http') { 
		return array(
            'status' => 'err', 
            'error' => 'Error: Only HTTP request are supported !'
        );
    }
 
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
 
    // open a socket connection on port 80 - timeout: 30 sec
    $fp = fsockopen($host, 80, $errno, $errstr, 30);
 
    if ($fp){
 
        // send the request headers:
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
 
        if ($referer != '')
            fputs($fp, "Referer: $referer\r\n");
 
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);
 
        $result = ''; 
        while(!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
    }
    else { 
        return array(
            'status' => 'err', 
            'error' => "$errstr ($errno)"
        );
    }
 
    // close the socket connection:
    fclose($fp);
 
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
 
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
 
    // return as structured array:
    return array(
        'status' => 'ok',
        'header' => $header,
        'content' => $content
    );
}
    
    public function prepareString($str = '')
    {
        return base64_encode(json_encode($str));
	}

}
