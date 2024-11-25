<?php
namespace App\Helpers;

class DahuaHelper {

    private $username;
    private $password;


    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }


    public function addAccessCard($ip, $userID, $cardName, $cardNo, $doors = [], $validDateStart, $validDateEnd) {
        $url = "http://{$ip}/cgi-bin/recordUpdater.cgi";

        $params = [
            'action' => 'insert',
            'name' => 'AccessControlCard',
            'CardName' => $cardName,
            'CardNo' => $cardNo,
            'UserID' => $userID,
            'CardStatus' => 0,  // Assuming 0 means active
            'CardType' => 0,    // Assuming 0 is the default card type
            'Password' => '',   // Assuming no password
            'VTOPosition' => '01018001',  // Default VTO position
            'ValidDateStart' => $validDateStart,
            'ValidDateEnd' => $validDateEnd
        ];

        foreach ($doors as $index => $door) {
            $params["Doors[{$index}]"] = $door;
        }

        // Use the object's username and password for Digest Authentication
        return $this->makeDahuaRequestWithDigestAuth($url, $params);
    }

    /**
     * Remove Access Card
     *
     * @param string $ip The IP address of the Dahua device
     * @param int $recno The record number of the access card to remove
     * @return string The API response
     */
    public function removeAccessCard($ip, $recno) {
        $url = "http://{$ip}/cgi-bin/recordUpdater.cgi";

        $params = [
            'action' => 'remove',
            'name' => 'AccessControlCard',
            'recno' => $recno
        ];

        // Use the object's username and password for Digest Authentication
        return $this->makeDahuaRequestWithDigestAuth($url, $params);
    }

    /**
     * Add Face Access
     *
     * @param string $ip The IP address of the Dahua device
     * @param string $userID The User ID to associate with the face access
     * @param string $userName The user name (e.g., "addFace")
     * @param string $photoData The base64-encoded photo data of the face
     * @return string The API response
     */
    public function addFaceAccess($ip, $userID, $userName, $photoData) {
        $url = "http://{$ip}/cgi-bin/FaceInfoManager.cgi?action=add";

        $data = [
            'Info' => [
                'UserName' => $userName,
                'PhotoData' => [$photoData]
            ],
            'UserID' => $userID
        ];

        $jsonData = json_encode($data);

        return $this->makePostRequest($url, $jsonData);
    }

    /**
     * Helper function to make a GET request to Dahua API with Digest Authentication
     *
     * @param string $url The URL to send the request to
     * @param array $params The parameters to include in the query string
     * @return string The API response
     */
    private function makeDahuaRequestWithDigestAuth($url, $params) {
        // Build query string from parameters
        $queryString = http_build_query($params);

        // Prepare Digest Authentication credentials
        $authHeader = $this->getDigestAuthHeader($url);

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?' . $queryString,  // Set URL with query parameters
            CURLOPT_RETURNTRANSFER => true,  // Return response as a string
            CURLOPT_ENCODING => '',  // Accept any encoding
            CURLOPT_MAXREDIRS => 10,  // Allow up to 10 redirects
            CURLOPT_TIMEOUT => 30,  // Set timeout to 30 seconds
            CURLOPT_FOLLOWLOCATION => true,  // Follow redirects if any
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,  // Use HTTP 1.1
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $authHeader,  // Add Digest Authentication header
            ],
            CURLOPT_CUSTOMREQUEST => 'GET',  // Set the request type to GET
        ]);

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            return "Error: " . $errorMessage;
        }

        // Close cURL connection
        curl_close($ch);

        // Return the API response
        return $response;
    }

    /**
     * Generate Digest Authentication Header
     *
     * @param string $url The API URL
     * @return string The Digest Authentication header
     */
    private function getDigestAuthHeader($url) {
        $realm = 'Dahua';  // Replace this with the actual realm for your Dahua device (check API docs or response headers)
        $nonce = $this->getNonce($url);  // Get the nonce from the server

        $nc = '00000001';  // Nonce count (increment with each request)
        $cnonce = bin2hex(random_bytes(8));  // Generate a random client nonce
        $qop = 'auth';  // Quality of protection (usually 'auth')

        // Create the digest authentication hash
        $ha1 = md5("{$this->username}:{$realm}:{$this->password}");
        $ha2 = md5("GET:{$url}");  // Assuming this is a GET request
        $response = md5("{$ha1}:{$nonce}:{$nc}:{$cnonce}:{$qop}:{$ha2}");

        // Build the Authorization header
        $authHeader = "Digest username=\"{$this->username}\", realm=\"{$realm}\", nonce=\"{$nonce}\", "
            . "uri=\"{$url}\", qop={$qop}, nc={$nc}, cnonce=\"{$cnonce}\", response=\"{$response}\"";

        return $authHeader;
    }

    /**
     * Function to retrieve the nonce value from the server.
     *
     * @param string $url The API URL
     * @return string The nonce value (this requires an initial request)
     */
    private function getNonce($url) {
        // This method requires sending an initial request to get the nonce
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,  // We need headers to extract the nonce
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',  // Get request to retrieve headers
        ]);

        // Send the request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            curl_close($ch);
            return "Error: " . curl_error($ch);
        }

        // Extract the nonce from the response headers
        preg_match('/nonce="([^"]+)"/', $response, $matches);

        curl_close($ch);

        if (isset($matches[1])) {
            return $matches[1];  // Return the nonce value
        }

        return '';  // Return empty if no nonce found
    }

    /**
     * Helper function to make a POST request to Dahua API
     *
     * @param string $url The URL to send the request to
     * @param string $jsonData The JSON data to send in the POST request
     * @return string The API response
     */
    private function makePostRequest($url, $jsonData) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);  // Set URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Get response as a string
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Set timeout for request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',  // Set content type to JSON
            'Content-Length: ' . strlen($jsonData),  // Set content length
        ]);
        curl_setopt($ch, CURLOPT_POST, true);  // Set request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);  // Attach data to the request

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            curl_close($ch);
            return "Error: " . curl_error($ch);
        }

        curl_close($ch);
        return $response;
    }
}
