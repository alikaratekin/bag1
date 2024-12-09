@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-3">
            <button class="btn btn-primary" data-toggle="modal" data-target="#newAccountModal">
                <i class="fas fa-plus"></i> Yeni Hesap Ekle
            </button>
        </div>
    </div>
    <div class="row" style="display: flex; flex-wrap: nowrap; justify-content: space-between; gap: 0;">
    <!-- Kasa Tanımları -->
    <div style="flex: 1; margin: 0 5px;">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>
                    {{ number_format($kasaBakiyeToplami, 2, ',', '.') }} <sup style="font-size: 16px;">TL</sup>
                </h3>
                <p>Kasa Bakiye Toplamı</p>
            </div>
            <div class="icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Banka Hesapları -->
    <div style="flex: 1; margin: 0 5px;">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>
                    {{ number_format($bankaBakiyeToplami, 2, ',', '.') }} <sup style="font-size: 16px;">TL</sup>
                </h3>
                <p>Banka Hesapları Bakiye Toplamı</p>
            </div>
            <div class="icon">
                <i class="fas fa-university"></i>
            </div>
        </div>
    </div>

    <!-- POS Hesapları -->
    <div style="flex: 1; margin: 0 5px;">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>
                    {{ number_format($posBakiyeToplami, 2, ',', '.') }} <sup style="font-size: 16px;">TL</sup>
                </h3>
                <p>Blokeli Hesaplar Bakiye Toplamı</p>
            </div>
            <div class="icon">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>
    </div>

    <!-- Kredi Kartları -->
    <div style="flex: 1; margin: 0 5px;">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>
                    {{ number_format($krediBakiyeToplami, 2, ',', '.') }} <sup style="font-size: 16px;">TL</sup>
                </h3>
                <p>Kredi Kartları Bakiye Toplamı</p>
            </div>
            <div class="icon">
                <i class="fas fa-credit-card"></i>
            </div>
        </div>
    </div>

    <!-- Genel Toplam -->
    <div style="flex: 1; margin: 0 5px;">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>
                    {{ number_format($genelToplam, 2, ',', '.') }} <sup style="font-size: 16px;">TL</sup>
                </h3>
                <p>Genel Toplam</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>
