@extends('layouts.admin')

@section('content')

<div class="container-fluid" style="padding: 20px;">
    <!-- Butonlar -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <!-- Masraflara Dön Tuşu -->
        <a href="{{ route('admin.masraflar.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Masraflara Dön
        </a>
        
        <!-- Yeni Ana Masraf Grubu Oluştur Tuşu -->
        <button class="btn btn-success" id="openAnaMasrafGrubuModal">
            <i class="fas fa-plus-circle"></i> Yeni Ana Masraf Grubu Oluştur
        </button>
    </div>

    <!-- Masraf Grupları -->
    <div class="row" style="margin: 0 -15px;">
        @foreach ($masrafGruplari as $grup)
        <div class="col-md-6 col-lg-4" style="padding: 15px;">
            <div class="card" style="border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <!-- Kart Başlığı -->
                <div class="card-header" style="background-color: #007bff; color: white; font-weight: bold; display: flex; align-items: center;">
                    <span class="editAnaGrup" style="flex-grow: 1; cursor: pointer;" data-id="{{ $grup->id }}" data-ad="{{ $grup->ad }}">{{ $grup->ad }}</span>
                    <button class="btn btn-warning btn-sm openAltKalemModal" data-id="{{ $grup->id }}" style="font-size: 12px; padding: 5px 10px; background-color: #ffc107; color: black; border: none;">
                        Alt Kalem Ekle <i class="fas fa-plus"></i>
                    </button>
                </div>
                <!-- Kart Gövdesi -->
                <div class="card-body" style="padding: 20px;">
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        @foreach ($grup->masrafKalemleri as $kalem)
                        <span class="editAltKalem" data-id="{{ $kalem->id }}" data-ad="{{ $kalem->ad }}" style="cursor: pointer; font-size: 14px; padding: 10px 15px; border-radius: 5px; color: white; 
                            @switch($loop->index % 8)
                                @case(0) background-color: #4caf50; @break /* Green */
                                @case(1) background-color: #2196f3; @break /* Blue */
                                @case(2) background-color: #ffeb3b; color: black; @break /* Yellow */
                                @case(3) background-color: #f44336; @break /* Red */
                                @case(4) background-color: #9c27b0; @break /* Purple */
                                @case(5) background-color: #009688; @break /* Teal */
                                @case(6) background-color: #ff9800; @break /* Orange */
                                @case(7) background-color: #00bcd4; @break /* Cyan */
                                @default background-color: #607d8b; /* Grey */
                            @endswitch">
                            {{ $kalem->ad }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Ana Masraf Grubu Ekle Modal -->
<div class="modal fade" id="anaMasrafGrubuModal" tabindex="-1" aria-labelledby="anaMasrafGrubuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="anaMasrafGrubuModalLabel"><i class="fas fa-plus-circle"></i> Ana Masraf Grubu Oluştur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="anaMasrafGrubuForm">
                    <div class="form-group">
                        <label for="ana-grup-ad">Masraf Grubu Adı</label>
                        <input type="text" class="form-control" id="ana-grup-ad" name="ad" placeholder="Grup adı girin" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="saveAnaMasrafGrubu">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Ana Grup Düzenle Modal -->
<div class="modal fade" id="editAnaGrupModal" tabindex="-1" aria-labelledby="editAnaGrupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editAnaGrupModalLabel">Ana Grup Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAnaGrupForm">
                    <div class="form-group">
                        <label for="edit-ana-grup-ad">Grup Adı</label>
                        <input type="text" class="form-control" id="edit-ana-grup-ad" name="ad" required>
                        <input type="hidden" id="edit-ana-grup-id" name="id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteAnaGrup">Sil</button>
                <button type="button" class="btn btn-primary" id="saveAnaGrupEdit">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Alt Kalem Düzenle Modal -->
<div class="modal fade" id="editAltKalemModal" tabindex="-1" aria-labelledby="editAltKalemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editAltKalemModalLabel">Alt Kalem Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAltKalemForm">
                    <div class="form-group">
                        <label for="edit-alt-kalem-ad">Alt Kalem Adı</label>
                        <input type="text" class="form-control" id="edit-alt-kalem-ad" name="ad" required>
                        <input type="hidden" id="edit-alt-kalem-id" name="id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteAltKalem">Sil</button>
                <button type="button" class="btn btn-primary" id="saveAltKalemEdit">Kaydet</button>
            </div>
        </div>
    </div>
</div>
<!-- Alt Kalem Ekle Modal -->
<div class="modal fade" id="altKalemEkleModal" tabindex="-1" aria-labelledby="altKalemEkleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="altKalemEkleModalLabel"><i class="fas fa-plus-circle"></i> Alt Kalem Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="altKalemForm">
                    <div class="form-group">
                        <label for="alt-kalem-ad">Alt Kalem Adı</label>
                        <input type="text" class="form-control" id="alt-kalem-ad" name="ad" placeholder="Alt kalem adı girin" required>
                        <input type="hidden" id="alt-kalem-grup-id" name="masraf_grubu_id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-success" id="saveAltKalem">Kaydet</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        let enterCounter = {
            anaMasraf: 0,
            altKalem: 0,
            editAnaGrup: 0,
            editAltKalem: 0,
        };

        // Enter Tuşu ile İşlemler
        function handleEnter(modalSelector, saveButtonSelector, counterKey) {
            $(modalSelector).on('keydown', function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    enterCounter[counterKey]++;

                    if (enterCounter[counterKey] === 1) {
                        $(saveButtonSelector).click(); // Kaydet butonuna tıkla
                    } else if (enterCounter[counterKey] === 2) {
                        Swal.close(); // SweetAlert'i kapat
                        enterCounter[counterKey] = 0; // Sayaç sıfırla
                    }
                }
            });
        }

        handleEnter('#anaMasrafGrubuModal', '#saveAnaMasrafGrubu', 'anaMasraf');
        handleEnter('#altKalemEkleModal', '#saveAltKalem', 'altKalem');
        handleEnter('#editAnaGrupModal', '#saveAnaGrupEdit', 'editAnaGrup');
        handleEnter('#editAltKalemModal', '#saveAltKalemEdit', 'editAltKalem');

        // Ana Grup Düzenleme Modalını Aç
        $(document).on('click', '.editAnaGrup', function () {
            const id = $(this).data('id');
            const ad = $(this).data('ad');

            $('#edit-ana-grup-id').val(id);
            $('#edit-ana-grup-ad').val(ad);
            $('#editAnaGrupModal').modal('show');
        });

        // Ana Grup Güncelleme
        $('#saveAnaGrupEdit').click(function () {
            const id = $('#edit-ana-grup-id').val();
            const ad = $('#edit-ana-grup-ad').val();

            if (!ad) {
                Swal.fire('Uyarı!', 'Grup adı boş olamaz!', 'warning');
                return;
            }

            $.ajax({
                url: `/admin/masraf-tanimlari/updateGrup/${id}`,
                method: 'POST',
                data: { ad, _token: "{{ csrf_token() }}" },
                success: function () {
                    Swal.fire('Başarılı!', 'Grup adı güncellendi!', 'success');
                    location.reload();
                },
                error: function () {
                    Swal.fire('Hata!', 'Grup güncellenirken bir hata oluştu.', 'error');
                }
            });
        });

        // Ana Grup Silme
        $('#deleteAnaGrup').click(function () {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu işlem tüm alt kalemleri de silecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const id = $('#edit-ana-grup-id').val();
                    $.ajax({
                        url: `/admin/masraf-tanimlari/deleteGrup/${id}`,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {
                            Swal.fire('Silindi!', 'Grup ve tüm alt kalemler silindi.', 'success');
                            location.reload();
                        },
                        error: function () {
                            Swal.fire('Hata!', 'Grup silinirken bir hata oluştu.', 'error');
                        }
                    });
                }
            });
        });

        // Alt Kalem Düzenleme Modalını Aç
        $(document).on('click', '.editAltKalem', function () {
            const id = $(this).data('id');
            const ad = $(this).data('ad');

            $('#edit-alt-kalem-id').val(id);
            $('#edit-alt-kalem-ad').val(ad);
            $('#editAltKalemModal').modal('show');
        });

        // Alt Kalem Güncelleme
        $('#saveAltKalemEdit').click(function () {
            const id = $('#edit-alt-kalem-id').val();
            const ad = $('#edit-alt-kalem-ad').val();

            if (!ad) {
                Swal.fire('Uyarı!', 'Alt kalem adı boş olamaz!', 'warning');
                return;
            }

            $.ajax({
                url: `/admin/masraf-tanimlari/updateKalem/${id}`,
                method: 'POST',
                data: { ad, _token: "{{ csrf_token() }}" },
                success: function () {
                    Swal.fire('Başarılı!', 'Alt kalem adı güncellendi!', 'success');
                    location.reload();
                },
                error: function () {
                    Swal.fire('Hata!', 'Alt kalem güncellenirken bir hata oluştu.', 'error');
                }
            });
        });

        // Alt Kalem Silme
        $('#deleteAltKalem').click(function () {
            const id = $('#edit-alt-kalem-id').val();

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/masraf-tanimlari/deleteKalem/${id}`,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {
                            Swal.fire('Silindi!', 'Alt kalem silindi.', 'success');
                            location.reload();
                        },
                        error: function () {
                            Swal.fire('Hata!', 'Alt kalem silinirken bir hata oluştu.', 'error');
                        }
                    });
                }
            });
        });

        // Ana Masraf Grubu Oluşturma Modalını Aç
        $('#openAnaMasrafGrubuModal').click(function () {
            $('#anaMasrafGrubuModal').modal('show');
        });

        // Ana Masraf Grubu Oluşturma
        $('#saveAnaMasrafGrubu').click(function () {
            const ad = $('#ana-grup-ad').val();

            if (!ad) {
                Swal.fire('Uyarı!', 'Grup adı boş olamaz!', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('admin.masraf-tanimlari.store') }}",
                method: 'POST',
                data: { ad, _token: "{{ csrf_token() }}" },
                success: function () {
                    Swal.fire('Başarılı!', 'Ana masraf grubu oluşturuldu!', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire('Hata!', 'Ana masraf grubu oluşturulurken bir hata oluştu.', 'error');
                }
            });
        });

        // Alt Kalem Ekleme Modalını Aç
        $(document).on('click', '.openAltKalemModal', function () {
            const grupId = $(this).data('id');
            $('#alt-kalem-grup-id').val(grupId);
            $('#altKalemEkleModal').modal('show');
        });

        // Alt Kalem Ekleme
        $('#saveAltKalem').click(function () {
            const ad = $('#alt-kalem-ad').val();
            const grupId = $('#alt-kalem-grup-id').val();

            if (!ad || !grupId) {
                Swal.fire('Uyarı!', 'Alt kalem adı veya grup ID eksik!', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('admin.masraf-tanimlari.storeKalem') }}",
                method: 'POST',
                data: {
                    ad: ad,
                    masraf_grubu_id: grupId,
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    Swal.fire('Başarılı!', 'Alt kalem oluşturuldu!', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire('Hata!', 'Alt kalem oluşturulurken bir hata oluştu.', 'error');
                }
            });
        });
    });
</script>
@endsection
