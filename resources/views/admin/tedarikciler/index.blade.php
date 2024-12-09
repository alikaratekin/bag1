@extends('layouts.admin')
<style>
    .card {
        border: none;
        border-radius: 16px;
        /* Daha yuvarlatılmış köşeler */
        overflow: hidden;
        background: #ffffff;
        /* Materyal tasarım için canlı beyaz arka plan */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        /* Hover ve hareket için geçişler */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.06);
        /* Yumuşak bir gölge efekti */
    }

    .card:hover {
        transform: translateY(-10px);
        /* Hover'da daha belirgin yukarı hareket */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15), 0 8px 16px rgba(0, 0, 0, 0.1);
        /* Hover'da daha yoğun gölge */
    }

    .icon-container {
        font-size: 3rem;
        color: #6c757d;
        margin-right: 15px;
        display: flex;
        align-items: flex-end;
        /* İkonu alt hizala */
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .icon-container:hover {
        transform: scale(1.2);
        /* Hover'da ikonu büyüt */
        color: #007bff;
        /* Materyal tasarım için canlı mavi renk */
    }

    .card h5 {
        font-size: 1.25rem;
        color: #333333;
        /* Daha koyu bir başlık rengi */
        font-weight: 600;
        /* Materyal tasarım için hafif kalınlık */
        margin-bottom: 0;
        display: flex;
        align-items: flex-end;
        height: 3rem;
        /* İkon boyutuyla uyumlu olacak şekilde */
    }

    .progress {
        height: 8px;
        /* İnce bir yükleme çubuğu */
        border-radius: 4px;
        overflow: hidden;
        background-color: #f1f1f1;
        /* Daha belirgin çubuk arka planı */
    }

    .progress-bar {
        border-radius: 4px;
        background-color: #007bff;
        /* Çubuğun daha canlı bir mavi tonu */
    }

    .card .fw-bold.text-secondary {
        font-size: 0.9rem;
        color: #6c757d;
        /* Daha nötr bir renk tonu */
    }

    .card .fw-bold.text-danger {
        font-size: 1rem;
        color: #ff5722;
        /* Materyal tasarım için canlı bir kırmızı tonu */
    }

    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        /* Kartın tamamını tıklanabilir yap */
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Yeni Tedarikçi Oluştur Tuşu -->
            <button class="btn btn-sm btn-primary" id="openTedarikciModal">
                <i class="fas fa-plus"></i> Yeni Tedarikçi
            </button>

            <!-- Arama Kutusu -->
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Tedarikçi Ara...">
                <button class="btn btn-sm btn-outline-secondary" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Tedarikçi Kartları -->
        <div class="row" id="tedarikciList">
            @forelse ($tedarikciler as $tedarikci)
                <div class="col-md-4 mb-4 tedarikci-card" data-name="{{ strtolower($tedarikci->ad) }}">
                    <a href="{{ route('admin.tedarikciler.show', $tedarikci) }}" class="card-link">
                        <div class="card border-0">
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="icon-container">
                                        <i class="fas fa-industry"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold mb-0">{{ $tedarikci->ad }}</h5>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-secondary">Güncel Borç:</span>
                                        <span class="fw-bold text-danger">{{ number_format($tedarikci->borc, 2) }} ₺</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $tedarikci->borc_yuzde }}%;"
                                            aria-valuenow="{{ $tedarikci->borc_yuzde }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            @empty
                <div class="col-12 text-center">
                    <p>Hiç tedarikçi bulunamadı.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tedarikçi Modal -->
    <div class="modal fade" id="createTedarikciModal" tabindex="-1" aria-labelledby="createTedarikciModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title" id="createTedarikciModalLabel">Yeni Tedarikçi</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <form id="createTedarikciForm" action="{{ route('admin.tedarikciler.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="ad" class="form-label">Firma Adı</label>
                            <input type="text" class="form-control form-control-sm" id="ad" name="ad"
                                required>
                        </div>
                        <div class="mb-2">
                            <label for="numara" class="form-label">Telefon Numarası</label>
                            <input type="text" class="form-control form-control-sm" id="numara" name="numara">
                        </div>
                        <div class="mb-2">
                            <label for="vergino" class="form-label">Vergi No</label>
                            <input type="text" class="form-control form-control-sm" id="vergino" name="vergino">
                        </div>
                        <div class="mb-2">
                            <label for="adres" class="form-label">Adres</label>
                            <textarea class="form-control form-control-sm" id="adres" name="adres" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="not" class="form-label">Not</label>
                            <textarea class="form-control form-control-sm" id="not" name="not" rows="2"></textarea>
                        </div>
                        <input type="hidden" name="team_id" value="{{ auth()->user()->team_id }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-sm btn-primary" form="createTedarikciForm">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#openTedarikciModal').click(function() {
                $('#createTedarikciForm')[0].reset();
                $('#createTedarikciModal').modal('show');
            });
        });

        //arama
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tedarikciList = document.getElementById('tedarikciList');

            searchInput.addEventListener('input', function() {
                const filter = searchInput.value.toLowerCase();
                const cards = tedarikciList.getElementsByClassName('tedarikci-card');

                Array.from(cards).forEach(function(card) {
                    const name = card.getAttribute('data-name');
                    if (name.includes(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