</div>

    <div class="row">
    <!-- Kasa Tanımları -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-header text-white" style="background-color: #007bff;">
                <i class="fas fa-wallet"></i> Kasa Tanımları
            </div>
            <div class="card-body">
                @foreach ($kasaTanimlari as $kasa)
                <div class="card mb-2" style="background-color: {{ $kasa->etiket_rengi }};">
                    <div class="card-header text-white d-flex align-items-center">
                        <a href="{{ route('admin.hesaplar.show', $kasa->hesap_no) }}" class="text-white text-decoration-none flex-grow-1">
                            <span>{{ $kasa->tanım }}</span>
                        </a>
                        <button type="button" class="btn btn-link text-white p-0 ml-auto"
                            data-toggle="modal" data-target="#updateAccountModal"
                            onclick="openUpdateModal('{{ $kasa->hesap_no }}', '{{ $kasa->tanım }}', '{{ $kasa->etiket_rengi }}')">
                            <i class="fas fa-cog" style="font-size: 1.2rem;"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.hesaplar.show', $kasa->hesap_no) }}" class="text-decoration-none">
                        <div class="card-body bg-light text-dark">
                            <span style="font-size: 1.2em; font-weight: bold;">
                                Güncel Bakiye: {{ number_format($kasa->guncel_bakiye, 2, ',', '.') }} {{ $kasa->para_birimi }}
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Banka Hesapları -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-header text-white" style="background-color: #28a745;">
                <i class="fas fa-university"></i> Banka Hesapları
            </div>
            <div class="card-body">
                @foreach ($bankaHesaplari as $banka)
                <div class="card mb-2" style="background-color: {{ $banka->etiket_rengi }};">
                    <div class="card-header text-white d-flex align-items-center">
                        <a href="{{ route('admin.hesaplar.show', $banka->hesap_no) }}" class="text-white text-decoration-none flex-grow-1">
                            <span>{{ $banka->tanım }}</span>
                        </a>
                        <button type="button" class="btn btn-link text-white p-0 ml-auto"
                            data-toggle="modal" data-target="#updateAccountModal"
                            onclick="openUpdateModal('{{ $banka->hesap_no }}', '{{ $banka->tanım }}', '{{ $banka->etiket_rengi }}')">
                            <i class="fas fa-cog" style="font-size: 1.2rem;"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.hesaplar.show', $banka->hesap_no) }}" class="text-decoration-none">
                        <div class="card-body bg-light text-dark">
                            <span style="font-size: 1.2em; font-weight: bold;">
                                Güncel Bakiye: {{ number_format($banka->guncel_bakiye, 2, ',', '.') }} {{ $banka->para_birimi }}
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- POS Hesapları -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-header text-white" style="background-color: #ffc107;">
                <i class="fas fa-cash-register"></i> Blokeli Hesaplar
            </div>
            <div class="card-body">
                @foreach ($posHesaplari as $pos)
                <div class="card mb-2" style="background-color: {{ $pos->etiket_rengi }};">
                    <div class="card-header text-white d-flex align-items-center">
                        <a href="{{ route('admin.hesaplar.show', $pos->hesap_no) }}" class="text-white text-decoration-none flex-grow-1">
                            <span>{{ $pos->tanım }}</span>
                        </a>
                        <button type="button" class="btn btn-link text-white p-0 ml-auto"
                            data-toggle="modal" data-target="#updateAccountModal"
                            onclick="openUpdateModal('{{ $pos->hesap_no }}', '{{ $pos->tanım }}', '{{ $pos->etiket_rengi }}')">
                            <i class="fas fa-cog" style="font-size: 1.2rem;"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.hesaplar.show', $pos->hesap_no) }}" class="text-decoration-none">
                        <div class="card-body bg-light text-dark">
                            <span style="font-size: 1.2em; font-weight: bold;">
                                Güncel Bakiye: {{ number_format($pos->guncel_bakiye, 2, ',', '.') }} {{ $pos->para_birimi }}
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kredi Kartları -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-header text-white" style="background-color: #dc3545;">
                <i class="fas fa-credit-card"></i> Kredi Kartları
            </div>
            <div class="card-body">
                @foreach ($krediKartlari as $kredi)
                <div class="card mb-2" style="background-color: {{ $kredi->etiket_rengi }};">
                    <div class="card-header text-white d-flex align-items-center">
                        <a href="{{ route('admin.hesaplar.show', $kredi->hesap_no) }}" class="text-white text-decoration-none flex-grow-1">
                            <span>{{ $kredi->tanım }}</span>
                        </a>
                        <button type="button" class="btn btn-link text-white p-0 ml-auto"
                            data-toggle="modal" data-target="#updateAccountModal"
                            onclick="openUpdateModal('{{ $kredi->hesap_no }}', '{{ $kredi->tanım }}', '{{ $kredi->etiket_rengi }}')">
                            <i class="fas fa-cog" style="font-size: 1.2rem;"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.hesaplar.show', $kredi->hesap_no) }}" class="text-decoration-none">
                        <div class="card-body bg-light text-dark">
                            <span style="font-size: 1.2em; font-weight: bold;">
                                Güncel Bakiye: {{ number_format($kredi->guncel_bakiye, 2, ',', '.') }} {{ $kredi->para_birimi }}
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>




