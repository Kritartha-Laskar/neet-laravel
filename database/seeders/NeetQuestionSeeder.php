<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseName;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;

class NeetQuestionSeeder extends Seeder
{
    /**
     * NEET subject → question count mapping
     */
    private array $subjects = [
        'Biology'   => 90,
        'Physics'   => 45,
        'Chemistry' => 45,
    ];

    // ----------------------------------------------------------------
    // Sample question banks per subject (realistic short-form stubs)
    // ----------------------------------------------------------------
    private array $biologyQuestions = [
        'Which organelle is known as the powerhouse of the cell?',
        'The process by which plants make food using sunlight is called?',
        'DNA replication occurs during which phase of the cell cycle?',
        'Which blood group is called a universal donor?',
        'The functional unit of the kidney is called?',
        'Mitosis results in how many daughter cells?',
        'Which hormone regulates blood glucose levels?',
        'The carrying capacity of an ecosystem is determined by?',
        'Mendel\'s law of segregation states that?',
        'Which type of RNA carries amino acids to the ribosome?',
        'The site of protein synthesis in a cell is?',
        'Which vitamin is produced by the skin on exposure to sunlight?',
        'What is the shape of DNA molecule?',
        'Which enzyme breaks down starch in the mouth?',
        'The largest organ of the human body is?',
        'Crossing over occurs during which sub-phase of meiosis?',
        'Which part of the brain controls body temperature?',
        'Haemoglobin is a protein found in?',
        'The scientific study of heredity is called?',
        'Which gas is released during photosynthesis?',
        'Transpiration in plants occurs mainly through?',
        'Which cells produce antibodies?',
        'The process of breaking down glucose to release energy is?',
        'What is the normal pH of human blood?',
        'Chromosomes are made up of?',
        'Which disease is caused by deficiency of Vitamin C?',
        'The number of chromosomes in a human cell is?',
        'Which hormone triggers ovulation?',
        'The basic structural unit of proteins is?',
        'Which part of the cell contains genetic information?',
        'ABO blood grouping is controlled by?',
        'The process of cell division in gametes is called?',
        'Which enzyme is used in DNA fingerprinting?',
        'The powerhouse of the plant cell is?',
        'Which vitamin helps in blood clotting?',
        'Autotrophic nutrition is seen in?',
        'The study of fossils is called?',
        'Which gas is a greenhouse gas?',
        'What is the full form of ATP?',
        'Which part of the brain is responsible for memory?',
        'The yellow colour of urine is due to?',
        'Which phytohormone promotes fruit ripening?',
        'DNA polymerase adds nucleotides in which direction?',
        'Which organ produces bile?',
        'Neurons communicate via?',
        'Root nodules contain which bacteria?',
        'What is the role of the spleen?',
        'Which type of immunity is conferred by vaccines?',
        'The fluid mosaic model describes?',
        'Which organism was used by Mendel in his experiments?',
        'Glycolysis occurs in which part of the cell?',
        'Which organ of the body detoxifies drugs?',
        'The structural and functional unit of life is?',
        'Which test is used to diagnose malaria?',
        'Ozone layer is found in which layer of atmosphere?',
        'Which protist causes sleeping sickness?',
        'Photolysis of water occurs in?',
        'Which bond holds base pairs in DNA?',
        'Lysosome is also known as?',
        'Which tissue connects muscle to bone?',
        'What is the role of ribosomes?',
        'Which kingdom do fungi belong to?',
        'Nitrogen fixation is performed by?',
        'The fluid filled space inside chloroplast is?',
        'Which is the longest bone in the human body?',
        'Chlorophyll absorbs light of which colour most?',
        'The smallest organism capable of independent life is?',
        'Which enzyme is absent in a newborn\'s gut?',
        'Insulin is produced in which part of the pancreas?',
        'Which ion is essential for muscle contraction?',
        'Which blood cells fight infection?',
        'Hemophilia is an example of which type of disease?',
        'Which layer of the skin contains melanocytes?',
        'What is the function of the epiglottis?',
        'Which hormone is released during stress?',
        'The nitrogenous base not found in RNA is?',
        'Which phylum includes earthworms?',
        'What triggers the opening of stomata?',
        'Incomplete dominance is seen in which flower?',
        'Which organ produces erythropoietin?',
        'Sickle cell anaemia is caused by mutation in which gene?',
        'The cardiac muscle is?',
        'Which enzyme converts fibrinogen to fibrin?',
        'Gibberellins promote?',
        'The term ecology was coined by?',
        'Which bond holds the two strands of DNA?',
        'Which vitamin is essential for vision in dim light?',
        'Osmoregulation in freshwater fish involves?',
        'Which gland is known as the master gland?',
        'Turner\'s syndrome karyotype is?',
    ];

