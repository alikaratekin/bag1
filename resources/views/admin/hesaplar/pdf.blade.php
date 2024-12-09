<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Hareketleri PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Hesap Hareketleri</h2>
    <table>
        <thead>
            <tr>
                <th>Tarih</th>
                <th>İşlem Tipi</th>
                <th>Kullanıcı</th>
                <th>Açıklama</th>
                <th>Gelen</th>
                <th>Giden</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hareketler as $hareket)
                <tr>
                    <td>{{ $hareket->tarih }}</td>
                    <td>{{ $hareket->islem_tipi }}</td>
                    <td>{{ $hareket->kullanici }}</td>
                    <td>{{ $hareket->aciklama }}</td>
                    <td>{{ number_format($hareket->gelen, 2, ',', '.') }}</td>
                    <td>{{ number_format($hareket->giden, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
