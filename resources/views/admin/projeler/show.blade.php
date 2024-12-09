@extends('layouts.admin')

@section('content')
<style>
    /* Tablo Genel Ayarları */
    #hareketler-table {
        table-layout: auto; /* Sütun genişliklerini içeriklere göre ayarla */
        width: 100%; /* Tabloyu tam genişlikte tut */
        border-collapse: collapse; /* Hücreler arası boşlukları kaldır */
    }

    /* Tablo Başlıkları */
    #hareketler-table thead th {
        background-color: #f8f9fa !important; /* Çok hafif gri arka plan */
        color: #333 !important; /* Yazı rengi gri tonunda */
        position: sticky;
        top: 0;
        z-index: 2;
        font-size: 14px; /* Daha küçük yazı tipi */
        padding: 5px; /* Daha az boşluk */
        text-align: left; /* Başlıkları sola hizala */
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); /* Hafif gölge */
    }
    /* Tablo Hücreleri */
    #hareketler-table tbody td {
        word-wrap: break-word; /* Uzun metinler alt satıra geçer */
        white-space: normal;  /* Metni sıkıştırmaz */
        font-size: 12px; /* Küçük yazı tipi */
        padding: 5px; /* Hücre içi boşlukları küçült */
        text-align: left; /* İçerikleri sola hizala */
    }

    /* Tablo Satırları */
    #hareketler-table tbody tr {
        height: auto; /* İçeriğe göre satır yüksekliği */
    }

    /* Tablo Konteyneri */
    .table-container {
        height: calc(100vh - 300px); /* Yükseklik hesaplama */
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    /* Kart Başlığı */
    .card-header {
        background-color: #007bff;
        color: #fff;
    }

    /* Kart Stil Ayarları */
    .card {
        margin-bottom: 20px;
    }

    /* Arama Kutusu */
    .search input {
        width: 300px;
        padding: 10px;
        font-size: 14px;
        border-radius: 5px;
    }

    .search {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }
</style>


<div class="container-fluid">
    <!-- Geri Dön Butonu -->
    <div class="mb-3">
        <a href="{{ route('admin.projeler.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <!-- Small Box Kartları -->
    <div class="row">
        <!-- Yapılan Masraf -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>₺{{ number_format($yapilanMasraf, 2, ',', '.') }}</h3>
                    <p>Yapılan Masraf</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <!-- Elde Edilen Gelir -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>₺{{ number_format($eldeEdilenGelir, 2, ',', '.') }}</h3>
                    <p>Elde Edilen Gelir</p>
                </div>
                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>

        <!-- Kar -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>₺{{ number_format($kar, 2, ',', '.') }}</h3>
                    <p>Kar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Hareketler Tablosu -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Hareketler</h3>
            <div class="card-tools">
                <button type="button" id="toggle-fullscreen" class="btn btn-tool">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="search mb-3">
                <input type="text" id="table-search" placeholder="Arama yapın..." class="form-control">
            </div>
            <div class="table-container" id="table-container">
                <table id="hareketler-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>İşlem Tipi</th>
                            <th>Tarih</th>
                            <th>Kullanıcı</th>
                            <th>Masraf Kalemi/Gelir Kalemi</th>
                            <th>Açıklama</th>
                            <th>Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX ile yüklenen veriler burada gösterilecek -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>$(document).ready(function () {
    const cardElement = document.querySelector('.card'); // Kartın kendisi
    const tableContainer = $('.table-container'); // Tablo kapsayıcı

    // Tam ekran butonuna tıklama işlemi
    $('.card-header .btn[data-card-widget="maximize"]').on('click', function () {
        if (document.fullscreenElement) {
            // Eğer tam ekrandaysa, tam ekrandan çık
            document.exitFullscreen()
                .then(() => {
                    tableContainer.removeClass('fullscreen'); // `fullscreen` sınıfını kaldır
                    console.log("Tam ekrandan çıkıldı.");
                })
                .catch(err => console.error("Tam ekrandan çıkılamadı:", err));
        } else {
            // Kartı tam ekran yap
            cardElement.requestFullscreen()
                .then(() => {
                    tableContainer.addClass('fullscreen'); // `fullscreen` sınıfını ekle
                    adjustTableHeight(); // Yüksekliği ayarla
                    console.log("Tam ekran açıldı.");
                })
                .catch(err => console.error("Tam ekran açılamadı:", err));
        }
    });

    // Tam ekran modundan çıkma olayını dinle
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            tableContainer.removeClass('fullscreen'); // Tam ekran çıkışında sınıfı kaldır
        } else {
            adjustTableHeight(); // Tam ekran modunda yükseklik ayarla
        }
    });

    // Tablo kapsayıcısının yüksekliğini ayarla
    function adjustTableHeight() {
        if (document.fullscreenElement) {
            const headerHeight = $('.card-header').outerHeight(); // Kart başlığının yüksekliği
            tableContainer.css('height', `calc(100vh - ${headerHeight}px)`); // Yükseklik ayarla
        } else {
            tableContainer.css('height', 'calc(100vh - 300px)'); // Normal mod yüksekliği
        }
    }
});
</script>
<script>
    $(document).ready(function () {
        const projeId = "{{ $proje->id }}"; // Şu anki proje ID'si

        // Tablo scroll'unu en alta götürme
        function scrollToBottom() {
            const container = $('#table-container');
            container.animate({ scrollTop: container[0].scrollHeight }, 500);
        }

        // AJAX ile hareketleri getirme
        function fetchHareketler(search = '') {
    $.ajax({
        url: `/admin/projeler/${projeId}/hareketler`,
        method: "GET",
        data: { search: search },
        success: function (response) {
            let tableBody = '';
            if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                response.data.forEach(function (row) {
                    const islemTipi = "Gider"; // Şu anda sadece "Gider" tipi destekleniyor
                    const masrafKalemiAd = row.masrafKalemi || '—'; // Grup Adı ve Kalem Adını birleştirilmiş şekilde al
                    const formattedTutar = parseFloat(row.tutar).toLocaleString('tr-TR', { minimumFractionDigits: 2 }); // Tutarı formatla
                    tableBody += `
                        <tr>
                            <td>${islemTipi}</td>
                            <td>${row.tarih}</td>
                            <td>${row.kullanici}</td>
                            <td>${masrafKalemiAd}</td> <!-- Grup Adı / Kalem Adı -->
                            <td>${row.aciklama}</td>
                            <td>₺${formattedTutar}</td>
                        </tr>`;
                });

                    } else {
                        tableBody = '<tr><td colspan="6" class="text-center">Veri bulunamadı.</td></tr>';
                    }
                    $('#hareketler-table tbody').html(tableBody);

                    // Veriler yüklendikten sonra otomatik scroll
                    scrollToBottom();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Hareketler yüklenirken bir hata oluştu.',
                    });
                }
            });
        }

        // Sayfa yüklenirken hareketleri çek
        fetchHareketler();

        // Arama yapıldığında hareketleri filtrele
        $('#table-search').on('input', function () {
            const search = $(this).val();
            fetchHareketler(search);
        });

        // Tam Ekran Yapma Butonu
        $('#toggle-fullscreen').on('click', function () {
            const card = $('.card')[0]; // Tablo kartını seç
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                card.requestFullscreen();
            }
        });
    });
</script>
@endsection
