<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Veli;
use App\Models\EkVeli;
use App\Models\Ogrenci;
use Illuminate\Http\Request;
use App\Models\Velihareketleri;
use Illuminate\Support\Facades\Log;
class VeliController extends Controller
{
    public function index()
    {
        $veliler = Veli::all();
        return view('admin.veliler.index', compact('veliler'));
    }
    public function getVeliler(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = Veli::query();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('isim', 'LIKE', "%{$search}%")
                      ->orWhere('tc', 'LIKE', "%{$search}%")
                      ->orWhere('meslek', 'LIKE', "%{$search}%")
                      ->orWhere('tel', 'LIKE', "%{$search}%")
                      ->orWhere('eposta', 'LIKE', "%{$search}%")
                      ->orWhere('is_tel', 'LIKE', "%{$search}%")
                      ->orWhere('ev_tel', 'LIKE', "%{$search}%")
                      ->orWhere('yakinlik', 'LIKE', "%{$search}%")
                      ->orWhere('adres', 'LIKE', "%{$search}%");
                });
            }

            $veliler = $query->orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $veliler]);
        } catch (\Exception $e) {
            Log::error('Veli getirme hatası: ' . $e->getMessage());
            return response()->json(['error' => 'Veriler alınırken bir hata oluştu'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'isim' => 'required',
            'tc' => 'required|unique:veliler,tc|max:11',
            'meslek' => 'nullable',
            'tel' => 'nullable|max:15',
            'eposta' => 'nullable|email|unique:veliler,eposta',
            'is_tel' => 'nullable|max:15',
            'ev_tel' => 'nullable|max:15',
            'yakinlik' => 'required|in:anne,baba,dede,akraba,komsu',
            'adres' => 'nullable|string',
        ]);

        $veli = Veli::create($request->all());
        return response()->json(['success' => true, 'message' => 'Veli başarıyla eklendi.', 'data' => $veli]);
    }

    public function show($id)
    {
        $veli = Veli::findOrFail($id);
        $ekVeli = EkVeli::where('veli_id', $id)->first();
        $ogrenciler = Ogrenci::where('veli_id', $id)->get();
        $veliHareketleri = Velihareketleri::where('veli_id', $id)->get();

        // Tüm zamanlar borç ve ödeme toplamları
        $totalBorcu = Velihareketleri::where('veli_id', $id)->sum('borcu');
        $totalOdedi = Velihareketleri::where('veli_id', $id)->sum('odedi');
        $currentBalance = $totalBorcu - $totalOdedi;

        return view('admin.veliler.show', compact('veli', 'ekVeli', 'ogrenciler', 'veliHareketleri', 'totalBorcu', 'totalOdedi', 'currentBalance'));
    }


    public function storeEkVeli(Request $request)
    {
        try {
            $request->validate([
                'veli_id' => 'required|exists:veliler,id',
                'isim' => 'required',
                'tc' => 'required|unique:ek_veliler,tc|max:11',
                'meslek' => 'nullable',
                'tel' => 'nullable|max:15',
                'eposta' => 'nullable|email|unique:ek_veliler,eposta',
                'is_tel' => 'nullable|max:15',
                'ev_tel' => 'nullable|max:15',
            ]);

            $veli = Veli::findOrFail($request->veli_id);
            $anne_baba = $veli->anne_baba === 'anne' ? 'baba' : 'anne';

            $data = $request->all();
            $data['anne_baba'] = $anne_baba;

            EkVeli::create($data);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.veliler.show', $veli->id) // Yönlendirme URL'si
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Bir hata oluştu: ' . $e->getMessage()]
            ], 422);
        }
    }

    public function storeOgrenci(Request $request)
    {
        try {
            $request->validate([
                'veli_id' => 'required|exists:veliler,id',
                'isim' => 'required',
                'tc' => 'required|unique:ogrenciler,tc|max:11',
                'cinsiyet' => 'required|in:erkek,kiz',
                'dogum_tarihi' => 'required|date',
                'mudureyet' => 'required|in:anakolu,ilkokul,ortaokul,anadolu_lisesi,fen_lisesi',
                'sinifi' => 'required',
                'egitim_donemi' => 'required',
                'kontenjan' => 'required|in:kurumsal,burslu,gazi,sehit,personel,yok',
                'egitimucreti' => 'required|numeric',
                'yemekucreti' => 'required|numeric',
                'etutucreti' => 'required|numeric',
                'kirtasiyeucreti' => 'required|numeric'
            ]);

            $ogrenci = Ogrenci::create($request->all());

            // Yeni veli hareketi ekle
            $toplamTutar = $request->egitimucreti + $request->yemekucreti +
                          $request->etutucreti + $request->kirtasiyeucreti;

            Velihareketleri::create([
                'veli_id' => $request->veli_id,
                'ogrenci_id' => $ogrenci->id,
                'islem_tipi' => 'Yeni Kayıt',
                'tarih' => now(),
                'borcu' => $toplamTutar,
                'odedi' => 0,
                'hesap_no' => null,
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.veliler.show', $request->veli_id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Bir hata oluştu: ' . $e->getMessage()]
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'isim' => 'required',
                'tc' => 'required|max:11|unique:veliler,tc,'.$id,
                'meslek' => 'nullable',
                'tel' => 'nullable|max:15',
                'eposta' => 'nullable|email|unique:veliler,eposta,'.$id,
                'is_tel' => 'nullable|max:15',
                'ev_tel' => 'nullable|max:15',
            ]);

            $veli = Veli::findOrFail($id);
            $veli->update($request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Bir hata oluştu: ' . $e->getMessage()]
            ], 422);
        }
    }

    public function updateEkVeli(Request $request, $id)
    {
        try {
            $request->validate([
                'isim' => 'required',
                'tc' => 'required|max:11|unique:ek_veliler,tc,'.$id,
                'meslek' => 'nullable',
                'tel' => 'nullable|max:15',
                'eposta' => 'nullable|email|unique:ek_veliler,eposta,'.$id,
                'is_tel' => 'nullable|max:15',
                'ev_tel' => 'nullable|max:15',
            ]);

            $ekVeli = EkVeli::findOrFail($id);
            $ekVeli->update($request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Bir hata oluştu: ' . $e->getMessage()]
            ], 422);
        }
    }

    public function updateOgrenci(Request $request, $id)
    {
        try {
            $request->validate([
                'isim' => 'required',
                'tc' => 'required|max:11|unique:ogrenciler,tc,'.$id,
                'cinsiyet' => 'required|in:erkek,kiz',
                'dogum_tarihi' => 'required|date',
                'mudureyet' => 'required|in:anakolu,ilkokul,ortaokul,anadolu_lisesi,fen_lisesi',
                'sinifi' => 'required',
                'egitim_donemi' => 'required',
                'kontenjan' => 'required|in:kurumsal,burslu,gazi,sehit,personel,yok',
                'egitimucreti' => 'required|numeric',
                'yemekucreti' => 'required|numeric',
                'etutucreti' => 'required|numeric',
                'kirtasiyeucreti' => 'required|numeric'
            ]);

            $ogrenci = Ogrenci::findOrFail($id);
            $ogrenci->update($request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Bir hata oluştu: ' . $e->getMessage()]
            ], 422);
        }
    }

    public function getOgrenci($id)
    {
        try {
            $ogrenci = Ogrenci::findOrFail($id);
            return response()->json($ogrenci);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Öğrenci bulunamadı'], 404);
        }
    }
}
