@extends('layouts.admin')

@section('content')
<style>
    /* Tablo Genel Ayarları */
    .table-container {
        height: calc(100vh - 300px); /* Yükseklik hesaplama */
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    #personelTable {
        table-layout: auto; /* Sütun genişliklerini içeriklere göre ayarla */
        width: 100%; /* Tabloyu tam genişlikte tut */
        border-collapse: collapse; /* Hücreler arası boşlukları kaldır */
    }

    /* Tablo Başlıkları */
    #personelTable thead th {
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
    #personelTable tbody td {
        word-wrap: break-word; /* Uzun metinler alt satıra geçer */
        white-space: normal;  /* Metni sıkıştırmaz */
        font-size: 12px; /* Küçük yazı tipi */
        padding: 5px; /* Hücre içi boşlukları küçült */
        text-align: left; /* İçerikleri sola hizala */
    }

    /* Tablo Satırları */
    #personelTable tbody tr {
        height: auto; /* İçeriğe göre satır yüksekliği */
    }

    @media (max-width: 768px) {
        #personelTable th,
        #personelTable td {
            font-size: 12px;
        }
    }

    .filters {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-bottom: 15px;
    }

    .filters .form-control {
        width: 200px;
    }

    .filters .form-switch {
        display: flex;
        align-items: center;
    }

    .filters .form-switch .form-check-label {
        margin-left: 10px;
    }

    .ms-auto {
        margin-left: auto !important;
    }
</style>

<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Personel Listesi</h3>
        <button class="btn btn-success btn-sm ms-auto" id="openModal">
            <i class="fas fa-plus"></i> Yeni Personel Ekle
        </button>
    </div>
    <div class="card-body">
        <div class="filters">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Personel Ara...">
            <div class="form-switch">
    <button id="toggleButton" class="btn {{ $showDeparted ? 'btn-danger' : 'btn-secondary' }} btn-sm">
        {{ $showDeparted ? 'İşten Ayrılanları Gizle' : 'İşten Ayrılanları Göster' }}
    </button>
</div>


        </div>
        <div class="table-container">
            <table id="personelTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>İsim</th>
                    </tr>
                </thead>
                <tbody>
    @foreach ($personeller as $personel)
        <tr class="personel-row" onclick="window.location='{{ route('admin.personeller.show', $personel->id) }}'" style="cursor: pointer;">
            <td>{{ $personel->isim }}</td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addPersonModal" tabindex="-1" aria-labelledby="addPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addPersonModalLabel">Yeni Personel Ekle</h5>
                
            </div>
            <div class="modal-body">
                <form id="addPersonForm">
                    @csrf

                    <!-- Sekme Başlıkları -->
<ul class="nav nav-tabs mb-3" id="personelTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tabCalisan" type="button" role="tab">
            Çalışan Bilgileri
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tabDiger" type="button" role="tab">
            Diğer Bilgiler
        </button>
    </li>
</ul>

<!-- Sekme İçerikleri -->
<div id="calisanBilgileriTab" class="tab-content">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="isim" class="form-label">İsim <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="isim" name="isim" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="tc_kimlik_no" class="form-label">T.C. Kimlik No <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="tc_kimlik_no" name="tc_kimlik_no" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="cep_telefonu" class="form-label">Cep Telefonu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="cep_telefonu" name="cep_telefonu" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="ise_giris_tarihi" class="form-label">İşe Giriş Tarihi <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="ise_giris_tarihi" name="ise_giris_tarihi" required>
        </div>
    </div>
</div>

