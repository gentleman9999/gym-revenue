<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class WorkoutGeneratorController extends Controller
{
    public function index()
    {
        $args = [
            'core' => $this->getCoreValues(),
            'upper' => $this->getUpperValues(),
            'lower' => $this->getLowerValues(),
        ];

        return Inertia::render('Extras/WorkoutGenerator', $args);
    }

    private function getCoreValues(): array
    {
        $results = [
            'Exercise' => [],
            'Muscle Group' => [],
            'Body or Band' => [],
            'TRX' => [],
        ];

        $str = $this->getCSV(str_replace("\n", "", "Plank,Core,True,False,
Side Plank,Core,True,False,
Push-up Plank,Core,True,False,
Leg Raises,Core,True,False,
Crunchy Frog,Core,True,False,
V-Ups,Core,True,False,
Cable Crunches,Core,False,False,
Crunch,Core,True,False,
Sit-Ups,Core,True,False,
Russian Twists,Core,True,False,
Pallof Press,Core,False,False,
Scissor Kicks,Core,True,False,
Flutter Kicks,Core,True,False,
Bench Press Leg Lifts,Core,True,False,
Single Leg lift Crunches,Core,True,False,
Captains Chair Weighted Leg Lifts,Core,False,False,
Atomic Sit-ups,Core,False,False,
Butt-ups,Core,True,False,
Winshield wipers,Core,True,False,
Planking Leg Rolls,Core,True,False,
Mountain Climbers,Core,True,False,
Knee To Elbow Sit-ups,Core,True,False,
Knee Crunches,Core,True,False,
Hanging Straight Leg Lifts,Core,True,False,
Hanging Bent Leg Lifts,Core,True,False,
Hanging Oblique Crunches,Core,True,False,
Cable Oblique Crunches,Core,False,False,
Cable Wood Choppers,Core,False,False,
Cable Standing Crunch,Core,False,False,
Bosu Ball Crunches,Core,True,False,
Bosu Ball Sit-ups,Core,True,False,
Bosu Ball Oblique Crunches,Core,True,False,
Bosu Ball Plank,Core,True,False,
TRX Plank,Core,True,True,
TRX Crunches,Core,True,True,
TRX Pikes,Core,True,True,
TRX Scissor Kicks,Core,True,True,
TRX Mountain Climbers,Core,True,True,
One Legged Bosu Crunches,Core,True,False,
SinGle Arm and Leg V-ups,Core,True,False,
Burpees,Core,True,False,
Superman,Core,True,False,
Band Pallof Press,Core,True,False,
Band Leg Lifts,Core,True,False,
Band knee Oblique Crunches,Core,True,False,
Hanging Band Leg Lifts,Core,True,False,
Hanging Single Leg Band Lifts,Core,True,False,
Hanging Leg Raises Static Hold,Core,True,False,
Standing Walk Outs,Core,True,False,
Standing One Legged Walk Outs,Core,True,False,
Wide Leg Sit-ups,Core,True,False,
Wide Leg Leg-Raises,Core,True,False,
Wide Leg Russian Twists,Core,True,False,
Two-Point Bridge,Core,True,False,
Twisting Back Extension (M),Core,True,False,
Weighted Ab Crunch (M),Core,False,False,
Oblique Twist (M),Core,True,False,
Negative Crunch,Core,True,False,
Figure-8 Crunch,Core,True,False,
Cable Leaning Towers,Core,True,False,
Prone Heel Touches,Core,True,False"));

        $count = 0;
        foreach ($str as $idx => $val) {
            switch ($count) {
                case 1:
                    $results['Muscle Group'][] = $val;

                    break;

                case 2:
                    $results['Body or Band'][] = $val;

                    break;

                case 3:
                    $results['TRX'][] = $val;

                    break;
                default:
                    $results['Exercise'][] = $val;
            }
            if ($count == 3) {
                $count = 0;
            } else {
                $count++;
            }
        }

        return $results;
    }

    private function getLowerValues(): array
    {
        $results = [
            'Exercise' => [],
            'Muscle Group' => [],
            'Direction' => [],
            'Major Lift' => [],
            'Body or Band' => [],
            'BB' => [],
            'KB' => [],
            'DB' => [],
            'TRX' => [],
        ];

        $str = $this->getCSV(str_replace("\n", "", "BB Deadlift,Hamstrings,Pull,True,False,True,False,False,False,
DB Deadlift,Hamstrings,Pull,True,False,False,False,True,False,
KB Deadlift,Hamstrings,Pull,True,False,False,True,False,False,
BB Straight Legged Deadlift,Hamstrings,Pull,True,False,True,False,False,False,
DB Straight Legged Deadlift,Hamstrings,Pull,True,False,False,False,True,False,
KB Straight Legged Deadlift,Hamstrings,Pull,True,False,False,True,False,False,
Band Deadlift,Hamstrings,Pull,True,True,False,False,False,False,
BB Squat,Glutes,Push,True,False,True,False,False,False,
BB Over Head Squat,Quadriceps,Push,True,False,True,False,False,False,
DB Squat,Glutes,Push,True,False,False,False,True,False,
DB Over Head Squat,Quadriceps,Push,True,False,False,False,True,False,
KB Squat,Glutes,Push,True,False,False,True,False,False,
KB Over Head Squat,Glutes,Push,True,False,False,True,False,False,
DB Squat to Press,Quadriceps,Push,True,False,False,False,True,False,
BB Squat to Press,Glutes,Push,True,False,True,False,False,False,
KB Squat to Press,Glutes,Push,True,False,False,True,False,False,
Medicine Ball Squat to Press,Glutes,Push,True,False,False,False,False,False,
BB Squat to High Pull,Quadriceps,Both,True,False,True,False,False,False,
KB Squat to High Pull,Quadriceps,Both,True,False,False,True,False,False,
BB Reverse Lunges,Hamstrings,Pull,True,False,True,False,False,False,
DB Reverse Lunges,Hamstrings,Pull,True,False,False,False,True,False,
BB Lunges,Quadriceps,Pull,True,False,True,False,False,False,
DB Lunges,Quadriceps,Pull,True,False,False,False,True,False,
Single DB Lunge to Press,Quadriceps,Pull,True,False,False,False,True,False,
KB Lunge to Press,Quadriceps,Pull,True,False,False,True,False,False,
Medicine Ball Lunge to Press,Quadriceps,Both,True,False,False,False,False,False,
KB Over Head Lunge,Quadriceps,Pull,True,False,False,True,False,False,
DB Over Head Lunge,Glutes,Pull,True,False,False,False,True,False,
Hamstring Curl (M),Hamstrings,Pull,False,False,False,False,False,False,
Leg Extension (M),Quadriceps,Push,False,False,False,False,False,False,
Hack Squat (M),Quadriceps,Push,True,False,False,False,False,False,
Abductors (M),Hips,Push,False,False,False,False,False,False,
Adductors (M),Inner-thigh,Pull,False,False,False,False,False,False,
Band abductors,Hips,Push,False,True,False,False,False,False,
Standing Band Abductors,Hips,Push,False,True,False,False,False,False,
Banded Walking Side lunges,Hips,Both,False,True,False,False,False,False,
Goblet Squats,Quadriceps,Both,False,True,False,False,False,False,
Floor Hip Thrusts,Hips,Push,False,True,False,False,False,False,
Smith Machine Hip Thrusts,Quadriceps,Push,False,False,False,False,False,False,
Cable Kickbacks,Glutes,Push,False,False,False,False,False,False,
Kickbacks (M),Glutes,Push,False,False,False,False,False,False,
Cable Lateral Leg Raises,Glutes,Push,False,False,False,False,False,False,
Bodyweight Kickbacks,Glutes,Push,False,False,False,False,False,False,
Leg Press (M),Quadriceps,Push,True,False,False,False,False,False,
Sissy Squats(M),Glutes,Push,True,False,False,False,False,False,
Prone Cable Hamstring Curl,Hamstrings,Pull,False,False,False,False,False,False,
Weighted Step Ups,Quadriceps,Push,False,False,False,False,False,False,
Step Ups,Hips,Push,False,True,False,False,False,False,
Calf Extension,Calves,Push,False,False,False,False,False,False,
Calf Raises,Calves,Push,False,True,False,False,False,False,
Calf Raises Smith Machine (M),Calves,Push,False,False,False,False,False,False,
Curtsy Lunge,Hamstrings,Push,False,True,False,False,False,False,
Jump Squats,Glutes,Push,False,True,False,False,False,False,
Pistol Squats,Glutes,Push,False,True,False,False,False,False,
Good Mornings,Hamstrings,Pull,False,True,False,False,False,False,
Back Extensions,Hamstrings,Pull,False,True,False,False,False,False,
Band Kick Backs,Glutes,Push,False,True,False,False,False,False,
Fire Hydrants,Glutes,Push,False,True,False,False,False,False,
Band Fire Hydrants,Glutes,Push,False,True,False,False,False,False,
Slider Lunges,Hamstrings,Pull,False,True,False,False,False,False,
Slider Curtsy Lunges,Hamstrings,Pull,False,True,False,False,False,False,
Slider Hamstring Bridges,Hamstrings,Pull,False,True,False,False,False,False,
Single Legged Bridges,Hamstrings,Pull,False,True,False,False,False,False,
Single DB Deadlift,Hamstrings,Pull,False,False,False,False,True,False,
Lateral Leg Raises,Glutes,Pull,False,True,False,False,False,False,
Prone Leg Raises,Quadriceps,Pull,False,True,False,False,False,False,
Pulsing Squats,Quadriceps,Push,True,True,False,False,False,False,
Bulgarian Lunge,Hamstrings,Push,True,True,False,False,False,False,
Weighted Bulgarian Lunge,Hamstrings,Push,True,False,False,False,False,False,
Walking Lunges,Hamstrings,Push,True,True,False,False,False,False,
Cable Donkey Kickbacks,Glutes,Push,False,False,False,False,False,False,
Front Squat,Quadriceps,Push,False,False,False,False,False,False,
Body Weight Donkey Kickbacks,Glutes,Push,False,True,False,False,False,False,
Elevated Side lunges,Hamstrings,Push,False,True,False,False,False,False,
TRX Assisted Squats,Glutes,Push,False,True,False,False,False,True,
TRX Curtsy Lunge,Hips,Pull,False,True,False,False,False,True,
TRX Lunge,Hamstrings,Pull,False,True,False,False,False,True,
TRX Jump Squats,Quadriceps,Push,False,True,False,False,False,True,
TRX Side to Side Squat,Quadriceps,Push,False,True,False,False,False,True,
TRX Assisted Kick backs,Glutes,Push,False,True,False,False,False,True,
KB Swings,Hips,Pull,False,False,False,True,False,False,
Single Arm KB Swings,Hips,Pull,False,False,False,True,False,False,
Single Arm Power Klings,Hips,Pull,False,False,False,False,False,False,
Single Legged Hip Thrusts,Hips,Push,False,False,False,False,False,False,
Skiers,Hamstrings,Both,False,True,False,False,False,False"));

        $count = 0;
        foreach ($str as $idx => $val) {
            switch ($count) {
                case 1:
                    $results['Muscle Group'][] = $val;

                    break;

                case 2:
                    $results['Direction'][] = $val;

                    break;

                case 3:
                    $results['Major Lift'][] = $val;

                    break;

                case 4:
                    $results['Body or Band'][] = $val;

                    break;

                case 5:
                    $results['BB'][] = $val;

                    break;

                case 6:
                    $results['KB'][] = $val;

                    break;

                case 7:
                    $results['DB'][] = $val;

                    break;

                case 8:
                    $results['TRX'][] = $val;

                    break;

                default:
                    $results['Exercise'][] = $val;
            }
            if ($count == 8) {
                $count = 0;
            } else {
                $count++;
            }
        }

        return $results;
    }

    private function getUpperValues(): array
    {
        $results = [
            'Exercise' => [],
            'Muscle Group' => [],
            'Direction' => [],
            'Major Lift' => [],
            'Body or Band' => [],
            'BB' => [],
            'KB' => [],
            'DB' => [],
            'TRX' => [],
        ];

        $str = $this->getCSV(str_replace("\n", "", "KB Standing Close Grip Chest Press,Chest,Push,True,False,False,True,False,False,
DB Chest Press,Chest,Push,True,False,False,False,True,False,
DB Incline Chest press,Chest,Push,True,False,False,False,True,False,
DB Incline Reverse Chest Press,Chest,Push,True,False,False,False,True,False,
DB Decline Chest press,Chest,Push,True,False,False,False,True,False,
DB Decline Reverse Chest Press,Chest,Push,True,False,False,False,True,False,
DB Chest Fly,Chest,Push,False,False,False,False,True,False,
DB Incline Chest Fly,Chest,Push,False,False,False,False,True,False,
DB Decline Chest Fly,Chest,Push,False,False,False,False,True,False,
Cable Undergrip Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Undergrip incline chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Undergrip Decline Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Chest Press,Chest,Push,True,False,False,False,False,False,
Cable Incline Chest press,Chest,Push,True,False,False,False,False,False,
Cable Decline Chest Press,Chest,Push,True,False,False,False,False,False,
Cable Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Incline Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Decline Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Kneeling Chest Press,Chest,Push,True,False,False,False,False,False,
Cable Kneeling Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Incline Chest Press,Chest,Push,True,False,False,False,False,False,
Cable Kneeling Incline Chest Fly,Chest,Push,False,False,False,False,False,False,
Cable Kneeling Decline Chest press,Chest,Push,True,False,False,False,False,False,
Cable Kneeling Decline Chest Fly,Chest,Push,False,False,False,False,False,False,
BB Chest Press,Chest,Push,True,False,True,False,False,False,
BB Incline Press,Chest,Push,True,False,True,False,False,False,
BB Decline Press,Chest,Push,True,False,True,False,False,False,
BB Close Grip Incline Chest Press,Chest,Push,True,False,True,False,False,False,
BB Close Grip Decline  Chest Press,Chest,Push,True,False,True,False,False,False,
Band Standing press,Chest,Push,True,True,False,False,False,False,
Band Incline Standing Press,Chest,Push,True,True,False,False,False,False,
Band Decline Standing Press,Chest,Push,True,True,False,False,False,False,
Band Fly,Chest,Push,False,True,False,False,False,False,
Band Incline Fly,Chest,Push,False,True,False,False,False,False,
Band Decline Fly,Chest,Push,False,True,False,False,False,False,
Band Tricep extension,Tricep,Push,False,True,False,False,False,False,
Stabilizing Plate Press,Chest,Push,False,False,False,False,False,False,
Stabilizing Plate incline Press,Chest,Push,False,False,False,False,False,False,
Stabilizing Decline Plate Press,Chest,Push,False,False,False,False,False,False,
DB Tricep extension,Tricep,Push,False,False,False,False,True,False,
DB Tricep Kickback,Tricep,Push,False,False,False,False,True,False,
DB Close Grip Press,Tricep,Push,False,False,False,False,True,False,
DB Close Grip incline Press,Tricep,Push,False,False,False,False,True,False,
DB Close Grip Decline Press,Tricep,Push,False,False,False,False,True,False,
BB Skull Crusher,Tricep,Push,False,False,True,False,False,False,
DB Skull Crusher,Tricep,Push,False,False,False,False,True,False,
KB Tricep Extension,Tricep,Push,False,False,False,True,False,False,
Tricep Cable Push-down,Tricep,Push,False,False,False,False,False,False,
Tricep Cable Overhead Extension,Tricep,Push,False,False,False,False,False,False,
Body Weight Dips,Tricep,Push,False,True,False,False,False,False,
Weighted Dips,Tricep,Push,False,False,False,False,False,False,
Dip Machine,Tricep,Push,False,False,False,False,False,False,
Tricep Extension Machine,Tricep,Push,False,False,False,False,False,False,
Rope Cable Tricep Extension,Tricep,Push,False,False,False,False,False,False,
Push Ups,Chest,Push,False,True,False,False,False,False,
Close Grip Push Ups,Tricep,Push,False,True,False,False,False,False,
Diamond Push Ups,Tricep,Push,False,True,False,False,False,False,
Incline Push Ups,Chest,Push,False,True,False,False,False,False,
Inverted Push ups,Chest,Push,False,True,False,False,False,False,
Seated Tricep Dips,Tricep,Push,False,True,False,False,False,False,
Lat Pull Down,Back,Pull,True,False,False,False,False,False,
DB Bent Over Row,Back,Pull,True,False,False,False,True,False,
BB standing Bent over Row,Back,Pull,True,False,True,False,False,False,
Cable standing Row,Back,Pull,True,False,False,False,False,False,
Straight Arm standing Lat pulldown,Back,Pull,True,False,False,False,False,False,
Seated Cable Row,Back,Pull,True,False,False,False,False,False,
Cable Isolated Lat pulldown,Back,Pull,True,False,False,False,False,False,
T-Bar Row,Back,Pull,True,False,False,False,False,False,
T-Bar single Arm Row,Back,Pull,True,False,False,False,False,False,
KB Single Arm Row,Back,Pull,True,False,False,True,False,False,
Cable Standing UnderRow to the hip,Back,Pull,True,False,False,False,False,False,
Cable Seated Underrow to the Hip,Back,Pull,True,False,False,False,False,False,
KB Bent Over Under Row,Back,Pull,True,False,False,True,False,False,
Kneeling Isolated Cable Lat Pull,Back,Pull,True,False,False,False,False,False,
Band Lat Row,Back,Pull,False,True,False,False,False,False,
Band Seated Row,Back,Pull,False,True,False,False,False,False,
Band Straight Arm Lat Push Down,Back,Pull,False,True,False,False,False,False,
Band Single Arm bent Row,Back,Pull,False,True,False,False,False,False,
DB Shoulder Press,Shoulder,Push,True,False,False,False,True,False,
BB Shoulder Press,Shoulder,Push,True,False,True,False,False,False,
DB Shoulder Fly,Shoulder,Push,False,False,False,False,True,False,
DB Isolated Shoulder Press,Shoulder,Push,False,False,False,False,True,False,
DB Lateral Raise,Shoulder,Push,False,False,False,False,True,False,
DB Upright Row,Shoulder,Push,False,False,False,False,True,False,
DB Front Raises,Shoulder,Push,False,False,False,False,True,False,
DB Inverted Front Raises,Shoulder,Push,False,False,False,False,True,False,
DB Bent Over Rear Delt Flys,Shoulder,Push,False,False,False,False,True,False,
DB Bent Rear Delt Shoulder Row,Shoulder,Push,False,False,False,False,True,False,
Cable Rear Delt Flys,Shoulder,Push,False,False,False,False,False,False,
Cable Rear Delt Pulls to face,Shoulder,Push,False,False,False,False,False,False,
Cable Lateral Raise,Shoulder,Push,False,False,False,False,False,False,
Cable Frontal Raise,Shoulder,Push,False,False,False,False,False,False,
Cable Upright Row,Shoulder,Push,False,False,False,False,False,False,
Cable Standing Shoulder Press,Shoulder,Push,False,False,False,False,False,False,
KB Frontal Raise,Shoulder,Push,False,False,False,True,False,False,
KB Upright Row,Shoulder,Push,False,False,False,True,False,False,
KB Single Arm Upright Right Row,Shoulder,Push,False,False,False,True,False,False,
KB Lateral Raise,Shoulder,Push,False,False,False,True,False,False,
KB Single Arm Press,Shoulder,Push,False,False,False,True,False,False,
Band Shoulder Press,Shoulder,Push,False,True,False,False,False,False,
Band Lateral Raise,Shoulder,Push,False,True,False,False,False,False,
Band Rear Delt Bent Raise,Shoulder,Push,False,True,False,False,False,False,
Band Upright Row,Shoulder,Push,False,True,False,False,False,False,
Band Frontal Raise,Shoulder,Push,False,True,False,False,False,False,
DB Bicep Curl,Bicep,Pull,False,False,False,False,True,False,
DB Reverse Bicep Curl,Bicep,Pull,False,False,False,False,True,False,
DB Hammer Curl,Bicep,Pull,False,False,False,False,True,False,
DB Drag Curl,Bicep,Pull,False,False,False,False,True,False,
BB Bicep Curl,Bicep,Pull,False,False,True,False,False,False,
BB Stabilized Bicep Curl,Bicep,Pull,False,False,True,False,False,False,
BB Reverse Bicep Curl,Bicep,Pull,False,False,True,False,False,False,
BB Drag Curl,Bicep,Pull,False,False,True,False,False,False,
Cable Robe Hammer Curl,Bicep,Pull,False,False,False,False,False,False,
Cable Drag Curl,Bicep,Pull,False,False,False,False,False,False,
Cable Isolated Bicep Curl,Bicep,Pull,False,False,False,False,False,False,
Cable Overhead Bicep Curl,Bicep,Pull,False,False,False,False,False,False,
Band Bicep Curl,Bicep,Pull,False,True,False,False,False,False,
Band Hammer Curl,Bicep,Pull,False,True,False,False,False,False,
Band Reverse Grip Curl,Bicep,Pull,False,True,False,False,False,False,
Landmine Pull and Press,Back,Pull,True,False,False,False,False,False,
Landmine Single Arm Row,Back,Pull,True,False,False,False,False,False"));

        $count = 0;
        foreach ($str as $idx => $val) {
            switch ($count) {
                case 1:
                    $results['Muscle Group'][] = $val;

                    break;

                case 2:
                    $results['Direction'][] = $val;

                    break;

                case 3:
                    $results['Major Lift'][] = $val;

                    break;

                case 4:
                    $results['Body or Band'][] = $val;

                    break;

                case 5:
                    $results['BB'][] = $val;

                    break;

                case 6:
                    $results['KB'][] = $val;

                    break;

                case 7:
                    $results['DB'][] = $val;

                    break;

                case 8:
                    $results['TRX'][] = $val;

                    break;

                default:
                    $results['Exercise'][] = $val;
            }
            if ($count == 8) {
                $count = 0;
            } else {
                $count++;
            }
        }

        return $results;
    }

    public function getCSV(string $csv)
    {
        return str_getcsv($csv);
    }
}
