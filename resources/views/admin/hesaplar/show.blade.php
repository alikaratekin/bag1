@extends('layouts.admin')

@section('content')
    <style>
        @media print {
            .toolbar-container {
                display: none !important;
                /* Toolbar tamamen gizlenecek */
            }

            .action-buttons {
                display: none !important;
                /* Üstteki butonlar tamamen gizlenecek */
            }

            footer,
            /* Eğer footer bir <footer> etiketi ise */
            .footer-container,
            /* Eğer footer bir sınıf kullanıyorsa */
            .card-footer {
                /* Eğer bir kartın alt kısmı footer olarak kullanılıyorsa */
                display: none !important;
                /* Footer tamamen gizlensin */
            }

            #hesaplar-table {
                width: 100% !important;
                /* Tablo tam sayfa genişliğini kapsasın */
            }
        }

        .small-box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: calc(100% + 3px);
            width: 100%;
            margin-bottom: 10px;
            /* Kartların altındaki boşluk azaltıldı */
        }

        .small-box .inner {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            padding-left: 15px;
            padding-top: 30px;
            text-align: left;
        }

        .small-box h3 {
            margin: 0;
        }

        .small-box p {
            margin: 0;
        }

        .small-box .icon {
            font-size: 80px;
            right: 10px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            /* Butonlar tek satırda */
            gap: 10px;
            margin-top: 0px;
            /* Tuşların üst boşluğu kaldırıldı */
        }

        .action-buttons button {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            text-transform: uppercase;
            width: 100%;
            height: 60px;
        }

        .action-buttons button i {
            font-size: 20px;
            margin-right: 10px;
        }

        .card {
            min-height: 550px;
            /* Kart yüksekliği biraz daha artırıldı */
        }

        /* Tablo Genel Ayarları */
        #hesaplar-table {
            table-layout: auto;
            /* Sütun genişliklerini içeriklere göre ayarla */
            width: 100%;
            /* Tabloyu tam genişlikte tut */
            border-collapse: collapse;
            /* Hücreler arası boşlukları kaldır */
        }

        /* Tablo Başlıkları */
        #hesaplar-table thead th {
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
        #hesaplar-table tbody td {
            word-wrap: break-word;
            /* Uzun metinler alt satıra geçer */
            white-space: normal;
            /* Metni sıkıştırmaz */
            font-size: 12px;
            /* Küçük yazı tipi */
            padding: 5px;
            /* Hücre içi boşlukları küçült */
            text-align: left;
            /* İçerikleri sola hizala */
            text-overflow: ellipsis;
        }

        /* Tablo Satırları */
        #hesaplar-table tbody tr {
            height: auto;
            /* İçeriğe göre satır yüksekliği */
        }

        /* Tablo Konteyneri */
        .table-container {
            height: calc(100vh - 300px);
            /* Yükseklik hesaplama */
            overflow-y: auto;
            /* Dikey kaydırma */
            overflow-x: hidden;
            /* Yatay kaydırma kapalı */
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .toolbar-container {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toolbar-container .buttons {
            display: flex;
            gap: 10px;
        }

        .toolbar-container .buttons button {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f4f4f4;
            color: #333;
            cursor: pointer;
        }

        .toolbar-container .buttons button:hover {
            background-color: #ddd;
        }

        .toolbar-container .search {
            display: flex;
            justify-content: flex-end;
        }

        .toolbar-container .search input {
            width: 300px;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Row Gelen ve Row Giden */
        .row-gelen {
            background-color: rgba(0, 255, 0, 0.1);
            /* Transparan yeşil */
        }

        .row-giden {
            background-color: rgba(255, 0, 0, 0.1);
            /* Transparan kırmızı */
        }

        /* Tam Ekran Modunda Tablo */
        .table-container.fullscreen {
            height: calc(100vh - 150px);
            /* Tam ekran modunda tablo daha fazla yer kaplayacak */
            overflow-y: auto;
            /* Dikey kaydırma etkin */
            overflow-x: auto;
            /* Yatay kaydırma gerektiğinde etkin */
        }
    </style>






    <div class="container-fluid">
        <div class="row align-items-stretch">
            <!-- Kartlar -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($toplamGelir, 2, ',', '.') }} <sup style="font-size: 20px">TL</sup></h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Toplam Gelir
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($toplamGider, 2, ',', '.') }} <sup style="font-size: 20px">TL</sup>
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Toplam Gider
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ number_format($guncelBakiye, 2, ',', '.') }} <sup style="font-size: 20px">TL</sup>
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Güncel Bakiye
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yeni Butonlar Bölgesi -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="action-buttons d-flex justify-content-between">
                    <button id="geriDonButton" class="btn btn-secondary"
                        onclick="window.location.href='{{ route('admin.hesaplar.index') }}'">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </button>

                    <button id="paraGirisButton" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Para Girişi Yap
                    </button>

                    <button id="paraCikisButton" class="btn btn-danger">
                        <i class="fas fa-minus-circle"></i> Para Çıkışı Yap
                    </button>

                    <button id="virmanButton" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Buradan Transfer Yap
                    </button>

                    <button id="transferAlButton" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Buraya Transfer Al
                    </button>

                    <button id="transferOtherTeamButton" class="btn btn-warning">
                        <i class="fas fa-exchange-alt"></i> Başka Sektöre Transfer
                    </button>

                </div>
            </div>
        </div>


        <div class="card hareketler-karti mt-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Hesap Hareketleri - {{ $hesapAdi }}</h3>
                <div class="card-tools">
                    <!-- Tam Ekran Butonu -->
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Üstteki Araç Çubuğu -->
                <div class="toolbar-container">
                    <div class="buttons">
                        <button id="excel-btn">Excel</button>
                        <button id="pdf-btn">PDF</button>
                        <button id="print-btn">Yazdır</button>
                    </div>
                    <div class="search">
                        <input type="text" id="table-search" placeholder="Arama yapın...">
                    </div>
                </div>

                <!-- Tablo için kaydırılabilir alan -->
                <div class="table-container">
                    <table id="hesaplar-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>İşlem Tipi</th>
                                <th>Kullanıcı</th>
                                <th>Açıklama</th>
                                <th>Gelen</th>
                                <th>Giden</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX ile yüklenen veriler burada gösterilecek -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="transferOtherTeamModal" tabindex="-1" aria-labelledby="transferOtherTeamModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="transferOtherTeamModalLabel">
                            <i class="fas fa-exchange-alt"></i> Başka Sektöre Transfer
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="transferOtherTeamForm">
                            <div class="form-group">
                                <label for="select-team"><i class="fas fa-users"></i> Takım Seç</label>
                                <select class="form-control" id="select-team" name="team_id" required>
                                    <option value="" selected disabled>Takım Seçin</option> <!-- Varsayılan seçim -->
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="source-account-no" name="kaynak_hesap_no"
                                    value="{{ $kaynak_hesap_no }}">

                                <label for="select-account"><i class="fas fa-wallet"></i> Hesap Seç</label>
                                <select class="form-control" id="select-account" name="hesap_no" required>
                                    <option value="" selected disabled>Hesap Seçin</option> <!-- Varsayılan seçim -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="transfer-description"><i class="fas fa-comment-dots"></i> Açıklama</label>
                                <textarea class="form-control" id="transfer-description" name="aciklama" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="transfer-amount"><i class="fas fa-money-bill-wave"></i> Tutar</label>
                                <input type="text" class="form-control" id="transfer-amount" name="tutar"
                                    placeholder="Tutar giriniz..." required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                        <button type="button" class="btn btn-warning" id="saveTransferOtherTeam">
                            <i class="fas fa-save"></i> Transfer Yap
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Düzenleme Modalı -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit"></i> Hareketi Düzenle</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="edit-id" name="id"> <!-- ID alanı gizli -->

                            <div class="form-group">
                                <label for="edit-tarih"><i class="fas fa-calendar-alt"></i> Tarih</label>
                                <input type="datetime-local" class="form-control" id="edit-tarih" name="tarih"
                                    placeholder="Tarih">
                            </div>



                            <div class="form-group">
                                <label for="edit-islem_tipi"><i class="fas fa-exchange-alt"></i> İşlem Tipi</label>
                                <input type="text" class="form-control" id="edit-islem_tipi" name="islem_tipi"
                                    placeholder="İşlem Tipi" readonly>
                            </div>

                            <div class="form-group">
                                <label for="edit-kullanici"><i class="fas fa-user"></i> Kullanıcı</label>
                                <input type="text" class="form-control" id="edit-kullanici" name="kullanici"
                                    placeholder="Kullanıcı" readonly>
                            </div>

                            <div class="form-group">
                                <label for="edit-aciklama"><i class="fas fa-comment-dots"></i> Açıklama</label>
                                <textarea class="form-control" id="edit-aciklama" name="aciklama" rows="2" placeholder="Açıklama"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="edit-gelen"><i class="fas fa-arrow-down"></i> Gelen</label>
                                <input type="text" class="form-control" id="edit-gelen" name="gelen"
                                    placeholder="Gelen Tutar">
                            </div>

                            <div class="form-group">
                                <label for="edit-giden"><i class="fas fa-arrow-up"></i> Giden</label>
                                <input type="text" class="form-control" id="edit-giden" name="giden"
                                    placeholder="Giden Tutar">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                        <button type="button" class="btn btn-danger" id="deleteEntry">
                            <i class="fas fa-trash-alt"></i> Sil
                        </button>
                        <button type="button" class="btn btn-primary" id="saveEdit">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <!-- Para Girişi Modalı -->
        <div class="modal fade" id="paraGirisiModal" tabindex="-1" aria-labelledby="paraGirisiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paraGirisiModalLabel"><i class="fas fa-plus-circle"></i> Para Girişi
                            Yap</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="paraGirisiForm">
                            <div class="form-group">
                                <label for="girisi-tarih"><i class="fas fa-calendar-alt"></i> Tarih</label>
                                <input type="datetime-local" class="form-control" id="girisi-tarih" name="tarih"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="girisi-aciklama"><i class="fas fa-comment-dots"></i> Açıklama</label>
                                <textarea class="form-control" id="girisi-aciklama" name="aciklama" rows="2"
                                    placeholder="Açıklama giriniz..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="girisi-tutar"><i class="fas fa-money-bill-wave"></i> Tutar</label>
                                <input type="text" class="form-control" id="girisi-tutar" name="tutar"
                                    placeholder="Tutar giriniz..." required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                        <button type="button" class="btn btn-primary" id="saveParaGirisi">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Para Çıkışı Modalı -->
        <div class="modal fade" id="paraCikisiModal" tabindex="-1" aria-labelledby="paraCikisiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="paraCikisiModalLabel"><i class="fas fa-minus-circle"></i> Para Çıkışı
                            Yap</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="paraCikisiForm">
                            <div class="form-group">
                                <label for="cikisi-tarih"><i class="fas fa-calendar-alt"></i> Tarih</label>
                                <input type="datetime-local" class="form-control" id="cikisi-tarih" name="tarih"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="cikisi-aciklama"><i class="fas fa-comment-dots"></i> Açıklama</label>
                                <textarea class="form-control" id="cikisi-aciklama" name="aciklama" rows="2"
                                    placeholder="Açıklama giriniz..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="cikisi-tutar"><i class="fas fa-money-bill-wave"></i> Tutar</label>
                                <input type="text" class="form-control" id="cikisi-tutar" name="tutar"
                                    placeholder="Tutar giriniz..." required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                        <button type="button" class="btn btn-danger" id="saveParaCikisi">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Virman Modalı -->
        <div class="modal fade" id="virmanModal" tabindex="-1" aria-labelledby="virmanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="virmanModalLabel">
                            <i class="fas fa-exchange-alt"></i> Virman İşlemi
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="virmanForm">
                            <div class="form-group">
                                <label for="hedef-hesap"><i class="fas fa-wallet"></i> Hedef Hesap</label>
                                <select class="form-control" id="hedef-hesap" name="hedef_hesap" required>
                                    <!-- Hesaplar AJAX ile doldurulacak -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="virman-aciklama"><i class="fas fa-comment-dots"></i> Açıklama</label>
                                <textarea class="form-control" id="virman-aciklama" name="aciklama" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="virman-tutar"><i class="fas fa-money-bill-wave"></i> Tutar</label>
                                <input type="text" class="form-control" id="virman-tutar" name="tutar"
                                    placeholder="Tutar giriniz..." required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                        <button type="button" class="btn btn-primary" id="saveVirman">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Buraya Transfer Al Modalı -->
        <div class="modal fade" id="transferAlModal" tabindex="-1" aria-labelledby="transferAlModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="transferAlModalLabel">
                            <i class="fas fa-arrow-left"></i> Buraya Transfer Al
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="transferAlForm">
                            <div class="form-group">
                                <label for="transfer-al-kaynak-hesap"><i class="fas fa-wallet"></i> Kaynak Hesap</label>
                                <select class="form-control" id="transfer-al-kaynak-hesap" name="kaynak_hesap" required>
                                    <!-- Hesaplar AJAX ile doldurulacak -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="transfer-al-aciklama"><i class="fas fa-comment-dots"></i> Açıklama</label>
                                <textarea class="form-control" id="transfer-al-aciklama" name="aciklama" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="transfer-al-tutar"><i class="fas fa-money-bill-wave"></i> Tutar</label>
                                <input type="text" class="form-control" id="transfer-al-tutar" name="tutar"
                                    placeholder="Tutar giriniz..." required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                        <button type="button" class="btn btn-primary" id="saveTransferAl">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>


    @section('scripts')
        <script>
            $(document).ready(function() {
                const hareketlerKart = document.querySelector('.hareketler-karti'); // Kartın kendisi
                const tableContainer = $('.table-container'); // Tablo kapsayıcı

                // Tam ekran butonuna tıklama işlemi
                $('.card-header .btn[data-card-widget="maximize"]').on('click', function() {
                    if (document.fullscreenElement) {
                        // Eğer tam ekrandaysa, tam ekrandan çık
                        document.exitFullscreen()
                            .then(() => {
                                tableContainer.removeClass('fullscreen'); // `fullscreen` sınıfını kaldır
                                console.log("Tam ekrandan çıkıldı.");
                            })
                            .catch(err => console.error("Tam ekrandan çıkılamadı:", err));
                    } else {
                        // Tam ekranı aç
                        hareketlerKart.requestFullscreen()
                            .then(() => {
                                tableContainer.addClass('fullscreen'); // `fullscreen` sınıfını ekle
                                console.log("Tam ekran açıldı.");
                            })
                            .catch(err => console.error("Tam ekran açılamadı:", err));
                    }
                });

                // Tam ekran modundan çıkma olayını dinle
                document.addEventListener('fullscreenchange', () => {
                    if (!document.fullscreenElement) {
                        tableContainer.removeClass('fullscreen'); // Tam ekran çıkışında sınıfı kaldır
                    }
                });
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
                $('#edit-gelen,#girisi-tutar,#transfer-al-tutar, #edit-giden, #cikisi-tutar,#virman-tutar').on('input',
                    function() {
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
                // Kart verilerini güncelleyen fonksiyon
                const updateCardData = () => {
                    $.ajax({
                        url: "{{ route('admin.hesaplar.kartlar', $hesap_no) }}",
                        method: "GET",
                        success: function(response) {
                            console.log(response); // Verileri kontrol etmek için ekleyin
                            const gelirText = response.toplamGelir > 0 ?
                                response.toplamGelir.toLocaleString('tr-TR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + " TL" :
                                "—";
                            const giderText = response.toplamGider > 0 ?
                                response.toplamGider.toLocaleString('tr-TR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + " TL" :
                                "—";
                            const bakiyeText = response.guncelBakiye.toLocaleString('tr-TR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + " TL";
                            $('.small-box.bg-success .inner h3').html(response.toplamGelir +
                                ' <sup style="font-size: 20px;">TL</sup>');
                            $('.small-box.bg-danger .inner h3').html(response.toplamGider +
                                ' <sup style="font-size: 20px;">TL</sup>');
                            $('.small-box.bg-info .inner h3').html(response.guncelBakiye +
                                ' <sup style="font-size: 20px;">TL</sup>');

                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Kart verileri güncellenirken bir hata oluştu.',
                            });
                        }
                    });
                };

                // Tablo verilerini getiren fonksiyon
                const fetchTableData = (search = '') => {
                    $.ajax({
                        url: "{{ route('admin.hesaplar.hareketler', $hesap_no) }}",
                        method: "GET",
                        data: {
                            search: search
                        },
                        success: function(response) {
                            let tableBody = '';
                            response.data.forEach(function(row) {
                                // Satır sınıfını gelen veya giden durumuna göre ayarla
                                const rowClass = parseFloat(row.gelen) > 0 ? 'row-gelen' :
                                    'row-giden';

                                // Gelen ve Giden değerlerini biçimlendir
                                const gelenText = row.gelen > 0 ?
                                    `₺${parseFloat(row.gelen).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` :
                                    "—";

                                const gidenText = row.giden > 0 ?
                                    `₺${parseFloat(row.giden).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` :
                                    "—";

                                tableBody += `
                           <tr class="${rowClass}" data-id="${row.id}" data-tarih="${row.tarih}"
                               data-islem_tipi="${row.islem_tipi}" data-kullanici="${row.kullanici}"
                               data-aciklama="${row.aciklama}" data-gelen="${row.gelen}" data-giden="${row.giden}">
                               <td>${row.tarih}</td>
                               <td>${row.islem_tipi}</td>
                               <td>${row.kullanici}</td>
                               <td>${row.aciklama}</td>
                               <td>${gelenText}</td>
                               <td>${gidenText}</td>
                           </tr>
                       `;
                            });

                            $('#hesaplar-table tbody').html(tableBody);
                            // Tablo verilerini başarıyla yükledikten sonra, kaydırma çubuğunu en alta taşıyın
                            $('.table-container').scrollTop($('.table-container')[0].scrollHeight);

                            // Türkçe formatında tutar biçimlendirme fonksiyonu
                            function formatCurrency(value) {
                                return parseFloat(value).toLocaleString('tr-TR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                });
                            }

                            // Satır çift tıklama işlemi: Düzenleme Modalını Aç
                            $('#hesaplar-table tbody').on('dblclick', 'tr', function() {
                                const row = $(this);
                                const id = row.data('id'); // Satırdan ID alınır
                                const gelen = row.data('gelen'); // Gelen tutar
                                const giden = row.data('giden'); // Giden tutar

                                if (!id) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Uyarı!',
                                        text: 'ID bulunamadı. Lütfen tablo verisini kontrol edin.',
                                    });
                                    return;
                                }

                                // "Gelen" veya "Giden" sıfır ise input alanını gizle
                                if (parseFloat(gelen) === 0) {
                                    $('#editModal #edit-gelen').closest('.form-group').hide();
                                } else {
                                    $('#editModal #edit-gelen').closest('.form-group').show();
                                    $('#editModal #edit-gelen').val(formatCurrency(
                                        gelen)); // Gelen tutarı formatla
                                }

                                if (parseFloat(giden) === 0) {
                                    $('#editModal #edit-giden').closest('.form-group').hide();
                                } else {
                                    $('#editModal #edit-giden').closest('.form-group').show();
                                    $('#editModal #edit-giden').val(formatCurrency(
                                        giden)); // Giden tutarı formatla
                                }

                                // Modal verilerini doldur
                                $('#editModal #edit-id').val(id);
                                $('#editModal #edit-tarih').val($(this).data('tarih'));
                                $('#editModal #edit-islem_tipi').val($(this).data(
                                    'islem_tipi'));
                                $('#editModal #edit-kullanici').val($(this).data('kullanici'));
                                $('#editModal #edit-aciklama').val($(this).data('aciklama'));

                                // Modalı göster
                                $('#editModal').modal('show');
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Tablo verileri yüklenirken bir hata oluştu!',
                            });
                        }
                    });
                };
                $('#deleteEntry').click(function() {
                    const id = $('#edit-id').val(); // Modalda düzenlenen ID'yi al

                    if (!id) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Silinecek ID bulunamadı!',
                        });
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
                                url: "{{ route('admin.hesaplar.delete') }}", // Silme rotası
                                method: "POST",
                                data: {
                                    id: id,
                                    _token: $('meta[name="csrf-token"]').attr(
                                        'content'), // CSRF token
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Başarılı!',
                                            text: response.message,
                                        }).then(() => {
                                            $('#editModal').modal(
                                                'hide'); // Modalı kapat
                                            if (typeof fetchTableData ===
                                                'function') {
                                                fetchTableData();
                                                updateCardData
                                                    (); // Tabloyu yeniden yükle
                                            } else {
                                                console.error(
                                                    "fetchTableData fonksiyonu tanımlı değil."
                                                );
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Hata!',
                                            text: response.message,
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: 'Silme işlemi sırasında bir hata oluştu!',
                                    });
                                },
                            });
                        }
                    });
                });
                $('#paraGirisButton').click(function() {
                    const now = new Date(); // Mevcut tarih ve saat
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2,
                        '0'); // Aylar 0-11 arası olduğu için +1
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');

                    const currentDateTime =
                        `${year}-${month}-${day}T${hours}:${minutes}`; // datetime-local formatı
                    $('#girisi-tarih').val(currentDateTime); // Tarih alanına yaz
                    $('#paraGirisiModal').modal('show'); // Modalı aç
                });
                $('#saveParaGirisi').click(function() {
                    const tarih = $('#girisi-tarih').val();
                    const aciklama = $('#girisi-aciklama').val();
                    let tutar = $('#girisi-tutar').val();

                    if (!tarih || !aciklama || !tutar) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Uyarı!',
                            text: 'Lütfen tüm alanları doldurun!',
                        });
                        return;
                    }

                    // Tutarı normalize et (binlik ayracı kaldır, ondalık ayracı noktaya çevir)
                    tutar = normalizeNumber(tutar);

                    $.ajax({
                        url: "{{ route('admin.hesaplar.paraGirisi') }}", // Backend rotası
                        method: "POST",
                        data: {
                            tarih: tarih,
                            aciklama: aciklama,
                            tutar: tutar, // Normalleştirilmiş tutarı gönder
                            hesap_no: "{{ $hesap_no }}", // Show'daki hesap_no
                            _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: response.message,
                                }).then(() => {
                                    $('#paraGirisiModal').modal('hide');
                                    fetchTableData(); // Tabloyu güncelle
                                    updateCardData(); // Kartları güncelle
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Para girişi sırasında bir hata oluştu!',
                            });
                        },
                    });
                });
                // Modal açılırken tarih alanını güncelle
                $('#paraCikisButton').click(function() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');

                    const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                    $('#cikisi-tarih').val(currentDateTime); // Tarih alanına yaz
                    $('#paraCikisiModal').modal('show'); // Modalı aç
                });

                // Para Çıkışı Kaydet
                $('#saveParaCikisi').click(function() {
                    const tarih = $('#cikisi-tarih').val();
                    const aciklama = $('#cikisi-aciklama').val();
                    let tutar = $('#cikisi-tutar').val();

                    if (!tarih || !aciklama || !tutar) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Uyarı!',
                            text: 'Lütfen tüm alanları doldurun!',
                        });
                        return;
                    }

                    // Tutarı normalize et (binlik ayracı kaldır, ondalık ayracı noktaya çevir)
                    tutar = normalizeNumber(tutar);

                    $.ajax({
                        url: "{{ route('admin.hesaplar.paraCikisi') }}", // Backend rotası
                        method: "POST",
                        data: {
                            tarih: tarih,
                            aciklama: aciklama,
                            tutar: tutar,
                            kaynak_hesap_no: "{{ $hesap_no }}", // Kaynak hesap no
                            islem_tipi: "Para Çıkışı", // İşlem tipi
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: response.message,
                                }).then(() => {
                                    $('#paraCikisiModal').modal('hide');
                                    fetchTableData(); // Tabloyu güncelle
                                    updateCardData(); // Kartları güncelle
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Para çıkışı sırasında bir hata oluştu!',
                            });
                        },
                    });
                });
                $(document).ready(function() {
                    $('#virmanButton').click(function() {
                        $('#virmanModal').modal('show'); // Modalı göster
                        $('#hedef-hesap').empty(); // Önceki hedef hesapları temizle

                        // Hesapları Getir
                        loadHesaplar();
                    });

                    function loadHesaplar() {
                        const mevcutHesap =
                            "{{ $hesap_no }}"; // Mevcut hesap bilgisini backend'e gönderiyoruz

                        $.ajax({
                            url: "{{ route('admin.hesaplar.getHesaplar') }}",
                            method: "GET",
                            dataType: "json", // JSON türünde veri bekliyoruz
                            data: {
                                mevcut_hesap: mevcutHesap
                            }, // Mevcut hesap parametresi gönderiliyor
                            success: function(response) {
                                console.log('Gelen JSON:', response); // Gelen veriyi konsola yaz

                                // Dönen verinin formatını kontrol et
                                if (!response || typeof response !== 'object' || !response.data) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: 'Dönen veri geçerli bir formatta değil!',
                                    });
                                    console.error('Geçersiz yanıt:', response);
                                    return; // Hata durumunda işlemi durdur
                                }

                                const hedefHesap = $('#hedef-hesap');
                                hedefHesap.empty(); // Önceki hesapları temizle

                                // Backend'den gelen hesaplar
                                const {
                                    kasaHesaplari,
                                    bankaHesaplari,
                                    posHesaplari,
                                    krediKartlari
                                } = response.data;

                                // Optgroup ekleme fonksiyonu
                                const addGroup = (hesaplar, label) => {
                                    if (Array.isArray(hesaplar) && hesaplar.length > 0) {
                                        const group = $('<optgroup>', {
                                            label
                                        });
                                        hesaplar.forEach((hesap) => {
                                            if (hesap.hesap_no !==
                                                mevcutHesap
                                                ) { // Mevcut hesabı hariç tut
                                                group.append($('<option>', {
                                                    value: hesap.hesap_no,
                                                    text: hesap.tanım
                                                }));
                                            }
                                        });
                                        hedefHesap.append(group);
                                    }
                                };

                                // Hesapları gruplar halinde ekle
                                addGroup(kasaHesaplari, 'Kasa Hesapları');
                                addGroup(bankaHesaplari, 'Banka Hesapları');
                                addGroup(posHesaplari, 'POS Hesapları');
                                addGroup(krediKartlari, 'Kredi Kartları');

                                console.log('Hesaplar başarıyla yüklendi!');
                            },
                            error: function(xhr) {
                                // AJAX hata kontrolü
                                console.error('AJAX Hatası:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: 'Hesaplar yüklenirken bir hata oluştu!',
                                });
                            },
                        });
                    }

                    // Virman işlemini kaydetme
                    $('#saveVirman').click(function() {
                        const hedefHesap = $('#hedef-hesap').val();
                        const aciklama = $('#virman-aciklama').val();
                        let tutar = $('#virman-tutar').val();

                        // Form alanlarının kontrolü
                        if (!hedefHesap || !aciklama || !tutar) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Uyarı!',
                                text: 'Lütfen tüm alanları doldurun!',
                            });
                            return;
                        }

                        tutar = normalizeNumber(
                            tutar); // Tutarı normalize et (örneğin, "1.000,00" → "1000.00")

                        $.ajax({
                            url: "{{ route('admin.hesaplar.virmanKaydet') }}",
                            method: "POST",
                            data: {
                                kaynak_hesap_no: "{{ $hesap_no }}",
                                hedef_hesap_no: hedefHesap,
                                tutar: tutar,
                                aciklama: aciklama,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Başarılı!',
                                        text: response.message,
                                    }).then(() => {
                                        $('#virmanModal').modal(
                                            'hide'); // Modalı kapat
                                        fetchTableData(); // Tabloyu güncelle
                                        updateCardData
                                            (); // Kart verilerini güncelle
                                        console.log(
                                            'Virman işlemi başarıyla kaydedildi:',
                                            response);
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: 'Virman işlemi sırasında bir hata oluştu!',
                                });
                            },
                        });
                    });
                });
                $(document).ready(function() {
                    // Buraya Transfer Al Modalını Aç
                    $('#transferAlButton').click(function() {
                        $('#transferAlModal').modal('show'); // Modalı göster
                        $('#transfer-al-kaynak-hesap').empty(); // Hesapları temizle

                        // Hesapları Getir
                        loadHesaplarTransferAl();
                    });

                    // Buraya Transfer Al Hesaplarını Yükle
                    function loadHesaplarTransferAl() {
                        const mevcutHesap = "{{ $hesap_no }}"; // Mevcut hesap (Hedef Hesap olacak)

                        $.ajax({
                            url: "{{ route('admin.hesaplar.getHesaplar') }}", // Backend'den hesaplar alınır
                            method: "GET",
                            dataType: "json",
                            data: {
                                mevcut_hesap: mevcutHesap
                            }, // Mevcut hesap parametresi gönderiliyor
                            success: function(response) {
                                console.log('Gelen JSON:', response);

                                if (!response || typeof response !== 'object' || !response.data) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: 'Dönen veri geçerli bir formatta değil!',
                                    });
                                    console.error('Geçersiz yanıt:', response);
                                    return;
                                }

                                const kaynakHesap = $('#transfer-al-kaynak-hesap');
                                kaynakHesap.empty(); // Önceki hesapları temizle

                                // Backend'den gelen hesaplar
                                const {
                                    kasaHesaplari,
                                    bankaHesaplari,
                                    posHesaplari,
                                    krediKartlari
                                } = response.data;

                                // Optgroup ekleme fonksiyonu
                                const addGroup = (hesaplar, label) => {
                                    if (Array.isArray(hesaplar) && hesaplar.length > 0) {
                                        const group = $('<optgroup>', {
                                            label
                                        });
                                        hesaplar.forEach((hesap) => {
                                            if (hesap.hesap_no !==
                                                mevcutHesap
                                                ) { // Mevcut hesabı hariç tut
                                                group.append($('<option>', {
                                                    value: hesap.hesap_no,
                                                    text: hesap.tanım
                                                }));
                                            }
                                        });
                                        kaynakHesap.append(group);
                                    }
                                };

                                // Hesapları gruplar halinde ekle
                                addGroup(kasaHesaplari, 'Kasa Hesapları');
                                addGroup(bankaHesaplari, 'Banka Hesapları');
                                addGroup(posHesaplari, 'POS Hesapları');
                                addGroup(krediKartlari, 'Kredi Kartları');

                                console.log('Hesaplar başarıyla yüklendi!');
                            },
                            error: function(xhr) {
                                console.error('AJAX Hatası:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: 'Hesaplar yüklenirken bir hata oluştu!',
                                });
                            },
                        });
                    }

                    // Buraya Transfer Al Kaydet
                    $('#saveTransferAl').click(function() {
                        const kaynakHesap = $('#transfer-al-kaynak-hesap')
                            .val(); // Kullanıcıdan seçilen hesap kaynak olacak
                        const aciklama = $('#transfer-al-aciklama').val();
                        let tutar = $('#transfer-al-tutar').val();

                        // Form doğrulama
                        if (!kaynakHesap || !aciklama || !tutar) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Uyarı!',
                                text: 'Lütfen tüm alanları doldurun!',
                            });
                            return;
                        }

                        tutar = normalizeNumber(
                            tutar); // Tutarı normalize et (ör. "1.000,00" → "1000.00")

                        $.ajax({
                            url: "{{ route('admin.hesaplar.transferAl') }}", // Backend rotası
                            method: "POST",
                            data: {
                                kaynak_hesap_no: kaynakHesap, // Seçilen kaynak hesap
                                hedef_hesap_no: "{{ $hesap_no }}", // Mevcut hesap hedef olacak
                                tutar: tutar,
                                aciklama: aciklama,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Başarılı!',
                                        text: response.message,
                                    }).then(() => {
                                        $('#transferAlModal').modal(
                                            'hide'); // Modalı kapat
                                        fetchTableData(); // Tabloyu güncelle
                                        updateCardData
                                            (); // Kart verilerini güncelle
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: 'Transfer işlemi sırasında bir hata oluştu!',
                                });
                            },
                        });
                    });
                });
                $('#transferOtherTeamButton').click(function() {
                    $('#transferOtherTeamModal').modal('show');
                    loadTeams();
                });

                function loadTeams() {
                    $.ajax({
                        url: "{{ route('admin.hesaplar.getOtherTeams') }}",
                        method: "GET",
                        success: function(response) {
                            const selectTeam = $('#select-team');
                            selectTeam.empty();
                            selectTeam.append(
                                '<option value="" selected disabled>Takım Seçin</option>'
                                ); // Varsayılan seçenek ekleniyor

                            if (response.teams && response.teams.length > 0) {
                                response.teams.forEach(team => {
                                    selectTeam.append(new Option(team.name, team.id));
                                });
                            } else {
                                Swal.fire('Uyarı!', 'Hiçbir takım bulunamadı.', 'warning');
                            }
                        },
                        error: function() {
                            Swal.fire('Hata!', 'Takımlar yüklenirken bir sorun oluştu.', 'error');
                        }
                    });
                }



                $('#select-team').change(function() {
                    const teamId = $(this).val(); // Seçilen takım ID'sini al

                    if (!teamId) return; // Eğer takım seçilmemişse işlemi durdur

                    // AJAX isteği ile hesapları getir
                    $.ajax({
                        url: "{{ route('admin.hesaplar.getTeamAccounts') }}", // Doğru rota
                        method: "GET",
                        data: {
                            team_id: teamId
                        }, // Takım ID'sini gönder
                        success: function(response) {
                            const selectAccount = $('#select-account'); // Hesap seçme alanı
                            selectAccount.empty(); // Önce mevcut seçenekleri temizle

                            if (response.groupedAccounts) {
                                // Gruplandırılmış hesapları döngüyle ekle
                                for (const [groupName, accounts] of Object.entries(response
                                        .groupedAccounts)) {
                                    // Grup başlığı için optgroup oluştur
                                    const optgroup = $('<optgroup>').attr('label', groupName);

                                    // Hesapları gruba ekle
                                    accounts.forEach(account => {
                                        optgroup.append(new Option(account.tanım, account
                                            .hesap_no));
                                    });

                                    // Select elementine grubu ekle
                                    selectAccount.append(optgroup);
                                }
                            } else {
                                Swal.fire('Uyarı!', 'Bu takıma ait hesap bulunamadı.', 'warning');
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText); // Hata varsa konsola yazdır
                            Swal.fire('Hata!', 'Hesaplar yüklenirken bir sorun oluştu.', 'error');
                        }
                    });
                });

                $('#saveTransferOtherTeam').click(function() {
                    // Kaynak hesap numarasını al
                    const kaynakHesapNo = $('#source-account-no').val();

                    // Form verisini oluştur
                    const formData = $('#transferOtherTeamForm').serialize() +
                        `&kaynak_hesap_no=${kaynakHesapNo}`;

                    // AJAX ile gönder
                    $.ajax({
                        url: "{{ route('admin.hesaplar.transferOtherTeam') }}",
                        method: "POST",
                        data: formData,
                        success: function(response) {
                            Swal.fire('Başarılı!', response.message, 'success');
                            $('#transferOtherTeamModal').modal('hide');
                            fetchTableData(); // Tabloyu güncelle
                            updateCardData(); // Kart verilerini güncelle
                        },
                        error: function(xhr) {
                            Swal.fire('Hata!', xhr.responseJSON.message, 'error');
                        }
                    });
                });


                $('#table-search').on('input', function() {
                    const search = $(this).val();
                    fetchTableData(search);
                });


                // Tablo ve kartları yükle
                fetchTableData();
                updateCardData();

                // Gelen ve Giden inputları için formatlama
                // Formatlama ve sınırlandırma kaldırma



                // Yazdırma
                $('#print-btn').click(function() {
                    window.print();
                });

                // Excel İndirme
                $('#excel-btn').click(function() {
                    const url =
                        "{{ route('admin.hesaplar.export', ['type' => 'excel', 'hesap_no' => $hesap_no]) }}";
                    window.location.href = url;
                });

                // PDF İndirme
                $('#pdf-btn').click(function() {
                    const url =
                        "{{ route('admin.hesaplar.export', ['type' => 'pdf', 'hesap_no' => $hesap_no]) }}";
                    window.location.href = url;
                });

                // AJAX Ayarları
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Güncelleme işlemi
                $('#saveEdit').click(function() {
                    const formData = $('#editForm').serializeArray();
                    const filteredData = {};

                    formData.forEach(field => {
                        filteredData[field.name] = field.value.replace(/\./g, '').replace(',', '.');
                    });

                    if (!filteredData.id) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'ID alanı eksik! Güncelleme yapılamaz.',
                        });
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.hesaplar.update') }}",
                        method: "POST",
                        data: filteredData,
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: response.message,
                                });
                                $('#editModal').modal('hide');
                                fetchTableData();
                                updateCardData();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Güncelleme sırasında bir hata oluştu:\n';
                            Object.keys(errors).forEach(key => {
                                errorMessage += `${errors[key][0]}\n`;
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: errorMessage,
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
@endsection
