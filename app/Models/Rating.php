<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'business_user_id',
        'rated_by_user_id',
        'rating',
        'comment'
    ];

    public function rater()
{
    return $this->belongsTo(User::class, 'rated_by_user_id')
                ->select('id', 'full_name', 'profile'); // select only needed fields
}


}

