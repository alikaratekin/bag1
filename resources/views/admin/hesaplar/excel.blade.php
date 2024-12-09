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
                <td>{{ $hareket->gelen }}</td>
                <td>{{ $hareket->giden }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
