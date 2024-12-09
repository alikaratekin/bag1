<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankaHesaplari;
use App\Models\KasaTanimlari;
use App\Models\KrediKartlari;
use App\Models\Masraflar;
use App\Models\POSHesaplari;
use App\Models\Proje;
use App\Models\MasrafKalemi;
use App\Models\Hareketler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasraflarController extends Controller
{
    /**
     * Masraflar listesi.
     */
    public function index()
{
    try {
        $user = Auth::user();
        $teamId = $user->team_id;

        // Masraflar (takım ID'ye göre filtreleniyor)
        $masraflar = $teamId == 1
            ? Masraflar::all()
            : Masraflar::where('team_id', $teamId)->get();

        // Projeler (takım ID'ye göre)
        $projeler = Proje::where('team_id', $teamId)->get();

        // Masraf Kalemleri (Takım ID'ye göre filtrelenmiş gruplar ve alt kalemler)
        $masrafGruplari = MasrafKalemi::with(['masrafGrubu' => function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        }])->where(function ($query) use ($teamId) {
            $query->where('team_id', $teamId) // Normal filtre
                  ->orWhere(function ($q) {
                      // Masraf grubu ve masraf kalemi ID'si 1 olanları ekle
                      $q->where('id', 1)
                        ->whereHas('masrafGrubu', function ($subQuery) {
                            $subQuery->where('id', 1);
                        });
                  });
        })->get()
          ->groupBy(function ($item) {
              return optional($item->masrafGrubu)->ad ?? 'Diğer';
          });


        // Ödeme Hesapları (Takım ID'ye göre gruplar halinde ayrılmış)
        $hesaplar = [
            'Banka Hesapları' => BankaHesaplari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(),
            'Kredi Kartları' => KrediKartlari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(),
            'POS Hesapları' => POSHesaplari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(),
            'Kasalar' => KasaTanimlari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(),
        ];

        return view('admin.masraflar.index', compact('masraflar', 'projeler', 'masrafGruplari', 'hesaplar'));
    } catch (\Exception $e) {
        Log::error('Masraflar listesi yüklenirken hata oluştu.', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Bir hata oluştu: ' . $e->getMessage());
    }
}

public function update(Request $request)
{
    try {
        // Gelen verileri doğrula
        $validated = $request->validate([
            'id' => 'required|exists:masraflar,id',
            'tarih' => 'required|date',
            'masraf_kalemi_id' => 'required|integer|exists:masraf_kalemleri,id',
            'aciklama' => 'required|string|max:255',
            'tutar' => 'required|numeric|min:0',
        ]);

        // Masraf kaydını bul
        $masraf = Masraflar::findOrFail($validated['id']);

        // Güncelleme öncesi eski verileri sakla
        $oldTarih = $masraf->tarih;
        $oldTutar = $masraf->tutar;
        $oldKullanici = $masraf->kullanici;

        // Masraf Kalemi ve Açıklamadan yeni açıklama oluştur
        $masrafKalemi = MasrafKalemi::with('masrafGrubu')->find($validated['masraf_kalemi_id']);
        $newAciklama = optional($masrafKalemi->masrafGrubu)->ad . ' - ' . $masrafKalemi->ad . ' | ' . $validated['aciklama'];

        // Masrafı güncelle
        $masraf->update([
            'tarih' => $validated['tarih'],
            'masraf_kalemi_id' => $validated['masraf_kalemi_id'],
            'aciklama' => $validated['aciklama'],
            'tutar' => $validated['tutar'],
        ]);

        // Hareketler tablosundaki ilgili kaydı güncelle (Masraf ve Tedarikçiye Ödeme işlem tipleri)
        Hareketler::where([
            ['tarih', '=', $oldTarih],
            ['giden', '=', $oldTutar],
            ['kullanici', '=', $oldKullanici],
        ])
        ->whereIn('islem_tipi', ['Masraf', 'Tedarikçiye Ödeme']) // İşlem tiplerini kontrol et
        ->update([
            'tarih' => $validated['tarih'],
            'giden' => $validated['tutar'], // Yeni tutarı kaydet
            'aciklama' => $newAciklama,    // Yeni açıklamayı kaydet
        ]);

        // t_hareketleri tablosundaki ilgili kaydı güncelle
        $tHareket = DB::table('t_hareketleri')->where([
            ['tarih', '=', $oldTarih],
            ['tutar', '=', $oldTutar],
            ['kullanici', '=', $oldKullanici],
        ])->first();

        if ($tHareket) {
            DB::table('t_hareketleri')->where('id', $tHareket->id)->update([
                'tarih' => $validated['tarih'],
                'tutar' => $validated['tutar'],
                'aciklama' => $validated['aciklama'], // Kullanıcının açıklaması
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => 'Masraf ve ilgili hareketler başarıyla güncellendi!'], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['error' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Masraf güncellenirken hata oluştu: ' . $e->getMessage());
        return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
    }
}




    /**
     * Yeni masraf kaydetme.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $teamId = $user->team_id;

            // Doğrulama
            $validated = $request->validate([
                'proje' => 'nullable|integer|exists:projeler,id',
                'tarih' => 'required|date',
                'masraf_kalemi_id' => 'required|integer|exists:masraf_kalemleri,id',
                'aciklama' => 'required|string|max:255',
                'tutar' => 'required|numeric|min:0',
                'kaynak_hesap_no' => 'required|string',
            ]);

            // Masraflar tablosuna kaydet
            $masraf = Masraflar::create([
                'kullanici' => $user->name,
                'team_id' => $teamId,
                'tarih' => $validated['tarih'],
                'masraf_kalemi_id' => $validated['masraf_kalemi_id'],
                'aciklama' => $validated['aciklama'],
                'kaynak_hesap_no' => $validated['kaynak_hesap_no'],
                'tutar' => $validated['tutar'],
                'proje' => $validated['proje'] ?? null, // Proje opsiyonel
            ]);

            // Masraf Kaleminin Grup ve Adını Al (Hareketler tablosu için)
            $masrafKalemi = MasrafKalemi::with('masrafGrubu')->find($validated['masraf_kalemi_id']);
            $aciklama = optional($masrafKalemi->masrafGrubu)->ad . ' - ' . $masrafKalemi->ad . ' | ' . $validated['aciklama'];

            // Hareketler tablosuna kaydet
            Hareketler::create([
                'kullanici' => $user->name,
                'team_id' => $teamId,
                'tarih' => $validated['tarih'],
                'islem_tipi' => 'Masraf',
                'aciklama' => $aciklama,
                'kaynak_hesap_no' => $validated['kaynak_hesap_no'],
                'giden' => $validated['tutar'],
            ]);

            return response()->json(['success' => 'Masraf başarıyla eklendi!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Doğrulama hatası oluştu.', ['errors' => $e->errors()]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Masraf kaydedilirken hata oluştu.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }
    public function getMasraflar(Request $request)
{
    try {
        $user = Auth::user();
        $teamId = $user->team_id;

        // Masraflar query'si
        $query = Masraflar::with(['masrafKalemi.masrafGrubu']); // Masraf kalemi ve grubu ile ilişki

        // Admin değilse takım ID'sine göre filtrele
        if ($teamId !== 1) {
            $query->where('team_id', $teamId);
        }

        // Arama yapılıyorsa filtre uygula
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('aciklama', 'LIKE', "%$search%")
                  ->orWhere('kaynak_hesap_no', 'LIKE', "%$search%");
            });
        }

        // Tüm masrafları al
        $masraflar = $query->get();

        return response()->json([
            'data' => $masraflar->map(function ($masraf) {
                // Hesap bilgilerini getir
                $hesapBilgisi = $this->getHesap($masraf->kaynak_hesap_no);

                // Masraf Grubu ve Kalemi Kontrolü
                if ($masraf->masraf_kalemi_id == 1) {
                    $masrafGrubuAdi = 'Tedarikçi';
                    $masrafKalemiAdi = 'Ödeme';
                } else {
                    $masrafGrubuAdi = $masraf->masrafKalemi && $masraf->masrafKalemi->masrafGrubu
                        ? $masraf->masrafKalemi->masrafGrubu->ad
                        : 'Belirtilmedi';

                    $masrafKalemiAdi = $masraf->masrafKalemi ? $masraf->masrafKalemi->ad : 'Belirtilmedi';
                }

                return [
                    'id' => $masraf->id,
                    'tarih' => $masraf->tarih,
                    'kullanici' => $masraf->kullanici,
                    'masrafKalemi' => $masrafKalemiAdi,
                    'masrafGrubu' => $masrafGrubuAdi,
                    'hesap' => $hesapBilgisi['grup'] . ' / ' . $hesapBilgisi['ad'],
                    'aciklama' => $masraf->aciklama,
                    'tutar' => $masraf->tutar,
                ];
            }),
        ], 200);
    } catch (\Exception $e) {
        Log::error('Masraflar verileri yüklenirken hata oluştu.', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
    }
}



    private function getHesap($kaynakHesapNo)
{
    // Hesap gruplarında arama
    $banka = BankaHesaplari::where('hesap_no', $kaynakHesapNo)->first();
    if ($banka) {
        return ['grup' => 'Banka Hesapları', 'ad' => $banka->tanım];
    }

    $krediKart = KrediKartlari::where('hesap_no', $kaynakHesapNo)->first();
    if ($krediKart) {
        return ['grup' => 'Kredi Kartları', 'ad' => $krediKart->tanım];
    }

    $pos = POSHesaplari::where('hesap_no', $kaynakHesapNo)->first();
    if ($pos) {
        return ['grup' => 'POS Hesapları', 'ad' => $pos->tanım];
    }

    $kasa = KasaTanimlari::where('hesap_no', $kaynakHesapNo)->first();
    if ($kasa) {
        return ['grup' => 'Kasalar', 'ad' => $kasa->tanım];
    }

    // Hiçbir gruba ait değilse
    return ['grup' => 'Bilinmiyor', 'ad' => 'Bilinmiyor'];
}
public function show($id)
{
    try {
        if (!$id || !is_numeric($id)) {
            throw new \Exception('Geçersiz masraf ID!');
        }

        // Masraf kaydını yükle
        $masraf = Masraflar::with(['masrafKalemi.masrafGrubu'])->findOrFail($id);

        // Tarihi doğru zaman dilimine dönüştür
        $localTarih = \Carbon\Carbon::parse($masraf->tarih)
            ->setTimezone('Europe/Istanbul') // Laravel timezone ayarına göre dönüştür
            ->format('Y-m-d\TH:i'); // datetime-local formatına uygun hale getir

        return response()->json([
            'id' => $masraf->id,
            'tarih' => $localTarih, // Dönüştürülmüş tarih
            'masraf_kalemi_id' => $masraf->masrafKalemi->id ?? null,
            'aciklama' => $masraf->aciklama,
            'hesap_no' => $masraf->kaynak_hesap_no,
            'tutar' => $masraf->tutar,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Masraf detayları yüklenirken hata oluştu.', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
    }
}



public function delete(Request $request)
{
    try {
        $validated = $request->validate([
            'id' => 'required|exists:masraflar,id', // Masraf ID'yi doğrula
        ]);

        // Masraf kaydını bul
        $masraf = Masraflar::findOrFail($validated['id']);

        // Hareketler tablosundaki ilgili kaydı sil (Masraf veya Tedarikçiye Ödeme işlem tipi)
        Hareketler::where(function ($query) use ($masraf) {
            $query->where([
                ['tarih', '=', $masraf->tarih],
                ['giden', '=', $masraf->tutar],
                ['kullanici', '=', $masraf->kullanici],
            ])
            ->whereIn('islem_tipi', ['Masraf', 'Tedarikçiye Ödeme']); // İşlem tipleri: Masraf ve Tedarikçiye Ödeme
        })->delete();

        // t_hareketleri tablosundaki ilgili kaydı sil
        DB::table('t_hareketleri')->where([
            ['tarih', '=', $masraf->tarih],
            ['tutar', '=', $masraf->tutar],
            ['kullanici', '=', $masraf->kullanici],
        ])->delete();

        // Masraf kaydını sil
        $masraf->delete();

        return response()->json(['success' => 'Masraf ve ilgili hareketler başarıyla silindi!'], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['error' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Masraf silinirken hata oluştu: ' . $e->getMessage());
        return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
    }
}



}