</div>
<div class="modal fade" id="updateAccountModal" tabindex="-1" role="dialog" aria-labelledby="updateAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="updateAccountModalLabel">Hesap Güncelle</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateAccountForm" action="{{ route('admin.hesaplar.updateAccount') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="hesap_no" id="modal_hesap_no">
                    <div class="form-group">
                        <label for="modal_tanım">Tanım</label>
                        <input type="text" name="tanım" id="modal_tanım" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Etiket Rengi</label>
                        <div class="d-flex flex-wrap justify-content-between">
                            @foreach (['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#4CAF50', '#FFEB3B'] as $color)
                            <div class="color-box" style="background-color: {{ $color }}; width: calc(10% - 5px); height: 40px; cursor: pointer; border-radius: 4px; margin-bottom: 5px;"
                                data-color="{{ $color }}"></div>
                            @endforeach
                        </div>
                        <input type="hidden" name="etiket_rengi" id="modal_etiket_rengi" value="#000000">
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Yeni Hesap Modal -->
<div class="modal fade" id="newAccountModal" tabindex="-1" role="dialog" aria-labelledby="newAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newAccountModalLabel">Yeni Hesap Ekle</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newAccountForm" action="{{ route('admin.hesaplar.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="account_type">Hesap Türü</label>
                            <select name="account_type" id="account_type" class="form-control" required>
                                <option value="kasa">Kasa Tanımları</option>
                                <option value="banka">Banka Hesapları</option>
                                <option value="pos">POS Hesapları</option>
                                <option value="kredi">Kredi Kartları</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanım">Tanım</label>
                            <input type="text" name="tanım" id="tanım" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="para_birimi">Para Birimi</label>
                            <select name="para_birimi" id="para_birimi" class="form-control" required>
                                <option value="TRY">TRY - Türk Lirası</option>
                                
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="güncel_bakiye">Açılış Bakiyesi</label>
                            <input type="text" name="güncel_bakiye" id="güncel_bakiye" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Etiket Rengi</label>
                        <div class="d-flex flex-wrap justify-content-between">
                            @foreach (['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#4CAF50', '#FFEB3B'] as $color)
                                <div class="color-box" 
                                    style="background-color: {{ $color }}; width: calc(10% - 5px); height: 40px; cursor: pointer; border-radius: 4px; margin-bottom: 5px;" 
                                    data-color="{{ $color }}">
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="etiket_rengi" id="etiket_rengi" value="#000000">
                    </div>

                    <div class="form-group">
                        <label for="hesap_no">Hesap No</label>
                        <input type="text" name="hesap_no" id="hesap_no" class="form-control" readonly>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function openUpdateModal(hesapNo, tanım, etiketRengi) {
    document.getElementById('modal_hesap_no').value = hesapNo;
    document.getElementById('modal_tanım').value = tanım;
    document.getElementById('modal_etiket_rengi').value = etiketRengi;

    // Renk kutularını güncelle
    document.querySelectorAll('.color-box').forEach(function (box) {
        if (box.dataset.color === etiketRengi) {
            box.style.border = '3px solid black'; // Seçilen rengi işaretle
        } else {
            box.style.border = ''; // Diğerlerini temizle
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Renk kutularını tıklanabilir yap
    document.querySelectorAll('.color-box').forEach(function (box) {
        box.addEventListener('click', function () {
            document.getElementById('modal_etiket_rengi').value = this.dataset.color;
            document.querySelectorAll('.color-box').forEach(function (b) {
                b.style.border = ''; // Tüm kutuların çerçevesini kaldır
            });
            this.style.border = '3px solid black'; // Seçilen kutuyu işaretle
        });
    });
});


</script>


<script>
function normalizeNumber(value) {
    // Binlik ayraçları kaldır ve ondalık ayraçlarını noktaya çevir
    return value.replace(/\./g, '').replace(/,/g, '.');
}

    $(document).ready(function () {
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
        $('#güncel_bakiye').on('input', function () {
            const inputElement = $(this);

            // Giriş değerini al ve biçimlendir
            let formattedValue = formatNumber(inputElement.val());

            // Biçimlendirilmiş değeri input alanına geri yaz
            inputElement.val(formattedValue);
        });
    });
    </script>
<script>
    
    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert2 Bildirimleri
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Başarılı!',
            text: '{{ session('success') }}',
            confirmButtonText: 'Tamam'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: '{{ session('error') }}',
            confirmButtonText: 'Tamam'
        });
        @endif

        // Hesap No Otomatik Oluştur
        $('#newAccountModal').on('show.bs.modal', function () {
            fetch("{{ route('admin.hesaplar.getNewAccountNumber') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById('hesap_no').value = data.hesap_no;
                });
        });

       

        // Renk Seçimi
        document.querySelectorAll('.color-box').forEach(function (box) {
            box.addEventListener('click', function () {
                document.getElementById('etiket_rengi').value = this.dataset.color;
                document.querySelectorAll('.color-box').forEach(b => b.style.border = '');
                this.style.border = '3px solid black';
            });
        });
    });
</script>
@endsection