<div id="digerBilgilerTab" class="tab-content d-none">
    <div class="row">
        <!-- Sol Sütun -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="e_posta" class="form-label">E-Posta</label>
                <input type="email" class="form-control" id="e_posta" name="e_posta" placeholder="E-posta adresi giriniz">
            </div>
            <div class="mb-3">
                <label for="dogum_tarihi" class="form-label">Doğum Tarihi</label>
                <input type="date" class="form-control" id="dogum_tarihi" name="dogum_tarihi">
            </div>
            <div class="mb-3">
                <label for="isten_ayrilis_tarihi" class="form-label">İşten Çıkış Tarihi</label>
                <input type="date" class="form-control" id="isten_ayrilis_tarihi" name="isten_ayrilis_tarihi">
            </div>
            <div class="mb-3">
                <label for="aylik_net_maas" class="form-label">Aylık Net Maaş</label>
                <input type="number" step="0.01" class="form-control" id="aylik_net_maas" name="aylik_net_maas" placeholder="Maaş bilgisi giriniz">
            </div>
            <div class="mb-3">
                <label for="banka_hesap_no" class="form-label">Banka Hesap No</label>
                <input type="text" class="form-control" id="banka_hesap_no" name="banka_hesap_no" placeholder="Hesap numarası giriniz">
            </div>
            <div class="mb-3">
                <label for="departman" class="form-label">Departman</label>
                <input type="text" class="form-control" id="departman" name="departman" placeholder="Departman giriniz">
            </div>
        </div>

        <!-- Sağ Sütun -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="adres" class="form-label">Adres</label>
                <textarea class="form-control" id="adres" name="adres" rows="2" placeholder="Adres giriniz"></textarea>
            </div>
            <div class="mb-3">
                <label for="banka_bilgileri" class="form-label">Banka Bilgileri</label>
                <textarea class="form-control" id="banka_bilgileri" name="banka_bilgileri" rows="2" placeholder="Banka bilgileri giriniz"></textarea>
            </div>
            <div class="mb-3">
                <label for="not_alani" class="form-label">Not</label>
                <textarea class="form-control" id="not_alani" name="not_alani" rows="2" placeholder="Not giriniz"></textarea>
            </div>
        </div>
    </div>
</div>



                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Modal Açma
    document.getElementById('openModal').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('addPersonModal'));
        modal.show();
    });

    // Sekme Geçişi
    document.querySelectorAll('.nav-link').forEach(function (tab) {
        tab.addEventListener('click', function () {
            // Tüm tab içeriklerini gizle
            document.querySelectorAll('.tab-content').forEach(function (content) {
                content.classList.add('d-none');
            });

            // Aktif tab'ı işaretle
            document.querySelectorAll('.nav-link').forEach(function (tab) {
                tab.classList.remove('active');
            });

            // Seçilen tab'ı aktif yap ve ilgili içeriği göster
            this.classList.add('active');
            const target = this.id === 'tabCalisan' ? '#calisanBilgileriTab' : '#digerBilgilerTab';
            document.querySelector(target).classList.remove('d-none');
        });
    });

    // Form Gönderme
    $('#addPersonForm').on('submit', function (e) {
        e.preventDefault(); // Sayfa yenilemesini engelle

        const formData = $(this).serialize();
        $.ajax({
            url: "{{ route('admin.personeller.store') }}",
            method: "POST",
            data: formData,
            success: function () {
                Swal.fire('Başarılı', 'Personel başarıyla kaydedildi!', 'success').then(() => {
                    location.reload(); // Sayfayı yenile
                });
            },
            error: function () {
                Swal.fire('Hata', 'Bir hata oluştu.', 'error');
            }
        });
    });

   // İşten Ayrılanları Göster/Gizle Butonu
const toggleButton = document.getElementById('toggleButton');

toggleButton.addEventListener('click', function () {
    // Mevcut durum kontrolü
    const isShowingDeparted = toggleButton.classList.contains('btn-danger');

    // URL'yi güncelle
    const url = new URL(window.location.href);
    url.searchParams.set('show_departed', isShowingDeparted ? 0 : 1);

    // Sayfayı yeniden yükle
    window.location.href = url.toString();
});

    // Arama Fonksiyonu
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('.personel-row').forEach(function (row) {
            const name = row.querySelector('td').textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    });
});

</script>
@endsection
