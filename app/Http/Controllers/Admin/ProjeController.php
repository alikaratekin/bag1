<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Masraflar;
use App\Models\Proje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjeController extends Controller
{
    /**
     * Projeler listesi.
     */
    public function index()
    {
        $teamId = auth()->user()->team_id;
        $projeler = Proje::where('team_id', $teamId)->get(); // Sadece kullanıcının takımına ait projeler
        return view('admin.projeler.index', compact('projeler'));
    }
    /**
     * Yeni proje oluşturma formu.
     */
    public function create()
    {
        return view('admin.projeler.create');
    }

    /**
     * Yeni proje kaydetme.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'ad' => 'required|string|max:255',
        'aciklama' => 'nullable|string',
    ]);

    Proje::create([
        'ad' => $validated['ad'],
        'aciklama' => $validated['aciklama'],
        'team_id' => auth()->user()->team_id, // Otomatik olarak oturum açan kullanıcının team_id'sini al
        'durum' => true, // Varsayılan olarak aktif
    ]);

    return response()->json(['message' => 'Proje başarıyla eklendi!']);
}

    /**
     * Belirli bir proje detaylarını göster.
     */
    public function show($id)
{
    $proje = Proje::findOrFail($id);

    // Proje ile ilgili masrafları alıyoruz
    $masraflar = Masraflar::where('proje', $id)->get();

    // Toplam masrafı hesaplıyoruz
    $yapilanMasraf = $masraflar->sum('tutar');

    // Statik gelir ve kar (ileride dinamik hale getirilecek)
    $eldeEdilenGelir = 0;
    $kar = $eldeEdilenGelir - $yapilanMasraf;

    return view('admin.projeler.show', compact('proje', 'masraflar', 'yapilanMasraf', 'eldeEdilenGelir', 'kar'));
}
public function getHareketler(Request $request, $projeId)
{
    try {
        $search = $request->get('search');

        // Masraflar tablosunu gruplarla birleştirerek hareketleri getiriyoruz
        $hareketler = Masraflar::query()
            ->where('proje', $projeId)
            ->leftJoin('masraf_kalemleri', 'masraflar.masraf_kalemi_id', '=', 'masraf_kalemleri.id') // Masraf Kalemi join
            ->leftJoin('masraf_gruplari', 'masraf_kalemleri.masraf_grubu_id', '=', 'masraf_gruplari.id') // Masraf Grubu join
            ->select(
                'masraflar.*',
                'masraf_kalemleri.ad as masraf_kalemi_ad',  // Masraf Kalemi adı
                'masraf_gruplari.ad as masraf_grubu_ad'    // Masraf Grubu adı
            );

        // Arama filtresi ekliyoruz
        if ($search) {
            $hareketler->where(function ($query) use ($search) {
                $query->where('masraflar.kullanici', 'LIKE', "%$search%")
                    ->orWhere('masraflar.aciklama', 'LIKE', "%$search%")
                    ->orWhere('masraf_kalemleri.ad', 'LIKE', "%$search%")   // Masraf Kalemi adına göre filtre
                    ->orWhere('masraf_gruplari.ad', 'LIKE', "%$search%");  // Masraf Grubu adına göre filtre
            });
        }

        $hareketler = $hareketler->get();

        // Grup adı ve masraf kalemi adını birleştirmek için verileri dönüştür
        $hareketler = $hareketler->map(function ($hareket) {
            return [
                'id' => $hareket->id,
                'tarih' => $hareket->tarih,
                'kullanici' => $hareket->kullanici,
                'masrafKalemi' => ($hareket->masraf_grubu_ad ? $hareket->masraf_grubu_ad . ' / ' : '') . ($hareket->masraf_kalemi_ad ?: 'Belirtilmedi'), // Grup Adı / Kalem Adı
                'aciklama' => $hareket->aciklama,
                'tutar' => $hareket->tutar,
            ];
        });

        return response()->json(['data' => $hareketler]);
    } catch (\Exception $e) {
        Log::error('Hareketler yüklenirken hata oluştu.', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
    }
}

    /**
     * Proje düzenleme formu.
     */
    public function edit($id)
    {
        $proje = Proje::findOrFail($id);
        return view('admin.projeler.edit', compact('proje'));
    }

    /**
     * Proje güncelleme.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'team_id' => 'required|integer',
        ]);

        $proje = Proje::findOrFail($id);
        $proje->update($validated);

        return redirect()->route('admin.projeler.index')->with('success', 'Proje başarıyla güncellendi!');
    }

    /**
     * Proje silme.
     */
    public function destroy($id)
    {
        $proje = Proje::findOrFail($id);
        $proje->delete();

        return redirect()->route('admin.projeler.index')->with('success', 'Proje başarıyla silindi!');
    }
}
