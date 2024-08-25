<?php
// Database connection details
$host = '127.0.0.1';
$dbname = 'jadwal';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // answr 1
    $stmt = $pdo->prepare("
        SELECT penumpang.nama_p, keberangkatan.waktu, tujuan.nama_t
        FROM penumpang
        JOIN keberangkatan ON penumpang.id_p = keberangkatan.id_p
        JOIN tujuan ON keberangkatan.id_t = tujuan.id_t
        WHERE penumpang.nama_p LIKE :nama_p
    ");
    $stmt->execute(['nama_p' => 'Yuli%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //answr 2
    $stmt2 = $pdo->prepare("
        SELECT tujuan.nama_t
        FROM tujuan
        LEFT JOIN keberangkatan ON tujuan.id_t = keberangkatan.id_t
        WHERE keberangkatan.id_t IS NULL
    ");
    $stmt2->execute();
    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    //answr 3
    $stmt3 = $pdo->prepare("
    SELECT keberangkatan.waktu, tujuan.nama_t
    FROM keberangkatan
    JOIN tujuan ON keberangkatan.id_t = tujuan.id_t
    WHERE keberangkatan.waktu < NOW()
    ");
    $stmt3->execute();
    $results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    //answr 5
    $stmt4 = $pdo->prepare("
    SELECT t.nama_t, 
            SUM(t.harga_tiket) AS total_pendapatan
    FROM keberangkatan k
    JOIN tujuan t ON k.id_t = t.id_t
    GROUP BY t.nama_t
    ORDER BY total_pendapatan DESC
    ");
    $stmt4->execute();
    $results4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $values = [];

    foreach ($results4 as $row) {
        $labels[] = $row['nama_t'];
        $values[] = $row['total_pendapatan'];
    }

    //answr 5
    $stmt5 = $pdo->prepare("
    SELECT t.nama_t, 
        SUM(t.harga_tiket) AS total_pendapatan
    FROM keberangkatan k
    JOIN tujuan t ON k.id_t = t.id_t
    WHERE k.waktu BETWEEN '2023-01-01' AND '2023-01-31'
    GROUP BY t.nama_t
    ORDER BY total_pendapatan DESC
    LIMIT 1
    ");
    $stmt5->execute();
    $results5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tes Programmer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Jadwal Keberangkatan Penumpang dengan nama penumpang berawalan yuli.</h1>

<?php if (!empty($results)): ?>
    <table>
        <thead>
            <tr>
                <th>Nama Penumpang</th>
                <th>Waktu Keberangkatan</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_p']); ?></td>
                    <td><?php echo htmlspecialchars($row['waktu']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_t']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada data penumpang dengan nama yang diawali 'Yuli'.</p>
<?php endif; ?>

<br>

<h1>Nama tujuan yang saat ini tidak ada jadwal di tabel keberangkatan.</h1>

<?php if (!empty($results2)): ?>
    <table>
        <thead>
            <tr>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results2 as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_t']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada data.</p>
<?php endif; ?>

<br>

<h1>Jadwal keberangkatan yang sudah selesai, berdasarkan waktu saat ini.</h1>

<?php if (!empty($results3)): ?>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results3 as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['waktu']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_t']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada data.</p>
<?php endif; ?>

<br>

<h1>Data grafik total pendapatan tiap daerah.</h1>
<canvas id="myChart"></canvas>
<script>
        const labels = <?php echo json_encode($labels); ?>;
        const values = <?php echo json_encode($values); ?>;

        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pendapatan',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

<br>

<h1>Jumlah pedapatan terbanyak di bulan januari 2023.</h1>

<?php if (!empty($results5)): ?>
    <table>
        <thead>
            <tr>
                <th>Tujuan</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results5 as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_t']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_pendapatan']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada data.</p>
<?php endif; ?>

</body>
</html>
