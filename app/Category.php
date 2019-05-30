<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    // le decimos que tabla usar
    protected $table = 'categories';


    /**
     * Una relacion uno muchos
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {

        return $this->hasMany('App\Post');

    }

    //
}
