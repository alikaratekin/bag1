@extends('layouts.admin')

@section('content')
    <style>
        /* Tablo Genel Ayarları */
        #hareketler-table {
            table-layout: auto;
            width: 100%;
            border-collapse: collapse;
        }

        /* Tablo Başlıkları */
        #hareketler-table thead th {
            background-color: #f8f9fa !important;
            color: #333 !important;
            position: sticky;
            top: 0;
            z-index: 2;
            font-size: 14px;
            padding: 5px;
            text-align: left;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Tablo Hücreleri */
        #hareketler-table tbody td {
            word-wrap: break-word;
            white-space: normal;
            font-size: 12px;
            padding: 5px;
            text-align: left;
        }

        /* Tablo Satırları */
        #hareketler-table tbody tr {
            height: auto;
        }

        /* Tablo Konteyneri */
        .table-container {
            height: calc(100vh - 300px);
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
            margin-top: 5px;
            /* Tablo kartının üst boşluğunu diğer öğelerle eşitle */
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
        <!-- Tedarikçi Bilgileri ve Finansal Kartlar -->
        <div class="row align-items-start">
            <!-- Tedarikçi Bilgileri -->
            <div class="col-md-3">
                <div class="small-box bg-primary text-white shadow-sm">
                    <div class="inner">
                        <h4 class="fw-bold">{{ $tedarikci->ad ?? 'Tedarikçi Adı' }}</h4>
                        <p><i class="fas fa-phone-alt me-2"></i> Telefon: {{ $tedarikci->numara ?? 'Belirtilmemiş' }}</p>
                        <p><i class="fas fa-id-card me-2"></i> Vergi No: {{ $tedarikci->vergino ?? 'Belirtilmemiş' }}</p>
                        <p><i class="fas fa-map-marker-alt me-2"></i> Adres: {{ $tedarikci->adres ?? 'Belirtilmemiş' }}</p>
                        <p><i class="fas fa-sticky-note me-2"></i> Not: {{ $tedarikci->not ?? 'Belirtilmemiş' }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-industry"></i>
                    </div>
                </div>
            </div>

            <!-- Finansal Kartlar -->
            <div class="col-md-9">
                <div class="row">
                    <!-- Toplam Alınan -->
                    <div class="col-md-4">
                        <div class="small-box bg-success shadow-sm">
                            <div class="inner">
                                <h3 class="toplam-alinan">₺0.00</h3> <!-- Dinamik tutar buraya yazılacak -->
                                <p>Toplam Alınan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-arrow-circle-down"></i>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="small-box bg-danger shadow-sm">
                            <div class="inner">
                                <h3 class="toplam-odenen">₺0.00</h3> <!-- Dinamik değer -->
                                <p>Toplam Ödenen</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-arrow-circle-up"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="small-box bg-info shadow-sm">
                            <div class="inner">
                                <h3 class="guncel-bakiye">₺0.00</h3> <!-- Dinamik değer -->
                                <p>Güncel Bakiye</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Butonlar -->
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.tedarikciler.index') }}" class="btn btn-dark btn-block shadow-sm"
                            style="height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-arrow-left me-2"></i> Geri Dön
                        </a>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-warning btn-block text-white shadow-sm" style="height: 80px;"
                            data-bs-toggle="modal" data-bs-target="#editTedarikciModal">
                            <i class="fas fa-edit me-2"></i> Tedarikçi Güncelle
                        </button>
                    </div>

                    <!-- Tedarikçiden Alım Yap Butonu -->
                    <div class="col-md-3">
                        <button class="btn btn-success btn-block shadow-sm" style="height: 80px;" data-bs-toggle="modal"
                            data-bs-target="#tedarikciAlimModal">
                            <i class="fas fa-cart-plus me-2"></i> Tedarikçiden Alım Yap
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-danger btn-block shadow-sm" style="height: 80px;" data-bs-toggle="modal"
                            data-bs-target="#tedarikciOdemeModal">
                            <i class="fas fa-credit-card me-2"></i> Tedarikçiye Ödeme Yap
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Hareketler Tablosu -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tedarikçi Hareketleri</h3>
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
                                <th>Tarih</th>
                                <th>İşlem Tipi</th>
                                <th>Kullanıcı</th>
                                <th>Açıklama</th>
                                <th>Hesap</th>
                                <th>Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX ile yüklenecek veriler -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tedarikçi Güncelle Modal -->
    <div class="modal fade" id="editTedarikciModal" tabindex="-1" aria-labelledby="editTedarikciModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editTedarikciModalLabel">Tedarikçi Bilgilerini Güncelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTedarikciForm">
                    <div class="modal-body">
                        <!-- Ad -->
                        <div class="mb-3">
                            <label for="ad" class="form-label">Ad</label>
                            <input type="text" id="ad" name="ad" class="form-control" required>
                        </div>
                        <!-- Telefon -->
                        <div class="mb-3">
                            <label for="numara" class="form-label">Telefon</label>
                            <input type="text" id="numara" name="numara" class="form-control">
                        </div>
                        <!-- Vergi No -->
                        <div class="mb-3">
                            <label for="vergino" class="form-label">Vergi No</label>
                            <input type="text" id="vergino" name="vergino" class="form-control">
                        </div>
                        <!-- Adres -->
                        <div class="mb-3">
                            <label for="adres" class="form-label">Adres</label>
                            <textarea id="adres" name="adres" class="form-control"></textarea>
                        </div>
                        <!-- Not -->
                        <div class="mb-3">
                            <label for="not" class="form-label">Not</label>
                            <textarea id="not" name="not" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Tedarikçiden Alım Yap Modal -->
    <div class="modal fade" id="tedarikciAlimModal" tabindex="-1" aria-labelledby="tedarikciAlimModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="tedarikciAlimModalLabel">Tedarikçiden Alım Yap</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="tedarikciAlimForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="alimTarih" class="form-label">Tarih</label>
                            <input type="date" id="alimTarih" name="tarih" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="alimAciklama" class="form-label">Açıklama</label>
                            <textarea id="alimAciklama" name="aciklama" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="alimTutar" class="form-label">Tutar</label>
                            <input type="text" id="alimTutar" name="tutar" class="form-control tutar-input"
                                step="0.01" min="0" required>
                        </div>
                    </form>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-success" id="submitAlimForm">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Tedarikçiye Ödeme Modal -->
    <div class="modal fade" id="tedarikciOdemeModal" tabindex="-1" aria-labelledby="tedarikciOdemeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="tedarikciOdemeModalLabel">Tedarikçiye Ödeme Yap</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tedarikciOdemeForm">
                        @csrf
                        <!-- Tarih -->
                        <div class="form-group">
                            <label for="odemeTarih" class="form-label">Tarih</label>
                            <input type="date" id="odemeTarih" name="tarih" class="form-control" required>
                        </div>
                        <!-- Tutar -->
                        <div class="form-group">
                            <label for="odemeTutar" class="form-label">Tutar</label>
                            <input type="text" id="odemeTutar" name="tutar" class="form-control tutar-input"
                                required>
                        </div>
                        <!-- Hesap Seçimi -->
                        <div class="form-group">
                            <label for="odemeHesapNo" class="form-label">Hesap</label>
                            <select id="odemeHesapNo" name="hesap_no" class="form-control" required>
                                <option value="" disabled selected>Hesap Seçin</option>
                                <!-- Dinamik olarak doldurulacak -->
                            </select>
                        </div>
                        <!-- Açıklama -->
                        <div class="form-group">
                            <label for="odemeAciklama" class="form-label">Açıklama</label>
                            <textarea id="odemeAciklama" name="aciklama" class="form-control" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-danger" id="submitOdemeForm">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editDeleteHareketModal" tabindex="-1" aria-labelledby="editDeleteHareketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Başlığı -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editDeleteHareketModalLabel">Hareketi Düzenle veya Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal İçeriği -->
                <form id="editDeleteHareketForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit-delete-id">
                        <input type="hidden" id="edit-delete-islem-tipi">

                        <!-- Tarih -->
                        <div class="mb-3">
                            <label for="edit-delete-tarih" class="form-label">Tarih</label>
                            <input type="datetime-local" id="edit-delete-tarih" class="form-control" required>
                        </div>


                        <!-- Açıklama -->
                        <div class="mb-3">
                            <label for="edit-delete-aciklama" class="form-label">Açıklama</label>
                            <textarea id="edit-delete-aciklama" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- Tutar -->
                        <div class="mb-3">
                            <label for="edit-delete-tutar" class="form-label">Tutar</label>
                            <input type="text" id="edit-delete-tutar" class="form-control tutar-input" required>
                        </div>
                    </div>

                    <!-- Modal Alt Kısmı -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="deleteHareketBtn">Sil</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const tedarikciId = "{{ $tedarikci->id }}";
            const teamId = "{{ auth()->user()->team_id }}"; // Takım ID'si
            const kullanici = "{{ auth()->user()->name }}"; // Kullanıcı adı

            // Tedarikçi bilgilerini modal açıldığında doldur
            $('#editTedarikciModal').on('show.bs.modal', function() {
                $('#ad').val("{{ $tedarikci->ad }}");
                $('#numara').val("{{ $tedarikci->numara }}");
                $('#vergino').val("{{ $tedarikci->vergino }}");
                $('#adres').val("{{ $tedarikci->adres }}");
                $('#not').val("{{ $tedarikci->not }}");
            });

            // Tedarikçi güncelleme işlemi
            $('#editTedarikciForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    _token: "{{ csrf_token() }}",
                    ad: $('#ad').val(),
                    numara: $('#numara').val(),
                    vergino: $('#vergino').val(),
                    adres: $('#adres').val(),
                    not: $('#not').val(),
                };

                $.ajax({
                    url: `/admin/tedarikciler/${tedarikciId}`,
                    method: "PUT",
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: response.message ||
                                'Tedarikçi başarıyla güncellendi.',
                        }).then(() => {
                            $('#editTedarikciModal').modal('hide');
                            location
                                .reload(); // Sayfayı yeniden yükleyerek güncellenmiş bilgileri göster
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Tedarikçi güncellenemedi.';
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            html: errorMessage,
                        });
                    }
                });
            });

            // Hesapları modal içine dinamik olarak yükle
            function loadHesaplar() {
                $.ajax({
                    url: `/admin/tedarikciler/hesaplar/${teamId}`,
                    method: "GET",
                    success: function(response) {
                        if (response.hesaplar) {
                            let options = '<option value="" disabled selected>Hesap Seçin</option>';
                            for (const grup in response.hesaplar) {
                                options += `<optgroup label="${grup}">`;
                                response.hesaplar[grup].forEach(function(hesap) {
                                    options +=
                                        `<option value="${hesap.hesap_no}">${hesap.tanım}</option>`;
                                });
                                options += '</optgroup>';
                            }
                            $('#odemeHesapNo').html(options);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Hesaplar bulunamadı.',
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Hesaplar yüklenirken bir hata oluştu.',
                        });
                    }
                });
            }

            // Ödeme modalı açıldığında hesapları yükle
            $('#tedarikciOdemeModal').on('show.bs.modal', function() {
                loadHesaplar();
            });

            // Finansal bilgileri getir ve kartlara yaz
            function fetchFinansalBilgiler() {
                $.ajax({
                    url: `/admin/tedarikciler/${tedarikciId}/finansal-bilgiler`,
                    method: "GET",
                    success: function(response) {
                        if (response.success) {
                            $('.toplam-alinan').text(
                                `₺${response.toplamAlinan.toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`
                            );
                            $('.toplam-odenen').text(
                                `₺${response.toplamOdenen.toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`
                            );
                            $('.guncel-bakiye').text(
                                `₺${response.guncelBakiye.toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`
                            );
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Finansal bilgiler alınırken bir hata oluştu.',
                        });
                    }
                });
            }

            // "Tedarikçiden Alım Yap" işleminde kaydet
            $('#submitAlimForm').on('click', function() {
                const formData = {
                    _token: "{{ csrf_token() }}",
                    tedarikci_id: tedarikciId,
                    team_id: teamId,
                    kullanici: kullanici,
                    tarih: $('#alimTarih').val(),
                    aciklama: $('#alimAciklama').val(),
                    tutar: $('#alimTutar').val(),
                    islem_tipi: 'Tedarikçiden Alım',
                };

                $.ajax({
                    url: `/admin/tedarikciler/${tedarikciId}/alim`,
                    method: "POST",
                    data: formData,
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Tedarikçiden alım başarıyla kaydedildi.',
                        }).then(() => {
                            $('#tedarikciAlimModal').modal('hide');
                            $('#tedarikciAlimForm')[0].reset();
                            fetchHareketler();
                            fetchFinansalBilgiler();
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Tedarikçiden alım kaydedilemedi.';
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            html: errorMessage,
                        });
                    }
                });
            });

            // "Tedarikçiye Ödeme Yap" işleminde kaydet
            $('#submitOdemeForm').on('click', function() {
                const formData = {
                    _token: "{{ csrf_token() }}",
                    tedarikci_id: tedarikciId,
                    team_id: teamId,
                    kullanici: kullanici,
                    tarih: $('#odemeTarih').val(),
                    tutar: $('#odemeTutar').val(),
                    hesap_no: $('#odemeHesapNo').val(),
                    aciklama: $('#odemeAciklama').val(),
                };

                $.ajax({
                    url: `/admin/tedarikciler/${tedarikciId}/odeme`,
                    method: "POST",
                    data: formData,
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Tedarikçiye ödeme başarıyla kaydedildi.',
                        }).then(() => {
                            $('#tedarikciOdemeModal').modal('hide');
                            $('#tedarikciOdemeForm')[0].reset();
                            fetchHareketler();
                            fetchFinansalBilgiler();
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Tedarikçiye ödeme kaydedilemedi.';
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            html: errorMessage,
                        });
                    }
                });
            });

            // Hareket tablosunu AJAX ile çek
            function fetchHareketler(search = '') {
                $.ajax({
                    url: `/admin/tedarikciler/${tedarikciId}/hareketler`,
                    method: "GET",
                    data: {
                        search: search
                    },
                    success: function(response) {
                        let tableBody = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(row) {
                                const hesapBilgisi = row.hesap_bilgisi ?
                                    `${row.hesap_bilgisi.grup} - ${row.hesap_bilgisi.ad}` :
                                    '';
                                tableBody += `
                                <tr data-id="${row.id}">
                                    <td>${row.tarih}</td>
                                    <td>${row.islem_tipi}</td>
                                    <td>${row.kullanici}</td>
                                    <td>${row.aciklama}</td>
                                    <td>${hesapBilgisi}</td>
                                    <td>₺${parseFloat(row.tutar).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}</td>
                                </tr>`;
                            });
                        } else {
                            tableBody =
                                '<tr><td colspan="6" class="text-center">Veri bulunamadı.</td></tr>';
                        }
                        $('#hareketler-table tbody').html(tableBody);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Hareketler yüklenirken bir hata oluştu.',
                        });
                    }
                });
            }
            $(document).ready(function() {
                // Çift Tıklama ile Modal Açma
                $('#hareketler-table').on('dblclick', 'tr', function() {
                    const row = $(this);
                    const id = row.data('id');

                    if (!id) {
                        Swal.fire('Hata!', 'Geçersiz hareket ID!', 'error');
                        return;
                    }

                    // Hareket detaylarını al
                    $.ajax({
                        url: `/admin/hareketler/${id}`, // GET isteği
                        method: 'GET',
                        success: function(response) {
                            // ID
                            $('#edit-delete-id').val(response.id);

                            // Tarihi formatla ve inputa yaz
                            const formattedDate = formatDateForDatetimeLocal(response
                                .tarih);
                            $('#edit-delete-tarih')
                                .val(formattedDate) // Formatlanmış tarihi inputa yaz
                                .data('old-tarih', response.tarih); // Eski tarih verisi

                            // Açıklama
                            $('#edit-delete-aciklama').val(response.aciklama);

                            // Tutarı formatla ve inputa yaz
                            $('#edit-delete-tutar')
                                .val(formatTutarForModal(response.tutar))
                                .data('old-tutar', response.tutar); // Eski tutar verisi

                            // İşlem Tipi
                            $('#edit-delete-islem-tipi').val(response.islem_tipi);

                            // Modalı aç
                            $('#editDeleteHareketModal').modal('show');
                        },
                        error: function() {
                            Swal.fire('Hata!', 'Hareket detayları alınamadı!', 'error');
                        },
                    });
                });

                // Tarihi `datetime-local` formatına dönüştürme fonksiyonu
                function formatDateForDatetimeLocal(date) {
                    const d = new Date(date);
                    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}T${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
                }

                // Tutarı formatlama fonksiyonu
                function formatTutarForModal(tutar) {
                    const parsedTutar = parseFloat(tutar).toFixed(2); // 2 ondalık
                    if (parsedTutar.endsWith('.00')) {
                        return parsedTutar.split('.')[0]; // Eğer .00 ise sadece tam kısmını döndür
                    }
                    return parsedTutar.replace('.', ','); // Normal gösterim (virgül ayırıcı)
                }

                // Güncelleme İşlemi
                $('#editDeleteHareketForm').on('submit', function(e) {
                    e.preventDefault();

                    const id = $('#edit-delete-id').val();
                    const tarih = $('#edit-delete-tarih').val();

                    // Tarih formatını kontrol et
                    if (!isValidDatetimeLocal(tarih)) {
                        Swal.fire('Hata!', 'Lütfen geçerli bir tarih ve saat girin.', 'error');
                        return;
                    }

                    const formData = {
                        _token: "{{ csrf_token() }}",
                        tarih: tarih, // Tarih doğrudan alınır
                        aciklama: $('#edit-delete-aciklama').val(),
                        tutar: $('#edit-delete-tutar').val(), // Tutar doğrudan alınır
                        islem_tipi: $('#edit-delete-islem-tipi').val(),
                        old_tarih: $('#edit-delete-tarih').data('old-tarih'), // Eski tarih
                        old_tutar: $('#edit-delete-tutar').data('old-tutar'), // Eski tutar
                    };

                    $.ajax({
                        url: `/admin/hareketler/${id}`,
                        method: 'PUT',
                        data: formData,
                        success: function() {
                            Swal.fire('Başarılı!', 'Hareket başarıyla güncellendi.',
                                'success').then(() => {
                                $('#editDeleteHareketModal').modal('hide');
                                fetchHareketler(); // Tabloyu yenile
                            });
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors || {};
                            let errorMessage = 'Hareket güncellenemedi.';
                            if (errors) {
                                errorMessage = Object.values(errors).flat().join(
                                '<br>');
                            }
                            Swal.fire('Hata!', errorMessage, 'error');
                        },
                    });
                });

                // `datetime-local` formatını kontrol eden fonksiyon
                function isValidDatetimeLocal(value) {
                    const datetimeLocalRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
                    return datetimeLocalRegex.test(value);
                }


                // Silme İşlemi
                $('#deleteHareketBtn').on('click', function() {
                    const id = $('#edit-delete-id').val(); // Silinecek hareketin ID'si
                    const islemTipi = $('#edit-delete-islem-tipi').val(); // İşlem tipi
                    const tarih = $('#edit-delete-tarih').val(); // Tam tarih ve saat formatı
                    const tutar = $('#edit-delete-tutar').val(); // Tutar

                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: 'Bu işlem geri alınamaz!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Evet, sil!',
                        cancelButtonText: 'İptal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/hareketler/${id}`,
                                method: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}", // CSRF token
                                    islem_tipi: islemTipi,
                                    tarih: tarih, // Tarih olduğu gibi gönderilir
                                    tutar: tutar,
                                },
                                success: function(response) {
                                    Swal.fire('Başarılı!',
                                        'Hareket başarıyla silindi.',
                                        'success').then(() => {
                                        $('#editDeleteHareketModal')
                                            .modal('hide');
                                        fetchHareketler
                                            (); // Tabloyu yenile
                                    });
                                },
                                error: function(xhr) {
                                    const error = xhr.responseJSON?.error ||
                                        'Hareket silinemedi.';
                                    Swal.fire('Hata!', error, 'error');
                                }
                            });
                        }
                    });
                });

                // Tutarı formatla: 00'ı gizle
                function formatTutarForModal(tutar) {
                    const parsedTutar = parseFloat(tutar).toFixed(2); // 2 ondalık
                    if (parsedTutar.endsWith('.00')) {
                        return parsedTutar.split('.')[0]; // Eğer .00 ise sadece tam kısmını döndür
                    }
                    return parsedTutar.replace('.', ','); // Normal gösterim
                }
            });




            // Sayfa yüklendiğinde hareketler ve finansal bilgileri getir
            fetchHareketler();
            fetchFinansalBilgiler();
            scrollToBottom(document.getElementById('table-container'));

            // Arama alanı için olay
            $('#table-search').on('input', function() {
                const search = $(this).val();
                fetchHareketler(search);
            });

            // Tam ekran özelliği
            $('#toggle-fullscreen').on('click', function() {
                const card = $('.card')[0];
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    card.requestFullscreen();
                }
            });
        });
    </script>
@endsection
