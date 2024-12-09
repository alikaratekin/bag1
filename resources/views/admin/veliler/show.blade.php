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
        <style>
            /* Tablo Genel Ayarları */
            #veli-hareketleri-table {
                table-layout: auto;
                width: 100%;
                border-collapse: collapse;
            }

            /* Tablo Başlıkları */
            #veli-hareketleri-table thead th {
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
            #veli-hareketleri-table th,
            #veli-hareketleri-table td {
                text-align: left;
                vertical-align: middle;
                font-size: 12px;
                padding: 5px;
                word-wrap: break-word;
                white-space: normal;
                border: 1px solid #dee2e6;
            }

            /* Tablo Satırları */
            #veli-hareketleri-table tbody tr {
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

            .card {
                margin-top: 10px !important;
                /* Daha düşük bir değer ile ayarlayın */
            }
        </style>
        <div class="row">
            <!-- Sol taraf: Birincil ve Ek Veli Bilgileri ve Veli Hareketleri -->
            <div class="col-md-8">
                <div class="row">
                    <!-- Birincil Veli Bilgileri -->
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title"><i class="fas fa-user mr-2"></i>Birincil Veli Bilgileri</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool text-white" data-toggle="modal"
                                        data-target="#editBirincilVeliModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>İsim:</strong> {{ $veli->isim }}</p>
                                        <p class="mb-1"><strong>TC:</strong> {{ $veli->tc }}</p>
                                        <p class="mb-1"><strong>Meslek:</strong> {{ $veli->meslek }}</p>
                                        <p class="mb-1"><strong>Yakınlık:</strong> {{ $veli->yakinlik }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Telefon:</strong> {{ $veli->tel }}</p>
                                        <p class="mb-1"><strong>İş Telefonu:</strong> {{ $veli->is_tel }}</p>
                                        <p class="mb-1"><strong>E-Posta:</strong> {{ $veli->eposta }}</p>
                                        <p class="mb-1"><strong>Adres:</strong> {{ $veli->adres }}</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Ek Veli Bilgileri -->
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Ek Veli Bilgileri</h3>
                                @if ($ekVeli)
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool text-white" data-toggle="modal"
                                            data-target="#editEkVeliModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                @if ($ekVeli)
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-1"><strong>İsim:</strong> {{ $ekVeli->isim }}</p>
                                            <p class="mb-1"><strong>TC:</strong> {{ $ekVeli->tc }}</p>
                                            <p class="mb-1"><strong>Meslek:</strong> {{ $ekVeli->meslek }}</p>
                                            <p class="mb-1"><strong>Yakınlık:</strong> {{ $ekVeli->yakinlik }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-1"><strong>Telefon:</strong> {{ $ekVeli->tel }}</p>
                                            <p class="mb-1"><strong>İş Telefonu:</strong> {{ $ekVeli->is_tel }}</p>
                                            <p class="mb-1"><strong>E-Posta:</strong> {{ $ekVeli->eposta }}</p>
                                            <p class="mb-1"><strong>Adres:</strong> {{ $ekVeli->adres }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">Ek veli bilgileri bulunmamaktadır.</div>
                                    <button type="button" class="btn btn-primary btn-sm mt-2" data-toggle="modal"
                                        data-target="#veliModal">
                                        Ek Veli Ekle
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finans Özeti -->
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($totalBorcu, 2, ',', '.') }} ₺</h3>
                                <p>Tüm Zamanlar Borçlanması</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($totalOdedi, 2, ',', '.') }} ₺</h3>
                                <p>Tüm Zamanlar Ödemesi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($currentBalance, 2, ',', '.') }} ₺</h3>
                                <p>Güncel Bakiye</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Veli Hareketleri -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">Veli Hareketleri</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table id="veli-hareketleri-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tarih</th>
                                        <th>İşlem Tipi</th>
                                        <th>Öğrenci Adı</th>
                                        <th>Borcu</th>
                                        <th>Ödediği</th>
                                        <th>Hesap No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($veliHareketleri as $hareket)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($hareket->tarih)->format('d/m/Y') }}</td>
                                            <td>{{ $hareket->islem_tipi }}</td>
                                            <td>{{ $hareket->ogrenci->isim ?? '—' }}</td>
                                            <td>{{ number_format($hareket->borcu, 2, ',', '.') }} ₺</td>
                                            <td>{{ number_format($hareket->odedi, 2, ',', '.') }} ₺</td>
                                            <td>{{ $hareket->hesap_no ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Henüz işlem yok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ taraf: Öğrenci Bilgileri -->
            <div class="col-md-4">
                <div class="card card-warning">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-user-graduate mr-2"></i>Öğrenci Bilgileri</h3>
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
                                    <div class="card-header p-2 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title">{{ $ogrenci->isim }}|{{ $ogrenci->egitim_donemi }}
                                        </h5>
                                        <div class="card-tools">
                                            @if ($ogrenci->id)
                                                <button type="button" class="btn btn-tool"
                                                    onclick="editOgrenci({{ $ogrenci->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="ogrenci-{{ $ogrenci->id }}">
                                        <div class="card-body p-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>TC:</strong> {{ $ogrenci->tc }}</p>
                                                    <p class="mb-1"><strong>Cinsiyet:</strong> {{ $ogrenci->cinsiyet }}
                                                    </p>
                                                    <p class="mb-1"><strong>Doğum Tarihi:</strong>
                                                        {{ \Carbon\Carbon::parse($ogrenci->dogum_tarihi)->format('d/m/Y') }}
                                                    </p>
                                                    <p class="mb-1"><strong>Müdüriyet:</strong>
                                                        {{ $ogrenci->mudureyet }}</p>
                                                    <p class="mb-1"><strong>Sınıfı:</strong> {{ $ogrenci->sinifi }}</p>

                                                    <p class="mb-1"><strong>Kontenjan:</strong>
                                                        {{ $ogrenci->kontenjan }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Eğitim Dönemi:</strong>
                                                        {{ $ogrenci->egitim_donemi }}</p>
                                                    <p class="mb-1"><strong>Eğitim Ücreti:</strong>
                                                        {{ number_format($ogrenci->egitimucreti, 2, ',', '.') }} ₺</p>
                                                    <p class="mb-1"><strong>Yemek Ücreti:</strong>
                                                        {{ number_format($ogrenci->yemekucreti, 2, ',', '.') }} ₺</p>
                                                    <p class="mb-1"><strong>Etüt Ücreti:</strong>
                                                        {{ number_format($ogrenci->etutucreti, 2, ',', '.') }} ₺</p>
                                                    <p class="mb-1"><strong>Kırtasiye Ücreti:</strong>
                                                        {{ number_format($ogrenci->kirtasiyeucreti, 2, ',', '.') }} ₺</p>
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

    <!-- Veli Modal -->
    <div class="modal fade" id="veliModal" tabindex="-1" aria-labelledby="veliModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="veliModalLabel">Ek Veli Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.veliler.storeEkVeli') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="veli_id" value="{{ $veli->id }}">

                        <div class="form-group">
                            <label for="yakinlik">Yakınlık</label>
                            <select name="yakinlik" class="form-control" id="yakinlik" required>
                                <option value="anne">Anne</option>
                                <option value="baba">Baba</option>
                                <option value="dede">Dede</option>
                                <option value="akraba">Akraba</option>
                                <option value="komsu">Komşu</option>
                            </select>
                        </div>

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

                        <div class="form-group">
                            <label for="adres">Adres</label>
                            <textarea name="adres" class="form-control" id="adres" rows="3"></textarea>
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

    <!-- Öğrenci Modal -->
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
                            <label for="ogrenci_cinsiyet">Cinsiyet</label>
                            <select name="cinsiyet" class="form-control" id="ogrenci_cinsiyet" required>
                                <option value="erkek">Erkek</option>
                                <option value="kiz">Kız</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ogrenci_dogum_tarihi">Doğum Tarihi</label>
                            <input type="date" name="dogum_tarihi" class="form-control" id="ogrenci_dogum_tarihi"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="ogrenci_mudureyet">Müdüriyet</label>
                            <select class="form-control" id="ogrenci_mudureyet" name="mudureyet" required>
                                <option value="anakolu">Anakolu</option>
                                <option value="ilkokul">İlkokul</option>
                                <option value="ortaokul">Ortaokul</option>
                                <option value="anadolu_lisesi">Anadolu Lisesi</option>
                                <option value="fen_lisesi">Fen Lisesi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ogrenci_sinifi">Sınıfı</label>
                            <input type="text" name="sinifi" class="form-control" id="ogrenci_sinifi" required>
                        </div>

                        <div class="form-group">
                            <label for="ogrenci_egitim_donemi">Eğitim Dönemi</label>
                            <input type="text" name="egitim_donemi" class="form-control" id="ogrenci_egitim_donemi"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="ogrenci_kontenjan">Kontenjan</label>
                            <select class="form-control" id="ogrenci_kontenjan" name="kontenjan" required>
                                <option value="kurumsal">Kurumsal</option>
                                <option value="burslu">Burslu</option>
                                <option value="gazi">Gazi</option>
                                <option value="sehit">Şehit</option>
                                <option value="personel">Personel</option>
                                <option value="yok">Yok</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ogrenci_egitimucreti">Eğitim Ücreti</label>
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

                        <div class="form-group">
                            <label for="ogrenci_kirtasiyeucreti">Kırtasiye Ücreti</label>
                            <input type="number" step="0.01" name="kirtasiyeucreti" class="form-control"
                                id="ogrenci_kirtasiyeucreti" required>
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

    <!-- Düzenleme Modalları -->
    <!-- Birincil Veli Düzenleme Modal -->
    <div class="modal fade" id="editBirincilVeliModal" tabindex="-1" aria-labelledby="editBirincilVeliModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBirincilVeliModalLabel">Birincil Veli Bilgilerini Düzenle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editBirincilVeliForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $veli->id }}">

                        <div class="form-group">
                            <label for="edit_veli_yakinlik">Yakınlık</label>
                            <select name="yakinlik" class="form-control" id="edit_veli_yakinlik" required>
                                <option value="anne" {{ $veli->yakinlik == 'anne' ? 'selected' : '' }}>Anne</option>
                                <option value="baba" {{ $veli->yakinlik == 'baba' ? 'selected' : '' }}>Baba</option>
                                <option value="dede" {{ $veli->yakinlik == 'dede' ? 'selected' : '' }}>Dede</option>
                                <option value="akraba" {{ $veli->yakinlik == 'akraba' ? 'selected' : '' }}>Akraba</option>
                                <option value="komsu" {{ $veli->yakinlik == 'komsu' ? 'selected' : '' }}>Komşu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_isim">İsim</label>
                            <input type="text" name="isim" class="form-control" id="edit_veli_isim"
                                value="{{ $veli->isim }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_tc">TC</label>
                            <input type="text" name="tc" class="form-control" id="edit_veli_tc"
                                value="{{ $veli->tc }}" required maxlength="11">
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_meslek">Meslek</label>
                            <input type="text" name="meslek" class="form-control" id="edit_veli_meslek"
                                value="{{ $veli->meslek }}">
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_tel">Telefon</label>
                            <input type="text" name="tel" class="form-control" id="edit_veli_tel"
                                value="{{ $veli->tel }}" maxlength="15">
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_eposta">E-Posta</label>
                            <input type="email" name="eposta" class="form-control" id="edit_veli_eposta"
                                value="{{ $veli->eposta }}">
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_is_tel">İş Telefonu</label>
                            <input type="text" name="is_tel" class="form-control" id="edit_veli_is_tel"
                                value="{{ $veli->is_tel }}" maxlength="15">
                        </div>

                        <div class="form-group">
                            <label for="edit_veli_adres">Adres</label>
                            <textarea name="adres" class="form-control" id="edit_veli_adres" rows="3">{{ $veli->adres }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ek Veli Düzenleme Modal -->
    <div class="modal fade" id="editEkVeliModal" tabindex="-1" aria-labelledby="editEkVeliModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEkVeliModalLabel">Ek Veli Bilgilerini Düzenle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editEkVeliForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $ekVeli ? $ekVeli->id : '' }}">


                        <div class="form-group">
                            <label for="edit_ek_veli_yakinlik">Yakınlık</label>
                            <select name="yakinlik" class="form-control" id="edit_ek_veli_yakinlik" required>
                                <option value="anne" {{ $ekVeli && $ekVeli->yakinlik == 'anne' ? 'selected' : '' }}>Anne
                                </option>
                                <option value="baba" {{ $ekVeli && $ekVeli->yakinlik == 'baba' ? 'selected' : '' }}>Baba
                                </option>
                                <option value="dede" {{ $ekVeli && $ekVeli->yakinlik == 'dede' ? 'selected' : '' }}>Dede
                                </option>
                                <option value="akraba" {{ $ekVeli && $ekVeli->yakinlik == 'akraba' ? 'selected' : '' }}>
                                    Akraba</option>
                                <option value="komsu" {{ $ekVeli && $ekVeli->yakinlik == 'komsu' ? 'selected' : '' }}>
                                    Komşu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_isim">İsim</label>
                            <input type="text" name="isim" class="form-control" id="edit_ek_veli_isim"
                                value="{{ $ekVeli ? $ekVeli->isim : '' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_tc">TC</label>
                            <input type="text" name="tc" class="form-control" id="edit_ek_veli_tc"
                                value="{{ $ekVeli ? $ekVeli->tc : '' }}" required maxlength="11">
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_meslek">Meslek</label>
                            <input type="text" name="meslek" class="form-control" id="edit_ek_veli_meslek"
                                value="{{ $ekVeli ? $ekVeli->meslek : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_tel">Telefon</label>
                            <input type="text" name="tel" class="form-control" id="edit_ek_veli_tel"
                                value="{{ $ekVeli ? $ekVeli->tel : '' }}" maxlength="15">
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_eposta">E-Posta</label>
                            <input type="email" name="eposta" class="form-control" id="edit_ek_veli_eposta"
                                value="{{ $ekVeli ? $ekVeli->eposta : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_is_tel">İş Telefonu</label>
                            <input type="text" name="is_tel" class="form-control" id="edit_ek_veli_is_tel"
                                value="{{ $ekVeli ? $ekVeli->is_tel : '' }}" maxlength="15">
                        </div>

                        <div class="form-group">
                            <label for="edit_ek_veli_adres">Adres</label>
                            <textarea name="adres" class="form-control" id="edit_ek_veli_adres" rows="3">{{ $ekVeli ? $ekVeli->adres : '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Öğrenci Düzenleme Modal -->
    <div class="modal fade" id="editOgrenciModal" tabindex="-1" aria-labelledby="editOgrenciModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOgrenciModalLabel">Öğrenci Bilgilerini Düzenle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editOgrenciForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_ogrenci_id">

                        <div class="form-group">
                            <label for="edit_ogrenci_isim">İsim</label>
                            <input type="text" name="isim" class="form-control" id="edit_ogrenci_isim" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_tc">TC</label>
                            <input type="text" name="tc" class="form-control" id="edit_ogrenci_tc" required
                                maxlength="11">
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_cinsiyet">Cinsiyet</label>
                            <select name="cinsiyet" class="form-control" id="edit_ogrenci_cinsiyet" required>
                                <option value="erkek">Erkek</option>
                                <option value="kiz">Kız</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_dogum_tarihi">Doğum Tarihi</label>
                            <input type="date" name="dogum_tarihi" class="form-control"
                                id="edit_ogrenci_dogum_tarihi" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_mudureyet">Müdüriyet</label>
                            <select class="form-control" id="edit_ogrenci_mudureyet" name="mudureyet" required>
                                <option value="anakolu">Anakolu</option>
                                <option value="ilkokul">İlkokul</option>
                                <option value="ortaokul">Ortaokul</option>
                                <option value="anadolu_lisesi">Anadolu Lisesi</option>
                                <option value="fen_lisesi">Fen Lisesi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_sinifi">Sınıfı</label>
                            <input type="text" name="sinifi" class="form-control" id="edit_ogrenci_sinifi" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_egitim_donemi">Eğitim Dönemi</label>
                            <input type="text" name="egitim_donemi" class="form-control"
                                id="edit_ogrenci_egitim_donemi" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_kontenjan">Kontenjan</label>
                            <select class="form-control" id="edit_ogrenci_kontenjan" name="kontenjan" required>
                                <option value="kurumsal">Kurumsal</option>
                                <option value="burslu">Burslu</option>
                                <option value="gazi">Gazi</option>
                                <option value="sehit">Şehit</option>
                                <option value="personel">Personel</option>
                                <option value="yok">Yok</option>
                            </select>
                        </div>

                        <!-- Öğrenci Düzenleme Modal içindeki ücret alanları -->
                        <div class="form-group">
                            <label for="edit_ogrenci_egitimucreti">Eğitim Ücreti</label>
                            <input type="number" step="0.01" name="egitimucreti" class="form-control"
                                id="edit_ogrenci_egitimucreti" readonly>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_yemekucreti">Yemek Ücreti</label>
                            <input type="number" step="0.01" name="yemekucreti" class="form-control"
                                id="edit_ogrenci_yemekucreti" readonly>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_etutucreti">Etüt Ücreti</label>
                            <input type="number" step="0.01" name="etutucreti" class="form-control"
                                id="edit_ogrenci_etutucreti" readonly>
                        </div>

                        <div class="form-group">
                            <label for="edit_ogrenci_kirtasiyeucreti">Kırtasiye Ücreti</label>
                            <input type="number" step="0.01" name="kirtasiyeucreti" class="form-control"
                                id="edit_ogrenci_kirtasiyeucreti" readonly>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
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
            // Öğrenci form işleyicisi
            $('#ogrenciModal form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: "{{ route('admin.veliler.storeOgrenci') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Öğrenci başarıyla eklendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                $('#ogrenciModal').modal('hide');
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
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

            // Veli form işleyicisi
            $('#veliModal form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: "{{ route('admin.veliler.storeEkVeli') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Ek veli başarıyla eklendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                $('#veliModal').modal('hide');
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
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

            // Birincil Veli düzenleme form işleyicisi
            $('#editBirincilVeliForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const id = form.find('input[name="id"]').val();

                $.ajax({
                    url: `/admin/veliler/${id}`,
                    method: "PUT",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Birincil veli bilgileri güncellendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
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

            // Ek Veli düzenleme form işleyicisi
            $('#editEkVeliForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const id = form.find('input[name="id"]').val();

                $.ajax({
                    url: `/admin/veliler/update-ek-veli/${id}`,
                    method: "PUT",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Ek veli bilgileri güncellendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
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

            // Öğrenci düzenleme form işleyicisi
            $('#editOgrenciForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const id = form.find('input[name="id"]').val();

                $.ajax({
                    url: `/admin/veliler/update-ogrenci/${id}`,
                    method: "PUT",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Öğrenci bilgileri güncellendi.',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
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

        function editOgrenci(id) {
            $.get(`/admin/veliler/get-ogrenci/${id}`, function(data) {
                $('#edit_ogrenci_id').val(data.id);
                $('#edit_ogrenci_isim').val(data.isim);
                $('#edit_ogrenci_tc').val(data.tc);
                $('#edit_ogrenci_cinsiyet').val(data.cinsiyet);
                $('#edit_ogrenci_dogum_tarihi').val(data.dogum_tarihi);
                $('#edit_ogrenci_mudureyet').val(data.mudureyet);
                $('#edit_ogrenci_sinifi').val(data.sinifi);
                $('#edit_ogrenci_egitim_donemi').val(data.egitim_donemi);
                $('#edit_ogrenci_kontenjan').val(data.kontenjan);
                $('#edit_ogrenci_egitimucreti').val(data.egitimucreti);
                $('#edit_ogrenci_yemekucreti').val(data.yemekucreti);
                $('#edit_ogrenci_etutucreti').val(data.etutucreti);
                $('#edit_ogrenci_kirtasiyeucreti').val(data.kirtasiyeucreti);
                $('#editOgrenciModal').modal('show');
            });
        }
    </script>
@endsection
