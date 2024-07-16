<?php

// $ids =['87848586', '87848097', '87848005', '87847656', '87846931', '87846058', '87845276', '87844859', '87844303', '87843026', '87842296', '87842269', '87842074', '87841916', '87841853', '87841668', '87841031', '87838939', '87838792', '87837970', '87837488', '87836528', '87836346', '87836094', '87836055', '87836010', '87835056', '87835022', '87835015', '87834981', '87834794', '87834333', '87834302', '87833744', '87833624', '87833610', '87833605', '87832766', '87832572', '87832167', '87831939', '87830998', '87829949', '87828867', '87828591', '87828588', '87827912', '87827695', '87827519', '87827046', '87826968', '87826717', '87826434', '87826420', '87826162', '87825550', '87825513', '87825084', '87824967', '87824095', '87823988', '87823709', '87823693', '87823653', '87822892', '87822430', '87822052', '87821821', '87821808', '87821680', '87821381', '87821357', '87821185', '87820454', '87820334', '87818933', '87818172'];
// // ; // Example array of IDs
$type = 'e_wallet';

if ($type == 'virtual_account') {
    $typeCallback = 'virtual_bank_accounts';
} elseif ($type == 'qris') {
    $typeCallback = 'qris';
} elseif ($type == 'disburst') {
    $typeCallback = 'disbursement';
} elseif ($type == 'retail') {
    $typeCallback = 'retail';
} elseif ($type == 'e_wallet') {
    $typeCallback = 'wallet';
} elseif ($type == 'credit_card') {
    $typeCallback = 'credit_card';
} else {
    echo 'Type Callback tidak benar, hubungi IT!';
    exit; // Use exit to stop execution after redirect
}

foreach ($ids as $id) {
    $resendCallback = sendCallback($id, $typeCallback);

    if ($resendCallback) {
        echo "Callback Berhasil Dikirim untuk ID: $id <br>";
    } else {
        echo "Callback Gagal Dikirim untuk ID: $id <br>";
    }
}

function sendCallback($idtrx, $type_callback = NULL)
{
    $endpoint_callback = 'https://api-prod.mitrapayment.com/api/admin/resend-callback';

    $data_payload = [
        'tr_id' => $idtrx,
        'type_callback' => $type_callback
    ];

    $send_payload = json_encode($data_payload);

    $ch = curl_init($endpoint_callback);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $send_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($send_payload),
        'Host: ' . parse_url($endpoint_callback, PHP_URL_HOST)
    ]);

    $curls = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpcode !== 200) {
        return false;
    }

    return true;
}
?>
