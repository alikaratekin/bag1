<?php

use App\Http\Controllers\Admin\VeliController;

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Team
    Route::delete('teams/destroy', 'TeamController@massDestroy')->name('teams.massDestroy');
    Route::resource('teams', 'TeamController');

    // Hesaplar

    Route::get('hesaplar', [App\Http\Controllers\Admin\HesaplarController::class, 'index'])->name('hesaplar.index');
    Route::get('hesaplar/new-account-number', [App\Http\Controllers\Admin\HesaplarController::class, 'getNewAccountNumber'])->name('hesaplar.getNewAccountNumber');
    Route::get('hesaplar/get-other-teams', [App\Http\Controllers\Admin\HesaplarController::class, 'getOtherTeams'])->name('hesaplar.getOtherTeams');
    Route::get('hesaplar/get-team-accounts', [App\Http\Controllers\Admin\HesaplarController::class, 'getTeamAccounts'])->name('hesaplar.getTeamAccounts');
    Route::get('hesaplar/get-hesaplar', [App\Http\Controllers\Admin\HesaplarController::class, 'getHesaplar'])->name('hesaplar.getHesaplar');
    Route::put('hesaplar/update', [App\Http\Controllers\Admin\HesaplarController::class, 'updateAccount'])->name('hesaplar.updateAccount');
    Route::post('hesaplar/store', [App\Http\Controllers\Admin\HesaplarController::class, 'store'])->name('hesaplar.store');
    Route::get('hesaplar/{hesap_no}', [App\Http\Controllers\Admin\HesaplarController::class, 'show'])->name('hesaplar.show');
    Route::get('hesaplar/{hesap_no}/hareketler', [App\Http\Controllers\Admin\HesaplarController::class, 'hareketler'])->name('hesaplar.hareketler');
    Route::get('hesaplar/export/{type}/{hesap_no}', [App\Http\Controllers\Admin\HesaplarController::class, 'export'])->name('hesaplar.export');
    Route::post('hesaplar/update', [App\Http\Controllers\Admin\HesaplarController::class, 'update'])->name('hesaplar.update');
    Route::get('hesaplar/kartlar/{hesap_no}', [App\Http\Controllers\Admin\HesaplarController::class, 'getKartlar'])->name('hesaplar.kartlar');
    Route::post('hesaplar/delete', [App\Http\Controllers\Admin\HesaplarController::class, 'delete'])->name('hesaplar.delete');
    Route::post('hesaplar/para-girisi', [App\Http\Controllers\Admin\HesaplarController::class, 'paraGirisi'])->name('hesaplar.paraGirisi');
    Route::post('hesaplar/paraCikisi', [App\Http\Controllers\Admin\HesaplarController::class, 'paraCikisi'])->name('hesaplar.paraCikisi');
    Route::post('hesaplar/transfer-al', [App\Http\Controllers\Admin\HesaplarController::class, 'transferAl'])->name('hesaplar.transferAl');
    Route::post('hesaplar/transfer-other-team', [App\Http\Controllers\Admin\HesaplarController::class, 'transferOtherTeam'])->name('hesaplar.transferOtherTeam');
    Route::post('hesaplar/virman-kaydet', [App\Http\Controllers\Admin\HesaplarController::class, 'virmanKaydet'])->name('hesaplar.virmanKaydet');

    //masraflar
    Route::resource('masraflar', App\Http\Controllers\Admin\MasraflarController::class);
    Route::post('masraflar/delete', [App\Http\Controllers\Admin\MasraflarController::class, 'delete'])->name('masraflar.delete');
    Route::get('masraflar/{id}', [App\Http\Controllers\Admin\MasraflarController::class, 'show'])->name('masraflar.show');
    Route::post('masraflar/update', [App\Http\Controllers\Admin\MasraflarController::class, 'update'])->name('masraflar.update');
    Route::get('getMasraflar', [App\Http\Controllers\Admin\MasraflarController::class, 'getMasraflar'])->name('masraflar.getMasraflar');
    Route::resource('masraf-tanimlari', \App\Http\Controllers\Admin\MasrafTanimiController::class)->only(['index', 'store']);
    Route::post('masraf-tanimlari/store-kalem', [\App\Http\Controllers\Admin\MasrafTanimiController::class, 'storeKalem'])->name('masraf-tanimlari.storeKalem');
    Route::post('masraf-tanimlari/updateGrup/{id}', [App\Http\Controllers\Admin\MasrafTanimiController::class, 'updateGrup'])->name('masraf-tanimlari.updateGrup');
    Route::delete('masraf-tanimlari/deleteGrup/{id}', [App\Http\Controllers\Admin\MasrafTanimiController::class, 'deleteGrup'])->name('masraf-tanimlari.deleteGrup');
    Route::post('masraf-tanimlari/updateKalem/{id}', [App\Http\Controllers\Admin\MasrafTanimiController::class, 'updateKalem'])->name('masraf-tanimlari.updateKalem');
    Route::delete('masraf-tanimlari/deleteKalem/{id}', [App\Http\Controllers\Admin\MasrafTanimiController::class, 'deleteKalem'])->name('masraf-tanimlari.deleteKalem');

    //projeler
    Route::resource('projeler', App\Http\Controllers\Admin\ProjeController::class);
    Route::get('projeler/{proje}/hareketler', [App\Http\Controllers\Admin\ProjeController::class, 'getHareketler'])->name('projeler.getHareketler');

    //personeller
    Route::resource('personeller', App\Http\Controllers\Admin\PersonelController::class);
    Route::get('personeller/{id}', [App\Http\Controllers\Admin\PersonelController::class, 'show'])->name('personeller.show');
    Route::get('personeller/get/{id}', [App\Http\Controllers\Admin\PersonelController::class, 'getPersonel']);
    Route::put('personeller/{id}', [App\Http\Controllers\Admin\PersonelController::class, 'update'])->name('personeller.update');
    Route::post('personeller/{id}/terminate', [App\Http\Controllers\Admin\PersonelController::class, 'terminatePerson']);

