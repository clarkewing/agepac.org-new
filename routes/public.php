<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'public.welcome')->name('home');

Route::view('epl/selection', 'public.selection')->name('epl.selection');
Route::view('epl/training', 'public.training')->name('epl.training');

Route::view('association', 'public.about')->name('association.about');
Route::view('association/team', 'public.team')->name('association.team');

Route::view('contact', 'public.contact')->name('contact');
Route::view('press', 'public.press')->name('press');

Route::view('/privacy', 'public.terms', ['terms' => 'privacy'])->name('privacy');
Route::view('/terms', 'public.terms', ['terms' => 'terms'])->name('terms');

Route::view('remembering', 'public.remembering')->name('remembering');
