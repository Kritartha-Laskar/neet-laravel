<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseName;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuestionPaper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionPaperSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Question Paper Seeder...');

        // 1. Ensure Course exists
        $course = CourseName::firstOrCreate(
            ['name' => 'NEET UG Medical'],
            ['status' => 1]
        );

        // 2. Ensure Subjects exist
        $subjectNames = ['Biology', 'Physics', 'Chemistry'];
        $subjectsMap  = [];

        foreach ($subjectNames as $name) {
            $sub = Subject::firstOrCreate(
                ['name' => $name],
                ['course_id' => $course->id]
            );
            $subjectsMap[$name] = $sub;
        }

        // 3. Create Sample Question Pool per Subject
        $questionTemplates = [
            'Biology' => [
                'Which cell organelle is known as the powerhouse of the cell?' => ['Mitochondria', 'Ribosome', 'Golgi apparatus', 'Lysosome'],
                'What is the primary site of photosynthesis in eukaryotic plants?' => ['Chloroplast', 'Nucleus', 'Vacuole', 'Peroxisome'],
                'Which human hormone regulates blood glucose concentration?' => ['Insulin', 'Glucagon', 'Thyroxine', 'Adrenaline'],
                'What is the structural and functional unit of kidney?' => ['Nephron', 'Neuron', 'Alveoli', 'Hepatocyte'],
                'In DNA, Adenine pairs with which nitrogenous base via double hydrogen bonds?' => ['Thymine', 'Cytosine', 'Guanine', 'Uracil'],
                'Which blood group is known as the universal donor?' => ['O Negative', 'AB Positive', 'A Positive', 'B Negative'],
                'What pigment absorbs light for photosynthesis?' => ['Chlorophyll', 'Carotenoid', 'Xanthophyll', 'Anthocyanin'],
                'The process of cell division producing four haploid daughter cells is called:' => ['Meiosis', 'Mitosis', 'Binary Fission', 'Budding'],
                'Which valve controls blood flow between the left atrium and left ventricle?' => ['Mitral (Bicuspid) Valve', 'Tricuspid Valve', 'Aortic Valve', 'Pulmonary Valve'],
                'Enzymes belong to which class of biological macromolecules?' => ['Proteins', 'Carbohydrates', 'Lipids', 'Nucleic Acids'],
                'Which enzyme breaks down starch into maltose in the human mouth?' => ['Salivary Amylase (Ptyalin)', 'Pepsin', 'Trypsin', 'Lipase'],
                'Root hair cells absorb water from soil primarily by:' => ['Osmosis', 'Active Transport', 'Diffusion', 'Phagocytosis'],
                'Which plant growth hormone promotes stem elongation and seed germination?' => ['Gibberellin', 'Auxin', 'Abscisic Acid', 'Cytokinin'],
                'Human oxygen-carrying respiratory pigment is:' => ['Hemoglobin', 'Hemocyanin', 'Myoglobin', 'Chlorocruorin'],
                'Which organ in humans secretes bile juice?' => ['Liver', 'Gallbladder', 'Pancreas', 'Stomach'],
            ],
            'Physics' => [
                'What is the SI unit of electric current?' => ['Ampere', 'Volt', 'Ohm', 'Watt'],
                'The rate of change of momentum of a body is directly proportional to:' => ['Applied Force', 'Velocity', 'Acceleration', 'Displacement'],
                'What is the acceleration due to gravity on the surface of Earth?' => ['9.8 m/s²', '9.8 cm/s²', '98 m/s²', '1.6 m/s²'],
                'Light year is a unit of measurement for:' => ['Astronomical Distance', 'Time', 'Speed of Light', 'Intensity of Light'],
                'Which law states that current is directly proportional to voltage across a conductor?' => ['Ohm\'s Law', 'Faraday\'s Law', 'Coulomb\'s Law', 'Lenz\'s Law'],
                'Sound waves cannot travel through:' => ['Vacuum', 'Water', 'Air', 'Steel'],
                'The kinetic energy of an object of mass m moving with velocity v is given by:' => ['1/2 m v²', 'm g h', 'm v', '1/2 m² v'],
                'Which mirror is used in headlights of vehicles to form a parallel beam?' => ['Concave Mirror', 'Convex Mirror', 'Plane Mirror', 'Cylindrical Mirror'],
                'Work done when a force of 10 N moves a body by 5 m in its direction is:' => ['50 Joules', '2 Joules', '15 Joules', '0.5 Joules'],
                'What type of wave is an electromagnetic wave?' => ['Transverse Wave', 'Longitudinal Wave', 'Mechanical Wave', 'Torsional Wave'],
                'Which physical quantity remains conserved in a perfectly elastic collision?' => ['Both Kinetic Energy and Momentum', 'Only Kinetic Energy', 'Only Momentum', 'Neither'],
                'Total internal reflection occurs when light travels from:' => ['Denser medium to rarer medium', 'Rarer medium to denser medium', 'Vacuum to air', 'Water to glass'],
                'Dimensional formula of universal gravitational constant G is:' => ['[M⁻¹ L³ T⁻²]', '[M L T⁻²]', '[M L² T⁻²]', '[M L T⁻¹]'],
                'The device used to measure electric current in a circuit is:' => ['Ammeter', 'Voltmeter', 'Galvanometer', 'Rheostat'],
                'Frequency of AC mains supply in India is:' => ['50 Hz', '60 Hz', '100 Hz', '120 Hz'],
            ],
            'Chemistry' => [
                'What is the chemical formula of Washing Soda?' => ['Na₂CO₃·10H₂O', 'NaHCO₃', 'NaOH', 'CaCO₃'],
                'Which element has the highest electronegativity in the periodic table?' => ['Fluorine', 'Oxygen', 'Chlorine', 'Nitrogen'],
                'What is the pH value of pure water at 25°C?' => ['7', '0', '14', '1'],
                'Avogadro\'s number is approximately equal to:' => ['6.022 × 10²³', '6.022 × 10²²', '1.602 × 10⁻¹⁹', '3.0 × 10⁸'],
                'The gas evolved when sodium bicarbonate reacts with dilute hydrochloric acid is:' => ['Carbon Dioxide (CO₂)', 'Hydrogen (H₂)', 'Oxygen (O₂)', 'Nitrogen (N₂)'],
                'Which acid is present in gastric juice produced in human stomach?' => ['Hydrochloric Acid (HCl)', 'Sulfuric Acid (H₂SO₄)', 'Nitric Acid (HNO₃)', 'Acetic Acid (CH₃COOH)'],
                'Oxidation state of Manganese in Potassium Permanganate (KMnO₄) is:' => ['+7', '+6', '+4', '+2'],
                'Which alloy is composed of copper and zinc?' => ['Brass', 'Bronze', 'Solder', 'Steel'],
                'The geometry of a Methane (CH₄) molecule is:' => ['Tetrahedral', 'Trigonal Planar', 'Linear', 'Octahedral'],
                'Which functional group is present in aldehydes?' => ['-CHO', '-COOH', '-OH', '-CO-'],
                'Process of conversion of liquid into vapour below its boiling point is:' => ['Evaporation', 'Boiling', 'Condensation', 'Sublimation'],
                'Principal quantum number n describes which property of an atomic orbital?' => ['Size and Energy level', 'Shape', 'Spatial Orientation', 'Spin state'],
                'Which substance is used as a disinfectant in drinking water?' => ['Bleaching Powder (CaOCl₂)', 'Baking Soda', 'Plaster of Paris', 'Gypsum'],
                'Hydrocarbon containing at least one carbon-carbon triple bond is called:' => ['Alkyne', 'Alkene', 'Alkane', 'Cycloalkane'],
                'Which gas is responsible for the greenhouse effect and global warming?' => ['Carbon Dioxide', 'Oxygen', 'Argon', 'Helium'],
            ]
        ];

        $this->command->info('📝 Generating Fake Questions & Answer Options...');

        foreach ($subjectsMap as $subName => $subject) {
            $templates = $questionTemplates[$subName] ?? [];
            
            // Create at least 30 questions per subject by duplicating/variating templates if needed
            for ($k = 1; $k <= 30; $k++) {
                $keys = array_keys($templates);
                $questionText = $keys[($k - 1) % count($keys)];
                if ($k > count($keys)) {
                    $questionText .= " (Practice Set #{$k})";
                }

                $options = $templates[$keys[($k - 1) % count($keys)]];

                $q = Question::create([
                    'subject_id'    => $subject->id,
                    'question'      => $questionText,
                    'question_type' => 'mcq',
                ]);

                // Create options (First option is correct)
                foreach ($options as $idx => $optText) {
                    Answer::create([
                        'question_id' => $q->id,
                        'answer'      => $optText,
                        'is_correct'  => ($idx === 0) ? 1 : 0,
                    ]);
                }
            }
        }

        // 4. Create 10 Subject-Wise Mock Test Question Papers FOR EVERY SUBJECT
        $this->command->info('📚 Creating 10 Subject-Wise Mock Test Question Papers for every subject...');

        foreach ($subjectsMap as $subName => $subject) {
            for ($i = 1; $i <= 10; $i++) {
                $title = "{$subName} Mock Test Paper {$i}";

                $paper = QuestionPaper::firstOrCreate(
                    ['title' => $title],
                    [
                        'description'      => "Official 180-minute subject-wise mock test paper for {$subName} (Set {$i}).",
                        'exam_name'        => 'NEET Subject Mock',
                        'course_id'        => $course->id,
                        'paper_type'       => 'mocktest',
                        'subject_id'       => $subject->id,
                        'subject_quotas'   => [$subName => 180],
                        'exam_year'        => 2026,
                        'duration_minutes' => 180,
                        'total_marks'      => 720,
                        'total_questions'  => 0,
                    ]
                );

                // Fetch questions for this subject
                $qIds = Question::where('subject_id', $subject->id)
                    ->whereNull('deleted_at')
                    ->inRandomOrder()
                    ->pluck('id');

                $pivotRows = [];
                $order = 1;
                foreach ($qIds as $qid) {
                    $pivotRows[] = [
                        'question_paper_id' => $paper->id,
                        'question_id'       => $qid,
                        'order'             => $order++,
                        'marks'             => 4,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }

                if (!empty($pivotRows)) {
                    // Refresh pivot
                    DB::table('question_paper_question')->where('question_paper_id', $paper->id)->delete();
                    DB::table('question_paper_question')->insert($pivotRows);

                    $paper->update([
                        'total_questions' => count($pivotRows),
                        'total_marks'     => count($pivotRows) * 4,
                    ]);
                }
            }
        }

        // 5. Create 5 Combined Question Papers
        $this->command->info('🌐 Creating 5 Combined Multi-Subject Question Papers...');

        for ($j = 1; $j <= 5; $j++) {
            $title = "NEET Combined Grand Mock Paper {$j}";

            $quotasMap = [
                'Biology'   => 90,
                'Physics'   => 45,
                'Chemistry' => 45,
            ];

            $paper = QuestionPaper::firstOrCreate(
                ['title' => $title],
                [
                    'description'      => "Full-length NEET combined mock paper covering Biology, Physics, and Chemistry (Series #{$j}).",
                    'exam_name'        => 'NEET Combined',
                    'course_id'        => $course->id,
                    'paper_type'       => 'combined',
                    'subject_id'       => null,
                    'subject_quotas'   => $quotasMap,
                    'exam_year'        => 2026,
                    'duration_minutes' => 180,
                    'total_marks'      => 720,
                    'total_questions'  => 0,
                ]
            );

            $pivotRows = [];
            $order = 1;

            foreach ($subjectsMap as $sName => $subObj) {
                $targetCount = $quotasMap[$sName] ?? 30;

                $qIds = Question::where('subject_id', $subObj->id)
                    ->whereNull('deleted_at')
                    ->inRandomOrder()
                    ->limit($targetCount)
                    ->pluck('id');

                foreach ($qIds as $qid) {
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

            if (!empty($pivotRows)) {
                DB::table('question_paper_question')->where('question_paper_id', $paper->id)->delete();
                DB::table('question_paper_question')->insert($pivotRows);

                $paper->update([
                    'total_questions' => count($pivotRows),
                    'total_marks'     => count($pivotRows) * 4,
                ]);
            }
        }

        $this->command->info('✅ QuestionPaperSeeder finished successfully!');
    }
}