//tedarikçi

    Route::resource('tedarikciler', App\Http\Controllers\Admin\TedarikciController::class);
    Route::put('tedarikciler/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'update'])->name('tedarikciler.update');
    Route::post('tedarikciler/{id}/alim', [App\Http\Controllers\Admin\TedarikciController::class, 'alımYap'])->name('tedarikci.alim');
    Route::get('tedarikciler/{id}/finansal-bilgiler', [App\Http\Controllers\Admin\TedarikciController::class, 'finansalBilgiler']);
    Route::put('tedarikciler/hareketler/{id}/update', [App\Http\Controllers\Admin\TedarikciController::class, 'updateHareket']);
    Route::delete('tedarikciler/hareketler/{id}/delete', [App\Http\Controllers\Admin\TedarikciController::class, 'deleteHareket']);
    Route::get('hareketler/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'getHareket']);
    Route::get('hareketler/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'getHareket']); // GET hareket
    Route::put('hareketler/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'updateHareket']); // PUT hareket
    Route::delete('hareketler/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'destroy']); // DELETE hareket


    Route::post('tedarikciler/{id}/odeme', [App\Http\Controllers\Admin\TedarikciController::class, 'odemeYap']);
    Route::get('tedarikciler/hesaplar/{teamId}', [App\Http\Controllers\Admin\TedarikciController::class, 'getHesaplar']);
    Route::get('tedarikciler/{id}/hareketler', [App\Http\Controllers\Admin\TedarikciController::class, 'hareketler'])->name('tedarikciler.hareketler');

    Route::get('tedarikciler/alim/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'getAlim'])->name('tedarikciler.alim.get');
    Route::get('tedarikciler/odeme/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'getOdeme'])->name('tedarikciler.odeme.get');

    // Tedarikçiden Alım ve Ödeme Güncelleme
    Route::put('tedarikciler/alim/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'updateAlim'])->name('tedarikciler.alim.update');
    Route::put('tedarikciler/odeme/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'updateOdeme'])->name('tedarikciler.odeme.update');

    // Tedarikçiden Alım ve Ödeme Silme
    Route::delete('tedarikciler/alim/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'deleteAlim'])->name('tedarikciler.alim.delete');
    Route::delete('tedarikciler/odeme/{id}', [App\Http\Controllers\Admin\TedarikciController::class, 'deleteOdeme'])->name('tedarikciler.odeme.delete');
    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    Route::get('team-members', 'TeamMembersController@index')->name('team-members.index');
    Route::post('team-members', 'TeamMembersController@invite')->name('team-members.invite');
    //öğrenci kayıt
    Route::get('veliler/get-veliler', [VeliController::class, 'getVeliler'])->name('veliler.getVeliler');
    Route::resource('veliler', VeliController::class);
    Route::post('veliler/store-ek-veli', [VeliController::class, 'storeEkVeli'])->name('veliler.storeEkVeli');
    Route::post('veliler/store-ogrenci', [VeliController::class, 'storeOgrenci'])->name('veliler.storeOgrenci');
    Route::put('veliler/{id}', [VeliController::class, 'update'])->name('veliler.update');
    Route::put('veliler/update-ek-veli/{id}', [VeliController::class, 'updateEkVeli'])->name('veliler.updateEkVeli');
    Route::put('veliler/update-ogrenci/{id}', [VeliController::class, 'updateOgrenci'])->name('veliler.updateOgrenci');
    Route::get('veliler/get-ogrenci/{id}', [VeliController::class, 'getOgrenci'])->name('veliler.getOgrenci');



});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
