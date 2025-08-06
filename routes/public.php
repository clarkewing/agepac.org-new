<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'public.welcome')->name('home');

Route::view('/privacy', 'public.terms', ['terms' => 'privacy']);
Route::view('/terms', 'public.terms', ['terms' => 'terms']);

Route::view('epl/selection', 'public.selection');
Route::view('epl/training', 'public.training');

Route::view('association', 'public.about');
Route::view('association/team', 'public.team');

Route::view('contact', 'public.contact');

Route::view('press', 'public.press');

Route::view('remembering', 'public.remembering');
