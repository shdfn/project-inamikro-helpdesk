<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProfileRequest extends Model
{
    protected $fillable = ['user_id', 'new_name', 'new_username', 'new_email', 'new_password', 'status'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}