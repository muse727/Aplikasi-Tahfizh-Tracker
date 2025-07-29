<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\LearningModule;

class MainContentSeeder extends Seeder
{
    public function run(): void
    {
        // --- Membuat Semua Kelas ---
        Course::create(['name' => 'PAUD - Mengenal Huruf']);
        for ($i = 1; $i <= 5; $i++) {
            Course::create(['name' => 'Kelas Tilawati Jilid ' . $i]);
        }
        $quranTajwidCourse = Course::create(['name' => 'Kelas Al-Qur\'an Juz 1 & Tajwid']);
        $gharibCourse = Course::create(['name' => 'Kelas Gharib']);
        $tahfizhCourse = Course::create(['name' => 'Kelas Tahfizh (Hafalan)']);

        // --- Mengisi Materi untuk setiap kelas ---

        // Materi Kelas Al-Qur'an Juz 1 & Tajwid
        $juz1Surahs = ['Al-Fatihah', 'Al-Baqarah']; // Juz 1 hanya berisi 2 surah ini
        foreach ($juz1Surahs as $index => $surahName) {
            LearningModule::create([
                'course_id' => $quranTajwidCourse->id,
                'module_name' => 'Bacaan Surah ' . $surahName,
                'type' => 'bacaan_quran',
                'order_column' => $index + 1,
            ]);
        }
        $tajwidModules = ['Hukum Nun Sukun & Tanwin', 'Hukum Mim Sukun', 'Hukum Mad', 'Qalqalah', 'Makharijul Huruf'];
        foreach ($tajwidModules as $index => $moduleName) {
            LearningModule::create([
                'course_id' => $quranTajwidCourse->id,
                'module_name' => $moduleName,
                'type' => 'tajwid',
                'order_column' => count($juz1Surahs) + $index + 1,
            ]);
        }

        // Materi Kelas Gharib
        $gharibModules = ['Bacaan Imalah', 'Bacaan Isymam', 'Bacaan Saktah', 'Bacaan Tashil', 'Bacaan Naql'];
        foreach ($gharibModules as $index => $moduleName) {
            LearningModule::create([ 'course_id' => $gharibCourse->id, 'module_name' => $moduleName, 'type' => 'gharib', 'order_column' => $index + 1, ]);
        }

        // Materi Kelas Tahfizh (seluruh 114 surah)
        $allSurahs = [ 'Al-Fatihah', 'Al-Baqarah', 'Ali \'Imran', 'An-Nisa', 'Al-Ma\'idah', 'Al-An\'am', /* ... dan seterusnya sampai An-Nas ... */ 'Al-Ikhlas', 'Al-Falaq', 'An-Nas' ];
        foreach ($allSurahs as $index => $surahName) {
            LearningModule::create([ 'course_id' => $tahfizhCourse->id, 'module_name' => 'Hafalan Surah ' . $surahName, 'type' => 'hafalan', 'order_column' => $index + 1, ]);
        }
    }
}