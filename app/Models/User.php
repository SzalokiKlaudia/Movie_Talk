<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'name',
        'gender',
        'birth_year',
        'is_admin',
        'deleted_at',
        'password',
    ];

    public function isAdmin()  {
        return $this->is_admin === 1;
    }


   


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userMovies()
    {
        return $this->hasMany(UserMovie::class, 'user_id');
    }

    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class, 'user_id');
    }

    public function forumComments()
    {
        return $this->hasMany(ForumComment::class, 'user_id');
    } 

    public function picture()
    {
        return $this->hasOne(Pictures::class, 'user_id');
    }

    protected static function booted() // itt állítjuk be a user törlését, és egyben a hozzá kapcsolód táblák rekordjait is
    {
        static::deleting(function ($user) {
            $user->userMovies()->update(['deleted_at' => now()]); // beállítás a hozzá kapcsolódó táblához is, h annak a rekordjai is "törlődjenek"
            $user->pictures()->update(['deleted_at' => now()]);
        });
    }

    protected static function boot() // itt állítjuk be a törölt userek visszaállítását, és a hozzá kapcsolódó táblák rekordjaiban is visszaáll.
    {
        parent::boot();

        static::restored(function ($user) {
            Log::info("User restored: " . $user->id);
            $user->userMovies()->withTrashed()->restore(); //withtrashed biztisít h a törölt rekordok visszaállnak
            $user->pictures()->withTrashed()->restore(); // visszaállítjuk a törölt profilképet

        });
    }
}
