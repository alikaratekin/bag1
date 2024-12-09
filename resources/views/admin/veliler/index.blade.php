@extends('layouts.admin')

@section('content')
    <style>
        /* Tablo Genel Ayarları */
        #veliler-table {
            table-layout: auto;
            width: 100%;
            border-collapse: collapse;
        }

        /* Tablo Başlıkları */
        #veliler-table thead th {
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
        #veliler-table th,
        #veliler-table td {
            text-align: left;
            vertical-align: middle;
            font-size: 12px;
            padding: 5px;
            word-wrap: break-word;
            white-space: normal;
        }

        /* Tablo Satırları */
        #veliler-table tbody tr {
            height: auto;
            cursor: pointer;
        }

        #veliler-table tbody tr:hover {
            background-color: #f5f5f5;
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

        /* Kart Genel Ayarları */
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

        /* Üst Butonlar için */
        .top-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
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
        <button class="btn btn-success" id="openVeliEkleModal">
            <i class="fas fa-plus"></i> Yeni Veli Ekle
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Veliler</h3>
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
                <table id="veliler-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>İsim</th>
                            <th>TC</th>
                            <th>Meslek</th>
                            <th>Telefon</th>
                            <th>E-Posta</th>
                            <th>İş Tel</th>
                            <th>Ev Tel</th>
                            <th>Yakınlık</th>
                            <th>Adres</th>
                        </tr>
                    </thead>
                    <tbody id="veliTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Yeni Veli Ekle Modal -->
    <div class="modal fade" id="veliEkleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Yeni Veli Ekle</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="veliEkleForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="yakinlik">Yakınlık</label>
                            <select class="form-control" id="yakinlik" name="yakinlik" required>
                                <option value="anne">Anne</option>
                                <option value="baba">Baba</option>
                                <option value="dede">Dede</option>
                                <option value="akraba">Akraba</option>
                                <option value="komsu">Komşu</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="isim">İsim</label>
                            <input type="text" class="form-control" id="isim" name="isim" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tc">TC</label>
                            <input type="text" class="form-control" id="tc" name="tc" maxlength="11"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="meslek">Meslek</label>
                            <input type="text" class="form-control" id="meslek" name="meslek">
                        </div>

                        <div class="form-group mb-3">
                            <label for="tel">Telefon</label>
                            <input type="text" class="form-control" id="tel" name="tel">
                        </div>

                        <div class="form-group mb-3">
                            <label for="eposta">E-Posta</label>
                            <input type="email" class="form-control" id="eposta" name="eposta">
                        </div>

                        <div class="form-group mb-3">
                            <label for="is_tel">İş Telefonu</label>
                            <input type="text" class="form-control" id="is_tel" name="is_tel">
                        </div>

                        <div class="form-group mb-3">
                            <label for="ev_tel">Ev Telefonu</label>
                            <input type="text" class="form-control" id="ev_tel" name="ev_tel">
                        </div>

                        <div class="form-group mb-3">
                            <label for="adres">Adres</label>
                            <textarea class="form-control" id="adres" name="adres" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Tam ekran işlevselliği
            const cardElement = document.querySelector('.card');
            const tableContainer = $('.table-container');

            $('.card-header .btn[data-card-widget="maximize"]').on('click', function() {
                if (!document.fullscreenElement) {
                    // Tam ekrana geç
                    if (cardElement.requestFullscreen) {
                        cardElement.requestFullscreen();
                    } else if (cardElement.mozRequestFullScreen) {
                        cardElement.mozRequestFullScreen();
                    } else if (cardElement.webkitRequestFullscreen) {
                        cardElement.webkitRequestFullscreen();
                    } else if (cardElement.msRequestFullscreen) {
                        cardElement.msRequestFullscreen();
                    }
                } else {
                    // Tam ekrandan çık
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                }
            });

            // Tam ekran değişikliklerini dinle
            document.addEventListener('fullscreenchange', adjustTableHeight);
            document.addEventListener('webkitfullscreenchange', adjustTableHeight);
            document.addEventListener('mozfullscreenchange', adjustTableHeight);
            document.addEventListener('MSFullscreenChange', adjustTableHeight);

            function adjustTableHeight() {
                if (document.fullscreenElement) {
                    tableContainer.css('height', '100vh');
                    cardElement.classList.add('card-maximized');
                } else {
                    tableContainer.css('height', 'calc(100vh - 300px)');
                    cardElement.classList.remove('card-maximized');
                }
            }
            // Velileri yükle fonksiyonunu güncelle
            function loadVeliler(search = '') {
                $.ajax({
                    url: "{{ route('admin.veliler.getVeliler') }}",
                    method: "GET",
                    data: {
                        search: search
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        let tableBody = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(veli) {
                                tableBody += `
                                    <tr ondblclick="window.location.href='{{ url('admin/veliler') }}/${veli.id}'">
                                        <td>${veli.isim || ''}</td>
                                        <td>${veli.tc || ''}</td>
                                        <td>${veli.meslek || ''}</td>
                                        <td>${veli.tel || ''}</td>
                                        <td>${veli.eposta || ''}</td>
                                        <td>${veli.is_tel || ''}</td>
                                        <td>${veli.ev_tel || ''}</td>
                                        <td>${veli.yakinlik ? veli.yakinlik.charAt(0).toUpperCase() + veli.yakinlik.slice(1) : ''}</td>
                                        <td>${veli.adres || ''}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            tableBody =
                                '<tr><td colspan="9" class="text-center">Kayıt bulunamadı</td></tr>';
                        }
                        $('#veliTableBody').html(tableBody);
                    },
                    error: function(xhr) {
                        console.error('Hata:', xhr);
                        Swal.fire('Hata!', 'Veliler yüklenirken bir hata oluştu.', 'error');
                    }
                });
            }

            // Sayfa yüklendiğinde velileri getir
            loadVeliler();

            // Veli ekleme formu gönderimi
            $('#veliEkleForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: "{{ route('admin.veliler.store') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Veli başarıyla eklendi.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#veliEkleModal').modal('hide');
                        form[0].reset();
                        loadVeliler();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        let errorMessage = 'Veli eklenirken bir hata oluştu.';
                        if (errors) {
                            errorMessage = Object.values(errors).map(err => err.join(', '))
                                .join('<br>');
                        }
                        Swal.fire('Hata!', errorMessage, 'error');
                    }
                });
            });

            // Arama işlemi
            let searchTimer;
            $('#table-search').on('input', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();

                searchTimer = setTimeout(() => {
                    loadVeliler(searchValue);
                }, 300);
            });

            // Modal açma
            $('#openVeliEkleModal').click(function() {
                $('#veliEkleForm')[0].reset();
                $('#veliEkleModal').modal('show');
            });
        });
    </script>
@endsection
