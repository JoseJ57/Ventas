<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TallaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrdenController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Clientes - Rutas simples (mantén como estaban)
    Route::get('/clientes', function () {
        return Inertia::render('Clientes/Index');
    })->name('clientes.index');

    Route::get('/clientes/create', function () {
        return Inertia::render('Clientes/Create');
    })->name('clientes.create');

    Route::get('/clientes/{id}/edit', function ($id) {
        return Inertia::render('Clientes/Edit', ['id' => $id]);
    })->name('clientes.edit');

    // Artículos - Rutas simples
    Route::get('/articulos', function () {
        return Inertia::render('Articulos/Index');
    })->name('articulos.index');

    // CRUD completo con controladores
    Route::resource('tallas', TallaController::class);
    Route::resource('marcas', MarcaController::class);
    Route::resource('materiales', MaterialController::class);
    Route::resource('ordenes', OrdenController::class);
});

require __DIR__.'/auth.php';
