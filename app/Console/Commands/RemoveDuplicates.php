<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicates extends Command
{
    protected $signature   = 'admin:remove-duplicates';
    protected $description = 'Delete duplicate rows from courses, subjects, questions, and answers tables (keeps the lowest id in each group).';

    public function handle(): int
    {
        // ── 1. course_names ──────────────────────────────────────────
        $deletedCourses = $this->removeDuplicatesFrom(
            table    : 'course_names',
            groupBy  : ['name'],
            softDelete: true
        );
        $this->info("course_names : {$deletedCourses} duplicate(s) removed.");

        // ── 2. subjects ───────────────────────────────────────────────
        $deletedSubjects = $this->removeDuplicatesFrom(
            table    : 'subjects',
            groupBy  : ['course_id', 'name'],
            softDelete: true
        );
        $this->info("subjects     : {$deletedSubjects} duplicate(s) removed.");

        // ── 3. questions ──────────────────────────────────────────────
        $deletedQuestions = $this->removeDuplicatesFrom(
            table    : 'questions',
            groupBy  : ['subject_id', 'question'],
            softDelete: true
        );
        $this->info("questions    : {$deletedQuestions} duplicate(s) removed.");

        // ── 4. answers ────────────────────────────────────────────────
        $deletedAnswers = $this->removeDuplicatesFrom(
            table    : 'answers',
            groupBy  : ['question_id', 'answer'],
            softDelete: false
        );
        $this->info("answers      : {$deletedAnswers} duplicate(s) removed.");

        $this->newLine();
        $this->info('✅  Duplicate cleanup complete.');

        return Command::SUCCESS;
    }

    /**
     * Find all rows that share the same groupBy columns and delete every
     * row EXCEPT the one with the smallest id.
     *
     * @param  string   $table
     * @param  string[] $groupBy
     * @param  bool     $softDelete  Whether the table uses soft-deletes
     * @return int  Number of rows deleted
     */
    private function removeDuplicatesFrom(string $table, array $groupBy, bool $softDelete): int
    {
        // Build a query that returns the MIN id for each group
        $query = DB::table($table)
            ->select($groupBy)
            ->selectRaw('MIN(id) as keep_id')
            ->groupBy($groupBy)
            ->havingRaw('COUNT(*) > 1');

        // Only filter out soft-deleted rows when the table supports it
        if ($softDelete) {
            $query->whereNull('deleted_at');
        }

        $groups = $query->get();

        if ($groups->isEmpty()) {
            return 0;
        }

        $deleted = 0;

        foreach ($groups as $group) {
            // Build a base query matching this group
            $base = DB::table($table);

            if ($softDelete) {
                $base->whereNull('deleted_at');
            }

            foreach ($groupBy as $col) {
                $base->where($col, $group->{$col});
            }

            // Exclude the row we want to keep
            $base->where('id', '!=', $group->keep_id);

            if ($softDelete) {
                // Soft-delete: set deleted_at
                $deleted += $base->update(['deleted_at' => now()]);
            } else {
                // Hard-delete
                $deleted += $base->delete();
            }
        }

        return $deleted;
    }
}
