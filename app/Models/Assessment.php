<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'integer',
        'number' => 'integer',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function sectionOneAnswers(): HasMany
    {
        return $this->hasMany(Answer::class)->where('section', '=', 0);
    }

    public function sectionTwoAnswers(): HasMany
    {
        return $this->hasMany(Answer::class)->where('section', '=', 1);
    }

    public function sectionThreeAnswers(): HasMany
    {
        return $this->hasMany(Answer::class)->where('section', '=', 2);
    }

    public function latestAnswer()
    {
        return $this->hasOne(Answer::class)->latest();
    }

    public function incorrectAnswers(): HasMany
    {
        return $this->hasMany(Answer::class)->where('is_correct', '=', false);
    }

    public function incorrectAnswersSectionOne(): HasMany
    {
        return $this->hasMany(Answer::class)->where('is_correct', '=', false)->where('section', '=', 0);
    }

    public function incorrectAnswersSectionTwo(): HasMany
    {
        return $this->hasMany(Answer::class)->where('is_correct', '=', false)->where('section', '=', 1);
    }

    public function incorrectAnswersSectionThree(): HasMany
    {
        return $this->hasMany(Answer::class)->where('is_correct', '=', false)->where('section', '=', 2);
    }
}
