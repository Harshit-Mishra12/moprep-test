<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicMaterials extends Model
{
    use HasFactory;

    public function topicData()
    {
        return $this->hasMany(\App\Models\NotesMaterials::class, 'topic_id', 'id');
    }

}
