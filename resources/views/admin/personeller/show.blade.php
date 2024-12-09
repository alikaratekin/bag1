@extends('layouts.admin')

@section('content')
<style>
    .action-buttons {
        margin-bottom: 1rem;
    }

    .back-button {
        margin-bottom: 1rem;
    }

    .table-bordered td,
    .table-bordered th {
        padding: 0.2rem;
        vertical-align: middle;
    }
</style>

<div class="action-buttons d-flex align-items-center" style="gap: 1rem;">
    <a href="{{ route('admin.personeller.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
    <button class="btn btn-primary btn-sm" id="updatePerson" data-id="{{ $personel->id }}">
        <i class="fas fa-edit"></i> Güncelle
    </button>
    <button class="btn btn-danger btn-sm" id="terminatePerson" data-id="{{ $personel->id }}">
        <i class="fas fa-user-slash"></i> İşten Çıkar
    </button>
</div>


<div class="row">
    <!-- Sol Taraf: Çalışan Bilgi Kartı -->
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="personel-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active small" id="basic-tab" data-toggle="tab" href="#basic" role="tab" aria-controls="basic" aria-selected="true">
                            <i class="fas fa-user"></i> Temel Bilgiler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
                            <i class="fas fa-phone"></i> İletişim Bilgileri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" id="bank-tab" data-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="false">
                            <i class="fas fa-university"></i> Banka Bilgileri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">
                            <i class="fas fa-sticky-note"></i> Diğer
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <!-- Yüksekliği azaltılmış stil -->
                <div class="tab-content small" id="personel-tabs-content" style="height: 145px; overflow-y: auto;">
                    
                    <!-- Temel Bilgiler Sekmesi -->
                    <div class="tab-pane fade show active p-2" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 40%"><i class="fas fa-id-badge"></i> İsim</th>
                                    <td>{{ $personel->isim }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-id-card"></i> T.C. Kimlik No</th>
                                    <td>{{ $personel->tc_kimlik_no ?? 'Bilgi Yok' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar"></i> Doğum Tarihi</th>
                                    <td>{{ $personel->dogum_tarihi ? \Carbon\Carbon::parse($personel->dogum_tarihi)->format('d.m.Y') : 'Bilgi Yok' }}</td>
                                </tr>
                                
                                <tr>
                                    <th><i class="fas fa-building"></i> Departman</th>
                                    <td>{{ $personel->departman ?? 'Bilgi Yok' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- İletişim Bilgileri Sekmesi -->
                    <div class="tab-pane fade p-2" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 40%"><i class="fas fa-phone"></i> Cep Telefonu</th>
                                    <td>{{ $personel->cep_telefonu ?? 'Bilgi Yok' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-envelope"></i> E-posta</th>
                                    <td>{{ $personel->e_posta ?? 'Bilgi Yok' }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%"><i class="fas fa-map-marker-alt"></i> Adres</th>
                                    <td>{{ $personel->adres ?? 'Bilgi Yok' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Banka Bilgileri Sekmesi -->
                    <div class="tab-pane fade p-2" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th><i class="fas fa-money-bill"></i> Aylık Net Maaş</th>
                                    <td>{{ $personel->aylik_net_maas ? '₺' . number_format($personel->aylik_net_maas, 2, ',', '.') : 'Bilgi Yok' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-university"></i> Banka Hesap No</th>
                                    <td>{{ $personel->banka_hesap_no ?? 'Bilgi Yok' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-info-circle"></i> Banka Bilgileri</th>
                                    <td>{{ $personel->banka_bilgileri ?? 'Bilgi Yok' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Diğer Bilgiler Sekmesi -->
                    <div class="tab-pane fade p-2" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th><i class="fas fa-calendar-check"></i> İşe Giriş Tarihi</th>
                                    <td>{{ $personel->ise_giris_tarihi ? \Carbon\Carbon::parse($personel->ise_giris_tarihi)->format('d.m.Y') : 'Bilgi Yok' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-times"></i> İşten Ayrılış Tarihi</th>
                                    <td>{{ $personel->isten_ayrilis_tarihi ? \Carbon\Carbon::parse($personel->isten_ayrilis_tarihi)->format('d.m.Y') : 'Çalışıyor' }}</td>
                                </tr>
                                
                                <tr>
                                    <th><i class="fas fa-sticky-note"></i> Not Alanı</th>
                                    <td>{{ $personel->not_alani ?? 'Bilgi Yok' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</div>
<!-- Modal -->
<div class="modal fade" id="editPersonModal" tabindex="-1" aria-labelledby="editPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editPersonModalLabel">Personel Bilgilerini Düzenle</h5>
                
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="card card-outline card-primary">
                    <!-- Card Header with Nav Tabs -->
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="modalTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active small" id="modal-basic-tab" data-bs-toggle="tab" href="#modal-basic" role="tab" aria-controls="modal-basic" aria-selected="true">
                                    <i class="fas fa-user"></i> Temel Bilgiler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link small" id="modal-other-tab" data-bs-toggle="tab" href="#modal-other" role="tab" aria-controls="modal-other" aria-selected="false">
                                    <i class="fas fa-info-circle"></i> Diğer Bilgiler
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Card Body with Tab Contents -->
                    <div class="card-body p-0">
                        <form id="editPersonForm">
                            @csrf
                            <div class="tab-content" id="modalTabsContent">
                                <!-- Temel Bilgiler Tab -->
                                <div class="tab-pane fade show active p-3" id="modal-basic" role="tabpanel" aria-labelledby="modal-basic-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_isim" class="form-label">İsim <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_isim" name="isim" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_tc_kimlik_no" class="form-label">T.C. Kimlik No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_tc_kimlik_no" name="tc_kimlik_no" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_cep_telefonu" class="form-label">Cep Telefonu <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_cep_telefonu" name="cep_telefonu" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_ise_giris_tarihi" class="form-label">İşe Giriş Tarihi <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="edit_ise_giris_tarihi" name="ise_giris_tarihi" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Diğer Bilgiler Tab -->
                                <div class="tab-pane fade p-3" id="modal-other" role="tabpanel" aria-labelledby="modal-other-tab">
                                    <div class="row">
                                        <!-- Sol Sütun -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="e_posta" class="form-label">E-Posta</label>
                                                <input type="email" class="form-control" id="e_posta" name="e_posta" placeholder="E-posta adresi giriniz">
                                            </div>
                                            <div class="mb-3">
                                                <label for="dogum_tarihi" class="form-label">Doğum Tarihi</label>
                                                <input type="date" class="form-control" id="dogum_tarihi" name="dogum_tarihi">
                                            </div>
                                            <div class="mb-3">
                                                <label for="isten_ayrilis_tarihi" class="form-label">İşten Çıkış Tarihi</label>
                                                <input type="date" class="form-control" id="isten_ayrilis_tarihi" name="isten_ayrilis_tarihi">
                                            </div>
                                            <div class="mb-3">
                                                <label for="aylik_net_maas" class="form-label">Aylık Net Maaş</label>
                                                <input type="number" step="0.01" class="form-control" id="aylik_net_maas" name="aylik_net_maas" placeholder="Maaş bilgisi giriniz">
                                            </div>
                                            <div class="mb-3">
                                                <label for="banka_hesap_no" class="form-label">Banka Hesap No</label>
                                                <input type="text" class="form-control" id="banka_hesap_no" name="banka_hesap_no" placeholder="Hesap numarası giriniz">
                                            </div>
                                            <div class="mb-3">
                                                <label for="departman" class="form-label">Departman</label>
                                                <input type="text" class="form-control" id="departman" name="departman" placeholder="Departman giriniz">
                                            </div>
                                        </div>

                                        <!-- Sağ Sütun -->
                                        <div class="col-md-6">
                                            
                                            <div class="mb-3">
                                                <label for="adres" class="form-label">Adres</label>
                                                <textarea class="form-control" id="adres" name="adres" rows="2" placeholder="Adres giriniz"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="banka_bilgileri" class="form-label">Banka Bilgileri</label>
                                                <textarea class="form-control" id="banka_bilgileri" name="banka_bilgileri" rows="2" placeholder="Banka bilgileri giriniz"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="not_alani" class="form-label">Not</label>
                                                <textarea class="form-control" id="not_alani" name="not_alani" rows="2" placeholder="Not giriniz"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="submit" form="editPersonForm" class="btn btn-success">Güncelle</button>
                
            </div>
        </div>
    </div>
</div>


@endsection
<script></script>
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const terminateButton = document.getElementById('terminatePerson');

    // "İşten Çıkar" butonuna tıklama
    terminateButton.addEventListener('click', function () {
        const personelId = terminateButton.dataset.id;

        if (!personelId) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Personel ID alınamadı. Lütfen tekrar deneyin!',
            });
            return;
        }

        // Bugünün tarihini YYYY-MM-DD formatında al
        const today = new Date().toISOString().split('T')[0];

        // Kullanıcıdan tarih iste
        Swal.fire({
            title: 'İşten Çıkış Tarihi',
            html: `<input type="date" id="terminationDate" class="form-control" value="${today}" placeholder="Tarih giriniz">`,
            confirmButtonText: 'Kaydet',
            focusConfirm: false,
            preConfirm: () => {
                const date = document.getElementById('terminationDate').value;
                if (!date) {
                    Swal.showValidationMessage('Lütfen bir tarih giriniz!');
                }
                return date;
            }
        }).then(result => {
            if (result.isConfirmed) {
                const terminationDate = result.value;

                // API isteği ile tarihi kaydet
                fetch(`/admin/personeller/${personelId}/terminate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ isten_ayrilis_tarihi: terminationDate })
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Güncelleme başarısız oldu!');
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message || 'İşten çıkış tarihi başarıyla kaydedildi!',
                        }).then(() => {
                            window.location.reload(); // Sayfayı yenile
                        });
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Güncelleme sırasında bir hata oluştu. Lütfen tekrar deneyin!',
                        });
                    });
            }
        });
    });
});

 document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.getElementById('updatePerson');
    const modalElement = document.getElementById('editPersonModal');
    const modal = new bootstrap.Modal(modalElement);

    // Edit Button Click Event
    editButton.addEventListener('click', function () {
        const personelId = editButton.dataset.id;

        if (!personelId) {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Personel ID alınamadı. Lütfen tekrar deneyin!',
            });
            return;
        }

        // Fetch Data from API
        fetch(`/admin/personeller/get/${personelId}`)
            .then(response => {
                if (!response.ok) throw new Error('Veriler alınamadı!');
                return response.json();
            })
            .then(data => {
                // Fill Form Fields
                document.getElementById('edit_isim').value = data.isim || '';
                document.getElementById('edit_tc_kimlik_no').value = data.tc_kimlik_no || '';
                document.getElementById('edit_cep_telefonu').value = data.cep_telefonu || '';
                document.getElementById('edit_ise_giris_tarihi').value = data.ise_giris_tarihi || '';
                document.getElementById('e_posta').value = data.e_posta || '';
                document.getElementById('dogum_tarihi').value = data.dogum_tarihi || '';
                document.getElementById('isten_ayrilis_tarihi').value = data.isten_ayrilis_tarihi || '';
                document.getElementById('aylik_net_maas').value = data.aylik_net_maas || '';
                document.getElementById('banka_hesap_no').value = data.banka_hesap_no || '';
                document.getElementById('adres').value = data.adres || '';
                document.getElementById('banka_bilgileri').value = data.banka_bilgileri || '';
                document.getElementById('not_alani').value = data.not_alani || '';
                document.getElementById('departman').value = data.departman || '';
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Veriler alınamadı. Lütfen tekrar deneyin!',
                });
            });

        modal.show();
    });

    // Form Submit Event
    const form = document.getElementById('editPersonForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const personelId = editButton.dataset.id;

        fetch(`/admin/personeller/${personelId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData)),
        })
            .then(response => {
                if (!response.ok) throw new Error('Güncelleme işlemi başarısız!');
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: data.message || 'Güncelleme başarıyla tamamlandı!',
                }).then(() => {
                    modal.hide();
                    window.location.reload(); // Sayfayı yenilemek için
                });
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Güncelleme sırasında bir hata oluştu. Lütfen tekrar deneyin!',
                });
            });
    });

    // Bootstrap Tab Switching - MODAL İÇİN EKLENDİ
    const modalTabs = document.querySelectorAll('#modalTabs a');
    modalTabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const targetTab = new bootstrap.Tab(tab);
            targetTab.show(); // Bu, sekmelerin doğru şekilde değişmesini sağlar
        });
    });
});



</script>
@endsection
