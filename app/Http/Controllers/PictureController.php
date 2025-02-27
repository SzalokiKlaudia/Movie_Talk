<?php

namespace App\Http\Controllers;

use App\Models\Pictures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PictureController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();
        $picture = Pictures::where('user_id', $user->id)->first();
    
        if ($picture) {
            return response()->json(['picture' => asset('storage/' . $picture->name)]);
        }else{
            return response()->json(['picture' => null]); // ha nincs képünk akkor nllt küldünk

        }

    }

    public function store(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $picture = Pictures::where('user_id', $user->id)->first();


        // ha van, akkor töröljük a régit
        if ($picture && Storage::disk('public')->exists($picture->name)) {
            // A régi kép törlése
            Storage::disk('public')->delete($picture->name);
            \Log::info('Old profile picture deleted: ' . $picture->name);
        }
        // mentjük a picture mappába
        $path = $request->file('profile_picture')->store('pictures', 'public');

        if ($picture) {// ha van képünk frissítjük ab-ban elérési utat
            $picture->name = $path;
            $picture->save();
        } else {// ha nicns kép akkor új rekord kerül ab-ba
            $picture = new Pictures();
            $picture->user_id = $user->id;
            $picture->name = $path;
            $picture->save();
        }
        return response()->json([
            'message' => 'Profile picture succesfully uploaded!',
            'picture' => asset('storage/' . $path)
        ]);
    }
}
