@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-3 text-left">
        <!-- Proje Ekle Tuşu -->
        <button class="btn btn-primary" id="openProjeEkleModal">
            <i class="fas fa-plus"></i> Proje Ekle
        </button>
    </div>

    <!-- Projeler Listesi -->
    <div class="row">
        @foreach ($projeler as $proje)
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.projeler.show', $proje->id) }}" class="text-decoration-none">
                <div class="card project-card" 
                     style="border: none; 
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
                            border-radius: 10px; 
                            background-color: {{ $proje->durum ? '#28a745' : '#dc3545' }};">
                    <div class="card-body d-flex flex-column text-white">
                        <h5 class="card-title mb-3">{{ $proje->ad }}</h5>
                        <p class="card-text text-truncate">{{ $proje->aciklama }}</p>
                        <span class="badge bg-dark mt-auto align-self-start">{{ $proje->durum ? 'Aktif' : 'Pasif' }}</span>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Proje Ekle Modal -->
<div class="modal fade" id="projeEkleModal" tabindex="-1" aria-labelledby="projeEkleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="projeEkleModalLabel">Proje Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="projeEkleForm">
                    <div class="form-group">
                        <label for="proje-ad">Proje Adı</label>
                        <input type="text" class="form-control" id="proje-ad" name="ad" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="proje-aciklama">Proje Açıklaması</label>
                        <textarea class="form-control" id="proje-aciklama" name="aciklama"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="saveProjeEkle">Kaydet</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Modalı açma ve form temizleme
        $('#openProjeEkleModal').click(function () {
            $('#projeEkleForm')[0].reset(); // Formu temizle
            $('#projeEkleModal').modal('show'); // Modalı göster
        });

        // Proje Ekleme
        $('#saveProjeEkle').click(function () {
            const ad = $('#proje-ad').val();
            const aciklama = $('#proje-aciklama').val();

            if (!ad) {
                Swal.fire('Uyarı!', 'Proje adı boş olamaz!', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('admin.projeler.store') }}",
                method: "POST",
                data: {
                    ad: ad,
                    aciklama: aciklama,
                    team_id: "{{ auth()->user()->team_id }}",
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    Swal.fire('Başarılı!', 'Proje başarıyla eklendi!', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire('Hata!', 'Proje eklenirken bir hata oluştu.', 'error');
                }
            });
        });
    });
</script>
@endsection
