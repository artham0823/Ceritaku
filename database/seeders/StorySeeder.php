<?php

namespace Database\Seeders;

/**
 * =====================================================
 * SEEDER: StorySeeder
 * =====================================================
 * Mengimport semua data cerita dari ceritaku/assets/data/stories.json
 * ke database Laravel.
 * 
 * Data yang diimport:
 * - 1 cerita utama (Tsuki to Yami) dengan 3 chapter
 * - 13 cerita profil dengan chapter masing-masing
 * 
 * Cerita utama akan ditandai sebagai "featured" (tampil di hero)
 * =====================================================
 */

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\Chapter;
use App\Models\User;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID author (artham)
        $author = User::where('username', 'artham')->first();
        $authorId = $author ? $author->id : 1;

        // Baca file JSON dari database/data
        $jsonPath = database_path('data/stories.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->warn('File stories.json tidak ditemukan! Menggunakan data hardcoded...');
            $this->seedHardcodedData($authorId);
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        
        if (!$data) {
            $this->command->warn('File stories.json kosong/rusak! Menggunakan data hardcoded...');
            $this->seedHardcodedData($authorId);
            return;
        }

        // Import cerita utama (main_story)
        if (isset($data['main_story'])) {
            $mainStory = $data['main_story'];
            $story = Story::create([
                'title' => $mainStory['title'],
                'description' => $mainStory['description'],
                'cover_image' => 'img/tsukitoyami.png',
                'genre' => 'Drama, Slice of Life',
                'views_count' => 0,
                'is_featured' => true,
                'created_by' => $authorId,
            ]);

            foreach ($mainStory['chapters'] as $index => $chapter) {
                Chapter::create([
                    'story_id' => $story->id,
                    'title' => $chapter['title'],
                    'content' => $chapter['content'],
                    'chapter_number' => $index + 1,
                ]);
            }
            $this->command->info("✅ Cerita utama '{$mainStory['title']}' berhasil diimport!");
        }

        // Import cerita profil
        if (isset($data['profiles'])) {
            foreach ($data['profiles'] as $profile) {
                $story = Story::create([
                    'title' => $profile['title'],
                    'description' => "Kisah epik berjudul {$profile['title']} dalam genre {$profile['genre']}.",
                    'cover_image' => 'img/p2.jpg',
                    'genre' => $profile['genre'],
                    'views_count' => $profile['views'] ?? 0,
                    'is_featured' => false,
                    'created_by' => $authorId,
                ]);

                if (isset($profile['chapters_data'])) {
                    foreach ($profile['chapters_data'] as $index => $chapter) {
                        Chapter::create([
                            'story_id' => $story->id,
                            'title' => $chapter['title'],
                            'content' => $chapter['content'],
                            'chapter_number' => $index + 1,
                        ]);
                    }
                }
                $this->command->info("✅ Cerita '{$profile['title']}' ({$profile['genre']}) berhasil diimport!");
            }
        }

        $this->command->info("\n🎉 Semua data cerita berhasil diimport ke database!");
    }

    /**
     * Data hardcoded jika file JSON tidak ditemukan.
     * Ini backup agar seeder tetap bisa jalan.
     */
    private function seedHardcodedData(int $authorId): void
    {
        // Cerita utama
        $story = Story::create([
            'title' => 'Tsuki to Yami',
            'description' => 'Sebuah perjalanan mencari makna di tengah hiruk pikuk kota metropolitan yang tak pernah tidur.',
            'cover_image' => 'img/tsukitoyami.png',
            'genre' => 'Drama, Slice of Life',
            'views_count' => 0,
            'is_featured' => true,
            'created_by' => $authorId,
        ]);

        Chapter::create([
            'story_id' => $story->id,
            'title' => 'Bab 1: Awal Mula',
            'content' => '<div class="chapter-content-rich"><div class="scene-setting">Kota Jakarta, pukul 06.00 pagi. 
                            Matahari baru saja menyembul dari balik gedung-gedung pencakar langit.</div><div class="narration">Rian berjalan menyusuri trotoar Jalan Sudirman dengan langkah gontai. Di tangannya tergenggam selembar kertas — surat penolakan kerja yang kesekian kalinya.</div></div>',
            'chapter_number' => 1,
        ]);

        $this->command->info("✅ Data cerita hardcoded berhasil diimport!");
    }
}
