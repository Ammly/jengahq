<?php

use Ammly\Jengahq\Jengahq;

Route::group(['prefix' => 'jengahq'], function () {
    Route::get('authenticate', 'Ammly\Jengahq\Jengahq@authenticate');

    Route::get('welcome', function () {
        $params = [
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
          'transfer_reference' => 322388163,
          'transfer_description' => 'Some description',
        ];
        $jenga = new Jengahq;
        $jenga->sendMoney($params);
    });
});
