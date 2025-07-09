<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'role'
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




    /**
     * Generate an avatar image with the user's initials.
     *
     * @param int $size
     * @param string $textColor Hex color code without the # (e.g., "7F9CF5")
     * @param string $backgroundColor Hex color code without the # (e.g., "EBF4FF")
     * @return string Data URI for the generated PNG image.
     */
    /**
     * Get the initials from the user's name.
     *
     * @return string
     */
    public function getInitials(): string
    {
        $name = trim($this->name);
        $words = preg_split('/\s+/', $name);

        if (count($words) >= 2) {
            // Use the first character of the first and last word.
            return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        }

        // If only one word is available, return the first two characters.
        return strtoupper(substr($name, 0, 2));
    }



    /**
     * Build the avatar API URL using the user's initials.
     *
     * @param int $size
     * @param string $textColor
     * @param string $backgroundColor
     * @return string
     */
    public function avatarUrl($size = 128, $textColor = '7F9CF5', $backgroundColor = 'EBF4FF'): string
    {
        $initials = $this->getInitials();

        // Build and return the full URL to the API endpoint.
        return url('/api/avatar') . '?' . http_build_query([
            'name'       => $initials,
            'size'       => $size,
            'color'      => $textColor,
            'background' => $backgroundColor,
        ]);
    }

    public function getRoleAttribute($value)
    {
        return ucfirst($value); // Example: Capitalize the role
    }

    
}
