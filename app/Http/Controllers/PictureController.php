<?php

namespace App\Http\Controllers;

use App\Models\Pictures;
use Illuminate\Http\Request;

class PictureController extends Controller
{
    public function index() 
    { 
        $files = Pictures::latest()->get(); 
        return $files; 
    }

    public function store(Request $request)
    {
 // a kérés validálásához a validate függvényt használjuk. Beállítjuk az elfogadott képformátumokat
 // és a feltölthető kép maximális méretét. 
     $request->validate([
         'title' => 'required',
         'name' =>  'image|mimes:jpeg,png,jpg,gif|max:2048',
     ]);
     $file = $request->file('name');   // fájl nevének lekérése  
     $extension = $file->getClientOriginalName(); //kiterjesztés
     $imageName = time() . '.' . $extension; // a kép neve az időbéjegnek köszönhetően egyedi lesz. 
     $file->move(public_path('pictures'), $imageName); //átmozgatjuk a public mappa kepek könyvtárába 
     $kepek = new Pictures(); // Létrehozzuk a kép objektumot. 
     $kepek->name = 'pictures/' . $imageName; // megadjuk az új fájl elérési utját
     $kepek->title = $request->title; // megadjuk a kép címét
     $kepek->save(); //elmentjük

     return redirect()->route('file.upload')->with('success', 'Product created successfully.');
    }
}
