<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
    <title>dsadsa</title>
    <style>
        .invoice-box {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header img {
            max-width: 100px;
        }

        .cronos-text {
            font-size: 24px;
            /* Adjust the font size as needed */
            font-weight: bold;
            /* Adjust the font weight as needed */
            margin-top: 10px;
            font-family: 'poppins';

        }

        .reference-container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }

        .reference-field {
            grid-column: span 4;
            padding: 10px;
        }

        .horizontal-line {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <!-- MPAY 3 CRED 
Credential Key: 
Key: CE-TKY2ZLBAI6BCVLJP
Token: nt0FnQD5NxBdYlt9LSe1jLB8LQrvSOhi 

You can refer to the documentation here: https://docs.cronosengine.com
-->

<h1>HALOZZZ</h1>
    <?php
    date_default_timezone_set('Asia/Jakarta');

    function getIdPending()
    {
        $hostname = '139.59.228.46';
        $username = 'digippob_user';
        $password = 'p9febi4bfsn7ob5qz746';
        $database = 'digippob';

        // Create a mysqli connection
        $mysqli = new mysqli($hostname, $username, $password, $database);

        // Check for connection errors
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }

        date_default_timezone_set('Asia/Jakarta');
        $now = time();
        $startOfDay = strtotime('today', $now);
        $endOfDay = strtotime('tomorrow', $now) - 1;

        // Your SQL query
        $sql = "SELECT transaksi_history.tr_id,transaksi_history.tr_reffid_operator,
             transaksi_history.tr_nominal_akhir,transaksi_history.tr_create_tanggal,
             users_project.project_name,
             FROM_UNIXTIME(transaksi_history.tr_create_tanggal) AS formatted_create_tanggal
             FROM transaksi_history
             LEFT JOIN users_project ON transaksi_history.tr_project_id = users_project.project_id
             WHERE tr_payment_type = 'send'
             AND tr_payment_status = 'pending'
             AND tr_create_tanggal >= $startOfDay 
             AND tr_create_tanggal <= $endOfDay
             ORDER BY tr_create_tanggal ASC
            --  LIMIT 24
             ";

        // Execute the query
        $result = $mysqli->query($sql);

        // Check for query execution errors
        if (!$result) {
            die('Query failed: ' . $mysqli->error);
        }

        $resultArray = [];
        $message = "";
        // Count the number of rows
        if ($result->num_rows > 60) {
            foreach ($result as $data) {
                // $message .= "TR ID: " . $data['tr_id'] . "\n";
                $message .= $data['tr_reffid_operator'] . " ";
                // $message .= "Nominal: Rp. " . number_format($data['tr_nominal_akhir'], 0, ',', '.') . " ";
                // $message .= "Project : " . $data['project_name'] . "\n";
                // $message .= "Tanggal : " . date('Y-m-d H:i:s', $data['tr_create_tanggal']) . "\n";
                $message .= "\n";
            }
        } elseif ($result->num_rows < 60) {
            foreach ($result as $data) {
                $message .= "TR ID: " . $data['tr_id'] . "\n";
                $message .= "Reff ID: " . $data['tr_reffid_operator'] . "\n";
                $message .= "Nominal: Rp. " . number_format($data['tr_nominal_akhir'], 0, ',', '.') . "\n";
                $message .= "Project : " . $data['project_name'] . "\n";
                $message .= "Tanggal : " . date('Y-m-d H:i:s', $data['tr_create_tanggal']) . "\n";
                $message .= "\n";
            }
        }
        // $x = $result->num_rows < 60;
        // $resultString = var_export($x, true); // Mengonversi ke string
        // return $message;

        // var_dump($message);
        // die;
        $message .= "\n" . "Total Transaksi Pending : " . $result->num_rows . "\n";
        $message .= "Cek Transaksi dilakukan pada: " . "\n";
        $message .=  date('Y:m:d H:i:s') . "\n\n";

        $currentHour = date("H"); // Mendapatkan jam saat ini dalam format 24 jam

        if ($currentHour >= 21 && $currentHour <= 24 || $currentHour >= 00 && $currentHour <= 3) {
            if ($result->num_rows > 15) {
                $message .= "*Cut off Bank*\n";
                $message .= "Selamat malam team, Terkait kendala tersebut, mengacu pada [pedoman operasional produk](https://www.oyindonesia.com/id/pedoman-operasional-produk) dimana transaksi tersebut terkena cut off Bank BCA dan Mandiri. Terima kasih.";
            }
        } elseif ($currentHour >= 24 && $currentHour < 3) {
            if ($result->num_rows > 15) {
                $message .= "*Cut off Bank*\n";
                $message .= "Selamat malam team, Terkait kendala tersebut, mengacu pada [pedoman operasional produk](https://www.oyindonesia.com/id/pedoman-operasional-produk) dimana transaksi tersebut terkena cut off Bank BCA dan Mandiri. Terima kasih.";
            }
        } elseif ($result->num_rows > 25) {
            $message .= "*Butuh Penanganan!*";
        }
        // Close the database connection
        $mysqli->close();
        // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($jenis_transaksi));
        return $message;
    }
    function getPending()
    {
        $hostname = '139.59.228.46';
        $username = 'digippob_user';
        $password = 'p9febi4bfsn7ob5qz746';
        $database = 'digippob';
        // Create a mysqli connection
        $mysqli = new mysqli($hostname, $username, $password, $database);

        // Check for connection errors
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }
        date_default_timezone_set('Asia/Jakarta');
        $now = time();
        $startOfDay = strtotime('today', $now);
        $endOfDay = strtotime('tomorrow', $now) - 1;

        // Your SQL query
        $sql = "SELECT *, FROM_UNIXTIME(tr_create_tanggal) FROM transaksi_history 
            WHERE tr_payment_type = 'send' 
            AND tr_payment_status = 'pending' 
            AND tr_create_tanggal >= $startOfDay 
            AND tr_create_tanggal <= $endOfDay";

        // Execute the query
        $result = $mysqli->query($sql);


        // Check for query execution errors
        if (!$result) {
            die('Query failed: ' . $mysqli->error);
        }

        // Count the number of rows
        $rowCount = $result->num_rows;
        $message = "";
        $message .= "Terdapat pending disbursement sebanyak : " . "\n";
        $message .= "                   " . $rowCount . "\n";
        $message .= "Cek Transaksi dilakukan pada: " . "\n";
        $message .= date("Y:m:d H:i:s") . "\n\n";
        if ($rowCount > 25) {
            $message .= "Butuh Penanganan!" . "\n";
        }

        // Output the row count
        return $message;
        // Close the database connection
        // $mysqli->close();
    }


    function getsaldo()
    {
        date_default_timezone_set('Asia/Jakarta');
        // URL API saldo
        $url = 'https://api-prod.mitrapayment.com/api/v4/balance';

        // Data yang akan dikirimkan dalam permintaan
        $postData = [
            'key' => 'MPI-4BB399AC42',
            'token' => '1d5e6ab9bd1d886ec7d84f8bcfc387e8'
        ];

        // Inisialisasi cURL
        $ch = curl_init();

        // Konfigurasi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Eksekusi cURL dan ambil responsenya
        $response = curl_exec($ch);

        // Cek apakah ada error dalam permintaan cURL
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Proses respons dari API
            $saldoData = json_decode($response, true);
            // Tutup koneksi cURL
            curl_close($ch);

            // Ambil saldo dari data respons
            $saldo = $saldoData['data_user']['balance'];

            if ($saldo < 1000000000) {
                $message = "URGENT!!\n\n";
                $message .= "Saldo Digippob tersisa: Rp. " . number_format($saldo, 0, ',', '.') . "\n";
                $message .= "Cek saldo dilakukan pada: " . date('Y:m:d H:i:s') . "\n\n";
                $message .= "LAKUKAN TOP UP SALDO SEGERA!";
            } else {
                $message = "";
                $message .= 'Saldo Digippob tersisa: Rp. ' . number_format($saldo, 0, ',', '.') . "\n\n";
                $message .= 'Cek saldo dilakukan pada: ' . date('Y:m:d H:i:s');
            }
            // var_dump($message);
            // // var_dump($saldo < 75964839373);
            // die;
            // Kembalikan saldo
            return $message;
        }
    }

    function cekmutasi($id)
    {
        $hostname = '139.59.228.46';
        $username = 'digippob_user';
        $password = 'p9febi4bfsn7ob5qz746';
        $database = 'digippob';

        // Create a mysqli connection
        $mysqli = new mysqli($hostname, $username, $password, $database);

        // Check for connection errors
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }

        $sql = "SELECT *, transaksi_history.tr_payment_status  FROM balance_history LEFT JOIN transaksi_history ON transaksi_history.tr_id = balance_history.tr_id WHERE balance_history.tr_id = '$id' LIMIT 5";
        $result = $mysqli->query($sql);

        // Check for query execution errors
        if (!$result) {
            die('Query failed: ' . $mysqli->error);
        }
        $message = "";
        foreach ($result as $data) {
            $message = "Transaksi ID : " . $data['tr_id'] . "\n";
            $message .= "Kredit : " . $data['bl_kredit'] . "\n";
            $message .= "Debet : " . $data['bl_debet'] . "\n";
            $message .= "Info : " . $data['bl_info'] . "\n";
            $message .= "Tanggal : " . date('Y-m-d H:i:s', $data['bl_tanggal']) . "\n";
            $message .= "\n";
            $message .= "Status : " . $data['tr_payment_status'] . "\n";
        }
        // var_dump($result->num_rows);
        if ($result->num_rows > 1) {
            $message .= "Transaksi ini telah Refund Automatis";
        }

        return $message;
    }



    function qris()
    {
        $content = file_get_contents("php://input");
        if ($content) {
            $token = "6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs"; // Replace with your Telegram Bot token
            $apiLink = "https://api.telegram.org/bot$token/";

            $update = json_decode($content, true);
            $chat_id = $update['message']['chat']['id'];
            $message = $update['message']['text'];

            $pattern = '/^\/qris-(\d+)$/';

            if (preg_match($pattern, $message, $matches)) {
                $amountValue = $matches[1];

                $keyProject = "DIGI-4B7F906FD0";
                $tokenProject = "b8aa8f34541f56d3effbfb0f1b77042f";
                $referenceId = "QRISBYTelegram-" . uniqid();
                $requestData = [
                    "key" => $keyProject,
                    "token" => $tokenProject,
                    "referenceId" => $referenceId,
                    "signHash" =>  hash_hmac('sha512', $keyProject . $referenceId, $tokenProject),
                    "amount" => $amountValue,
                    "callbackUrl" => "https://webhook.site/a8dcc71f-117c-457b-b03e-4aa77bad3bc4",
                    "viewName" => "Antzein Group",
                    "expTime" => 3,
                ];

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://api-merchant.digippob.com/api/v4/qris/created',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($requestData),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);

                curl_close($curl);

                $qrisData = json_decode($response, true);
                $imgqris = $qrisData['data_payment']['paymentDetail']['imageQris'];

                $gambar = file_get_contents($imgqris);

                // Inisialisasi pesan untuk bot Telegram
                $pesan = [
                    'chat_id' => $chat_id,
                    'photo' => $gambar,
                    'caption' => 'Ini adalah gambar yang Anda minta.'
                ];

                // Membuat URL untuk mengirim pesan gambar
                $url = "https://api.telegram.org/bot$token/sendPhoto";

                // Mengirim pesan gambar menggunakan cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $pesan);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                curl_close($ch);

                // Mengecek hasil pengiriman pesan
                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($result));
                if ($result === false) {
                    echo 'Terjadi kesalahan saat mengirim pesan gambar.';
                } else {
                    echo 'Pesan gambar berhasil dikirim!';
                }

                // You don't need to decode the base64 image data, as it's already a valid image.
                // You can directly send the image to Telegram.

                // Build the POST request for sending a photo
                $postData = [
                    'chat_id' => $chat_id,
                    'photo' => new CURLFile($imgqris), // Use CURLFile to attach the image
                    'caption' => "TRANSAKSI QRIS DIGIPPOB VIA TELEGRAM\n\nNominal Transaksi : RP." . number_format($amountValue, 0, ',', '.')
                ];

                // file_get_contents("https://api.telegram.org/bot$token/sendPhoto?chat_id=$chat_id&photo=" . urlencode($imgqris));
                // Send the photo to Telegram
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $apiLink . 'sendPhoto',
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData,
                ]);

                $response = curl_exec($curl);

                // var_dump($response);
                // die;
                curl_close($curl);
            }
        }
    }






    function getresendid()
    {
        $content = file_get_contents("php://input");
        if ($content) {
            $token = "6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs";
            $apiLink = "https://api.telegram.org/bot$token/";


            $update = json_decode($content, true);

            $chat_id = $update['message']['chat']['id'];
            $message = $update['message']['text'];


            $pattern = '/^\/rc-(\d+)-(\w+)$/';
            // Membagi pesan berdasarkan tanda "-"
            $parts = explode("-", $message);

            // Memastikan pesan memiliki format yang benar
            if (count($parts) !== 3) {
                // Pesan tidak sesuai format, mungkin perlu menangani kesalahan di sini
                return;
            }

            // Bagian pertama (indeks 1) akan berisi ID transaksi
            $id_transaksi = $parts[1];

            // Bagian kedua (indeks 2) akan berisi jenis transaksi
            $jenis_transaksi = $parts[2];
            // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($jenis_transaksi));

            // return $message;

            // Panggil fungsi resendCallback dengan ID transaksi dan jenis transaksi yang didapatkan
            resendCallback($id_transaksi, $jenis_transaksi);
            $message .= "\n\n" . "Resend Callback ID : " . $id_transaksi . "\n";
            $message .=  "Transaksi : " . $jenis_transaksi . "\n";
            $message .=  "Pada : " . date("Y:m:d H:i:s") . "\n";
            // Kirim gambar QR ke pengguna Telegram

            // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($message));
            return $message;
        }
    }
    function resendCallback($idtrx, $type)
    {
        $content = file_get_contents("php://input");
        if ($content) {
            $token = "6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs";
            $apiLink = "https://api.telegram.org/bot$token/";


            $update = json_decode($content, true);

            $chat_id = $update['message']['chat']['id'];
            $message = $update['message']['text'];

            // Sesuaikan fungsi resendCallback sesuai kebutuhan Anda
            // Contoh:
            // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($idtrx));

            $endpoint_callback = 'https://api-prod.mitrapayment.com/api/admin/resend-callback';

            // Data payload untuk dikirim
            $data_payload = [
                'tr_id' => $idtrx,
                'type_callback' => $type
            ];

            $send_payload = json_encode($data_payload);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint_callback);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $send_payload);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length:' . strlen($send_payload);
            $headers[] = 'Host:' . parse_url($endpoint_callback, PHP_URL_HOST);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $curls = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpcode !== 200) {
                // Penanganan kesalahan jika diperlukan
                return false;
            }
        }

        // Callback berhasil dikirim
        return true;
    }

    function saveMessageToDatabase($chat_id, $message)
    {
        $hostname = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'antzcompany';

        // Buat koneksi ke database
        $mysqli = new mysqli($hostname, $username, $password, $database);

        // Check koneksi
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }


        $content = file_get_contents("php://input");
        if ($content) {
            $update = json_decode($content, true);

            if ($update) {
                $chatId = $update['message']['chat']['id'];
                $userId = $update['message']['from']['id'];
                $username = $update['message']['from']['username'];
                $text = $update['message']['text'];
                $date = $update['message']['date'];

                // Siapkan query SQL untuk menyisipkan data ke dalam tabel "notif"
                $sql = "INSERT INTO notif (chatId, userId, username, text, date) VALUES ('$chatId', '$userId', '$username', '$text', '$date')";

                // Jalankan query
                if ($mysqli->query($sql) === TRUE) {
                    // Data berhasil disimpan
                    $result = "Error: " . $sql . "<br>" . $mysqli->error;
                    "Data berhasil disimpan ke dalam tabel notif.";
                } else {
                    // Terjadi kesalahan saat menyimpan data
                    $result = "Error: " . $sql . "<br>" . $mysqli->error;
                }
            }
        } else {
            echo "Permintaan tidak diterima";
        }
        return $result;
        // Tutup koneksi ke database
        $mysqli->close();
    }

    function botgajah()
    {
        $content = file_get_contents("php://input");
        $update = json_decode($content, true);


        if ($content) {
            // getdata();
            $token = "6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs";
            $apiLink = "https://api.telegram.org/bot$token/";


            // https://api.telegram.org/bot6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs/setwebhook?url=https://9014-206-189-43-14.ngrok-free.app/webhook/
            $update = json_decode($content, true);

            $chat_id = $update['message']['chat']['id'];
            $message = $update['message']['text'];
            $username = $update['message']['from']['username'];
            preg_match_all('/L(?:\d+)/', $message, $matches);

            // $matches[0] berisi semua string yang cocok
            // $matches[1] berisi semua angka setelah "L"
            $resultArray = $matches[0];

            // Konversi array menjadi string dengan koma sebagai pemisah
            $resultString = implode(', ', $resultArray);

            // Output hasil dalam bentuk string
            // var_dump($resultString);

            // Kirim hasil ke API atau tempat lain

            // Buat koneksi ke database (gantilah dengan informasi koneksi yang sesuai)
            $servername = "139.59.228.46";
            $username = "gajahpayuser";
            $password = "rahasia!@#4567890";
            $dbname = "gajahpay";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Periksa koneksi database
            if ($conn->connect_error) {
                die("Koneksi database gagal: " . $conn->connect_error);
            }

            // Escape nilai-nilai ID pengguna (untuk menghindari SQL injection)
            $escapedUserIds = array_map(function ($userId) use ($conn) {
                return "'" . $conn->real_escape_string($userId) . "'";
            }, $resultArray);

            // Gabungkan nilai-nilai ID pengguna menjadi string
            $userIdString = implode(', ', $escapedUserIds);
            // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($userIdString));

            // Buat kueri SQL dengan klausa IN
            $sql = "SELECT tr_id, tr_reffid_user, tr_reffid_operator, tr_nominal_awal FROM transaksi_history WHERE tr_reffid_user IN ($userIdString)";

            // Eksekusi kueri
            $result = $conn->query($sql);
            $text = "";
            // Periksa apakah kueri berhasil dieksekusi
            if ($result) {
                // Loop melalui hasil kueri jika ada data yang cocok
                if ($result->num_rows > 0) {
                    foreach ($result as $data) {
                        $text .= "Reff Merchant : " . $data['tr_reffid_user'] . "\n";
                        $text .= "Reff Operator : " . $data['tr_reffid_operator'] . " \n";
                        $text .= "Nominal: Rp. " . number_format($data['tr_nominal_awal'], 0, ',', '.') . "\n\n ";
                        // $text .= "Project : " . $data['project_name'] . "\n";
                        // $text .= "Tanggal : " . date('Y-m-d H:i:s', $data['tr_create_tanggal']) . "\n";
                        $text .= "\n";
                    }
                } else {
                    $text = "Tidak ada hasil yang cocok di database kami.";
                }
            } else {
                $text = "Error dalam menjalankan kueri: " . $conn->error;
            }

            // Tutup koneksi database
            $conn->close();
            file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text=" . urlencode($text));

            // die;
        }
    }
    function saldogala()
    {
        $url = 'https://partner.oyindonesia.com/api/balance';
        $username = 'galatechamerta';
        $apiKey = 'a247fff6-39c5-485c-809a-06ba87b509ca';

        $headers = array(
            'x-oy-username: ' . $username,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Proses respons dari API
            $saldoData = json_decode($response, true);
            // Tutup koneksi cURL
            curl_close($ch);

            // Ambil saldo dari data respons
            $saldo = $saldoData['availableBalance'];

            if ($saldo < 2000000000) {
                $message = "URGENT!!\n\n";
                $message .= "Saldo Gajahpay tersisa: Rp. " . number_format($saldo, 0, ',', '.') . "\n";
                $message .= "Cek saldo dilakukan pada: " . date('Y:m:d H:i:s') . "\n\n";
                $message .= "LAKUKAN TOP UP SALDO SEGERA!";
            } else {
                $message = "";
                $message .= 'Saldo Gajahpay tersisa: Rp. ' . number_format($saldo, 0, ',', '.') . "\n\n";
                $message .= 'Cek saldo dilakukan pada: ' . date('Y:m:d H:i:s');
            }
            // var_dump($message);
            // // var_dump($saldo < 75964839373);
            // die;
            // Kembalikan saldo
            return $message;
        }
    }
    function getsaldogajah()
    {
        date_default_timezone_set('Asia/Jakarta');
        // URL API saldo
        $url = 'https://api-prod.mitrapayment.com/api/v4/balance';

        // Data yang akan dikirimkan dalam permintaan
        $postData = [
            'key' => 'MPI-21BEA81B7C',
            'token' => '0edff02beb3a538c46f4e7f6c13223ba'
        ];

        // Inisialisasi cURL
        $ch = curl_init();

        // Konfigurasi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Eksekusi cURL dan ambil responsenya
        $response = curl_exec($ch);

        // Cek apakah ada error dalam permintaan cURL
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Proses respons dari API
            $saldoData = json_decode($response, true);
            // Tutup koneksi cURL
            curl_close($ch);

            // Ambil saldo dari data respons
            $saldo = $saldoData['data_user']['balance'];

            if ($saldo < 2000000000) {
                $message = "URGENT!!\n\n";
                $message .= "Saldo Gajahpay tersisa: Rp. " . number_format($saldo, 0, ',', '.') . "\n";
                $message .= "Cek saldo dilakukan pada: " . date('Y:m:d H:i:s') . "\n\n";
                $message .= "LAKUKAN TOP UP SALDO SEGERA!";
            } else {
                $message = "";
                $message .= 'Saldo Gajahpay tersisa: Rp. ' . number_format($saldo, 0, ',', '.') . "\n\n";
                $message .= 'Cek saldo dilakukan pada: ' . date('Y:m:d H:i:s');
            }
            // var_dump($message);
            // // var_dump($saldo < 75964839373);
            // die;
            // Kembalikan saldo
            return $message;
        }
    }
    function index()
    {

        $content = file_get_contents("php://input");
        $update = json_decode($content, true);


        if ($content) {
            // getdata();
            $token = "6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs";
            $apiLink = "https://api.telegram.org/bot$token/";


            // https://api.telegram.org/bot6089548320:AAE9863qvH8LmsS4mD7VEAQdg-HWq3g2ZJs/setwebhook?url=https://ae03-206-189-43-14.ngrok-free.app/webhook/
            $update = json_decode($content, true);

            $chat_id = $update['message']['chat']['id'];
            $message = $update['message']['text'];
            $username = $update['message']['from']['username'];

            // saveMessageToDatabase($chat_id, $message);


            $pattern_rc = '/^\/rc-(\d+)-(\w+)$/';
            $pattern_qris = '/^\/qris-(\d+)$/';

            if ($username == 'Bellashal') {

                // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text=" . "Berisik banget ya pemain slot");
            }
            // Memeriksa apakah pesan sesuai dengan pola regex

            if ($message == "/repeat") {
                for ($i = 0; $i < 3; $i++) {
                    // Lakukan sesuatu di dalam loop
                    $messageText = "zzz"; // Pesan yang ingin Anda kirim
                    $chatId = 1095793222; // ID chat yang ingin Anda tuju

                    // Kirim pesan ke bot Telegram
                    file_get_contents($apiLink . "sendmessage?chat_id=$chatId&text=" . urlencode($messageText));
                }
            }

            if ($message == "/saldo") {
                $text = getsaldo();

                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= " . urlencode($text));
            } elseif ($message == "/gsaldo") {
                $text = getsaldogajah();

                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= " . urlencode($text));
            } elseif ($message == "/gala") {
                $text = saldogala();

                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= " . urlencode($text));
            } elseif ($message == "/pending") {
                $text = getPending();

                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= " . urlencode($text));
            } elseif ($message == "/getreff") {
                $text = getIdPending();

                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($text));
            } elseif (preg_match($pattern_qris, $message, $matches)) {
                $text = qris();

                // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($text));
                // file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode('dsad'));
            } elseif (preg_match($pattern_rc, $message, $matches_rc)) {
                $id_transaksi = $matches_rc[1];
                $jenis_transaksi = $matches_rc[2];
                $text = getresendid($id_transaksi, $jenis_transaksi);
                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($text));
            } elseif ($message == "/help") {
                date_default_timezone_set('Asia/Jakarta');
                $now = time();
                $startOfDay = strtotime('today', $now);
                $endOfDay = strtotime('tomorrow', $now) - 1;
                $startOfDayFormatted = date('Y-m-d H:i:s', $startOfDay);
                $endOfDayFormatted = date('Y-m-d H:i:s', $endOfDay);
                $text = "Berikut adalah daftar perintah yang tersedia:" . "\n\n";
                $text .= "/saldo = Mengambil jumlah saldo terakhir Digippob" . "\n";
                $text .= "/gsaldo = Mengambil jumlah saldo terakhir Gajahpay" . "\n";
                $text .= "/pending = Mengambil jumlah transaksi pending Digippob hari ini" .  "\n";
                $text .= "/getreff = Mengambil Reff ID pending Disbursement Digippob hari ini" . "\n";

                $text .= "\n\n" . "Transaksi Cash In" . "\n\n";
                $text .= "/qris-nominal = Memunculkan URL QRIS (min. Rp. 10.000)" . "\n";
                file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= " . urlencode($text));
            } else {
                if ($message) {
                    $text = cekmutasi($message);

                    // return $message;
                    // $mysqli->close();
                    file_get_contents($apiLink . "sendmessage?chat_id=$chat_id&text= "  . urlencode($text));
                }
                // file_get_contents($apiLink . "sendmessage?chat_id=-4021018506&text= Permintaan tidak diterima");

            }
        } else {
            echo "Permintaan tidak diterima\n";
        }
    }


    function calculateOnSignature($key, $token, $requestBody)
    {
        // Concatenate the key and JSON-encoded body
        $message = $key . json_encode($requestBody);

        // Calculate the hash using HMAC-SHA512
        $hash = hash_hmac('sha512', $message, $token);

        return $hash;
    }


    function sendMultipleVARequests($key, $token, $requestData, $count)
    {
        // Initialize an array to store responses
        $responses = [];

        // Send multiple VA requests with unique references
        for ($i = 0; $i < $count; $i++) {
            // Generate a unique reference ID using timestamp
            $timestamp = time();
            $uniqueReference = "DepositTEAM-OPCO-$timestamp"; // Reff id harus diganti
            // $uniqueReference = "anstdsssddssssassxsdz1sss1sss2111";

            // Set the unique reference in the request data
            $requestData['reference'] = $uniqueReference;

            // Generate the common signature for all requests
            $signature = calculateOnSignature($key, $token, $requestData);
            // var_dump($signature);
            // die;
            // Set up the common headers for all requests
            $commonHeaders = [
                'Content-Type: application/json',
                'On-Key:' . $key,
                'On-Token:' . $token,
                'On-Signature:' . $signature,
            ];

            // Send the VA request
            $ch = curl_init('https://api.cronosengine.com/api/virtual-account');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $commonHeaders);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
            $response = curl_exec($ch);
            curl_close($ch);
            $responses[] = $response;
        }

        // Return the responses
        return $responses;
    }

    function genva()
    {

        // Example usage
        // ANTZEIN CRONOS CRED
        // $key = 'CE-FR15JPKY8XPGFDXY';
        // $token = 'p47s4f3gve57Pn60XLSNjbCFLDKWqGRs';

        //CREDENTIAL TEAM_OPCO

        // $key = 'CE-IJ4NXMIPYPFWTAJS';
        // $token = '9k3l3t4W6jGlZkBFWjwfHhtIRkC418Jd';

        //ALPHA GROUP CRED
        // $key = 'CE-Z6BZE9XONKOEED3D';
        // $token = 'q3yjhzFuALaZw8EZ9p4bKcd9jM3VkUlB';

        //TEAM SETTLEMENT CRED
        // $key = 'CE-IJ4NXMIPYPFWTAJS';
        // $token = '9k3l3t4W6jGlZkBFWjwfHhtIRkC418Jd';
        // PASTIKAN CREDENTIAL BENER

        $requestData = [
            "bankCode" => "009",
            "singleUse" => true,
            "type" => "ClosedAmount",
            "amount" => 49000000,
            "expiryMinutes" => 1440,
            "viewName" => "Mr. Dinda",
            "additionalInfo" => [
                "callback" => "http://your-site-callback.com/notify"
            ]
        ];

        $numberOfRequests = 19; // jumlah qty deposit

        $responses = sendMultipleVARequests($key, $token, $requestData, $numberOfRequests);

        // Process and handle the responses as needed
        foreach ($responses as $index => $response) {
            $res = json_decode($response);
            // var_dump($res->responseData->virtualAccount->vaNumber);
            // die;
            echo $res->responseData->virtualAccount->vaNumber . "<br>" . PHP_EOL;
        }
    }


    function sendCurlRequest()
    {
        $url = 'https://api.cronosengine.com/api/qris';

        // $key = 'CE-UIU2A3HSGPEHHRHY'; //MPAY GROUP 2
        // $token = 'emL3ivTWAl2PtfyRZCwWMCZfxqp5bjxH';
        // $key = 'CE-TKY2ZLBAI6BCVLJP'; //mpay 3 CRED
        // $token = 'nt0FnQD5NxBdYlt9LSe1jLB8LQrvSOhi';
        $key = 'CE-FR15JPKY8XPGFDXY'; //ANTZEIN CRED
        $token = 'p47s4f3gve57Pn60XLSNjbCFLDKWqGRs';
        // $key = 'CE-TKY2ZLBAI6BCVLJP';
        // $token = 'nt0FnQD5NxBdYlt9LSe1jLB8LQrvSOhi';
        for ($i = 0; $i < 1; $i++) {
            usleep(10);
            $codeSignature = hash_hmac('sha512', $key, $token);
            $data = json_encode([
                'reference' => 'qrisstestCRONOS2-MPAY' . time(),
                'amount' => 1000,
                'expiryMinutes' => 30,
                'viewName' => 'Antzyn',
                'additionalInfo' => [
                    'callback' => 'https://kraken.free.beeceptor.com/notify',
                ],
            ]);
            $codeSignature = hash_hmac('sha512', $key . $data, $token);
            $headers = [
                "On-Key: $key",
                "On-Token: $token",
                "On-Signature: " . $codeSignature,
                "Content-Type: application/json",
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

            $response = json_decode(curl_exec($ch));

            $imageUrl = $response->responseData->qris->image;
            $content = $response->responseData->qris->content;
            // var_dump($imageUrl);
            echo "<div style='text-align: center; margin-top: 150px;'>";
            echo "<img src=" . $imageUrl . " alt='QR Code' />";
            echo "$content";

            echo "<br><br>";

            // sendvareq();
            // sendwallet();
            var_dump($response);
            curl_close($ch);
        }
    }
    function sendvareq()
    {
        $url = 'https://api.cronosengine.com/api/virtual-account';
        $key = 'CE-FR15JPKY8XPGFDXY'; //ANTZEIN CRED
        $token = 'p47s4f3gve57Pn60XLSNjbCFLDKWqGRs';

        // $key = 'CE-UIU2A3HSGPEHHRHY'; //MPAY GROUP 2
        // $token = 'emL3ivTWAl2PtfyRZCwWMCZfxqp5bjxH';

        // $key = 'CE-TKY2ZLBAI6BCVLJP'; //mpay group 3
        // $token = 'nt0FnQD5NxBdYlt9LSe1jLB8LQrvSOhi';

        $bankCodes = [
            // "014",
            "008",
            "002",
            "009",
            // "013",
            // "011",
            // "022"
        ];

        foreach ($bankCodes as $bankCode) {
            usleep(10);
            $amount = 500000;
            $reference = "testingva-" . time();
            $expiryMinutes = 30;
            $viewName = "Mr. antzein";
            $callbackURL = "http://your-site-callback.com/notify";
            $data = json_encode([
                "bankCode" => $bankCode,
                "singleUse" => true,
                "type" => "ClosedAmount",
                "reference" => $reference,
                "amount" => $amount,
                "expiryMinutes" => $expiryMinutes,
                "viewName" => $viewName,
                "additionalInfo" => [
                    "callback" => $callbackURL
                ]
            ]);

            $codeSignature = hash_hmac('sha512', $key . $data, $token);
            $headers = [
                "On-Key: $key",
                "On-Token: $token",
                "On-Signature: " . $codeSignature,
                "Content-Type: application/json",
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

            $response = curl_exec($ch);
            var_dump($response);

            if ($response === false) {
                echo "Error for Bank Code $bankCode: " . curl_error($ch) . "<br>";
            } else {
                $responseData = json_decode($response);
                if ($responseData === null) {
                    echo "Error for Bank Code $bankCode: Unable to parse JSON response <br>";
                } else {
                    $result = $responseData->responseData;
                    $res = $result; // Mengambil elemen pertama dari array, sesuaikan jika perlu

                    // Mengakses nilai-nilai yang diperlukan
                    $vaNumber = $res->virtualAccount->vaNumber;
                    $amount = $result->amount;
                    $receiverName = $result->virtualAccount->viewName;
                    $bankCode = $result->virtualAccount->bankCode;

                    // Menampilkan nilai-nilai tersebut
                    // echo "For Bank Code $bankCode: <br>";
                    echo "VA Number: $vaNumber <br>";
                    echo "Amount: $amount <br>";
                    // echo "Nama Penerima: $receiverName <br>";
                    if ($bankCode === "014") {
                        $bankName = "BCA";
                    } elseif ($bankCode === "008") {
                        $bankName = "Mandiri";
                    } elseif ($bankCode === "002") {
                        $bankName = "BRI";
                    } elseif ($bankCode === "009") {
                        $bankName = "BNI";
                    } elseif ($bankCode === "013") {
                        $bankName = "Permata";
                    } elseif ($bankCode === "011") {
                        $bankName = "Danamon";
                    } elseif ($bankCode === "022") {
                        $bankName = "CIMB";
                    } elseif ($bankCode === "153") {
                        $bankName = "Sahabat Sampoerna";
                    } else {
                        $bankName = "Tidak dikenal";
                    }
                    echo "Bank Name: $bankName <br><br><br>";
                }
            }
            var_dump($response);
            curl_close($ch);
        }
    }
    function sendwallet()
    {
        $url = 'https://api.cronosengine.com/api/e-wallet';
        $key = 'CE-FR15JPKY8XPGFDXY'; //ANTZEIN CRED
        $token = 'p47s4f3gve57Pn60XLSNjbCFLDKWqGRs';
        // $key = 'CE-UIU2A3HSGPEHHRHY'; //MPAY GROUP 2
        // $token = 'emL3ivTWAl2PtfyRZCwWMCZfxqp5bjxH';
        // $key = 'CE-TKY2ZLBAI6BCVLJP';
        // $token = 'nt0FnQD5NxBdYlt9LSe1jLB8LQrvSOhi';

        $bankCodes = [
            // "dana",
            "ovo",
            // "shopeepay"
        ];

        $results = []; // Array to store the results

        foreach ($bankCodes as $bankCode) {
            usleep(10);
            $amount = 10000;
            $reference = "testingwallet-" . time() . rand();
            $expiryMinutes = 30;
            $viewName = "Mr. antzein";
            $callbackURL = "http://your-site-callback.com/notify";
            $data = json_encode([
                "reference" => "$reference",
                "phoneNumber" => "085641591552",
                "channel" => $bankCode,
                "amount" => 10000,
                "expiryMinutes" => $expiryMinutes,
                "viewName" => "Mr. Zyn",
                "additionalInfo" => [
                    "callback" => "http://your-site-callback.com/notify",
                    "successRedirectUrl" => "http://redirect-after-success.com"
                ]
            ]);

            $codeSignature = hash_hmac('sha512', $key . $data, $token);
            $headers = [
                "On-Key: $key",
                "On-Token: $token",
                "On-Signature: " . $codeSignature,
                "Content-Type: application/json",
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

            $response = curl_exec($ch);
            // var_dump($response);
            // die;
            $responseData = json_decode($response);
            if ($responseData !== null) {
                // Extract the specific values
                $result = $responseData->responseData;

                $results[] = [
                    'amount' => $result->amount,
                    'channel' => $result->eWallet->channel,
                    'viewname' => $result->eWallet->viewName,
                    'id' => $result->id,
                    'merchantRef' => $result->merchantRef,
                    'url' => $result->eWallet->url
                ];
            }

            curl_close($ch);
        }

        // Return the results as JSON
        // header('Content-Type: application/json');
        // echo json_encode($results);
        // var_dump($results);
        foreach ($results as $result) {
            $amount = $result['amount'];
            $channel = $result['channel'];
            $viewname = $result['viewname'];
            $id = $result['id'];
            $merchantRef = $result['merchantRef'];
            $url = $result['url'];
            echo "Channel: $channel<br>";
            echo "Amount: $amount<br>";
            echo "Account Holder: $viewname<br>";
            echo "Id: $id<br>";
            echo "Merchant Id: $merchantRef<br><br>";
            echo "Url : <a target='_blank'href='$url'>Bayar</a><br><br>";
        }
        // echo $results['amount'];
    }
    function checkDisbursementStatus()
    {
        $url = 'https://api-prod.mitrapayment.com/api/v4/disburst_status/';

        $postData = array(
            'key' => 'MPI-34533592F0',
            'token' => 'a101e5a3f95a01d672a6848c9cf721f6',
            'accountNo' => '122085892397632',
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            echo "Error: $error";
        } else {
            echo "Response: $response";
        }
    }
    function cek($id)
    {
        $url = 'https://api.cronosengine.com/api/check/fc35691f-8ed9-44eb-92f1-35da4e79a841';

        $headers = array(
            'On-Key: CE-FR15JPKY8XPGFDXY',
            'On-Token: p47s4f3gve57Pn60XLSNjbCFLDKWqGRs',
            'On-Signature: 8eb7529a8ee63fcca73e1f7798e62a30bcd526ce778641c1c978eef9acb18e0f735560f2d73bf92cb73289ce4b3a857c08f00955d824890982fbea1e2a6027f5',
        );

        do {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $error = curl_error($ch);

            curl_close($ch);

            if ($error) {
                echo "Error: $error";
                break; // Hentikan loop jika terjadi kesalahan
            } else {
                echo "Response: $response";
                $responseData = json_decode($response, true);

                // Cek status
                if ($responseData['responseCode'] == 200) {
                    $status = $responseData['responseData']['status'];
                    if ($status == 'success') {
                        echo "Transaction successful!";
                        break; // Hentikan loop jika status sukses
                    } elseif ($status == 'failed') {
                        echo "Transaction failed!";
                        break; // Hentikan loop jika status gagal
                    } else {
                        // Jeda sebelum mengulang permintaan
                        sleep(5);
                    }
                } else {
                    echo "Transaction check failed!";
                    break; // Hentikan loop jika terjadi kesalahan pada pengecekan transaksi
                }
            }
        } while (true);
    }

    function disbtest()
    {
        $url = 'https://api-prod.mitrapayment.com/api/v4/disburst/disburst_c2';
        $referenceId = "DGP-" . rand() . '-' . time();
        // $referenceId = "testdisbursementfordana" . time();
        // $keyProject = "MPI-34533592F0";
        // $tokenProject = "a101e5a3f95a01d672a6848c9cf721f6";


        $keyProject = "MPI-4BB399AC42"; //DIGIPPOB CRED
        $tokenProject = "1d5e6ab9bd1d886ec7d84f8bcfc387e8";
        // $keyProject = "MPI-21BEA81B7C"; //Gajah CRED
        // $tokenProject = "0edff02beb3a538c46f4e7f6c13223ba";

        $signHash = hash_hmac('sha512', $keyProject . $referenceId, $tokenProject);

        $data = array(
            "key" => $keyProject,
            "token" => $tokenProject,
            "referenceId" => $referenceId,
            "signHash" => $signHash,


            "bankShort" => "bca",
            "recipient_account" => "5465900099",
            "amount" => 80137,

            // "bankShort" => "dana",
            // "recipient_account" => "5465900099",
            // "amount" => 20000,

            // "bankShort" => "BCA",
            // "recipient_account" => "7750937866",
            // "amount" => 100000,


            "callbackUrl" => "https://webhook.site/a8dcc71f-117c-457b-b03e-4aa77bad3bc4",
            "useCase" => "single",
            "paidCounter" => 1,
            // "model" => "Cronos"
        );



        $data_string = json_encode($data);

        $headers = array(
            'Content-Type: application/json'
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = json_decode(curl_exec($ch));

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        curl_close($ch);

        var_dump($result);

        // cek($result->data_payment->referenceId);
    }

    function testva()
    {

        $key = "MPI-34533592F0";
        $token = "a101e5a3f95a01d672a6848c9cf721f6";
        $reff = "testvatocronos" . time();
        $signHash = hash_hmac('sha512', $key . $reff, $token);

        // $bankCodes = [
        //     "va_bca",
        //     // "va_mandiri",
        //     // "va_bri",
        //     // "va_bni",
        //     // "va_permata",
        //     // "va_cimb",
        //     // "va_danamon",
        // ];

        $results = [];
        $url = 'https://api-prod.mitrapayment.com/api/v4/va/bca';
        $data = [
            "key" => $key,
            "token" => $token,
            "referenceId" => $reff,
            "signHash" => $signHash,
            "amount" => 10000,
            "callbackUrl" => "https://webhook.site/a8dcc71f-117c-457b-b03e-4aa77bad3bc4",
            "reqType" => "oneoff",
            "viewName" => "Antzein",
            "expTime" => 5,
            "useCase" => "single",
            "vaExpired" => 5,
            "PaidCounter" => 1,
            // "model" => "Cronos"

        ];

        // Convert the data array to a JSON string
        $jsonData = json_encode($data);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        $response = curl_exec($ch);
    
        if ($response === false) {
            echo "cURL Error: " . curl_error($ch);
        } else {
            echo $response;
        }

        curl_close($ch);
    }


    function qrismpi()
    {
        for ($i = 1; $i <= 20; $i++) {
            usleep(10);
            // Data yang akan dikirim sebagai JSON
            $key = "MPI-34533592F0";
            $token = "a101e5a3f95a01d672a6848c9cf721f6";
            $reff = "testqris" . time();
            $signHash = hash_hmac('sha512', $key . $reff, $token);

            $data = array(
                "key" => $key,
                "token" => $token,
                "referenceId" => $reff,
                "signHash" => $signHash,
                "amount" => 10000,
                "callbackUrl" => "https://webhook.site/a8dcc71f-117c-457b-b03e-4aa77bad3bc4",
                "viewName" => "Antzein",
                "expTime" => 3,
                "model" => "Prismalink"
            );

            // Konversi data ke format JSON
            $jsonData = json_encode($data);

            // Set header untuk mengirim data JSON
            $headers = array(
                'Content-Type: application/json'
            );

            // Inisialisasi cURL session
            $ch = curl_init('https://api-prod.mitrapayment.com/api/v4/qris/created');

            // Set pilihan cURL
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Eksekusi permintaan cURL
            $response = curl_exec($ch);

            // Tampilkan hasil atau proses sesuai kebutuhan
            echo "Request #$i: $response<br>";
        }
    }

    function disbcronos()
    {

        $prefix = 'MPI-';
        $part1 = bin2hex(random_bytes(4));
        $part2 = bin2hex(random_bytes(2));
        $part3 = bin2hex(random_bytes(2));
        $part4 = bin2hex(random_bytes(6));

        $referenceId = $prefix . $part1 . '-' . $part2 . '-' . $part3 . '-' . $part4;
        // $referenceId = "testingdisbuntukqrisweb-" . time();
        $url = 'https://api.cronosengine.com/api/disburse';

        // $key = 'CE-SZWFR70N1IWEUZIL';
        // $token = '1U9rFJS93qttufKSkxqC5UhwB6keB6XK';
        // $key = 'CE-UIU2A3HSGPEHHRHY'; //MPAY GROUP 2
        // $token = 'emL3ivTWAl2PtfyRZCwWMCZfxqp5bjxH';
        // $key = 'CE-TKY2ZLBAI6BCVLJP'; //mpay 3 CRED
        // $token = 'nt0FnQD5NxBdYlt9LSe1jLB8LQrvSOhi';

        // ANTZEIN CRONOS CRED
        $key = 'CE-FR15JPKY8XPGFDXY';
        $token = 'p47s4f3gve57Pn60XLSNjbCFLDKWqGRs';

        $bankCode = 'dana';
        $recipientAccount = '085892397632';
        $amount = 22000;
        $data = json_encode([
            'bankCode' => $bankCode,
            'recipientAccount' => $recipientAccount,
            'reference' => $referenceId,
            'amount' => $amount,
            'additionalInfo' => [
                'callback' => 'http://your-site-callback.com/notify',
            ],
        ]);

        $codeSignature = hash_hmac('sha512', $key . $data, $token);

        $headers = [
            "On-Key: $key",
            "On-Token: $token",
            "On-Signature: " . $codeSignature,
            "Content-Type: application/json",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  // Pass the encoded data directly
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        var_dump($response);
    }

    function qrisdgp()
    {
        $keyProject = "DIGI-4B7F906FD0";
        $tokenProject = "b8aa8f34541f56d3effbfb0f1b77042f";
        $referenceId = "QRIStestingdgp-" . uniqid();
        $requestData = [
            "key" => $keyProject,
            "token" => $tokenProject,
            "referenceId" => $referenceId,
            "signHash" =>  hash_hmac('sha512', $keyProject . $referenceId, $tokenProject),
            "amount" => 10000,
            "callbackUrl" => "https://webhook.site/a8dcc71f-117c-457b-b03e-4aa77bad3bc4",
            "viewName" => "Antzein Group",
            "expTime" => 3,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api-merchant.digippob.com/api/v4/qris/created',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            echo 'Error: ' . curl_error($curl);
        } else {
            // Decode the JSON response
            $responseObject = json_decode($response);

            // Check if decoding was successful
            if ($responseObject) {
                // Debugging: Dump the decoded JSON for inspection
                // Check if the necessary properties exist
                if (isset($responseObject->data_payment->paymentDetail->imageQris)) {
                    $imageUrl = $responseObject->data_payment->paymentDetail->imageQris;
                    $content = $responseObject->data_payment->paymentDetail->contentQris;

                    // Display the QR Code image and content
                    echo "<div style='text-align: center; margin-top: 150px;'>";
                    echo "<a href='$imageUrl' target=”_blank” >Show QR</a>";
                    echo "<br>";
                    echo "Content QRIS: " . htmlspecialchars($content);
                    echo "</div>";
                } else {
                    echo "Invalid JSON structure. Missing required properties.";
                }
            } else {
                echo "Error decoding JSON response.";
            }
        }

        // Close cURL session
        curl_close($curl);

        // Inisialisasi pesan untuk bot Telegram

        // Membuat URL untuk mengirim pesan gambar


    }

    // Example usage
    function inq()
    {
        // API endpoint
        $api_url = 'https://api.cronosengine.com/api/account-inquiry';
        $key = 'CE-FR15JPKY8XPGFDXY'; // ANTZEIN CRED
        $token = 'p47s4f3gve57Pn60XLSNjbCFLDKWqGRs';

        // Data payload
        $data = [
            'bankCode' => '014',
            'accountNumber' => '2040546807',
            'reference' => 'qrisstestCRONOS2-MPAY' . time(),
            'additionalInfo' => [
                'callback' => 'https://kraken.free.beeceptor.com/notify',
            ]
        ];

        // Convert data array to JSON
        $jsonData = json_encode($data);

        // Generate signature
        $codeSignature = hash_hmac('sha512', $key . $jsonData, $token);

        // Headers
        $headers = [
            "On-Key: $key",
            "On-Token: $token",
            "On-Signature: $codeSignature",
            "Content-Type: application/json",
        ];

        // cURL setup
        $ch = curl_init($api_url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        // Execute cURL session
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Handle the response
        echo $response;
    }
    function vadg()
    { {

            $url = 'https://api-merchant.digippob.com/api/v4/va/va_bca';
            $key = "DIGI-4B7F906FD0";
            $token = "b8aa8f34541f56d3effbfb0f1b77042f";
            $reff = "VAtesting-" . uniqid();
            $signHash = hash_hmac('sha512', $key . $reff, $token);

            $bankCodes = [
                "va_bca",
                // "va_mandiri",
                // "va_bri",
                // "va_bni",
                // "va_permata",
                // "va_cimb",
                // "va_danamon",
            ];

            $results = [];
            foreach ($bankCodes as $bankCode) {
            }
            $data = [
                "key" => $key,
                "token" => $token,
                "referenceId" => $reff,
                "signHash" => $signHash,
                "amount" => 10000,
                "callbackUrl" => "https://webhook.site/a8dcc71f-117c-457b-b03e-4aa77bad3bc4",
                "reqType" => "oneoff",
                "viewName" => "Antzein",
                "expTime" => 5,
                "useCase" => "single",
                "vaExpired" => 5,
                "PaidCounter" => 1,
                "model" => "Cronos"

            ];

            // Convert the data array to a JSON string
            $jsonData = json_encode($data);

            $headers = [
                'Accept: application/json',
                'Content-Type: application/json'
            ];

            $ch = curl_init($url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

            $response = curl_exec($ch);
         
            if ($response === false) {
                echo "cURL Error: " . curl_error($ch);
            } else {
                echo $response;
            }

            curl_close($ch);
        }
    }
    function editProjectName()
    {
        $projectId = 88;
        $newProjectName = 'Smooth Project';
        $hostname = '139.59.228.46';
        $username = 'mitrapayment_user';
        $password = 'jqqas4i6u0hperkl7n14';
        $database = 'mitrapayment';

        $conn = new mysqli($hostname, $username, $password, $database);

        if ($conn->connect_error) {
            die("Koneksi Gagal: " . $conn->connect_error);
        }
        $projectId = $conn->real_escape_string($projectId);
        $newProjectName = $conn->real_escape_string($newProjectName);

        // Update the project_name for the specified user ID
        $sql = "UPDATE users_project SET project_name = '$newProjectName' WHERE project_id = $projectId";

        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }

        // Close the database connection
        $conn->close();
    }

    // editProjectName()
    // Call the function to edit the project name for user ID 88
    // include 'add.php';
    // include 'rc.php';
    // welcome();
    // Function to generate HTML for invoice
    // sendCurlRequest();
    // Output the generated HTML
    // echo generateInvoiceHTML($invoiceData);
    // Panggil fungsi untuk melakukan permintaan cURL
    // sendwallet();
    // sendCurlRequest();
    // qrisdgp();
    // cek();
    // disbcronos();
    // vadg();
    // cs($id);
    // testva();
    // disbtest();
    // sendvareq();
    // inq();
    // qrismpi();
    // dbc();
    // phpinfo();

    ?>
</body>

</html>