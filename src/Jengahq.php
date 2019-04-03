<?php

namespace Ammly\Jengahq;

use GuzzleHttp\Client;

class Jengahq
{
    public $username;
    public $password;
    public $api_key;
    public $phone;
    public $endpoint;
    public $token;
    public function __construct()
    {
        $this->username = config('app.jenga_username') ?? '';
        $this->password = config('app.jenga_password') ?? '';
        $this->api_key = config('app.jenga_api_key') ?? '';
        $this->phone = config('app.jenga_phone') ?? '';
        $this->endpoint = config('app.jenga_base_endpoint') ?? '';
        $this->token = $this->authenticate() ?? '';
    }
    /**
     * Generate authentication token for use with all the requests to Jenga API
     * @return [type] [description]
     */
    public function authenticate()
    {
        try {
            $client = new Client(['verify' => false]);
            $request = $client->request('POST', '/identity/v2/token', [
                'headers' => [
                'Authorization' => $this->api_key,
                'Content-type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'username' => $this->username,
                    'password' => $this->password
                ]
            ]);
            $response = json_decode($request->getBody()->getContents())->access_token;
            return $response;
        } catch (RequestException $e) {
            return (string) $e->getResponse()->getBody();
        }
    }
    public function accountBalance($params)
    {
        $defaults = [
            'account_id' => 127381,
            'country_code' => 'KE',
            'date' => date('Y-m-d'),
        ];
        $params = array_merge($defaults, $params);
        $plainText  = $params['country_code'].$params['account_id'];
        $privateKey = openssl_pkey_get_private('file://'.storage_path('privatekey.pem'));
        $token      = $this->token;
        openssl_sign($plainText, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        try {
            $client = new Client();
            $request = $client->request('GET', 'https://uat.jengahq.io/account/v2/accounts/balances/'.$params['country_code'].'/'.$params['account_id'], [
                'headers' => [
                'Authorization' => 'Bearer '.$token,
                'signature' => base64_encode($signature),
                'Content-type' => 'application/json',
                'Accept' => '*/*'
                ]
            ]);
            $response = json_decode($request->getBody()->getContents());
            // dd($response);
            return $response;
        } catch (RequestException $e) {
            return (string) $e->getResponse()->getBody();
        }
    }
    /**
     * Check to determine if the request should be sent via sendMoneyPesalink() or sendMoneyInternal()
     * @param  Array $params An array of data equired by the API to perform the request
     * @return Action         Call to the appropriate action
     */
    public function sendMoney($params)
    {
        $defaults = [
            'country_code' => 'KE',
            'date' => date('Y-m-d'),
            'source_name' => 'John Doe',
            'source_accountNumber' => '0001092883',
            'destination_name' => 'Jane Doe',
            'destination_mobileNumber' => '25474738846',
            'destination_bankCode' => 63,
            'destination_accountNumber' => '9200002773',
            'transfer_currencyCode' => 'KES',
            'transfer_amount' => '10',
            'transfer_type' => 'PesaLink', //InternalFundsTransfer
            'transfer_reference' => '127364836548',
            'transfer_description' => 'Some description',
        ];
        $params = array_merge($defaults, $params);
        if ( $params['transfer_type'] != null && $params['transfer_type'] == 'InternalFundsTransfer' ) {
            return $this->sendMoneyInternal($params);
        }
        if($params['transfer_type'] != null && $params['transfer_type'] == 'PesaLink') {
            return $this->sendMoneyPesalink($params);
        }
    }
    /**
     * Send money through Pesalink
     * @param  Array $params An array of data equired by the API to perform the request
     * @return Array        Status
     */
    public function sendMoneyPesalink($params)
    {
        $plainText  = $params['transfer_amount'].$params['transfer_currencyCode'].$params['transfer_reference'].$params['destination_name'].$params['source_accountNumber'];
        $privateKey = openssl_pkey_get_private('file://'.storage_path('privatekey.pem'));
        $token      = $this->authenticate();
        openssl_sign($plainText, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        try {
            $client = new Client();
            $request = $client->request('POST', 'https://uat.jengahq.io/transaction/v2/remittance', [
                'headers' => [
                'Authorization' => 'Bearer '.$token,
                'signature' => base64_encode($signature),
                'Content-type' => 'application/json',
                'Accept' => '*/*'
                ],
                'json' => [
                    "source" => [
                      "countryCode" => $params['country_code'],
                      "name" => $params['source_name'],
                      "accountNumber" => $params['source_accountNumber']
                   ],
                   "destination" => [
                      "type" => "bank",
                      "countryCode" => $params['country_code'],
                      "name" => $params['destination_name'],
                      "mobileNumber" => $params['destination_mobileNumber'],
                      "bankCode" => $params['destination_bankCode'],
                      "accountNumber" => $params['destination_accountNumber']
                   ],
                   "transfer" => [
                      "type" => $params['transfer_type'],
                      "amount" => $params['transfer_amount'],
                      "currencyCode" => $params['transfer_currencyCode'],
                      "reference" => $params['transfer_reference'],
                      "date" => $params['date'],
                      "description" => $params['transfer_description']
                   ]
                ]
            ]);
            $response = json_decode($request->getBody()->getContents());
            // dd($response);
            return $response;
        } catch (RequestException $e) {
            // dd($e);
            return (string) $e->getResponse()->getBody();
        }
    }
    /**
     * Send money within Equity bank.
     * @param  Array $params An array of data equired by the API to perform the request
     * @return Array         Status
     */
    public function sendMoneyInternal($params)
    {
      $plainText = $params['source_accountNumber'].$params['transfer_amount'].$params['transfer_currencyCode'].$params['transfer_reference'];
      $privateKey = openssl_pkey_get_private('file://'.storage_path('privatekey.pem'));
      $token      = $this->authenticate();
      openssl_sign($plainText, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        try {
            $client = new Client();
            $request = $client->request('POST', 'https://uat.jengahq.io/transaction/v2/remittance', [
                'headers' => [
                'Authorization' => 'Bearer '.$token,
                'signature' => base64_encode($signature),
                'Content-type' => 'application/json',
                'Accept' => '*/*'
                ],
                'json' => [
                    "source" => [
                      "countryCode" => $params['country_code'],
                      "name" => $params['source_name'],
                      "accountNumber" => $params['source_accountNumber']
                   ],
                   "destination" => [
                      "type" => "bank",
                      "countryCode" => $params['country_code'],
                      "name" => $params['destination_name'],
                      "accountNumber" => $params['destination_accountNumber']
                   ],
                   "transfer" => [
                      "type" => $params['transfer_type'],
                      "amount" => $params['transfer_amount'],
                      "currencyCode" => $params['transfer_currencyCode'],
                      "reference" => $params['transfer_reference'],
                      "date" => $params['date'],
                      "description" => $params['transfer_description']
                   ]
                ]
            ]);
            $response = json_decode($request->getBody());
            // dd($response);
            return $response;
        } catch (RequestException $e) {
            // dd($e);
            return (string) $e->getResponse()->getBody();
        }
    }
    /**
     * Perform an IPRS search to verify provided information.
     * @param  Array $params Array containing the details you want to verify.
     * @return Array         Array containing verified details.
     */
    public function iprsSearch($params)
    {
        $defaults = [
            'country_code' => 'KE',
            'account_id' => '1100161816677',
            'document_type' => 'ID',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'document_number' => '28663883',
        ];
        $params = array_merge($defaults, $params);
        $plainText  = $params['account_id'].$params['document_number'].$params['country_code'];
        $privateKey = openssl_pkey_get_private('file://'.storage_path('privatekey.pem'));
        $token      = $this->authenticate();
        openssl_sign($plainText, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        try {
            $client = new Client();
            $request = $client->request('POST', 'https://uat.jengahq.io/customer-test/v2/identity/verify', [
                'headers' => [
                'Authorization' => 'Bearer '.$token,
                'signature' => base64_encode($signature),
                'Content-type' => 'application/json',
                'Accept' => '*/*'
                ],
                'json' => [
                    "identity" => [
                      "documentType" => $params['document_type'],
                      "firstName" => $params['first_name'],
                      "lastName" => $params['last_name'],
                      "documentNumber" => $params['document_number'],
                      "countryCode" => $params['country_code']
                   ]
                ]
            ]);
            $response = json_decode($request->getBody()->getContents());
            return $response;
        } catch (RequestException $e) {
            return (string) $e->getResponse()->getBody();
        }
    }
}
