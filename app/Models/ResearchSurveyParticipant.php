<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchSurveyParticipant extends Model
{
    use HasFactory;

    protected $connection = 'mysql_second';

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('database.second_tables.participants');
    }
}
