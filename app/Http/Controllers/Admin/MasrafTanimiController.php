<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasrafGrubu;
use App\Models\MasrafKalemi;
use Illuminate\Http\Request;

class MasrafTanimiController extends Controller
{
    // Ana masraf gruplarını listeleme
    // Ana masraf gruplarını listeleme (Takım ID'ye göre filtreleme)
public function index()
{
    $teamId = auth()->user()->team_id; // Oturum açan kullanıcının takım ID'si

    // Kullanıcının takımına ait grupları ve alt kalemlerini al
    $masrafGruplari = MasrafGrubu::with(['masrafKalemleri' => function ($query) use ($teamId) {
        $query->where('team_id', $teamId); // Alt kalemleri takım ID'ye göre filtrele
    }])->where('team_id', $teamId) // Grupları takım ID'ye göre filtrele
      ->get();

    return view('admin.masraf-tanimlari.index', compact('masrafGruplari'));
}


    // Ana masraf grubu oluşturma
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
        ]);

        MasrafGrubu::create([
            'ad' => $validated['ad'],
            'team_id' => auth()->user()->team_id, // Oturum açan kullanıcının takım ID'si
        ]);

        return redirect()->back()->with('success', 'Ana masraf grubu başarıyla oluşturuldu!');
    }

    // Alt kalem oluşturma
    public function storeKalem(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'masraf_grubu_id' => 'required|exists:masraf_gruplari,id',
        ]);

        MasrafKalemi::create([
            'ad' => $validated['ad'],
            'masraf_grubu_id' => $validated['masraf_grubu_id'],
            'team_id' => auth()->user()->team_id,
        ]);

        return redirect()->back()->with('success', 'Alt masraf kalemi başarıyla oluşturuldu!');
    }
    public function updateGrup(Request $request, $id)
{
    $grup = MasrafGrubu::findOrFail($id);

    // Validasyon
    $request->validate([
        'ad' => 'required|string|max:255',
    ]);

    // Güncelleme
    $grup->update([
        'ad' => $request->ad,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Grup başarıyla güncellendi.',
    ]);
}
public function deleteGrup($id)
{
    $grup = MasrafGrubu::findOrFail($id);

    // Silme işlemi ve bağlı alt kalemlerin silinmesi
    $grup->masrafKalemleri()->delete();
    $grup->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Grup ve bağlı tüm alt kalemler başarıyla silindi.',
    ]);
}
public function updateKalem(Request $request, $id)
{
    $kalem = MasrafKalemi::findOrFail($id);

    // Validasyon
    $request->validate([
        'ad' => 'required|string|max:255',
    ]);

    // Güncelleme
    $kalem->update([
        'ad' => $request->ad,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Alt kalem başarıyla güncellendi.',
    ]);
}
public function deleteKalem($id)
{
    $kalem = MasrafKalemi::findOrFail($id);

    // Silme işlemi
    $kalem->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Alt kalem başarıyla silindi.',
    ]);
}

}
