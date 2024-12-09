<?php
// app/Traits/TransactionSync.php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait TransactionSync
{
    protected function syncUpdate($data)
    {
        try {
            DB::beginTransaction();

            $tarih = date('Y-m-d H:i:s', strtotime($data['tarih']));
            $oldTarih = date('Y-m-d H:i:s', strtotime($data['old_tarih']));

            // Normalize tutar
            $tutar = floatval(str_replace(',', '.', str_replace('.', '', $data['tutar'])));
            $oldTutar = floatval(str_replace(',', '.', str_replace('.', '', $data['old_tutar'])));

            // t_hareketleri güncelleme
            if (isset($data['islem_tipi']) && $data['islem_tipi'] === 'Tedarikçiye Ödeme') {
                DB::table('t_hareketleri')
                    ->where([
                        ['tarih', $oldTarih],
                        ['tutar', $oldTutar],
                    ])
                    ->update([
                        'tarih' => $tarih,
                        'aciklama' => $data['aciklama'],
                        'tutar' => $tutar,
                        'updated_at' => now()
                    ]);
            }

            // masraflar güncelleme
            DB::table('masraflar')
                ->where([
                    ['tarih', $oldTarih],
                    ['tutar', $oldTutar],
                ])
                ->update([
                    'tarih' => $tarih,
                    'aciklama' => $data['aciklama'],
                    'tutar' => $tutar,
                    'updated_at' => now()
                ]);

            // hareketler güncelleme
            DB::table('hareketler')
                ->where([
                    ['tarih', $oldTarih],
                    ['giden', $oldTutar],
                ])
                ->update([
                    'tarih' => $tarih,
                    'aciklama' => $data['aciklama'],
                    'giden' => $tutar,
                    'updated_at' => now()
                ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Senkronizasyon hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function syncDelete($data)
    {
        try {
            DB::beginTransaction();

            $tarih = date('Y-m-d H:i:s', strtotime($data['tarih']));
            $tutar = floatval(str_replace(',', '.', str_replace('.', '', $data['tutar'])));

            // t_hareketleri silme
            if (isset($data['islem_tipi']) && $data['islem_tipi'] === 'Tedarikçiye Ödeme') {
                DB::table('t_hareketleri')
                    ->where([
                        ['tarih', $tarih],
                        ['tutar', $tutar],
                    ])
                    ->delete();
            }

            // masraflar silme
            DB::table('masraflar')
                ->where([
                    ['tarih', $tarih],
                    ['tutar', $tutar],
                ])
                ->delete();

            // hareketler silme
            DB::table('hareketler')
                ->where([
                    ['tarih', $tarih],
                    ['giden', $tutar],
                ])
                ->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Silme senkronizasyon hatası: ' . $e->getMessage());
            throw $e;
        }
    }
}
