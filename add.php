<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <title>Invoice View</title>
    <style>
        .borders {
            border-width: 100px;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <?php
        $tr_reffid_operators = [""];

        function cek($tr_reffid_operator)
        {
            $hostname = '139.59.228.46';
            $username = 'mitrapayment_user';
            $password = 'jqqas4i6u0hperkl7n14';
            $database = 'mitrapayment';

            $conn = new mysqli($hostname, $username, $password, $database);

            if ($conn->connect_error) {
                die("Koneksi Gagal: " . $conn->connect_error);
            }

            $sql = "SELECT
                    tr_id,
                    tr_reffid_operator,
                    tr_reffid_user,
                    tr_payment_id,
                    tr_nominal_awal,
                    tr_fee,
                    payment.payment_name AS payment_name, 
                    tr_payment_status AS tr_status,
                    tr_create_tanggal AS create_date,
                    tr_bayar_tanggal
                FROM
                    transaksi_history
                JOIN payment ON transaksi_history.tr_payment_id = payment.payment_id
                WHERE
                    tr_reffid_operator = '$tr_reffid_operator'";

            $result = $conn->query($sql);

            if (!$result) {
                die('Query failed: ' . $conn->error);
            }

            return $result;
        }

        function formatRupiah($amount)
        {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }

        foreach ($tr_reffid_operators as $reffId) {
            $result = cek($reffId);

            if ($result->num_rows > 0) {
                echo "<table class='table' style='border-width: thick'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<thead>
                            <tr>
                                <th colspan='3' class='text-center'>
                                    <h1><b>Invoice</b></h1>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='border borders border-dark'>";
                    echo "<tr>
                               <td>Refference ID<p><b>{$row['tr_reffid_operator']}</b></p></td>
                               <td>Merchant Refference<p><b>{$row['tr_reffid_user']}</b></p></td>
                               <td>Aggregator Refference<p><b>-</b></p></td>
                            </tr>
                            <tr>
                               <td>RRN<p><b>-</b></p></td>
                               <td>Payment Method<p><b>{$row['payment_name']}</b></p></td>
                               <td>Amount<p><b>" . formatRupiah($row['tr_nominal_awal']) . "</b></p></td>
                            </tr>
                            <tr>
                               <td>Fee<p><b>" . formatRupiah($row['tr_fee']) . "</b></p></td>
                               <td>Grand Total<p><b>" . formatRupiah($row['tr_nominal_awal']) . "</b></p></td>
                               <td>Status<p class=''style='font-size: 25px;color: aquamarine;'><b>{$row['tr_status']}</b></p></td>
                            </tr>
                            <tr>
                               <td>Request Date<p><b>" . date("Y-m-d H:i:s", $row['create_date']) . "</b></p></td>
                               <td>Paid Date<p><b>" . date("Y-m-d H:i:s", $row['tr_bayar_tanggal']) . "</b></p></td>
                               <td></td>
                            </tr>
                            ";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No results found for Refference ID: $reffId</p>";
            }
        }
        ?>
    </div>

    <script>
        function generatePDF(reffId) {
            var doc = new jsPDF();

            // Add content to the PDF using the doc.text() method
            doc.text('Invoice', 20, 10);

            // Example: Add Refference ID
            doc.text('Refference ID: ' + reffId, 20, 20);

            // Repeat similar doc.text() calls for other content...

            // Save the PDF with a specific name
            doc.save('invoice_' + reffId + '.pdf');
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>