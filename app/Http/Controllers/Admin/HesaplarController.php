<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankaHesaplari;
use App\Models\KasaTanimlari;
use App\Models\KrediKartlari;
use App\Models\Masraflar;
use App\Models\POSHesaplari;
use App\Models\Hareketler;
use App\Models\Team;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\HareketlerExport;
use Barryvdh\DomPDF\Facade\Pdf;


class HesaplarController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('hesaplar_goruntuleme'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = auth()->user()->team_id;

        // Eğer kullanıcı admin (team_id = 1) ise tüm verileri al
        $isAdmin = ($teamId == 1);

        // Kasa Tanımları Güncel Bakiye Toplamı
        $kasaBakiyeToplami = KasaTanimlari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->sum(function ($kasa) {
            return $this->hesaplaGuncelBakiye($kasa->hesap_no, $kasa->team_id);
        });

        // Banka Hesapları Güncel Bakiye Toplamı
        $bankaBakiyeToplami = BankaHesaplari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->sum(function ($banka) {
            return $this->hesaplaGuncelBakiye($banka->hesap_no, $banka->team_id);
        });

        // POS Hesapları Güncel Bakiye Toplamı
        $posBakiyeToplami = POSHesaplari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->sum(function ($pos) {
            return $this->hesaplaGuncelBakiye($pos->hesap_no, $pos->team_id);
        });

        // Kredi Kartları Güncel Bakiye Toplamı
        $krediBakiyeToplami = KrediKartlari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->sum(function ($kredi) {
            return $this->hesaplaGuncelBakiye($kredi->hesap_no, $kredi->team_id);
        });

        // Genel Toplam
        $genelToplam = $kasaBakiyeToplami + $bankaBakiyeToplami + $posBakiyeToplami + $krediBakiyeToplami;

        // Kasa Tanımları
        $kasaTanimlari = KasaTanimlari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->map(function ($kasa) {
            $kasa->guncel_bakiye = $this->hesaplaGuncelBakiye($kasa->hesap_no, $kasa->team_id);
            return $kasa;
        });

        // Banka Hesapları
        $bankaHesaplari = BankaHesaplari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->map(function ($banka) {
            $banka->guncel_bakiye = $this->hesaplaGuncelBakiye($banka->hesap_no, $banka->team_id);
            return $banka;
        });

        // POS Hesapları
        $posHesaplari = POSHesaplari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->map(function ($pos) {
            $pos->guncel_bakiye = $this->hesaplaGuncelBakiye($pos->hesap_no, $pos->team_id);
            return $pos;
        });

        // Kredi Kartları
        $krediKartlari = KrediKartlari::when(!$isAdmin, function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get()->map(function ($kredi) {
            $kredi->guncel_bakiye = $this->hesaplaGuncelBakiye($kredi->hesap_no, $kredi->team_id);
            return $kredi;
        });

        return view('admin.hesaplar.index', compact(
            'kasaTanimlari',
            'bankaHesaplari',
            'posHesaplari',
            'krediKartlari',
            'kasaBakiyeToplami',
            'bankaBakiyeToplami',
            'posBakiyeToplami',
            'krediBakiyeToplami',
            'genelToplam'
        ));
    }


    private function hesaplaGuncelBakiye($hesapNo, $teamId)
    {
        $isAdmin = ($teamId == 1); // Admin kullanıcı kontrolü

        // Gelen toplam
        $gelenToplam = Hareketler::where('hedef_hesap_no', $hesapNo)
            ->when(!$isAdmin, function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Eğer admin değilse team_id filtrele
            })
            ->sum('gelen');

        // Giden toplam
        $gidenToplam = Hareketler::where('kaynak_hesap_no', $hesapNo)
            ->when(!$isAdmin, function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Eğer admin değilse team_id filtrele
            })
            ->sum('giden');

        return $gelenToplam - $gidenToplam; // Net bakiye
    }


    public function getNewAccountNumber()
    {
        // Tüm tabloların hesap numaralarını al
        $kasaNoList = KasaTanimlari::pluck('hesap_no')->map(fn($no) => (int) $no)->toArray();
        $bankaNoList = BankaHesaplari::pluck('hesap_no')->map(fn($no) => (int) $no)->toArray();
        $posNoList = POSHesaplari::pluck('hesap_no')->map(fn($no) => (int) $no)->toArray();
        $krediNoList = KrediKartlari::pluck('hesap_no')->map(fn($no) => (int) $no)->toArray();

        // Tüm hesap numaralarını birleştir
        $allNumbers = array_merge($kasaNoList, $bankaNoList, $posNoList, $krediNoList);

        // Eğer hiçbir hesap numarası yoksa 1 ile başla
        $maxHesapNo = count($allNumbers) > 0 ? max($allNumbers) : 0;

        // Yeni hesap numarasını oluştur ve döndür
        return response()->json(['hesap_no' => $maxHesapNo + 1]);
    }



    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'account_type' => 'required|string',
            'tanım' => 'required|string|max:255',
            'güncel_bakiye' => 'required|string', // Şu anda string olarak geliyor, numeric olarak dönüşecek
            'etiket_rengi' => 'nullable|string',
            'para_birimi' => 'required|string|max:10',
            'hesap_no' => 'required|string|max:50',
        ]);

        // 'güncel_bakiye' alanını normalize et
        $validatedData['güncel_bakiye'] = (float) str_replace(['.', ','], ['', '.'], $request->input('güncel_bakiye'));

        $validatedData['team_id'] = auth()->user()->team_id;

        try {
            $hesap = null;
            switch ($request->account_type) {
                case 'kasa':
                    $hesap = KasaTanimlari::create($validatedData);
                    break;
                case 'banka':
                    $hesap = BankaHesaplari::create($validatedData);
                    break;
                case 'pos':
                    $hesap = POSHesaplari::create($validatedData);
                    break;
                case 'kredi':
                    $hesap = KrediKartlari::create($validatedData);
                    break;
                default:
                    return back()->with('error', 'Geçersiz hesap türü');
            }

            if ($hesap) {
                Hareketler::create([
                    'tarih' => now(),
                    'islem_tipi' => 'Bakiye Güncelleme',
                    'gelen' => $validatedData['güncel_bakiye'], // Normalleştirilmiş bakiye
                    'giden' => 0,
                    'aciklama' => 'Açılış Bakiyesi',
                    'kaynak_hesap_no' => null,
                    'hedef_hesap_no' => $request->hesap_no,
                    'team_id' => auth()->user()->team_id,
                    'kullanici' => auth()->user()->name,
                ]);
            }

            return redirect()->route('admin.hesaplar.index')->with('success', 'Hesap başarıyla eklendi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }
    public function updateAccount(Request $request)
    {
        // Gelen veriyi doğrula
        $validatedData = $request->validate([
            'hesap_no' => 'required|string', // Sadece hesap_no doğrulanıyor
            'tanım' => 'required|string|max:255',
            'etiket_rengi' => 'required|string',
        ]);

        $updated = false;

        try {
            // Tüm modelleri kontrol et
            foreach (['KasaTanimlari', 'BankaHesaplari', 'POSHesaplari', 'KrediKartlari'] as $model) {
                $modelClass = "App\\Models\\$model";
                $hesap = $modelClass::where('hesap_no', $request->hesap_no)->first();

                if ($hesap) {
                    // Güncelleme işlemini yap
                    $hesap->update([
                        'tanım' => $validatedData['tanım'],
                        'etiket_rengi' => $validatedData['etiket_rengi'],
                    ]);
                    $updated = true;
                    break;
                }
            }

            if ($updated) {
                return redirect()->route('admin.hesaplar.index')->with('success', 'Hesap başarıyla güncellendi.');
            } else {
                return back()->with('error', 'Hesap bulunamadı.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Hesap Güncelleme Hatası: ' . $e->getMessage());
            return back()->with('error', 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }




    public function show($hesap_no)
    {
        abort_if(Gate::denies('hesaplar_detay'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $hesaplama = $this->hesaplaGelirGiderBakiye($hesap_no);
        $hesapAdi = $this->getHesapAdi($hesap_no); // Hesap adı alınır.

        // Kaynak hesap numarası (hesap_no zaten mevcut)
        return view('admin.hesaplar.show', array_merge([
            'hesap_no' => $hesap_no,
            'hesapAdi' => $hesapAdi,
            'kaynak_hesap_no' => $hesap_no // Kaynak hesap numarasını ekledik
        ], $hesaplama));
    }



    private function hesaplaGelirGiderBakiye($hesap_no)
    {
        $teamId = auth()->user()->team_id;
        $isAdmin = ($teamId == 1); // Admin kontrolü

        // Toplam Gelir
        $toplamGelir = Hareketler::where('hedef_hesap_no', $hesap_no)
            ->when(!$isAdmin, function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Admin değilse team_id'ye göre filtrele
            })
            ->sum('gelen');

        // Toplam Gider
        $toplamGider = Hareketler::where('kaynak_hesap_no', $hesap_no)
            ->when(!$isAdmin, function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Admin değilse team_id'ye göre filtrele
            })
            ->sum('giden');

        // Güncel Bakiye
        $guncelBakiye = $toplamGelir - $toplamGider;

        // Tüm değerleri aynı formatta döndür
        return [
            'toplamGelir' => number_format($toplamGelir, 2, '.', ''), // Ondalık ayracı nokta
            'toplamGider' => number_format($toplamGider, 2, '.', ''), // Ondalık ayracı nokta
            'guncelBakiye' => number_format($guncelBakiye, 2, '.', '') // Ondalık ayracı nokta
        ];
    }




    public function hareketler(Request $request, $hesap_no)
    {
        $teamId = auth()->user()->team_id;
        $isAdmin = ($teamId == 1); // Admin kontrolü

        // Sorgu oluştur
        $query = Hareketler::where(function ($q) use ($hesap_no) {
            $q->where('kaynak_hesap_no', $hesap_no)
                ->orWhere('hedef_hesap_no', $hesap_no);
        });

        // Eğer admin değilse, team_id filtresi ekle
        if (!$isAdmin) {
            $query->where('team_id', $teamId);
        }

        // Arama işlemi
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('islem_tipi', 'like', "%$search%")
                    ->orWhere('kullanici', 'like', "%$search%")
                    ->orWhere('aciklama', 'like', "%$search%");
            });
        }

        // Verileri al
        $hareketler = $query->orderBy('tarih', 'asc')
            ->select(['id', 'tarih', 'islem_tipi', 'kullanici', 'aciklama', 'gelen', 'giden']) // ID eklendi
            ->get();

        return response()->json(['data' => $hareketler]);
    }



    public function export($type, $hesap_no)
    {
        $teamId = auth()->user()->team_id;
        $isAdmin = ($teamId == 1); // Admin kontrolü

        // Verileri filtrele
        $data = Hareketler::where(function ($query) use ($hesap_no) {
            $query->where('kaynak_hesap_no', $hesap_no)
                ->orWhere('hedef_hesap_no', $hesap_no);
        })
            ->when(!$isAdmin, function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Admin değilse team_id filtresi uygula
            })
            ->get();

        // Excel formatında çıktı
        if ($type === 'excel') {
            return Excel::download(new HareketlerExport($data), 'hareketler.xlsx');
        }

        // PDF formatında çıktı
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.hesaplar.pdf', ['hareketler' => $data]);
            return $pdf->download('hareketler.pdf');
        }

        // Geçersiz çıktı türü
        return back()->with('error', 'Geçersiz çıktı türü.');
    }


    public function update(Request $request)
    {
        try {
            // Gelen verileri doğrula
            $validated = $request->validate([
                'id' => 'required|exists:hareketler,id', // Hareketler tablosundaki ID
                'tarih' => 'nullable|date',
                'aciklama' => 'nullable|string|max:255',
                'gelen' => 'nullable|numeric|min:0',
                'giden' => 'nullable|numeric|min:0',
            ]);

            // Transaction başlat
            DB::beginTransaction();

            // Hareket kaydını bul ve eski değerleri sakla
            $hareket = Hareketler::findOrFail($validated['id']);
            $oldTarih = $hareket->tarih;
            $oldTutar = $hareket->giden; // "Giden" alanı masraf tutarını ifade ediyor
            $oldKullanici = $hareket->kullanici;

            // Açıklamayı `-` işaretine göre ayır
            $splitAciklama = explode('-', $hareket->aciklama, 2);
            $tedarikciAdi = trim($splitAciklama[0]); // `-` öncesi: Tedarikçi adı
            $kullaniciAciklama = isset($validated['aciklama']) ? trim($validated['aciklama']) : trim($splitAciklama[1] ?? '');

            // Kullanıcı açıklamasında değişiklik yapılmışsa yalnızca sağ tarafı güncelle
            if (isset($validated['aciklama']) && strpos($validated['aciklama'], '-') === false) {
                $yeniAciklama = $tedarikciAdi . ' - ' . $kullaniciAciklama;
            } else {
                $yeniAciklama = $validated['aciklama']; // Komple düzenleme yapıldıysa olduğu gibi al
            }

            // Hareketler tablosundaki kaydı güncelle
            if (isset($validated['tarih']))
                $hareket->tarih = $validated['tarih'];
            $hareket->aciklama = $yeniAciklama; // Güncellenmiş açıklamayı kaydet
            if (isset($validated['gelen']))
                $hareket->gelen = $validated['gelen'];
            if (isset($validated['giden']))
                $hareket->giden = $validated['giden'];
            $hareket->save();

            // Eğer işlem tipi "Masraf" ise Masraflar tablosunu güncelle
            if ($hareket->islem_tipi === 'Masraf') {
                $masraf = Masraflar::where([
                    ['tarih', '=', $oldTarih],
                    ['tutar', '=', $oldTutar],
                    ['kullanici', '=', $oldKullanici],
                ])->first();

                if ($masraf) {
                    $masraf->tarih = $validated['tarih'] ?? $masraf->tarih;
                    $masraf->aciklama = $kullaniciAciklama; // Masraflar için sadece kullanıcı açıklaması kaydediliyor
                    $masraf->tutar = $validated['giden'] ?? $masraf->tutar;
                    $masraf->save();
                }
            }

            // Eğer işlem tipi "Tedarikçi Ödemesi" ise t_hareketleri tablosunu da güncelle
            if ($hareket->islem_tipi === 'Tedarikçiye Ödeme') {
                $tHareket = DB::table('t_hareketleri')
                    ->where('tarih', '=', $oldTarih)
                    ->where('tutar', '=', $oldTutar)
                    ->where('kullanici', '=', $oldKullanici)
                    ->first();

                if ($tHareket) {
                    // t_hareketleri tablosunda sadece kullanıcı açıklamasını kaydet
                    DB::table('t_hareketleri')
                        ->where('id', '=', $tHareket->id)
                        ->update([
                            'tarih' => $validated['tarih'] ?? $tHareket->tarih,
                            'aciklama' => $kullaniciAciklama, // Sadece kullanıcı açıklaması
                            'tutar' => $validated['giden'] ?? $tHareket->tutar,
                            'updated_at' => now(),
                        ]);
                }

                // Masraflar tablosunu güncelle
                $masraf = Masraflar::where([
                    ['tarih', '=', $oldTarih],
                    ['tutar', '=', $oldTutar],
                    ['kullanici', '=', $oldKullanici],
                ])->first();

                if ($masraf) {
                    $masraf->tarih = $validated['tarih'] ?? $masraf->tarih;
                    $masraf->aciklama = $yeniAciklama; // Tedarikçi adı + kullanıcı açıklaması
                    $masraf->tutar = $validated['giden'] ?? $masraf->tutar;
                    $masraf->save();
                }
            }

            // İşlemi başarıyla tamamla
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Hareket ve ilgili kayıtlar başarıyla güncellendi.',
                'data' => $hareket,
            ]);
        } catch (\Exception $e) {
            // Hata durumunda transaction'ı geri al
            DB::rollBack();
            Log::error('Güncelleme Hatası: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getKartlar($hesap_no)
    {
        $hesaplama = $this->hesaplaGelirGiderBakiye($hesap_no);

        // Tüm değerleri formatlayarak döndür
        return response()->json([
            'toplamGelir' => number_format($hesaplama['toplamGelir'], 2, ',', '.'),
            'toplamGider' => number_format($hesaplama['toplamGider'], 2, ',', '.'),
            'guncelBakiye' => number_format($hesaplama['guncelBakiye'], 2, ',', '.'),
        ]);
    }
    public function delete(Request $request)
    {
        try {
            // Doğrulama: İlgili ID'nin var olup olmadığını kontrol et
            $validatedData = $request->validate([
                'id' => 'required|exists:hareketler,id', // ID alanı zorunlu ve veri tabanında var olmalı
            ]);

            // Silinecek hareketi bul
            $hareket = Hareketler::findOrFail($validatedData['id']);

            // Eğer işlem tipi "Masraf" ise Masraflar tablosundaki kaydı sil
            if ($hareket->islem_tipi === 'Masraf') {
                Masraflar::where([
                    ['tarih', '=', $hareket->tarih],
                    ['tutar', '=', $hareket->giden], // Hareketler tablosunda "giden" alanı masraf tutarını gösterir
                    ['kullanici', '=', $hareket->kullanici],
                ])->delete();
            }

            // Eğer işlem tipi "Tedarikçi Ödemesi" ise t_hareketleri tablosundaki kaydı da sil
            if ($hareket->islem_tipi === 'Tedarikçiye Ödeme') {
                DB::table('t_hareketleri')->where([
                    ['tarih', '=', $hareket->tarih],
                    ['tutar', '=', $hareket->giden], // "giden" alanını eşleştir
                    ['kullanici', '=', $hareket->kullanici],
                ])->delete();

                // Ayrıca Masraflar tablosundaki kaydı da sil
                Masraflar::where([
                    ['tarih', '=', $hareket->tarih],
                    ['tutar', '=', $hareket->giden],
                    ['kullanici', '=', $hareket->kullanici],
                ])->delete();
            }

            // Hareketler tablosundan hareketi sil
            $hareket->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Hareket ve ilgili kayıtlar başarıyla silindi.',
            ]);
        } catch (\Exception $e) {
            Log::error('Silme Hatası: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function paraGirisi(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Para Girişi İsteği Başladı', $request->all());

            Hareketler::create([
                'tarih' => $request->tarih,
                'kaynak_hesap_no' => null,
                'hedef_hesap_no' => $request->hesap_no,
                'gelen' => $request->tutar,

                'aciklama' => $request->aciklama,
                'team_id' => auth()->user()->team_id,
                'kullanici' => auth()->user()->name,
                'islem_tipi' => 'Para Girişi',
            ]);


            \Illuminate\Support\Facades\Log::info('Para Girişi Başarıyla Tamamlandı');

            return response()->json([
                'status' => 'success',
                'message' => 'Para girişi başarıyla kaydedildi.',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Para Girişi Hatası: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function paraCikisi(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Para Çıkışı İsteği Verileri:', $request->all());

        try {
            Hareketler::create([
                'tarih' => $request->tarih,
                'kaynak_hesap_no' => $request->kaynak_hesap_no, // Kaynak hesap no
                'hedef_hesap_no' => null, // Hedef hesap yok
                'giden' => $request->tutar, // Giden tutar
                'gelen' => 0, // Gelen tutar yok
                'aciklama' => $request->aciklama,
                'team_id' => auth()->user()->team_id,
                'kullanici' => auth()->user()->name,
                'islem_tipi' => 'Para Çıkışı', // İşlem tipi
            ]);

            \Illuminate\Support\Facades\Log::info('Para Çıkışı Kaydı Başarılı');

            return response()->json([
                'status' => 'success',
                'message' => 'Para çıkışı başarıyla kaydedildi.',
            ]);
        } catch (\Exception $e) {
            Log::error('Para Çıkışı Hatası: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function getHesaplar(Request $request)
    {
        try {
            $mevcutHesap = $request->get('mevcut_hesap'); // Mevcut hesap no
            $teamId = auth()->user()->team_id; // Kullanıcının takım ID'si
            $isAdmin = ($teamId == 1); // Admin kontrolü

            // Verileri çek, mevcut hesabı hariç tut ve team_id'ye göre filtrele
            $data = [
                'kasaHesaplari' => KasaTanimlari::where('hesap_no', '!=', $mevcutHesap)
                    ->when(!$isAdmin, function ($query) use ($teamId) {
                        $query->where('team_id', $teamId); // Admin değilse team_id filtrele
                    })
                    ->get(['hesap_no', 'tanım']),

                'bankaHesaplari' => BankaHesaplari::where('hesap_no', '!=', $mevcutHesap)
                    ->when(!$isAdmin, function ($query) use ($teamId) {
                        $query->where('team_id', $teamId); // Admin değilse team_id filtrele
                    })
                    ->get(['hesap_no', 'tanım']),

                'posHesaplari' => POSHesaplari::where('hesap_no', '!=', $mevcutHesap)
                    ->when(!$isAdmin, function ($query) use ($teamId) {
                        $query->where('team_id', $teamId); // Admin değilse team_id filtrele
                    })
                    ->get(['hesap_no', 'tanım']),

                'krediKartlari' => KrediKartlari::where('hesap_no', '!=', $mevcutHesap)
                    ->when(!$isAdmin, function ($query) use ($teamId) {
                        $query->where('team_id', $teamId); // Admin değilse team_id filtrele
                    })
                    ->get(['hesap_no', 'tanım']),
            ];

            // JSON formatında döndür
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Hesaplar API Hatası: ' . $e->getMessage());
            return response()->json(['message' => 'Bir hata oluştu!'], 500);
        }
    }





    public function getHesapAdi($hesapNo)
    {
        // 4 farklı modelden hesap adı sorgula
        return KasaTanimlari::where('hesap_no', $hesapNo)->value('tanım') ??
            BankaHesaplari::where('hesap_no', $hesapNo)->value('tanım') ??
            POSHesaplari::where('hesap_no', $hesapNo)->value('tanım') ??
            KrediKartlari::where('hesap_no', $hesapNo)->value('tanım') ?? 'Bilinmeyen Hesap';
    }
    public function virmanKaydet(Request $request)
    {
        $validatedData = $request->validate([
            'kaynak_hesap_no' => 'required|string', // Kaynak hesap eklenmeli
            'hedef_hesap_no' => 'required|string',
            'aciklama' => 'required|string|max:255',
            'tutar' => 'required|numeric|min:0.01',
        ]);

        $teamId = auth()->user()->team_id;
        $kullanici = auth()->user()->name; // İşlem yapan kullanıcı
        $tutar = $validatedData['tutar'];

        // Hesap adlarını çek
        $kaynakHesap = $this->getHesapAdi($validatedData['kaynak_hesap_no']);
        $hedefHesap = $this->getHesapAdi($validatedData['hedef_hesap_no']);

        // Açıklama oluştur
        $kaynakAciklama = 'Başka hesaba virman: ' . $hedefHesap . ' (' . $validatedData['aciklama'] . ')';
        $hedefAciklama = 'Başka hesaptan virman: ' . $kaynakHesap . ' (' . $validatedData['aciklama'] . ')';

        // Kaynak hesap için hareket
        Hareketler::create([
            'tarih' => now(), // Tarih eklendi
            'kaynak_hesap_no' => $validatedData['kaynak_hesap_no'],
            'hedef_hesap_no' => null,
            'giden' => $tutar,
            'gelen' => 0,
            'aciklama' => $kaynakAciklama,
            'islem_tipi' => 'Virman',
            'team_id' => $teamId,
            'kullanici' => $kullanici,
        ]);

        // Hedef hesap için hareket
        Hareketler::create([
            'tarih' => now(),
            'kaynak_hesap_no' => null,
            'hedef_hesap_no' => $validatedData['hedef_hesap_no'],
            'giden' => 0,
            'gelen' => $tutar,
            'aciklama' => $hedefAciklama,
            'islem_tipi' => 'Virman',
            'team_id' => $teamId,
            'kullanici' => $kullanici,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Virman işlemi başarıyla kaydedildi.']);
    }
    public function transferAl(Request $request)
    {
        try {
            // Verileri al ve doğrula
            $validatedData = $request->validate([
                'kaynak_hesap_no' => 'required|string', // Kaynak hesap zorunlu
                'hedef_hesap_no' => 'required|string', // Hedef hesap zorunlu
                'aciklama' => 'required|string|max:255', // Açıklama zorunlu
                'tutar' => 'required|numeric|min:0.01', // Tutar zorunlu ve 0'dan büyük olmalı
            ]);

            $teamId = auth()->user()->team_id; // Takım ID'si
            $kullanici = auth()->user()->name; // İşlem yapan kullanıcı

            $kaynakHesapNo = $validatedData['kaynak_hesap_no'];
            $hedefHesapNo = $validatedData['hedef_hesap_no'];
            $tutar = $validatedData['tutar'];
            $aciklama = $validatedData['aciklama'];

            if ($kaynakHesapNo === $hedefHesapNo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kaynak ve hedef hesap aynı olamaz!',
                ], 400);
            }

            // Hesap isimlerini çek
            $kaynakHesap = $this->getHesapAdi($validatedData['kaynak_hesap_no']);
            $hedefHesap = $this->getHesapAdi($validatedData['hedef_hesap_no']);

            // Açıklama oluştur
            $kaynakAciklama = 'Başka hesaba virman: ' . $hedefHesap . ' (' . $validatedData['aciklama'] . ')';
            $hedefAciklama = 'Başka hesaptan virman: ' . $kaynakHesap . ' (' . $validatedData['aciklama'] . ')';

            // Kaynak hesap için gider kaydı oluştur
            Hareketler::create([
                'tarih' => now(),
                'kaynak_hesap_no' => $kaynakHesapNo,
                'hedef_hesap_no' => null,
                'giden' => $tutar,
                'gelen' => 0,
                'aciklama' => $kaynakAciklama,
                'islem_tipi' => 'Virman',
                'team_id' => $teamId,
                'kullanici' => $kullanici,
            ]);

            // Hedef hesap için giriş kaydı oluştur
            Hareketler::create([
                'tarih' => now(),
                'kaynak_hesap_no' => null,
                'hedef_hesap_no' => $hedefHesapNo,
                'giden' => 0,
                'gelen' => $tutar,
                'aciklama' => $hedefAciklama,
                'islem_tipi' => 'Virman',
                'team_id' => $teamId,
                'kullanici' => $kullanici,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transfer başarıyla tamamlandı!',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Transfer Al Hatası: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getOtherTeams()
    {
        $teams = Team::where('id', '!=', 1)
            ->where('id', '!=', auth()->user()->team_id)
            ->select('id', 'name')
            ->get();

        return response()->json(['teams' => $teams]);
    }
    public function getTeamAccounts(Request $request)
    {
        $teamId = $request->team_id;

        // Her tablodan verileri al ve gruplandır
        $groupedAccounts = [
            'Kasa Tanımları' => KasaTanimlari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım')->get(),
            'Banka Hesapları' => BankaHesaplari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım')->get(),
            'POS Hesapları' => POSHesaplari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım')->get(),
            'Kredi Kartları' => KrediKartlari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım')->get(),
        ];

        // Gruplandırılmış hesapları döndür
        return response()->json(['groupedAccounts' => $groupedAccounts]);
    }

    public function transferOtherTeam(Request $request)
    {
        $validatedData = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'hesap_no' => 'required|string',
            'aciklama' => 'required|string|max:255',
            'tutar' => 'required|numeric|min:0.01',
            'kaynak_hesap_no' => 'required|string', // Kaynak hesap no doğrulaması
        ]);

        $teamId = auth()->user()->team_id;
        $kullanici = auth()->user()->name;

        $targetTeam = Team::findOrFail($validatedData['team_id']);
        $targetAccount = $this->getHesapAdi($validatedData['hesap_no']);
        $currentAccount = $this->getHesapAdi($validatedData['kaynak_hesap_no']); // Dinamik kaynak hesap

        // Kaynak Hareketi
        Hareketler::create([
            'tarih' => now(),
            'kaynak_hesap_no' => $validatedData['kaynak_hesap_no'], // Kaynak hesap no
            'hedef_hesap_no' => null,
            'giden' => $validatedData['tutar'],
            'gelen' => 0,
            'aciklama' => "Başka Sektöre Virman ($targetTeam->name: $targetAccount) ({$validatedData['aciklama']})",
            'islem_tipi' => 'S.Virman',
            'team_id' => $teamId,
            'kullanici' => $kullanici,
        ]);

        // Hedef Hareketi
        Hareketler::create([
            'tarih' => now(),
            'kaynak_hesap_no' => null,
            'hedef_hesap_no' => $validatedData['hesap_no'],
            'giden' => 0,
            'gelen' => $validatedData['tutar'],
            'aciklama' => "Başka Sektörden Virman ($currentAccount) ({$validatedData['aciklama']})",
            'islem_tipi' => 'S.Virman',
            'team_id' => $validatedData['team_id'],
            'kullanici' => $kullanici,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Transfer işlemi başarıyla tamamlandı!']);
    }

}
