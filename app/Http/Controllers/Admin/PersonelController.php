<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PersonelController extends Controller
{
    /**
     * Personel listesi sayfasını göster.
     */
    public function index(Request $request)
{
    $teamId = auth()->user()->team_id; // Kullanıcının takım ID'si
    $showDeparted = $request->input('show_departed', 0); // Varsayılan olarak işten ayrılmayanlar gösterilir

    $personeller = Personel::where('team_id', $teamId)
        ->when($showDeparted, function ($query) {
            $query->whereNotNull('isten_ayrilis_tarihi'); // İşten ayrılanları getir
        }, function ($query) {
            $query->whereNull('isten_ayrilis_tarihi'); // İşten ayrılmayanları getir
        })
        ->orderBy('isim', 'asc')
        ->get();

    return view('admin.personeller.index', compact('personeller', 'showDeparted'));
}

    /**
     * Yeni personel kaydet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isim' => 'required|string|max:255',
            'cep_telefonu' => 'required|string|max:15',
            'e_posta' => 'nullable|email|max:255',
            'ise_giris_tarihi' => 'nullable|date',
            'isten_ayrilis_tarihi' => 'nullable|date',
            'dogum_tarihi' => 'nullable|date',
            'tc_kimlik_no' => 'nullable|string|max:11',
            'aylik_net_maas' => 'nullable|numeric',
            'banka_hesap_no' => 'nullable|string|max:255',
            'adres' => 'nullable|string',
            'banka_bilgileri' => 'nullable|string',
            'not_alani' => 'nullable|string',
            'departman' => 'nullable|string|max:255',
        ]);
    
        $validated['team_id'] = auth()->user()->team_id; // Kullanıcının team_id'sini otomatik olarak ekle
    
        Personel::create($validated);
    
        return response()->json(['message' => 'Personel başarıyla eklendi.']);
    }
    
    public function show($id)
{
    $personel = Personel::find($id);

    if (!$personel) {
        abort(404, 'Personel bulunamadı.');
    }

    return view('admin.personeller.show', compact('personel'));
}
public function getPersonel($id)
{
    $personel = Personel::find($id);

    if (!$personel) {
        return response()->json(['error' => 'Personel bulunamadı.'], 404);
    }

    return response()->json($personel, 200);
}

    
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'isim' => 'required|string|max:255',
        'cep_telefonu' => 'required|string|max:15',
        'e_posta' => 'nullable|email|max:255',
        'ise_giris_tarihi' => 'nullable|date',
        'isten_ayrilis_tarihi' => 'nullable|date',
        'dogum_tarihi' => 'nullable|date',
        'tc_kimlik_no' => 'nullable|string|max:11',
        'aylik_net_maas' => 'nullable|numeric',
        'banka_hesap_no' => 'nullable|string|max:255',
        'adres' => 'nullable|string',
        'banka_bilgileri' => 'nullable|string',
        'not_alani' => 'nullable|string',
        'departman' => 'nullable|string|max:255',
    ]);

    $personel = Personel::find($id);

    if (!$personel) {
        return response()->json(['error' => 'Personel bulunamadı.'], 404);
    }

    $personel->update($validated);

    return response()->json(['message' => 'Personel başarıyla güncellendi.']);
}
public function terminatePerson(Request $request, $id)
{
    $personel = Personel::find($id);

    if (!$personel) {
        return response()->json(['error' => 'Personel bulunamadı.'], 404);
    }

    $validated = $request->validate([
        'isten_ayrilis_tarihi' => 'required|date',
    ]);

    $personel->isten_ayrilis_tarihi = $validated['isten_ayrilis_tarihi'];
    $personel->save();

    return response()->json(['message' => 'İşten çıkış tarihi başarıyla kaydedildi!'], 200);
}


    /**
     * Belirli bir personeli sil (soft delete).
     */
    public function destroy(Personel $personel)
    {
        $personel->delete();

        return redirect()->route('admin.personeller.index')->with('success', 'Personel başarıyla silindi.');
    }
}
