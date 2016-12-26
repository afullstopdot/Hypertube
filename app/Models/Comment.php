<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = [
      'movie_id',
      'user_id',
      'comment',
      'name',
      'profile'
    ];

    public function getMovieComments($movie_id)
    {
      return $this->where('movie_id', $movie_id)->get(array('comment_id', 'comment', 'name', 'profile', 'created_at'))->toArray();
    }

    public function addComment($movie_id, $user_id, $comment, $name, $profile)
    {
      return $this->create([
        'movie_id' => $movie_id,
        'user_id' => $user_id,
        'comment' => $comment,
        'name' => $name,
        'profile' => $profile
      ]);
    }
}