    private array $physicsQuestions = [
        'Newton\'s second law of motion states that?',
        'The SI unit of force is?',
        'Which law states that every action has an equal and opposite reaction?',
        'The speed of light in vacuum is approximately?',
        'Which type of lens is used to correct myopia?',
        'Ohm\'s law states the relationship between?',
        'The unit of electric charge is?',
        'Which phenomenon explains the bending of light at an interface?',
        'The gravitational acceleration on Earth is approximately?',
        'Work is defined as?',
        'The unit of power is?',
        'Which type of wave does not require a medium?',
        'Magnetic field lines emerge from which pole of a magnet?',
        'The principle of conservation of energy states?',
        'In a series circuit, the current through each component is?',
        'The Doppler effect is associated with?',
        'Which particle carries a negative charge?',
        'The unit of frequency is?',
        'Boyle\'s law relates pressure to?',
        'The centre of mass of a uniform disc is?',
        'Photoelectric effect was explained by?',
        'The critical angle depends on which factors?',
        'Fleming\'s left-hand rule gives the direction of?',
        'The unit of magnetic flux density is?',
        'Which law relates current, resistance and voltage?',
        'Projectile motion is a combination of?',
        'The escape velocity from Earth is approximately?',
        'Which mirror is used in car headlights?',
        'The phenomenon of light splitting into colours is called?',
        'Kirchhoff\'s current law is based on?',
        'The unit of capacitance is?',
        'What is the angle of incidence at total internal reflection?',
        'In a transformer, the turns ratio determines?',
        'The work done by a conservative force?',
        'Electromagnetic induction was discovered by?',
        'The time period of a simple pendulum depends on?',
        'What is the SI unit of pressure?',
        'Which equation relates energy and mass?',
        'The first law of thermodynamics is a statement of?',
        'The refractive index of a medium is defined as?',
        'Coulomb\'s law gives the force between?',
        'The principle of superposition applies to?',
        'Which type of spectrum does hydrogen produce?',
        'The half-life of a radioactive substance is?',
        'The dimension of velocity is?',
    ];

    private array $chemistryQuestions = [
        'The atomic number of carbon is?',
        'Which gas is produced when zinc reacts with dilute HCl?',
        'Avogadro\'s number is approximately?',
        'The IUPAC name of CH3-CH2-OH is?',
        'Which type of bond is formed by sharing electrons?',
        'Acids turn litmus paper to which colour?',
        'The process of converting ores to metals is called?',
        'Which element has the highest electronegativity?',
        'Periodic table was designed by?',
        'The empirical formula of benzene is?',
        'Which catalyst is used in the Haber process?',
        'The pH of a neutral solution at 25°C is?',
        'Which law states that matter cannot be created or destroyed?',
        'The oxidation state of oxygen in H2O is?',
        'Which reaction involves loss of electrons?',
        'Le Chatelier\'s principle deals with?',
        'The boiling point of water at standard pressure is?',
        'Which group of the periodic table contains noble gases?',
        'The functional group of alcohols is?',
        'What is an isomer?',
        'Faraday\'s laws relate to which process?',
        'The hybridisation of carbon in methane is?',
        'Which indicator is used in acid-base titration?',
        'The unit of molar mass is?',
        'Which allotrope of carbon is the hardest natural substance?',
        'Saponification is the process of making?',
        'The noble gas configuration confers?',
        'What is the valency of nitrogen in NH3?',
        'Galvanisation involves coating iron with?',
        'Which type of reaction releases heat?',
        'The rate of reaction increases with temperature because?',
        'Which polymer is used to make nylon?',
        'The molecular formula of glucose is?',
        'Isotopes have the same atomic number but different?',
        'Which gas is responsible for the greenhouse effect?',
        'SN2 reaction involves?',
        'The Maillard reaction occurs between?',
        'Buffer solutions resist changes in?',
        'The degree of unsaturation of benzene is?',
        'Which reaction is used to test for aldehydes?',
        'Raoult\'s law relates to?',
        'What is the hybridisation of the carbon in CO2?',
        'Which element is a liquid at room temperature (non-metal)?',
        'Esterification is a reaction between?',
        'The Henderson-Hasselbalch equation is used to calculate?',
    ];

