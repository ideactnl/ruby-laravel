<?php

namespace Database\Seeders;

use App\Models\VideoContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = [
            // Education Page Videos
            [
                'title' => 'Wat is de menstruele cyclus?',
                'location' => 'education',
                'order' => 1,
                'condition' => null, // NA - not shown on daily view
                'video_url' => 'https://youtube.com/shorts/YHxWLpAfY_M?feature=share',
            ],
            [
                'title' => 'Wat is normaal?',
                'location' => 'education',
                'order' => 2,
                'condition' => null, // NA
                'video_url' => 'https://youtube.com/shorts/P1Cs00lgyHE?feature=share',
            ],
            [
                'title' => 'Menstruatiepijn',
                'location' => 'education',
                'order' => 3,
                'condition' => 'painscore > 4',
                'video_url' => 'https://youtube.com/shorts/45GBEKxA9IQ?feature=share',
            ],
            [
                'title' => 'Bloedverlies',
                'location' => 'education',
                'order' => 4,
                'condition' => 'menstruation_bloodloss = true',
                'video_url' => 'https://youtube.com/shorts/cBzvrutpyhM?feature=share',
            ],
            [
                'title' => 'Alarmsignalen',
                'location' => 'education',
                'order' => 5,
                'condition' => null, // NA
                'video_url' => 'https://youtube.com/shorts/jbQFJYLtDz4?feature=share',
            ],
            [
                'title' => 'Bijkomende klachten',
                'location' => 'education',
                'order' => 6,
                'condition' => 'general_health.any = true',
                'video_url' => 'https://youtube.com/shorts/xLOqV6fAFAw?feature=share',
            ],
            [
                'title' => 'Menstruele aandoeningen',
                'location' => 'education',
                'order' => 7,
                'condition' => null, // NA
                'video_url' => 'https://youtube.com/shorts/aprAcqpSMq8?feature=share',
            ],
            [
                'title' => 'Energie',
                'location' => 'education',
                'order' => 8,
                'condition' => 'energy_level < 0',
                'video_url' => 'https://youtube.com/shorts/jxaM2Rdcd7w?feature=share',
            ],
            [
                'title' => 'Humeur & menstruele cyclus',
                'location' => 'education',
                'order' => 9,
                'condition' => 'mood.symptoms = true',
                'video_url' => 'https://youtube.com/shorts/WLyIzwAkBSk?feature=share',
            ],
            [
                'title' => 'Vruchtbaarheid',
                'location' => 'education',
                'order' => 10,
                'condition' => null, // NA
                'video_url' => 'https://youtube.com/shorts/xf2E1tNm_Bs?feature=share',
            ],
            [
                'title' => 'Wat voor behandelingen zijn er?',
                'location' => 'education',
                'order' => 11,
                'condition' => null, // NA
                'video_url' => 'https://youtube.com/shorts/5tGYtC_OqQQ?feature=share',
            ],
            [
                'title' => 'Poep & menstruele cyclus',
                'location' => 'education',
                'order' => 12,
                'condition' => 'stool_urine.issues = true',
                'video_url' => 'https://youtube.com/shorts/t_RRnQe5tWE?feature=share',
            ],
            
            // Self-Management Page Videos
            [
                'title' => 'Pijn verminderen door ontspanning',
                'location' => 'self-management',
                'order' => 1,
                'condition' => null, // NA
                'video_url' => 'https://youtube.com/shorts/oxTaiuHAq-U?feature=share',
            ],
            [
                'title' => 'Pijnstillers',
                'location' => 'self-management',
                'order' => 2,
                'condition' => 'painscore > 4',
                'video_url' => 'https://youtube.com/shorts/UzZubQCyF8E?feature=share',
            ],
            [
                'title' => 'Slapen',
                'location' => 'self-management',
                'order' => 3,
                'condition' => 'sleep.issues = true',
                'video_url' => 'https://youtube.com/shorts/5RhWn-1gVLY?feature=share',
            ],
            [
                'title' => 'Sporten',
                'location' => 'self-management',
                'order' => 4,
                'condition' => 'exercise_minutes BETWEEN 0 AND 30',
                'video_url' => 'https://youtube.com/shorts/_DHX3dOW5XI?feature=share',
            ],
        ];

        foreach ($videos as $videoData) {
            $videoId = VideoContent::extractYoutubeId($videoData['video_url']);
            
            VideoContent::create([
                'title' => $videoData['title'],
                'location' => $videoData['location'],
                'order' => $videoData['order'],
                'condition' => $videoData['condition'],
                'video_url' => $videoData['video_url'],
                'video_type' => 'youtube',
                'video_id' => $videoId,
                'is_active' => true,
            ]);
        }

        $this->command->info('Video content seeded successfully!');
    }
}
