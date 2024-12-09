<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tedarikci;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TedarikciController extends Controller
{
    public function index()
    {
        $maksimumBorc = 1000000; // Bar için maksimum borç referansı (1 milyon TL)

        $tedarikciler = Tedarikci::all()->map(function ($tedarikci) use ($maksimumBorc) {
            $toplamAlinan = DB::table('t_hareketleri')
                ->where('tedarikci_id', $tedarikci->id)
                ->where('islem_tipi', 'Tedarikçiden Alım')
                ->sum('tutar');

            $toplamOdenen = DB::table('t_hareketleri')
                ->where('tedarikci_id', $tedarikci->id)
                ->where('islem_tipi', 'Tedarikçiye Ödeme')
                ->sum('tutar');

            $tedarikci->borc = $toplamAlinan - $toplamOdenen; // Güncel borç

            // Borcun yüzdesi 1 milyon TL'ye göre hesaplanıyor
            $tedarikci->borc_yuzde = min(100, max(0, ($tedarikci->borc / $maksimumBorc) * 100));

            return $tedarikci;
        });

        return view('admin.tedarikciler.index', compact('tedarikciler'));
    }

    public function create()
    {
        return view('admin.tedarikciler.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'numara' => 'nullable|string|max:255',
            'vergino' => 'nullable|string|max:255',
            'adres' => 'nullable|string',
            'not' => 'nullable|string',
            'team_id' => 'required|integer',
        ]);

        Tedarikci::create($validated);

        return redirect()->route('admin.tedarikciler.index')->with('success', 'Tedarikçi başarıyla eklendi!');
    }

    public function show($id)
    {
        $tedarikci = Tedarikci::findOrFail($id); // ID ile tedarikçiyi bulun

        return view('admin.tedarikciler.show', compact('tedarikci'));
    }



    public function edit($id)
    {
        $tedarikci = Tedarikci::findOrFail($id); // ID'ye göre tedarikçiyi bul
        return view('admin.tedarikciler.edit', compact('tedarikci'));
    }

    public function update(Request $request, $id)
{
    $tedarikci = Tedarikci::findOrFail($id);

    $validated = $request->validate([
        'ad' => 'required|string|max:255',
        'numara' => 'nullable|string|max:255',
        'vergino' => 'nullable|string|max:255',
        'adres' => 'nullable|string',
        'not' => 'nullable|string',
    ]);

    $tedarikci->update($validated);

    return response()->json(['success' => true, 'message' => 'Tedarikçi bilgileri güncellendi.']);
}
public function updateHareket(Request $request, $id)
{
    $validated = $request->validate([
        'tarih' => 'required|string', // Gelen tarih string olarak alınacak
        'aciklama' => 'required|string',
        'tutar' => 'required|numeric',
        'islem_tipi' => 'required|string',
        'old_tarih' => 'required|string', // Eski tarih de string
        'old_tutar' => 'required|numeric',
    ]);

    try {
        DB::beginTransaction();

        // Hareketi al
        $hareket = DB::table('t_hareketleri')->where('id', $id)->first();
        if (!$hareket) {
            throw new \Exception('Hareket bulunamadı.');
        }

        $tedarikci = Tedarikci::findOrFail($hareket->tedarikci_id);

        // Açıklama
        $orijinalAciklama = $validated['aciklama'];
        $birlesikAciklama = "{$tedarikci->ad} - {$validated['aciklama']}";

        // Gelen tarih formatını `YYYY-MM-DD HH:mm:ss` formatına dönüştür
        $tarihSaat = date('Y-m-d H:i:s', strtotime($validated['tarih'])); // Doğru tarih formatı

        // t_hareketleri tablosunu güncelle
        DB::table('t_hareketleri')->where('id', $id)->update([
            'tarih' => $tarihSaat, // Dönüştürülmüş tarih formatı
            'aciklama' => $orijinalAciklama,
            'tutar' => $validated['tutar'],
        ]);

        if ($validated['islem_tipi'] === 'Tedarikçiye Ödeme') {
            // masraflar tablosunda güncelleme
            $masrafExists = DB::table('masraflar')
                ->where('tutar', $validated['old_tutar'])
                ->where('tarih', date('Y-m-d H:i:s', strtotime($validated['old_tarih']))) // Eski tarih formatı
                ->exists();

            if ($masrafExists) {
                DB::table('masraflar')
                    ->where('tutar', $validated['old_tutar'])
                    ->where('tarih', date('Y-m-d H:i:s', strtotime($validated['old_tarih'])))
                    ->update([
                        'tarih' => $tarihSaat, // Güncel tarih formatı
                        'aciklama' => $birlesikAciklama,
                        'tutar' => $validated['tutar'],
                    ]);
            }

            // hareketler tablosunda güncelleme
            $hareketExists = DB::table('hareketler')
                ->where('giden', $validated['old_tutar'])
                ->where('tarih', date('Y-m-d H:i:s', strtotime($validated['old_tarih']))) // Eski tarih formatı
                ->exists();

            if ($hareketExists) {
                DB::table('hareketler')
                    ->where('giden', $validated['old_tutar'])
                    ->where('tarih', date('Y-m-d H:i:s', strtotime($validated['old_tarih'])))
                    ->update([
                        'tarih' => $tarihSaat, // Güncel tarih formatı
                        'aciklama' => $birlesikAciklama,
                        'giden' => $validated['tutar'],
                    ]);
            }
        }

        DB::commit();

        return response()->json(['message' => 'Hareket başarıyla güncellendi.'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
    }
}




public function destroy(Request $request, $id)
{
    try {
        DB::beginTransaction();

        // Get the original record first to ensure we have the correct data
        $hareket = DB::table('t_hareketleri')
            ->where('id', $id)
            ->first();

        if (!$hareket) {
            throw new \Exception('Hareket bulunamadı.');
        }

        // Format the date and amount consistently
        $tarihSaat = $hareket->tarih;
        $tutar = $hareket->tutar;

        Log::info('Silme işlemi başlatıldı.', [
            'id' => $id,
            'tarihSaat' => $tarihSaat,
            'tutar' => $tutar,
            'islem_tipi' => $hareket->islem_tipi
        ]);

        // Delete from t_hareketleri
        $tHareketSilindi = DB::table('t_hareketleri')
            ->where('id', $id)
            ->delete();

        // Only delete from other tables if it's a payment transaction
        if ($hareket->islem_tipi === 'Tedarikçiye Ödeme') {
            // Delete from masraflar
            $masrafSilindi = DB::table('masraflar')
                ->where('tarih', $tarihSaat)
                ->where('tutar', $tutar)
                ->where('team_id', $hareket->team_id)
                ->delete();

            // Delete from hareketler
            $hareketSilindi = DB::table('hareketler')
                ->where('tarih', $tarihSaat)
                ->where('giden', $tutar)
                ->where('team_id', $hareket->team_id)
                ->delete();

            Log::info('İlgili kayıtlar silindi', [
                'masraflar_silindi' => $masrafSilindi,
                'hareketler_silindi' => $hareketSilindi
            ]);
        }

        DB::commit();

        return response()->json([
            'message' => 'Hareket başarıyla silindi.',
            'silinen_kayitlar' => [
                't_hareket' => $tHareketSilindi,
                'masraf' => $masrafSilindi ?? 0,
                'hareket' => $hareketSilindi ?? 0
            ]
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Silme işlemi sırasında hata:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}

public function getHareket($id)
{
    try {
        // Hareketi t_hareketleri tablosundan bul
        $hareket = DB::table('t_hareketleri')->where('id', $id)->first();

        if (!$hareket) {
            return response()->json(['error' => 'Hareket bulunamadı.'], 404);
        }

        return response()->json([
            'id' => $hareket->id,
            'tarih' => date('Y-m-d H:i:s', strtotime($hareket->tarih)), // Tam tarih ve saat formatı
            'aciklama' => $hareket->aciklama,
            'tutar' => $hareket->tutar,
            'islem_tipi' => $hareket->islem_tipi,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Hareket bilgisi alınırken hata oluştu: ' . $e->getMessage()], 500);
    }
}



public function hareketler($id, Request $request)
{
    $query = DB::table('t_hareketleri')->where('tedarikci_id', $id);

    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('aciklama', 'like', "%$search%")
              ->orWhere('islem_tipi', 'like', "%$search%")
              ->orWhere('hesap_no', 'like', "%$search%");
        });
    }

    $hareketler = $query->get();

    $hareketler = $hareketler->map(function ($hareket) {
        $hesapBilgisi = $this->getHesap($hareket->hesap_no); // Hesap bilgilerini getir
        return [
            'id' => $hareket->id, // ID'yi ekliyoruz
            'tarih' => $hareket->tarih,
            'islem_tipi' => $hareket->islem_tipi,
            'kullanici' => $hareket->kullanici,
            'aciklama' => $hareket->aciklama,
            'hesap_no' => $hareket->hesap_no,
            'hesap_bilgisi' => $hesapBilgisi, // Grup ve ad bilgisi
            'tutar' => $hareket->tutar,
        ];
    });

    return response()->json(['data' => $hareketler]);
}

public function alımYap(Request $request, $id)
{
    $request->validate([
        'tarih' => 'required|date', // Kullanıcının seçtiği tarih
        'aciklama' => 'required|string',
        'tutar' => 'required|string', // Gelen veri normalize edilmeden string olarak kontrol edilir
    ]);

    // 'tutar' değerini normalize ederek float'a çevir
    $tutar = floatval(str_replace(',', '.', str_replace('.', '', $request->tutar)));

    // Kullanıcının seçtiği tarihe işlem saatini ekle
    $tarihSaat = $request->tarih . ' ' . now()->format('H:i:s'); // Tarih + Saat

    // Yeni hareket oluştur
    DB::table('t_hareketleri')->insert([
        'tedarikci_id' => $id,
        'team_id' => $request->team_id,
        'kullanici' => $request->kullanici,
        'tarih' => $tarihSaat, // Tarih saat ile birlikte kaydediliyor
        'aciklama' => $request->aciklama,
        'tutar' => $tutar, // Normalize edilmiş değer kaydediliyor
        'islem_tipi' => $request->islem_tipi,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Hareket başarıyla kaydedildi.'], 200);
}

public function finansalBilgiler($id)
{
    try {
        // Toplam Alınan
        $toplamAlinan = DB::table('t_hareketleri')
            ->where('tedarikci_id', $id)
            ->where('islem_tipi', 'Tedarikçiden Alım')
            ->sum('tutar');

        // Toplam Ödenen
        $toplamOdenen = DB::table('t_hareketleri')
            ->where('tedarikci_id', $id)
            ->where('islem_tipi', 'Tedarikçiye Ödeme')
            ->sum('tutar');

        // Güncel Bakiye
        $guncelBakiye = $toplamAlinan - $toplamOdenen;

        return response()->json([
            'success' => true,
            'toplamAlinan' => $toplamAlinan,
            'toplamOdenen' => $toplamOdenen,
            'guncelBakiye' => $guncelBakiye,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Finansal bilgiler alınırken bir hata oluştu.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function odemeYap(Request $request, $id)
{
    $request->validate([
        'tarih' => 'required|date', // Kullanıcının seçtiği tarih
        'tutar' => 'required|string', // Gelen veri string olarak doğrulanıyor
        'hesap_no' => 'required|string',
        'aciklama' => 'required|string',
    ]);

    // 'tutar' değerini normalize ederek float'a çevir
    $tutar = floatval(str_replace(',', '.', str_replace('.', '', $request->tutar)));

    $tedarikci = Tedarikci::findOrFail($id);
    $aciklamaTam = "{$tedarikci->ad} - {$request->aciklama}";

    // Kullanıcının seçtiği tarihe işlem saatini ekle
    $tarihSaat = $request->tarih . ' ' . now()->format('H:i:s'); // Tarih + Saat

    // t_hareketleri tablosuna ekle
    DB::table('t_hareketleri')->insert([
        'tedarikci_id' => $id,
        'team_id' => $request->team_id,
        'kullanici' => $request->kullanici,
        'tarih' => $tarihSaat, // Tarih saat ile birlikte kaydediliyor
        'tutar' => $tutar, // Normalize edilmiş tutar kaydediliyor
        'hesap_no' => $request->hesap_no,
        'islem_tipi' => 'Tedarikçiye Ödeme',
        'aciklama' => $request->aciklama,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // hareketler tablosuna ekle
    DB::table('hareketler')->insert([
        'tarih' => $tarihSaat, // Tarih saat ile birlikte kaydediliyor
        'islem_tipi' => 'Tedarikçiye Ödeme',
        'giden' => $tutar, // Normalize edilmiş tutar kaydediliyor
        'kaynak_hesap_no' => $request->hesap_no,
        'aciklama' => $aciklamaTam,
        'team_id' => $request->team_id,
        'kullanici' => $request->kullanici,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // masraflar tablosuna ekle
    DB::table('masraflar')->insert([
        'tarih' => $tarihSaat, // Tarih saat ile birlikte kaydediliyor
        'masraf_kalemi_id' => 1, // Tedarikçiye Ödeme kategorisi
        'aciklama' => $aciklamaTam,
        'kaynak_hesap_no' => $request->hesap_no,
        'tutar' => $tutar, // Normalize edilmiş tutar kaydediliyor
        'team_id' => $request->team_id,
        'kullanici' => $request->kullanici,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Tedarikçiye ödeme başarıyla kaydedildi.'], 200);
}
public function getTeamAccounts($teamId)
    {
        try {
            // Hesap gruplarını ayrı ayrı getir
            $bankaHesaplari = \App\Models\BankaHesaplari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım as ad', DB::raw("'Banka Hesapları' as grup"))
                ->get();

            $krediKartlari = \App\Models\KrediKartlari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım as ad', DB::raw("'Kredi Kartları' as grup"))
                ->get();

            $posHesaplari = \App\Models\POSHesaplari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım as ad', DB::raw("'POS Hesapları' as grup"))
                ->get();

            $kasaTanimlari = \App\Models\KasaTanimlari::where('team_id', $teamId)
                ->select('hesap_no', 'tanım as ad', DB::raw("'Kasalar' as grup"))
                ->get();

            // Tüm hesapları birleştir
            $hesaplar = $bankaHesaplari
                ->merge($krediKartlari)
                ->merge($posHesaplari)
                ->merge($kasaTanimlari)
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $hesaplar,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hesaplar yüklenirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function getHesapGruplari($teamId)
{
    return [
        'Banka Hesapları' => \App\Models\BankaHesaplari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(['hesap_no', 'tanım']),
        'Kredi Kartları' => \App\Models\KrediKartlari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(['hesap_no', 'tanım']),
        'POS Hesapları' => \App\Models\POSHesaplari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(['hesap_no', 'tanım']),
        'Kasalar' => \App\Models\KasaTanimlari::where('team_id', $teamId)->where('aktiflik_durumu', 1)->get(['hesap_no', 'tanım']),
    ];
}
private function getHesap($hesapNo)
{
    if (!$hesapNo) {
        return null; // Eğer hesap yoksa null döndür
    }

    $banka = DB::table('banka_hesaplari')->where('hesap_no', $hesapNo)->first();
    if ($banka) {
        return ['grup' => 'Banka Hesapları', 'ad' => $banka->tanım];
    }

    $krediKart = DB::table('kredi_kartlari')->where('hesap_no', $hesapNo)->first();
    if ($krediKart) {
        return ['grup' => 'Kredi Kartları', 'ad' => $krediKart->tanım];
    }

    $pos = DB::table('pos_hesaplari')->where('hesap_no', $hesapNo)->first();
    if ($pos) {
        return ['grup' => 'POS Hesapları', 'ad' => $pos->tanım];
    }

    $kasa = DB::table('kasa_tanimlari')->where('hesap_no', $hesapNo)->first();
    if ($kasa) {
        return ['grup' => 'Kasalar', 'ad' => $kasa->tanım];
    }

    return null; // Hiçbir gruba ait değilse
}

public function getHesaplar($teamId)
{
    try {
        $hesaplar = $this->getHesapGruplari($teamId);
        return response()->json(['hesaplar' => $hesaplar], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Hesaplar alınırken bir hata oluştu: ' . $e->getMessage()], 500);
    }
}

}
