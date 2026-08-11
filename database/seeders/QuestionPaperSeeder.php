<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionPaper;
use App\Models\Subject;
use App\Models\Question;

class QuestionPaperSeeder extends Seeder
{
    /**
     * NEET paper structure:
     *   Biology   → 90 questions (45 Botany + 45 Zoology, but we keep it as Biology)
     *   Physics   → 45 questions
     *   Chemistry → 45 questions
     *   Total     → 180 questions | 720 marks | 180 minutes
     */
    private array $papers = [
        [
            'title'            => 'NEET Mock Test — Paper 1',
            'description'      => 'Full-length NEET mock paper covering Biology (90), Physics (45), and Chemistry (45) with randomised question selection.',
            'exam_name'        => 'NEET',
            'total_questions'  => 180,
            'duration_minutes' => 180,
            'total_marks'      => 720,
            'exam_year'        => 2025,
        ],
        [
            'title'            => 'NEET Mock Test — Paper 2',
            'description'      => 'Second full-length NEET mock paper with a fresh random set of questions from Biology, Physics, and Chemistry.',
            'exam_name'        => 'NEET',
            'total_questions'  => 180,
            'duration_minutes' => 180,
            'total_marks'      => 720,
            'exam_year'        => 2025,
        ],
        [
            'title'            => 'NEET Mock Test — Paper 3',
            'description'      => 'Third full-length NEET mock paper — ideal for final revision before the actual exam.',
            'exam_name'        => 'NEET',
            'total_questions'  => 180,
            'duration_minutes' => 180,
            'total_marks'      => 720,
            'exam_year'        => 2025,
        ],
    ];

    /** Questions per subject per paper */
    private array $subjectQuota = [
        'Biology'   => 90,
        'Physics'   => 45,
        'Chemistry' => 45,
    ];

    public function run(): void
    {
        foreach ($this->papers as $paperData) {

            // Avoid creating the same paper twice
            $paper = QuestionPaper::firstOrCreate(
                ['title' => $paperData['title']],
                $paperData
            );

            if (! $paper->wasRecentlyCreated) {
                $this->command->warn("  ⚠  Already exists: {$paper->title} — skipped.");
                continue;
            }

            $pivotRows = [];
            $order     = 1;

            foreach ($this->subjectQuota as $subjectName => $quota) {

                $subject = Subject::where('name', $subjectName)->first();

                if (! $subject) {
                    $this->command->error("  ✖  Subject '{$subjectName}' not found. Run NeetQuestionSeeder first.");
                    continue;
                }

                // Randomly pick $quota questions from this subject
                $questionIds = Question::where('subject_id', $subject->id)
                    ->whereNull('deleted_at')
                    ->inRandomOrder()
                    ->limit($quota)
                    ->pluck('id');

                if ($questionIds->count() < $quota) {
                    $this->command->warn(
                        "  ⚠  Only {$questionIds->count()} questions available for {$subjectName} (need {$quota})."
                    );
                }

                foreach ($questionIds as $qid) {
                    $pivotRows[] = [
                        'question_paper_id' => $paper->id,
                        'question_id'       => $qid,
                        'order'             => $order++,
                        'marks'             => 4,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }
            }

            // Bulk insert pivot rows
            \DB::table('question_paper_question')->insert($pivotRows);

            $this->command->info("  ✔  Created: {$paper->title} ({$order - 1} questions attached)");
        }

        $this->command->newLine();
        $this->command->info('✅  Question paper seeding complete.');
    }
}
