<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $table = 'downloaded';
    protected $fillable = [
      'movie_id',
      'path',
      'last_watched',
      'full_path',
    ];

    public function removeInactive($id)
    {
      if (!empty($id))
      {
        $movie = $this->find(intval($id));
        $movie->forceDelete();
      }
      else
        echo 'Error, movie id passed is empty.';
    }

    public function getInactive()
    {
      return $this->get()->toArray();
    }

    public function getMoviePath($movie_id)
    {
      return $this->where('movie_id', $movie_id)->get(array('movie_id', 'path'))->toArray()[0];
    }

    public function setLastWatched($id, $time)
    {
        return $this->where('movie_id', $id)->update(array('last_watched' => $time));
    }

    public function addDownloadedMovie($movie_id, $path, $fullpath)
    {
      $exists = $this->where('movie_id', $movie_id)->first();
      if (empty($exists)) {
        return $this->create([
          'movie_id' => $movie_id,
          'path' => $path,
          'last_watched' => 'created',
          'full_path' => $fullpath,
        ]);
      }
      return ($this->setLastWatched( $movie_id, 'updated'));
    }
}
