<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pbac', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->index();
            $table->date('reported_date')->index();
            $table->timestamp('created_date')->useCurrent();

            // Q3 questions
            $table->integer('q3a')->nullable();
            $table->integer('q3b')->nullable();
            $table->integer('q3c')->nullable();
            $table->integer('q3d')->nullable();

            // Q4 questions
            $table->integer('q4a')->nullable();
            $table->integer('q4b')->nullable();
            $table->integer('q4c')->nullable();
            $table->integer('q4d')->nullable();
            $table->integer('q4e')->nullable();
            $table->integer('q4f')->nullable();

            // Q5 questions
            $table->integer('q5a')->nullable();
            $table->integer('q5b')->nullable();
            $table->integer('q5c')->nullable();
            $table->integer('q5d')->nullable();
            $table->integer('q5e')->nullable();
            $table->integer('q5f')->nullable();
            $table->integer('q5g')->nullable();
            $table->integer('q5h')->nullable();
            $table->integer('q5i')->nullable();
            $table->integer('q5j')->nullable();
            $table->integer('q5k')->nullable();
            $table->integer('q5l')->nullable();
            $table->integer('q5m')->nullable();
            $table->integer('q5n')->nullable();
            $table->integer('q5o')->nullable();
            $table->integer('q5p')->nullable();

            // Q6 questions
            $table->integer('q6a')->nullable();
            $table->integer('q6b')->nullable();
            $table->integer('q6c')->nullable();
            $table->integer('q6d')->nullable();
            $table->integer('q6e')->nullable();
            $table->integer('q6f')->nullable();

            // Q7 questions
            $table->integer('q7a')->nullable();
            $table->integer('q7b')->nullable();

            // Q8 questions
            $table->integer('q8a')->nullable();
            $table->integer('q8b')->nullable();
            $table->integer('q8c')->nullable();
            $table->integer('q8d')->nullable();
            $table->integer('q8e')->nullable();
            $table->integer('q8f')->nullable();

            // Q9 questions
            $table->integer('q9a')->nullable();
            $table->integer('q9b')->nullable();
            $table->integer('q9c')->nullable();
            $table->integer('q9d')->nullable();
            $table->integer('q9e')->nullable();
            $table->integer('q9f')->nullable();
            $table->integer('q9g')->nullable();

            // Q10-Q16 questions
            $table->integer('q10')->nullable();
            $table->integer('q11')->nullable();
            $table->integer('q12')->nullable();

            // Q13 questions
            $table->integer('q13a')->nullable();
            $table->integer('q13b')->nullable();
            $table->integer('q13c')->nullable();
            $table->integer('q13d')->nullable();
            $table->integer('q13e')->nullable();

            // Q14 questions
            $table->integer('q14a')->nullable();
            $table->integer('q14b')->nullable();
            $table->integer('q14c')->nullable();
            $table->integer('q14d')->nullable();
            $table->integer('q14e')->nullable();

            $table->integer('q15')->nullable();

            // Q16 questions
            $table->integer('q16a')->nullable();
            $table->integer('q16b')->nullable();
            $table->integer('q16c')->nullable();

            // Q17 questions
            $table->integer('q17a')->nullable();
            $table->string('q17b', 100)->nullable();
            $table->string('q17c', 100)->nullable();
            $table->integer('q17d')->nullable();
            $table->integer('q17e')->nullable();
            $table->integer('q17f')->nullable();
            $table->integer('q17g')->nullable();

            // Q18 questions
            $table->integer('q18a')->nullable();
            $table->integer('q18b')->nullable();
            $table->integer('q18c')->nullable();
            $table->integer('q18d')->nullable();
            $table->integer('q18e')->nullable();

            // Q19 questions
            $table->integer('q19a')->nullable();
            $table->integer('q19b')->nullable();
            $table->integer('q19c')->nullable();
            $table->integer('q19d')->nullable();
            $table->integer('q19e')->nullable();
            $table->integer('q19f')->nullable();
            $table->integer('q19g')->nullable();
            $table->integer('q19h')->nullable();
            $table->integer('q19i')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbac');
    }
};
