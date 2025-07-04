<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pbac extends Model
{
    use HasFactory;

    protected $table = 'pbac';

    public $timestamps = false;

    protected $fillable = [
        'reported_date',
        'user_id',
        'created_date',
        'q3a',
        'q3b',
        'q3c',
        'q3d',
        'q4a',
        'q4b',
        'q4c',
        'q4d',
        'q4e',
        'q4f',
        'q5a',
        'q5b',
        'q5c',
        'q5d',
        'q5e',
        'q5f',
        'q5g',
        'q5h',
        'q5i',
        'q5j',
        'q5k',
        'q5l',
        'q5m',
        'q5n',
        'q5o',
        'q5p',
        'q6a',
        'q6b',
        'q6c',
        'q6d',
        'q6e',
        'q6f',
        'q7a',
        'q7b',
        'q8a',
        'q8b',
        'q8c',
        'q8d',
        'q8e',
        'q8f',
        'q9a',
        'q9b',
        'q9c',
        'q9d',
        'q9e',
        'q9f',
        'q9g',
        'q10',
        'q11',
        'q12',
        'q13a',
        'q13b',
        'q13c',
        'q13d',
        'q13e',
        'q14a',
        'q14b',
        'q14c',
        'q14d',
        'q14e',
        'q15',
        'q16a',
        'q16b',
        'q16c',
        'q17a',
        'q17b',
        'q17c',
        'q17d',
        'q17e',
        'q17f',
        'q17g',
        'q18a',
        'q18b',
        'q18c',
        'q18d',
        'q18e',
        'q19a',
        'q19b',
        'q19c',
        'q19d',
        'q19e',
        'q19f',
        'q19g',
        'q19h',
        'q19i',
    ];

    /**
     * Map legacy input fields to model attributes.
     *
     * @param array $input
     * @return array
     */
    public static function mapLegacyInputFields(array $input)
    {
        $fields = [
            'reported_date' => 'ReportedDate',
            'created_date' => 'CreatedDate',
            'q3b' => 'Spot',
            'q3c' => 'Mens',
            'q3d' => 'FirstDay',
            'q4a' => 'PadL',
            'q4b' => 'PadM',
            'q4c' => 'PadS',
            'q4d' => 'TamL',
            'q4e' => 'TamM',
            'q4f' => 'TamS',
            'q5a' => 'Pain',
            'q5b' => 'PainL1',
            'q5c' => 'PainL2',
            'q5d' => 'PainL3',
            'q5e' => 'PainL4',
            'q5f' => 'PainL5',
            'q5g' => 'PainL6',
            'q5h' => 'PainL7',
            'q5i' => 'PainL8',
            'q5j' => 'PainL9',
            'q5k' => 'PainL10',
            'q5l' => 'PainL11',
            'q5m' => 'PainL12',
            'q5n' => 'PainL13',
            'q5o' => 'PainL14',
            'q5p' => 'PainL15',
            'q6a' => 'PainTrig1',
            'q6b' => 'PainTrig2',
            'q6c' => 'PainTrig3',
            'q6d' => 'PainTrig4',
            'q6e' => 'PainTrig5',
            'q6f' => 'PainTrig6',
            'q7a' => 'PainBig',
            'q7b' => 'PainType',
            'q8a' => 'PainPCM',
            'q8b' => 'PainDiclo',
            'q8c' => 'PainNapr',
            'q8d' => 'PainTram',
            'q8e' => 'PainOxy',
            'q8f' => 'PainMedOth',
            'q9a' => 'work',
            'q9b' => 'school',
            'q9c' => 'sport',
            'q9d' => 'moving',
            'q9e' => 'sleeping',
            'q9f' => 'sitting',
            'q9g' => 'other',
            'q10' => 'QoL',
            'q11' => 'Mood',
            'q12' => 'Fatique',
            'q13a' => 'UrineFreq',
            'q13b' => 'UrineNoc',
            'q13c' => 'UrinePain',
            'q13d' => 'UrineBlood',
            'q13e' => 'UrinePainSco',
            'q14a' => 'StoolFreq',
            'q14b' => 'StoolPain',
            'q14c' => 'StoolBlood',
            'q14d' => 'StoolCons',
            'q14e' => 'StoolNight',
            'q15' => 'SexPain',
            'q16a' => 'SexBlood',
            'q16b' => 'SexDis',
            'q16c' => 'SexSat',
            'q17a' => 'SleepH',
            'q17b' => 'SleepDo',
            'q17c' => 'SleepUp',
            'q17d' => 'SleepTr',
            'q17e' => 'SleepRes',
            'q17f' => 'SleepWake',
            'q17g' => 'SleepSatis',
            'q18a' => 'Exer30',
            'q18b' => 'Exer60',
            'q18c' => 'Exer90',
            'q18d' => 'ExerRelx',
            'q18e' => 'ExerLimi',
            'q19a' => 'Diet1',
            'q19b' => 'Diet2',
            'q19c' => 'Diet3',
            'q19d' => 'Diet4',
            'q19e' => 'Diet5',
            'q19f' => 'Diet6',
            'q19g' => 'Diet7',
            'q19h' => 'Diet8',
            'q19i' => 'Diet9',
        ];

        $result = [];
        foreach ($fields as $code => $legacy) {
            $result[$code] = isset($input[$legacy]) && $input[$legacy] !== '' ? $input[$legacy] : null;
        }

        if (empty($result['created_date'])) {
            $result['created_date'] = date('Y-m-d');
        }

        $result['q3a'] = (
            isset($input['BL']) && $input['BL'] !== '' &&
            (
                (isset($input['Spot']) && $input['Spot'] !== '') ||
                (isset($input['Mens']) && $input['Mens'] !== '')
            )
        ) ? $input['BL'] : null;

        return $result;
    }

    /**
     * Get the user that owns the PBAC record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the PBAC score per day (sum of q4a to q4f).
     *
     * @return int
     */
    public function getPbacScorePerDayAttribute()
    {
        return (int) ($this->q4a + $this->q4b + $this->q4c + $this->q4d + $this->q4e + $this->q4f);
    }

    /**
     * Get spotting yes/no (1 if q3b == 1, else 0).
     *
     * @return int
     */
    public function getSpottingYesNoAttribute()
    {
        return ((int) $this->q3b === 1) ? 1 : 0;
    }

    /**
     * Get pain score per day (q5a).
     *
     * @return int
     */
    public function getPainScorePerDayAttribute()
    {
        return (int) $this->q5a;
    }

    /**
     * Get influence factor (1 if any q9a-q9g == 1, else 0).
     *
     * @return int
     */
    public function getInfluenceFactorAttribute()
    {
        return ((int) $this->q9a === 1 || (int) $this->q9b === 1 || (int) $this->q9c === 1 || (int) $this->q9d === 1 || (int) $this->q9e === 1 || (int) $this->q9f === 1 || (int) $this->q9g === 1) ? 1 : 0;
    }

    /**
     * Get pain medication (1 if any q8a-q8f == 1, else 0).
     *
     * @return int
     */
    public function getPainMedicationAttribute()
    {
        return ((int) $this->q8a === 1 || (int) $this->q8b === 1 || (int) $this->q8c === 1 || (int) $this->q8d === 1 || (int) $this->q8e === 1 || (int) $this->q8f === 1) ? 1 : 0;
    }

    /**
     * Get quality of life (q10).
     *
     * @return int
     */
    public function getQualityOfLifeAttribute()
    {
        return (int) $this->q10;
    }

    /**
     * Get energy level (q12).
     *
     * @return int
     */
    public function getEnergyLevelAttribute()
    {
        return (int) $this->q12;
    }

    /**
     * Get complaints with defecation (1 if q14b or q14c == 1, else 0).
     *
     * @return int
     */
    public function getComplaintsWithDefecationAttribute()
    {
        return ((int) $this->q14b === 1 || (int) $this->q14c === 1) ? 1 : 0;
    }

    /**
     * Get complaints with urinating (1 if q13c == 1, else 0).
     *
     * @return int
     */
    public function getComplaintsWithUrinatingAttribute()
    {
        return ((int) $this->q13c === 1) ? 1 : 0;
    }

    /**
     * Get quality of sleep (q17g).
     *
     * @return int
     */
    public function getQualityOfSleepAttribute()
    {
        return (int) $this->q17g;
    }

    /**
     * Get exercise (1 if any q18a-q18d == 1, else 0).
     *
     * @return int
     */
    public function getExerciseAttribute()
    {
        return ((int) $this->q18a === 1 || (int) $this->q18b === 1 || (int) $this->q18c === 1 || (int) $this->q18d === 1) ? 1 : 0;
    }
}
