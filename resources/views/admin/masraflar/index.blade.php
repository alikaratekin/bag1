@extends('layouts.admin')

@section('content')
    <style>
        /* Tablo Genel Ayarları */
        #masraflar-table {
            table-layout: auto;
            /* Sütun genişliklerini içeriğe göre ayarla */
            width: 100%;
            /* Tablonun tam genişlikte görünmesini sağlar */
            border-collapse: collapse;
            /* Hücreler arası boşlukları kaldırır */
        }

        /* Tablo Başlıkları */
        #masraflar-table thead th {
            background-color: #f8f9fa !important;
            /* Çok hafif gri arka plan */
            color: #333 !important;
            /* Yazı rengi gri tonunda */
            position: sticky;
            top: 0;
            z-index: 2;
            font-size: 14px;
            /* Daha küçük yazı tipi */
            padding: 5px;
            /* Daha az boşluk */
            text-align: left;
            /* Başlıkları sola hizala */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            /* Hafif gölge */
        }

        /* Tablo Hücreleri */
        #masraflar-table th,
        #masraflar-table td {
            text-align: left;
            /* Metni ortalar */
            vertical-align: middle;
            /* Satır ortasına hizalar */
            font-size: 12px;
            /* Daha küçük yazı tipi */
            padding: 5px;
            /* Hücre içi boşlukları küçült */
            word-wrap: break-word;
            /* Uzun kelimeler alt satıra geçer */
            white-space: normal;
            /* Taşma olmadan satırları böler */
        }

        /* Tablo Satırları */
        #masraflar-table tbody tr {
            height: auto;
            /* İçeriğe göre satır yüksekliği */
        }

        /* Tablo Konteyneri */
        .table-container {
            height: calc(100vh - 300px);
            /* Dinamik yükseklik ayarı */
            overflow-y: auto;
            /* Dikey kaydırma */
            overflow-x: hidden;
            /* Yatay kaydırmayı kapatır */
            border: 1px solid #ddd;
            /* Çerçeve */
            border-radius: 5px;
            /* Köşeleri yuvarlatır */
        }

        /* Kart Başlığı */
        .card-header {
            background-color: #007bff;
            /* Başlık arka plan rengi */
            color: #fff;
            /* Başlık yazı rengi */
        }

        /* Kart Genel Ayarları */
        .card {
            margin-bottom: 20px;
            /* Alt boşluk */
        }

        /* Arama Kutusu */
        .search input {
            width: 300px;
            /* Genişlik */
            padding: 10px;
            /* İç boşluk */
            font-size: 14px;
            /* Yazı boyutu */
            border-radius: 5px;
            /* Köşeleri yuvarlatır */
        }

        .search {
            display: flex;
            justify-content: flex-end;
            /* Sağ hizalar */
            margin-bottom: 15px;
            /* Alt boşluk */
        }

        /* Üst Butonlar için */
        .top-buttons {
            display: flex;
            gap: 10px;
            /* Butonlar arası boşluk */
            margin-bottom: 20px;
            /* Alt boşluk */
        }

        /* Kart Tam Ekran Modu */
        .card-maximized {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1050;
            overflow: auto;
        }
    </style>



    <div class="top-buttons">
        <!-- Yeni Masraf Ekle Tuşu -->
        <button class="btn btn-success" id="openMasrafEkleModal">
            <i class="fas fa-plus"></i> Yeni Masraf Ekle
        </button>

        <!-- Masraf Tanımları Ekle/Düzenle Tuşu -->
        <a href="{{ route('admin.masraf-tanimlari.index') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Masraf Tanımları Ekle/Düzenle
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Masraflar</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="search">
                <input type="text" id="table-search" placeholder="Arama yapın..." class="form-control">
            </div>
            <div class="table-container">
                <table id="masraflar-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Kullanıcı</th>
                            <th>Masraf Kalemi</th>
                            <th>Açıklama</th>
                            <th>Hesap</th> <!-- Yeni Sütun -->
                            <th>Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX ile yüklenecek -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Yeni Masraf Ekle Modal -->
    <div class="modal fade" id="masrafEkleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Yeni Masraf Ekle</h5>
                </div>
                <form id="masrafEkleForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Proje -->
                        <div class="form-group mb-3">
                            <label for="proje">Proje (Opsiyonel)</label>
                            <select class="form-control" id="proje" name="proje">
                                <option value="">Seçiniz</option>
                                @foreach ($projeler as $proje)
                                    <option value="{{ $proje->id }}">{{ $proje->ad }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tarih -->
                        <div class="form-group mb-3">
                            <label for="tarih">Tarih</label>
                            <input type="datetime-local" class="form-control" id="tarih" name="tarih" required>
                        </div>

                        <!-- Masraf Kalemi -->
                        <div class="form-group mb-3">
                            <label for="masrafKalemi">Masraf Kalemi</label>
                            <select class="form-control" id="masrafKalemi" name="masraf_kalemi_id" required>
                                <option value="">Seçiniz</option>
                                @foreach ($masrafGruplari as $grupAd => $kalemler)
                                    <optgroup label="{{ $grupAd }}">
                                        @foreach ($kalemler as $kalem)
                                            <option value="{{ $kalem->id }}">{{ $kalem->ad }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Açıklama -->
                        <div class="form-group mb-3">
                            <label for="aciklama">Açıklama</label>
                            <textarea class="form-control" id="aciklama" name="aciklama" rows="3" required></textarea>
                        </div>

                        <!-- Tutar -->
                        <div class="form-group mb-3">
                            <label for="tutar">Tutar (₺)</label>
                            <input type="text" class="form-control" id="masrafekletutar" name="tutar"
                                placeholder="Tutar giriniz..." required>
                        </div>

                        <!-- Ödeme Hesapları -->
                        <div class="form-group mb-3">
                            <label for="hesap">Ödeme Yapılan Hesap</label>
                            <select class="form-control" id="hesap" name="kaynak_hesap_no" required>
                                <option value="">Seçiniz</option>
                                @foreach ($hesaplar as $grupAd => $hesapListesi)
                                    <optgroup label="{{ $grupAd }}">
                                        @foreach ($hesapListesi as $hesap)
                                            <option value="{{ $hesap->hesap_no }}">{{ $hesap->tanım }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <!-- Düzenleme Modalı -->
    <div class="modal fade" id="editMasrafModal" tabindex="-1" aria-labelledby="editMasrafModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editMasrafModalLabel">
                        <i class="fas fa-edit"></i> Masrafı Düzenle
                    </h5>
                    <!-- Kapatma simgesi kaldırıldı -->
                </div>
                <form id="editMasrafForm">
                    @csrf
                    <div class="modal-body">
                        <!-- ID -->
                        <input type="hidden" id="edit-id" name="id">

                        <!-- Tarih -->
                        <div class="form-group mb-3">
                            <label for="edit-tarih">Tarih</label>
                            <input type="datetime-local" class="form-control" id="edit-tarih" name="tarih" required>
                        </div>

                        <!-- Masraf Kalemi -->
                        <div class="form-group mb-3">
                            <label for="edit-masrafKalemi">Masraf Kalemi</label>
                            <select class="form-control" id="edit-masrafKalemi" name="masraf_kalemi_id" required>
                                @foreach ($masrafGruplari as $grupAd => $kalemler)
                                    <optgroup label="{{ $grupAd }}">
                                        @foreach ($kalemler as $kalem)
                                            <option value="{{ $kalem->id }}">{{ $kalem->ad }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Açıklama -->
                        <div class="form-group mb-3">
                            <label for="edit-aciklama">Açıklama</label>
                            <textarea class="form-control" id="edit-aciklama" name="aciklama" rows="3" required></textarea>
                        </div>

                        <!-- Tutar -->
                        <div class="form-group mb-3">
                            <label for="edit-tutar">Tutar (₺)</label>
                            <input type="text" class="form-control" id="edit-tutar" name="tutar" required>
                        </div>

                        <!-- Hesap -->
                        <div class="form-group mb-3">
                            <label for="edit-hesap">Hesap</label>
                            <select class="form-control" id="edit-hesap" name="kaynak_hesap_no" disabled>
                                @foreach ($hesaplar as $grupAd => $hesapListesi)
                                    <optgroup label="{{ $grupAd }}">
                                        @foreach ($hesapListesi as $hesap)
                                            <option value="{{ $hesap->hesap_no }}">{{ $hesap->tanım }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <!-- Alttaki kapatma tuşu kaldırıldı -->
                        <button type="button" class="btn btn-danger" id="deleteMasraf">Sil</button>
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
            const cardElement = document.querySelector('.card'); // Kartın kendisi
            const tableContainer = $('.table-container'); // Tablo kapsayıcı

            // Tam ekran butonuna tıklama işlemi
            $('.card-header .btn[data-card-widget="maximize"]').on('click', function() {
                if (document.fullscreenElement) {
                    // Eğer tam ekrandaysa, tam ekrandan çık
                    document.exitFullscreen()
                        .then(() => {
                            tableContainer.removeClass('fullscreen'); // fullscreen sınıfını kaldır
                            console.log("Tam ekrandan çıkıldı.");
                        })
                        .catch(err => console.error("Tam ekrandan çıkılamadı:", err));
                } else {
                    // Kartı tam ekran yap
                    cardElement.requestFullscreen()
                        .then(() => {
                            tableContainer.addClass('fullscreen'); // fullscreen sınıfını ekle
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
        function normalizeNumber(value) {
            // Binlik ayraçları kaldır ve ondalık ayraçlarını noktaya çevir
            return value.replace(/\./g, '').replace(/,/g, '.');
        }

        $(document).ready(function() {
            // Biçimlendirme fonksiyonu
            function formatNumber(value) {
                // Sayıyı sadece rakamlara ve virgüle çevir
                value = value.replace(/[^\d,]/g, '');

                // Eğer virgülden sonra iki karakterden fazlası varsa kes
                const parts = value.split(',');
                if (parts.length > 1) {
                    parts[1] = parts[1].substring(0, 2); // Sadece 2 karaktere izin ver
                }
                value = parts.join(',');

                // Binlik ayracı ekle
                const numberParts = value.split(',');
                numberParts[0] = numberParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Binlik ayracı ekle
                return numberParts.join(',');
            }

            // Giriş olaylarını yönet
            $('#masrafekletutar,#edit-tutar').on('input', function() {
                const inputElement = $(this);

                // Giriş değerini al ve biçimlendir
                let formattedValue = formatNumber(inputElement.val());

                // Biçimlendirilmiş değeri input alanına geri yaz
                inputElement.val(formattedValue);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Modal Açma
            $('#openMasrafEkleModal').click(function() {
                $('#masrafEkleModal').modal('show');
                // Tarih alanını bugünün tarihi ve saatiyle doldur
                const tarihInput = $('#tarih');
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2,
                    '0'); // Aylar 0-11 arasıdır, +1 ekliyoruz
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                // datetime-local formatına uygun şekilde ayarla
                tarihInput.val(`${year}-${month}-${day}T${hours}:${minutes}`);

            });

            // Masraf Ekleme Formu Gönderimi
            $('#masrafEkleForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);

                // Tutar alanını normalize et
                const tutarField = form.find('input[name="tutar"]');
                const normalizedValue = normalizeNumber(tutarField.val());

                tutarField.val(normalizedValue); // Normalize edilmiş değeri input'a yaz

                // AJAX ile form gönderimi
                $.ajax({
                    url: "{{ route('admin.masraflar.store') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire('Başarılı!', 'Masraf başarıyla eklendi.', 'success');
                        $('#masrafEkleModal').modal('hide');
                        fetchMasraflar(); // Tabloyu yenile
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        let errorMessage = 'Masraf eklenirken bir hata oluştu.';
                        if (errors) {
                            errorMessage = Object.values(errors).map(err => err.join(', '))
                                .join('<br>');
                        }
                        Swal.fire('Hata!', errorMessage, 'error');
                    }
                });
            });

            // Masraflar Tablosunu Güncelle
            function fetchMasraflar(search = '') {
                $.ajax({
                    url: "{{ route('admin.masraflar.getMasraflar') }}",
                    method: "GET",
                    data: {
                        search
                    },
                    success: function(response) {
                        let tableBody = '';
                        response.data.forEach(function(row) {
                            tableBody += `
                            <tr data-id="${row.id}">
                                <td>${row.tarih}</td>
                                <td>${row.kullanici}</td>
                                <td>${row.masrafGrubu} / ${row.masrafKalemi}</td>
                                <td>${row.aciklama || 'Belirtilmedi'}</td>
                                <td>${row.hesap || 'Belirtilmedi'}</td>
                                <td>₺${parseFloat(row.tutar).toLocaleString('tr-TR')}</td>
                            </tr>`;
                        });
                        $('#masraflar-table tbody').html(tableBody);
                    },
                    error: function() {
                        Swal.fire('Hata!', 'Masraflar verileri yüklenirken bir hata oluştu.', 'error');
                    }
                });
            }

            fetchMasraflar();

            // Tutar alanını Türkçe formatına çeviren fonksiyon
            function formatCurrency(value) {
                return parseFloat(value).toLocaleString('tr-TR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Tablo satırına çift tıklama: Düzenleme Modalını Aç
            // Tablo satırına çift tıklama: Düzenleme Modalını Aç
            $('#masraflar-table').on('dblclick', 'tr', function() {
                const row = $(this);
                const id = row.data('id');

                if (!id) {
                    Swal.fire('Hata!', 'Geçersiz masraf ID!', 'error');
                    return;
                }

                // Backend'den masraf detaylarını al
                $.ajax({
                    url: "{{ route('admin.masraflar.show', '') }}/" + id,
                    method: "GET",
                    success: function(response) {
                        $('#edit-id').val(response.id);

                        // Tarih düzeltmesi
                        const date = new Date(response.tarih);
                        const offset = date.getTimezoneOffset();
                        date.setMinutes(date.getMinutes() - offset);

                        const formattedDate = date.toISOString().slice(0, 16);
                        $('#edit-tarih').val(formattedDate);

                        $('#edit-masrafKalemi').val(response.masraf_kalemi_id);
                        $('#edit-aciklama').val(response.aciklama);
                        $('#edit-hesap').val(response.hesap_no);

                        // Tutarı Türkçe formatına çevirerek göster
                        const formattedTutar = formatCurrency(response.tutar);
                        $('#edit-tutar').val(formattedTutar);

                        $('#editMasrafModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Hata!', 'Masraf detayları alınamadı!', 'error');
                    }
                });
            });


            // Güncelleme Formu Gönderimi
            $('#editMasrafForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);

                // Tutar alanını normalize et
                const tutarField = form.find('input[name="tutar"]');
                const normalizedValue = normalizeNumber(tutarField.val());
                tutarField.val(normalizedValue);

                // AJAX ile form gönderimi
                $.ajax({
                    url: "{{ route('admin.masraflar.update') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire('Başarılı!', 'Masraf başarıyla güncellendi.', 'success');
                        $('#editMasrafModal').modal('hide');
                        fetchMasraflar(); // Tabloyu yenile
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        let errorMessage = 'Masraf güncellenirken bir hata oluştu.';
                        if (errors) {
                            errorMessage = Object.values(errors).map(err => err.join(', '))
                                .join('<br>');
                        }
                        Swal.fire('Hata!', errorMessage, 'error');
                    }
                });
            });

            // Silme İşlemi
            $('#deleteMasraf').click(function() {
                const id = $('#edit-id').val();

                if (!id) {
                    Swal.fire('Hata!', 'Silinecek masraf bulunamadı!', 'error');
                    return;
                }

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
                            url: "{{ route('admin.masraflar.delete') }}",
                            method: "POST",
                            data: {
                                id: id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                Swal.fire('Başarılı!', 'Masraf başarıyla silindi.',
                                    'success');
                                $('#editMasrafModal').modal('hide');
                                fetchMasraflar();
                            },
                            error: function() {
                                Swal.fire('Hata!', 'Masraf silinirken bir hata oluştu.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Arama Kutusu
            $('#table-search').on('input', function() {
                const search = $(this).val();
                fetchMasraflar(search);
            });

            // Binlik ayraçları kaldır ve ondalık ayraçlarını noktaya çevir
            function normalizeNumber(value) {
                return value.replace(/\./g, '').replace(/,/g, '.');
            }
        });
    </script>
@endsection
