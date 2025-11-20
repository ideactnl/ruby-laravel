<?php

namespace App\Http\Requests\Api\Pbac;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdatePbacRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ** Required base ** //
            'reportedDate' => 'required|date',

            // ** Core flags and metadata ** //
            'isLive' => 'nullable|boolean',
            'isBloodLossAnswered' => 'nullable|boolean',
            'menstrualBloodLoss' => 'nullable|integer',
            'spotting' => 'nullable|boolean',
            'noBloodLoss' => 'nullable|boolean',
            'noPain' => 'nullable|boolean',
            'isBlFirstDayPeriod' => 'nullable|boolean',

            // Blood loss methods and details
            'isBlPads' => 'nullable|boolean',
            'isBlTampon' => 'nullable|boolean',
            'isBlMenstrualCup' => 'nullable|boolean',
            'isBlPeriodUnderwear' => 'nullable|boolean',
            'isBlOther' => 'nullable|boolean',
            'isBlOtherText' => 'nullable|string|max:255',
            'blPadSmall' => 'nullable|integer',
            'blPadMedium' => 'nullable|integer',
            'blPadLarge' => 'nullable|integer',
            'blTamponSmall' => 'nullable|integer',
            'blTamponMedium' => 'nullable|integer',
            'blTamponLarge' => 'nullable|integer',
            'isBlVeryLight' => 'nullable|boolean',
            'isBlLight' => 'nullable|boolean',
            'isBlModerate' => 'nullable|boolean',
            'isBlHeavy' => 'nullable|boolean',
            'isBlVeryHeavy' => 'nullable|boolean',
            'isBlBloodClots' => 'nullable|boolean',
            'isBlDoubleProtection' => 'nullable|boolean',
            'isBlLeakedClothes' => 'nullable|boolean',
            'isBlChangeProducts' => 'nullable|boolean',
            'isBlWakeUpNight' => 'nullable|boolean',

            // ** Pain section ** //
            'isPainAnswered' => 'nullable|boolean',
            'painSliderValue' => 'nullable|integer',
            'isPainHeadacheMigraine' => 'nullable|boolean',
            'isPainDuringPeeing' => 'nullable|boolean',
            'isPainDuringPooping' => 'nullable|boolean',
            'isPainDuringSex' => 'nullable|boolean',
            'isPainImage1Umbilical' => 'nullable|boolean',
            'isPainImage1LeftUmbilical' => 'nullable|boolean',
            'isPainImage1RightUmbilical' => 'nullable|boolean',
            'isPainImage1Bladder' => 'nullable|boolean',
            'isPainImage1LeftGroin' => 'nullable|boolean',
            'isPainImage1RightGroin' => 'nullable|boolean',
            'isPainImage1LeftLeg' => 'nullable|boolean',
            'isPainImage1RightLeg' => 'nullable|boolean',
            'isPainImage2UpperBack' => 'nullable|boolean',
            'isPainImage2Back' => 'nullable|boolean',
            'isPainImage2LeftButtock' => 'nullable|boolean',
            'isPainImage2RightButtock' => 'nullable|boolean',
            'isPainImage2LeftBackLeg' => 'nullable|boolean',
            'isPainImage2RightBackLeg' => 'nullable|boolean',

            // ** Impact section ** //
            'isImpactAnswered' => 'nullable|boolean',
            'impactSliderGradeYourDay' => 'nullable|integer',
            'impactSliderComplaints' => 'nullable|integer',
            'isImpactUsedMedication' => 'nullable|boolean',
            'isImpactMissedWork' => 'nullable|boolean',
            'isImpactMissedSchool' => 'nullable|boolean',
            'isImpactCouldNotSport' => 'nullable|boolean',
            'isImpactMissedSpecialActivities' => 'nullable|boolean',
            'isImpactMissedLeisureActivities' => 'nullable|boolean',
            'isImpactHadToSitMore' => 'nullable|boolean',
            'isImpactCouldNotMove' => 'nullable|boolean',
            'isImpactHadToStayLongerInBed' => 'nullable|boolean',
            'isImpactCouldNotDoUnpaidWork' => 'nullable|boolean',
            'isImpactOther' => 'nullable|boolean',
            'isImpactOtherText' => 'nullable|string|max:255',
            'isImpactMedParacetamol' => 'nullable|boolean',
            'isImpactMedDiclofenac' => 'nullable|boolean',
            'isImpactMedNaproxen' => 'nullable|boolean',
            'isImpactMedIronPills' => 'nullable|boolean',
            'isImpactMedTramodol' => 'nullable|boolean',
            'isImpactMedOxynorm' => 'nullable|boolean',
            'isImpactMedAnticonceptionPill' => 'nullable|boolean',
            'isImpactMedOtherHormones' => 'nullable|boolean',
            'isImpactMedTranexamineZuur' => 'nullable|boolean',
            'isImpactMedOther' => 'nullable|boolean',
            'isImpactMedOtherText' => 'nullable|string|max:255',
            'isImpactMedicineEffective' => 'nullable|integer|min:-2|max:2',

            // ** General health ** //
            'isGeneralHealthAnswered' => 'nullable|boolean',
            'generalHealthEnergyLevelSliderValue' => 'nullable|integer|min:-2|max:2',
            'isGeneralHealthDizzy' => 'nullable|boolean',
            'isGeneralHealthNauseous' => 'nullable|boolean',
            'isGeneralHealthHeadacheMigraine' => 'nullable|boolean',
            'isGeneralHealthBloated' => 'nullable|boolean',
            'isGeneralHealthPainfulSensitiveBreasts' => 'nullable|boolean',
            'isGeneralHealthAcne' => 'nullable|boolean',
            'isGeneralHealthMuscleJointPain' => 'nullable|boolean',

            // ** Mood ** //
            'isMoodAnswered' => 'nullable|boolean',
            'isMoodCalm' => 'nullable|boolean',
            'isMoodHappy' => 'nullable|boolean',
            'isMoodExcited' => 'nullable|boolean',
            'isMoodAnxiousStressed' => 'nullable|boolean',
            'isMoodAshamed' => 'nullable|boolean',
            'isMoodAngryIrritable' => 'nullable|boolean',
            'isMoodSad' => 'nullable|boolean',
            'isMoodSwings' => 'nullable|boolean',
            'isMoodWorthlessGuilty' => 'nullable|boolean',
            'isMoodOverwhelmed' => 'nullable|boolean',
            'isMoodHopeless' => 'nullable|boolean',
            'isMoodHopes' => 'nullable|boolean',
            'isMoodDepressedSadDown' => 'nullable|boolean',

            // ** Urine / Stool ** //
            'isUrineStoolAnswered' => 'nullable|boolean',
            'isUrineStoolBloodInUrine' => 'nullable|boolean',
            'isUrineStoolBloodInStool' => 'nullable|boolean',
            'isUrineStoolHard' => 'nullable|boolean',
            'isUrineStoolNormal' => 'nullable|boolean',
            'isUrineStoolSoft' => 'nullable|boolean',
            'isUrineStoolDiarrhea' => 'nullable|boolean',
            'isUrineStoolSomethingElse' => 'nullable|boolean',
            'isUrineStoolSomethingElseText' => 'nullable|string|max:255',
            'isUrineStoolNoStool' => 'nullable|boolean',

            // ** Sleep ** //
            'isSleepAnswered' => 'nullable|boolean',
            'sleepFellAsleepTime' => 'nullable|date_format:H:i',
            'sleepWokeUpTime' => 'nullable|date_format:H:i',
            'sleepHoursOfSleep' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $this->validateSleepConsistency($attribute, $value, $fail);
                },
            ],
            'isSleepWorkSchoolDay' => 'nullable|boolean',
            'isSleepFreeDay' => 'nullable|boolean',
            'isSleepTroubleAsleep' => 'nullable|boolean',
            'isSleepTiredRested' => 'nullable|boolean',
            'isSleepWakeUpDuringNight' => 'nullable|boolean',

            // ** Exercise ** //
            'isExerciseAnswered' => 'nullable|boolean',
            'isExerciseLessThirty' => 'nullable|boolean',
            'isExerciseThirtyToSixty' => 'nullable|boolean',
            'isExerciseGreaterSixty' => 'nullable|boolean',
            'isExerciseHighImpact' => 'nullable|boolean',
            'isExerciseLowImpact' => 'nullable|boolean',
            'isExercisePrecision' => 'nullable|boolean',

            // ** Diet ** //
            'isDietAnswered' => 'nullable|boolean',
            'isDietVegetables' => 'nullable|boolean',
            'isDietFruit' => 'nullable|boolean',
            'isDietPotatoRiceBread' => 'nullable|boolean',
            'isDietDairy' => 'nullable|boolean',
            'isDietNutsTofuTempe' => 'nullable|boolean',
            'isDietEggs' => 'nullable|boolean',
            'isDietFish' => 'nullable|boolean',
            'isDietMeat' => 'nullable|boolean',
            'isDietSnacks' => 'nullable|boolean',
            'isDietSoda' => 'nullable|boolean',
            'isDietWater' => 'nullable|boolean',
            'isDietCoffee' => 'nullable|boolean',
            'isDietAlcohol' => 'nullable|boolean',

            // ** Sex ** //
            'isSexAnswered' => 'nullable|boolean',
            'isSexToday' => 'nullable|boolean',
            'isSexAvoided' => 'nullable|boolean',
            'isSexBloodlossDuringAfter' => 'nullable|boolean',
            'isSexDiscomfortPelvicArea' => 'nullable|boolean',
            'isSexEmotionallyPhysicallySatisfied' => 'nullable|boolean',

            // ** Notes ** //
            'isAdditionalNotesAnswered' => 'nullable|boolean',
            'additionalNotes' => 'nullable|string',
        ];
    }

    /**
     * Body parameters for PBAC store/update.
     * Used by Scribe to render interactive docs.
     */
    public function bodyParameters(): array
    {
        return [
            // ** Required base ** //
            'reportedDate' => [
                'description' => 'The date the PBAC is reported for (YYYY-MM-DD).',
                'example' => '2025-09-15',
                'required' => true,
                'type' => 'date',
            ],

            // ** Core ** //
            'isLive' => ['description' => 'Live record flag', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBloodLossAnswered' => ['description' => 'Blood loss section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'menstrualBloodLoss' => ['description' => 'Menstrual blood loss value (if provided by app)', 'example' => 1, 'required' => false, 'type' => 'integer'],
            'spotting' => ['description' => 'Spotting today', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'noBloodLoss' => ['description' => 'No blood loss today', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'noPain' => ['description' => 'No pain today', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlFirstDayPeriod' => ['description' => 'First day of period', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Blood loss methods and details ** //
            'isBlPads' => ['description' => 'Used pads', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isBlTampon' => ['description' => 'Used tampons', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlMenstrualCup' => ['description' => 'Used menstrual cup', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlPeriodUnderwear' => ['description' => 'Used period underwear', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlOther' => ['description' => 'Used other method', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlOtherText' => ['description' => 'Other method description', 'example' => 'Cloth pad', 'required' => false, 'type' => 'string'],
            'blPadSmall' => ['description' => 'Count of small pads used', 'example' => 2, 'required' => false, 'type' => 'integer'],
            'blPadMedium' => ['description' => 'Count of medium pads used', 'example' => 1, 'required' => false, 'type' => 'integer'],
            'blPadLarge' => ['description' => 'Count of large pads used', 'example' => 0, 'required' => false, 'type' => 'integer'],
            'blTamponSmall' => ['description' => 'Count of small tampons used', 'example' => 0, 'required' => false, 'type' => 'integer'],
            'blTamponMedium' => ['description' => 'Count of medium tampons used', 'example' => 0, 'required' => false, 'type' => 'integer'],
            'blTamponLarge' => ['description' => 'Count of large tampons used', 'example' => 0, 'required' => false, 'type' => 'integer'],
            'isBlVeryLight' => ['description' => 'Very light day', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlLight' => ['description' => 'Light day', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isBlModerate' => ['description' => 'Moderate day', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlHeavy' => ['description' => 'Heavy day', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlVeryHeavy' => ['description' => 'Very heavy day', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlBloodClots' => ['description' => 'Blood clots present', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlDoubleProtection' => ['description' => 'Used double protection', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlLeakedClothes' => ['description' => 'Leaked clothes', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlChangeProducts' => ['description' => 'Had to change products often', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isBlWakeUpNight' => ['description' => 'Woke up at night due to bleeding', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Pain ** //
            'isPainAnswered' => ['description' => 'Pain section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'painSliderValue' => ['description' => 'Pain intensity slider (e.g., 0-10)', 'example' => 5, 'required' => false, 'type' => 'integer'],
            'isPainHeadacheMigraine' => ['description' => 'Headache/migraine', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainDuringPeeing' => ['description' => 'Pain during peeing', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainDuringPooping' => ['description' => 'Pain during pooping', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isPainDuringSex' => ['description' => 'Pain during sex', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1Umbilical' => ['description' => 'Pain at umbilical (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1LeftUmbilical' => ['description' => 'Pain left umbilical (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1RightUmbilical' => ['description' => 'Pain right umbilical (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1Bladder' => ['description' => 'Pain bladder (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1LeftGroin' => ['description' => 'Pain left groin (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1RightGroin' => ['description' => 'Pain right groin (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1LeftLeg' => ['description' => 'Pain left leg (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage1RightLeg' => ['description' => 'Pain right leg (front)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage2UpperBack' => ['description' => 'Pain upper back (back)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage2Back' => ['description' => 'Pain back (back)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage2LeftButtock' => ['description' => 'Pain left buttock (back)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage2RightButtock' => ['description' => 'Pain right buttock (back)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage2LeftBackLeg' => ['description' => 'Pain left back leg (back)', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isPainImage2RightBackLeg' => ['description' => 'Pain right back leg (back)', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Impact ** //
            'isImpactAnswered' => ['description' => 'Impact section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'impactSliderGradeYourDay' => ['description' => 'Grade your day', 'example' => 7, 'required' => false, 'type' => 'integer'],
            'impactSliderComplaints' => ['description' => 'Complaints slider', 'example' => 3, 'required' => false, 'type' => 'integer'],
            'isImpactUsedMedication' => ['description' => 'Used medication', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isImpactMissedWork' => ['description' => 'Missed work', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMissedSchool' => ['description' => 'Missed school', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactCouldNotSport' => ['description' => 'Could not do sport', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMissedSpecialActivities' => ['description' => 'Missed special activities', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMissedLeisureActivities' => ['description' => 'Missed leisure activities', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactHadToSitMore' => ['description' => 'Had to sit more', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactCouldNotMove' => ['description' => 'Could not move', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactHadToStayLongerInBed' => ['description' => 'Stayed longer in bed', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactCouldNotDoUnpaidWork' => ['description' => 'Could not do unpaid work', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactOther' => ['description' => 'Other impact', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactOtherText' => ['description' => 'Other impact text', 'example' => '', 'required' => false, 'type' => 'string'],
            'isImpactMedParacetamol' => ['description' => 'Medication: Paracetamol', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isImpactMedDiclofenac' => ['description' => 'Medication: Diclofenac', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedNaproxen' => ['description' => 'Medication: Naproxen', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedIronPills' => ['description' => 'Medication: Iron pills', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedTramodol' => ['description' => 'Medication: Tramadol', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedOxynorm' => ['description' => 'Medication: Oxynorm', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedAnticonceptionPill' => ['description' => 'Medication: Anticonception pill', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedOtherHormones' => ['description' => 'Medication: Other hormones', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedTranexamineZuur' => ['description' => 'Medication: Tranexamine zuur', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedOther' => ['description' => 'Medication: Other', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isImpactMedOtherText' => ['description' => 'Medication other text', 'example' => '', 'required' => false, 'type' => 'string'],
            'isImpactMedicineEffective' => ['description' => 'Medication effectiveness slider (-2 to 2, negative means ineffective)', 'example' => 2, 'required' => false, 'type' => 'integer'],

            // ** General health ** //
            'isGeneralHealthAnswered' => ['description' => 'General health section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'generalHealthEnergyLevelSliderValue' => ['description' => 'Energy level slider (-2=No energy, -1=Low, 0=Normal, 1=High, 2=Maximum)', 'example' => 0, 'required' => false, 'type' => 'integer'],
            'isGeneralHealthDizzy' => ['description' => 'Dizzy', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isGeneralHealthNauseous' => ['description' => 'Nauseous', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isGeneralHealthHeadacheMigraine' => ['description' => 'Headache/migraine', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isGeneralHealthBloated' => ['description' => 'Bloated', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isGeneralHealthPainfulSensitiveBreasts' => ['description' => 'Painful/sensitive breasts', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isGeneralHealthAcne' => ['description' => 'Acne', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isGeneralHealthMuscleJointPain' => ['description' => 'Muscle/joint pain', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Mood ** //
            'isMoodAnswered' => ['description' => 'Mood section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isMoodCalm' => ['description' => 'Calm', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodHappy' => ['description' => 'Happy', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isMoodExcited' => ['description' => 'Excited', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodAnxiousStressed' => ['description' => 'Anxious/stressed', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodAshamed' => ['description' => 'Ashamed', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodAngryIrritable' => ['description' => 'Angry/irritable', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodSad' => ['description' => 'Sad', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodSwings' => ['description' => 'Mood swings', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodWorthlessGuilty' => ['description' => 'Worthless/guilty', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodOverwhelmed' => ['description' => 'Overwhelmed', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodHopeless' => ['description' => 'Hopeless', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodHopes' => ['description' => 'Hopes', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isMoodDepressedSadDown' => ['description' => 'Depressed/sad/down', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Urine / Stool ** //
            'isUrineStoolAnswered' => ['description' => 'Urine/stool section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolBloodInUrine' => ['description' => 'Blood in urine', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolBloodInStool' => ['description' => 'Blood in stool', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolHard' => ['description' => 'Stool hard', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolNormal' => ['description' => 'Stool normal', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolSoft' => ['description' => 'Stool soft', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolDiarrhea' => ['description' => 'Stool diarrhea', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolSomethingElse' => ['description' => 'Stool something else', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isUrineStoolSomethingElseText' => ['description' => 'Stool something else text', 'example' => '', 'required' => false, 'type' => 'string'],
            'isUrineStoolNoStool' => ['description' => 'No stool', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Sleep ** //
            'isSleepAnswered' => ['description' => 'Sleep section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'sleepFellAsleepTime' => ['description' => 'Time fell asleep (HH:MM)', 'example' => '23:30', 'required' => false, 'type' => 'string'],
            'sleepWokeUpTime' => ['description' => 'Time woke up (HH:MM)', 'example' => '06:30', 'required' => false, 'type' => 'string'],
            'sleepHoursOfSleep' => ['description' => 'Total hours of sleep (HH:MM)', 'example' => '07:10', 'required' => false, 'type' => 'string'],
            'isSleepWorkSchoolDay' => ['description' => 'Work/school day', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isSleepFreeDay' => ['description' => 'Free day', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSleepTroubleAsleep' => ['description' => 'Trouble falling asleep', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSleepTiredRested' => ['description' => 'Woke up tired/not rested', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSleepWakeUpDuringNight' => ['description' => 'Woke up during night', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Exercise ** //
            'isExerciseAnswered' => ['description' => 'Exercise section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isExerciseLessThirty' => ['description' => 'Exercised less than 30 minutes', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isExerciseThirtyToSixty' => ['description' => 'Exercised 30-60 minutes', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isExerciseGreaterSixty' => ['description' => 'Exercised more than 60 minutes', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isExerciseHighImpact' => ['description' => 'High impact exercise', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isExerciseLowImpact' => ['description' => 'Low impact exercise', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isExercisePrecision' => ['description' => 'Precision exercise', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Diet ** //
            'isDietAnswered' => ['description' => 'Diet section answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isDietVegetables' => ['description' => 'Ate vegetables', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isDietFruit' => ['description' => 'Ate fruit', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isDietPotatoRiceBread' => ['description' => 'Ate potato/rice/bread', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietDairy' => ['description' => 'Had dairy products', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietNutsTofuTempe' => ['description' => 'Ate nuts/tofu/tempe', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietEggs' => ['description' => 'Ate eggs', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietFish' => ['description' => 'Ate fish', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietMeat' => ['description' => 'Ate meat', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietSnacks' => ['description' => 'Ate snacks', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietSoda' => ['description' => 'Drank soda', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietWater' => ['description' => 'Drank water', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'isDietCoffee' => ['description' => 'Drank coffee', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isDietAlcohol' => ['description' => 'Drank alcohol', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Sex ** //
            'isSexAnswered' => ['description' => 'Sex section answered', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSexToday' => ['description' => 'Had sex today', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSexAvoided' => ['description' => 'Avoided sex due to pain', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSexBloodlossDuringAfter' => ['description' => 'Bloodloss during/after sex', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSexDiscomfortPelvicArea' => ['description' => 'Discomfort/pelvic area', 'example' => 0, 'required' => false, 'type' => 'boolean'],
            'isSexEmotionallyPhysicallySatisfied' => ['description' => 'Felt emotionally/physically satisfied', 'example' => 0, 'required' => false, 'type' => 'boolean'],

            // ** Notes ** //
            'isAdditionalNotesAnswered' => ['description' => 'Additional notes answered', 'example' => 1, 'required' => false, 'type' => 'boolean'],
            'additionalNotes' => ['description' => 'Additional notes text', 'example' => 'Felt okay overall.', 'required' => false, 'type' => 'string'],
        ];
    }

    /**
     * Validate sleep time consistency between manual entry and calculated times.
     */
    protected function validateSleepConsistency($attribute, $value, $fail)
    {
        if (is_null($value)) {
            return;
        }

        $fellAsleep = $this->input('sleepFellAsleepTime');
        $wokeUp = $this->input('sleepWokeUpTime');

        if (empty($fellAsleep) || empty($wokeUp)) {
            return;
        }

        try {
            $start = Carbon::createFromFormat('H:i', $fellAsleep);
            $end = Carbon::createFromFormat('H:i', $wokeUp);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            $calculatedHours = round($start->diffInMinutes($end) / 60, 1);

            $manualTime = Carbon::createFromFormat('H:i', $value);
            $manualHours = round($manualTime->hour + ($manualTime->minute / 60), 1);

            $difference = abs($calculatedHours - $manualHours);
            if ($difference > 1.0) {
                $fail("The sleep hours entered ({$manualHours}h) don't match the calculated time from {$fellAsleep} to {$wokeUp} ({$calculatedHours}h). Please check your entries.");
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