    // ---------------------------------------------------------------

    public function run(): void
    {
        // 1. Create (or find) the NEET course
        $course = CourseName::firstOrCreate(
            ['name' => 'NEET'],
            ['status' => 'active']
        );

        $this->command->info('✔ Course: NEET');

        // 2. Create subjects + questions
        foreach ($this->subjects as $subjectName => $count) {

            $subject = Subject::firstOrCreate(
                ['course_id' => $course->id, 'name' => $subjectName]
            );

            $questionBank = match ($subjectName) {
                'Biology'   => $this->biologyQuestions,
                'Physics'   => $this->physicsQuestions,
                'Chemistry' => $this->chemistryQuestions,
                default     => [],
            };

            $created = 0;

            for ($i = 0; $i < $count; $i++) {
                // Cycle through the bank; append a number when exhausted
                $text = $questionBank[$i % count($questionBank)];
                if ($i >= count($questionBank)) {
                    $text .= ' (variation ' . (intdiv($i, count($questionBank)) + 1) . ')';
                }

                $question = Question::firstOrCreate(
                    ['subject_id' => $subject->id, 'question' => $text],
                    ['question_type' => 'mcq']
                );

                // Seed 4 answers for each fresh question
                if ($question->wasRecentlyCreated) {
                    $correctIdx = rand(0, 3);
                    $options = $this->makeOptions($subjectName, $text);
                    foreach ($options as $idx => $option) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer'      => $option,
                            'is_correct'  => ($idx === $correctIdx),
                        ]);
                    }
                    $created++;
                }
            }

            $this->command->info("  ✔ Subject: {$subjectName} — {$created} question(s) seeded.");
        }
    }

    // ---------------------------------------------------------------
    // Build 4 plausible MCQ options for a given question text
    // ---------------------------------------------------------------
    private function makeOptions(string $subject, string $question): array
    {
        $banks = [
            'Biology' => [
                ['Nucleus', 'Mitochondria', 'Ribosome', 'Golgi apparatus'],
                ['Photosynthesis', 'Respiration', 'Fermentation', 'Transpiration'],
                ['S phase', 'G1 phase', 'M phase', 'G2 phase'],
                ['O', 'A', 'B', 'AB'],
                ['Nephron', 'Neuron', 'Alveolus', 'Glomerulus'],
            ],
            'Physics' => [
                ['F = ma', 'F = mv', 'F = m/a', 'F = m²a'],
                ['Newton', 'Joule', 'Pascal', 'Watt'],
                ['First law', 'Second law', 'Third law', 'Law of gravitation'],
                ['3×10⁸ m/s', '3×10⁶ m/s', '3×10¹⁰ m/s', '3×10⁴ m/s'],
                ['Concave lens', 'Convex lens', 'Plane mirror', 'Prism'],
            ],
            'Chemistry' => [
                ['6', '12', '14', '8'],
                ['H₂', 'O₂', 'CO₂', 'N₂'],
                ['6.022×10²³', '6.022×10²¹', '3.011×10²³', '1.204×10²⁴'],
                ['Ethanol', 'Methanol', 'Propanol', 'Butanol'],
                ['Covalent', 'Ionic', 'Metallic', 'Hydrogen'],
            ],
        ];

        // Pick a random set from that subject's bank
        $pool = $banks[$subject] ?? $banks['Biology'];
        return $pool[array_rand($pool)];
    }
}
