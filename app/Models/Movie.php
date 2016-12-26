<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';
    protected $fillable = [
      'movie_id',
      'name',
      'user_id',
    ];

    public function getWatchedMovies($user)
    {
      return $this->where('user_id', $user)->get(array('movie_id', 'name'))->toArray();
    }

    public function addWatchedMovies($movie_id, $movie_name, $user_id)
    {
      $exists = $this->where(['movie_id' => $movie_id, 'user_id' => $user_id])->first();
      if (empty($exists)) {
        return $this->create([
          'movie_id' => $movie_id,
          'name' => $movie_name,
          'user_id' => $user_id,
        ]);
      }
      return (0);
    }
}
