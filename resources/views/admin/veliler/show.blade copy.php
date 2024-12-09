@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Geri Dön Butonu -->
        <div class="row mb-2">
            <div class="col-sm-6">
                <a href="{{ route('admin.veliler.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>

        <!-- Veli Bilgileri -->
        <div class="row">
            <!-- Anne Bilgileri -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-female mr-2"></i>Anne Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        @if ($anne)
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>İsim:</strong> {{ $anne->isim }}</p>
                                    <p class="mb-1"><strong>TC:</strong> {{ $anne->tc }}</p>
                                    <p class="mb-1"><strong>Meslek:</strong> {{ $anne->meslek }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Telefon:</strong> {{ $anne->tel }}</p>
                                    <p class="mb-1"><strong>İş Telefonu:</strong> {{ $anne->is_tel }}</p>
                                    <p class="mb-1"><strong>E-Posta:</strong> {{ $anne->eposta }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">Anne bilgileri bulunmamaktadır.</div>
                            <button type="button" class="btn btn-primary btn-sm mt-2" data-toggle="modal"
                                data-target="#veliModal" data-type="anne">
                                Anne Bilgisi Ekle
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Baba Bilgileri -->
            <div class="col-md-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-male mr-2"></i>Baba Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        @if ($baba)
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>İsim:</strong> {{ $baba->isim }}</p>
                                    <p class="mb-1"><strong>TC:</strong> {{ $baba->tc }}</p>
                                    <p class="mb-1"><strong>Meslek:</strong> {{ $baba->meslek }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Telefon:</strong> {{ $baba->tel }}</p>
                                    <p class="mb-1"><strong>İş Telefonu:</strong> {{ $baba->is_tel }}</p>
                                    <p class="mb-1"><strong>E-Posta:</strong> {{ $baba->eposta }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">Baba bilgileri bulunmamaktadır.</div>
                            <button type="button" class="btn btn-primary btn-sm mt-2" data-toggle="modal"
                                data-target="#veliModal" data-type="baba">
                                Baba Bilgisi Ekle
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Öğrenci Bilgileri -->
            <div class="col-md-4">
                <div class="card card-warning">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-user-graduate mr-2"></i>Öğrenci Bilgileri
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#ogrenciModal">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($ogrenciler->isNotEmpty())
                            @foreach ($ogrenciler as $ogrenci)
                                <div class="card card-outline card-info mb-2">
                                    <div class="card-header p-2" data-toggle="collapse"
                                        data-target="#ogrenci-{{ $ogrenci->id }}" aria-expanded="false">
                                        <strong>{{ $ogrenci->isim }}</strong>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="ogrenci-{{ $ogrenci->id }}" class="collapse">
                                        <div class="card-body p-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>TC:</strong> {{ $ogrenci->tc }}</p>
                                                    <p class="mb-1"><strong>Doğum Tarihi:</strong>
                                                        {{ \Carbon\Carbon::parse($ogrenci->dogum_tarihi)->format('d/m/Y') }}
                                                    </p>
                                                    <p class="mb-1"><strong>Sınıfı:</strong> {{ $ogrenci->sinifi }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Eğitim Ücreti:</strong>
                                                        {{ $ogrenci->egitimucreti }}</p>
                                                    <p class="mb-1"><strong>Yemek Ücreti:</strong>
                                                        {{ $ogrenci->yemekucreti }}</p>
                                                    <p class="mb-1"><strong>Etüt Ücreti:</strong>
                                                        {{ $ogrenci->etutucreti }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">Öğrenci bilgileri bulunmamaktadır.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="veliModal" tabindex="-1" aria-labelledby="veliModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="veliModalLabel">Veli Bilgisi Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.veliler.storeEkVeli') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="veli_id" value="{{ $veli->id }}">
                        <input type="hidden" name="anne_baba" id="anne_baba">

                        <div class="form-group">
                            <label for="isim">İsim</label>
                            <input type="text" name="isim" class="form-control" id="isim" required>
                        </div>
                        <div class="form-group">
                            <label for="tc">TC</label>
                            <input type="text" name="tc" class="form-control" id="tc" required
                                maxlength="11">
                        </div>
                        <div class="form-group">
                            <label for="meslek">Meslek</label>
                            <input type="text" name="meslek" class="form-control" id="meslek">
                        </div>
                        <div class="form-group">
                            <label for="tel">Telefon</label>
                            <input type="text" name="tel" class="form-control" id="tel" maxlength="15">
                        </div>
                        <div class="form-group">
                            <label for="eposta">E-Posta</label>
                            <input type="email" name="eposta" class="form-control" id="eposta">
                        </div>
                        <div class="form-group">
                            <label for="is_tel">İş Telefonu</label>
                            <input type="text" name="is_tel" class="form-control" id="is_tel" maxlength="15">
                        </div>
                        <div class="form-group">
                            <label for="ev_tel">Ev Telefonu</label>
                            <input type="text" name="ev_tel" class="form-control" id="ev_tel" maxlength="15">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Öğrenci Ekle Modal -->
    <div class="modal fade" id="ogrenciModal" tabindex="-1" aria-labelledby="ogrenciModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ogrenciModalLabel">Öğrenci Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.veliler.storeOgrenci') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="veli_id" value="{{ $veli->id }}">

                        <div class="form-group">
                            <label for="ogrenci_isim">İsim</label>
                            <input type="text" name="isim" class="form-control" id="ogrenci_isim" required>
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_tc">TC</label>
                            <input type="text" name="tc" class="form-control" id="ogrenci_tc" required
                                maxlength="11">
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_dogum_tarihi">Doğum Tarihi</label>
                            <input type="date" name="dogum_tarihi" class="form-control" id="ogrenci_dogum_tarihi"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_mudureyet">Müdürüyet</label>
                            <select class="form-control" id="ogrenci_mudureyet" name="mudureyet" required>
                                <option value="anakolu">Anakolu</option>
                                <option value="ilkokul">İlkokul</option>
                                <option value="ortaokul">Ortaokul</option>
                                <option value="lise">Lise</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_sinifi">Sınıfı</label>
                            <input type="text" name="sinifi" class="form-control" id="ogrenci_sinifi" required>
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_egitimucret painted">Eğitim Ücreti</label>
                            <input type="number" step="0.01" name="egitimucreti" class="form-control"
                                id="ogrenci_egitimucreti" required>
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_yemekucreti">Yemek Ücreti</label>
                            <input type="number" step="0.01" name="yemekucreti" class="form-control"
                                id="ogrenci_yemekucreti" required>
                        </div>
                        <div class="form-group">
                            <label for="ogrenci_etutucreti">Etüt Ücreti</label>
                            <input type="number" step="0.01" name="etutucreti" class="form-control"
                                id="ogrenci_etutucreti" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Ek Veli form gönderimi
            $('#veliModal form').on('submit', function(e) {
                e.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

                const form = $(this);

                $.ajax({
                    url: "{{ route('admin.veliler.storeEkVeli') }}", // storeEkVeli rotasını çağır
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Başarılı SweetAlert mesajı
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Ek veli başarıyla eklendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                // Modal'ı kapat ve ilgili sayfaya yönlendir
                                $('#veliModal').modal('hide');
                                window.location.href = response
                                    .redirect; // Redirect URL'sine yönlendir
                            });
                        }
                    },
                    error: function(xhr) {
                        // Hata mesajı için SweetAlert
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Ek veli eklenirken bir hata oluştu.';

                        if (Object.keys(errors).length > 0) {
                            errorMessage = Object.values(errors).map(error => error.join(', '))
                                .join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            html: errorMessage,
                            confirmButtonText: 'Tamam'
                        });
                    }
                });
            });

            // Öğrenci Ekleme form gönderimi
            $('#ogrenciModal form').on('submit', function(e) {
                e.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

                const form = $(this);

                $.ajax({
                    url: "{{ route('admin.veliler.storeOgrenci') }}", // storeOgrenci rotasını çağır
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Başarılı SweetAlert mesajı
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Öğrenci başarıyla eklendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                // Modal'ı kapat ve ilgili sayfaya yönlendir
                                $('#ogrenciModal').modal('hide');
                                window.location.href = response
                                    .redirect; // Redirect URL'sine yönlendir
                            });
                        }
                    },
                    error: function(xhr) {
                        // Hata mesajı için SweetAlert
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Öğrenci eklenirken bir hata oluştu.';

                        if (Object.keys(errors).length > 0) {
                            errorMessage = Object.values(errors).map(error => error.join(', '))
                                .join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            html: errorMessage,
                            confirmButtonText: 'Tamam'
                        });
                    }
                });
            });


        });
    </script>
@endsection
