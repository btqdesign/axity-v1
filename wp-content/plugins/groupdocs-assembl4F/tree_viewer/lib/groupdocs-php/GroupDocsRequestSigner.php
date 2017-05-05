<?php 
/**
 *  Copyright 2012 GroupDocs.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

class GroupDocsRequestSigner implements RequestSigner {
	
	function __construct($privateKey){
		$this->privateKey = $privateKey;
	}
    
    public function signUrl($url){
		$urlParts = parse_url($url);
		$pathAndQuery = $urlParts['path'].(empty($urlParts['query']) ? "" : "?".$urlParts['query']);
		$signature = base64_encode(hash_hmac("sha1", APIClient::encodeURI($pathAndQuery), $this->privateKey, true));
		if(substr($signature, -1) == '='){
			$signature = substr($signature, 0, - 1);
		}
		$url = $url . (empty($urlParts['query']) ? '?' : '&') . 'signature=' . APIClient::encodeURIComponent($signature);
		return $url;
	}
        
    public function signContent($requestBody, $headers){
        return $requestBody;
	}
}
