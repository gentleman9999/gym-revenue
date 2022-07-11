<template>
    <LayoutHeader title="Workout Generator">
        <h2 class="font-semibold text-xl leading-tight">
            Workout Generator (Preview)
        </h2>
    </LayoutHeader>

    <div class="py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-300 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="workout-generator-container">
                    <workout-form
                        @submit="generateRoutine"
                        :reset="resetForm"
                    ></workout-form>
                </div>
            </div>
            <div
                class="bg-base-300 overflow-hidden shadow-xl sm:rounded-lg mt-10"
                v-show="showRoutine"
            >
                <div class="workout-routine-container">
                    <generated-workout
                        v-if="showRoutine"
                        :workout="solution"
                        :rest-msg="restMessage"
                        @reset="resetEverything"
                    ></generated-workout>
                </div>
            </div>
        </div>
    </div>
    <daisy-modal id="generationModal" ref="generationModal">
        <font-awesome-icon :icon="['far', whichSmile]" size="8x" spin />
        <br />
        <div class="mt-6">
            <p>Generating your Workout Routine....</p>
        </div>
    </daisy-modal>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { computed } from "@vue/reactivity";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";

import WorkoutForm from "@/Presenters/WorkoutGenerator/WorkoutForm.vue";
import GeneratedWorkout from "@/Presenters/WorkoutGenerator/GeneratedWorkout.vue";

import DaisyModal from "@/Components/DaisyModal.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faSmile, faSmileWink } from "@fortawesome/pro-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faSmile, faSmileWink);

const props = defineProps({
    core: {
        type: null,
    },
    upper: {
        type: null,
    },
    lower: {
        type: null,
    },
});

const generationModal = ref(null);
const showModal = ref(false);
const showRoutine = ref(false);
const resetForm = ref(false);
const smile = ref("smile");

const bodyBand = ref(false);
const barType = ref("");
const injury = ref("");
const coreDaily = ref(false);
const goal = ref("maintain");
const rest = ref(2);
const injuryExclusions = ref({
    shoulder: { "Military Press": "", "Upright Rows": "" },
    neck: {
        "BB Squat": "",
        "BB Squat to High Pull": "",
        "BB Squat to Press": "",
        "Overhead Press": "",
        "Upright Rows": "",
        Shrugs: "",
    },

    back: {
        "BB Squat": "",
        "BB Squat to High Pull": "",
        "BB Squat to Press": "",
        "Front Squat": "",
        Deadlifts: "",
    },

    hip: [],

    knee: {
        "HIIT Lateral Movements": "",
        "Step Ups": "",
        "Leg Extensions": "",
        "Walking Lunges": "",
        "Forward Lunges": "",
    },

    ankle: { Lunges: "" },
});
const week = ref({
    Sunday: 1,
    Monday: 2,
    Tuesday: 3,
    Wednesday: 4,
    Thursday: 5,
    Friday: 6,
    Saturday: 7,
});
const legDays = ref({ Sunday: "", Thursday: "" });
const restMessage = ref("Rest -- low intensity cardio recommended");
const solution = ref("");
const plans = ref("");
watch([showModal], () => {
    if (showModal.value) {
        generationModal.open();
        setTimeout(function () {
            generationModal.close();
            showRoutine.value = true;
        }, 3500);
    }
});

watch([plans], () => {
    if (plans.value !== "") {
        console.log(
            "Setting up the workout solution for the GeneratedWorkout to react to",
            plans.value
        );
        solution.value = displayWorkout(plans.value);
    }
});
function resetEverything() {
    showRoutine.value = false;
    resetForm.value = true;
    setTimeout(function () {
        bodyBand.value = false;
        barType.value = "";
        coreDaily.value = false;
        goal.value = "";
        rest.value = 0;
        solution.value = "";
        plans.value = "";
        resetForm.value = false;
        showModal.value = false;
    }, 500);
}
function filterValidValues(df, column, value) {
    let validKeys = [];
    for (let idx in df[column]) {
        if (df[column][idx] === value) {
            validKeys.push(idx);
        }
    }

    console.log("validKeys", validKeys);

    let temp = {};
    for (let col in df) {
        temp[col] = {};
        for (let idy in validKeys) {
            let key = validKeys[idy];
            temp[col][idy] = df[col][key];
        }
    }

    df = temp;
    console.log("New df - ", df);

    return df;
}
function filterSampleValues(df, validKeys) {
    let temp = {};
    for (let col in df) {
        temp[col] = {};
        for (let idy in validKeys) {
            let key = validKeys[idy];
            if (key in df[col]) {
                temp[col][idy] = df[col][key];
            }
        }
    }

    df = temp;
    console.log("Sample df - ", df);

    return df;
}
function getLower(df, day, nMax) {
    /**
     * BIZ RULES
     * 1. Two Leg Days Per Week
     * 2. Split Deadlifts and Squats into different days
     * 3. Leg days should have a total of 6 exercises per day
     * 4. 2 Lower Body days should be split push and pull
     *
     * CONSTRAINTS
     * 1. Deadlift & Pull first
     * 2. Squat and Pull Second
     *
     * Optional Preferences
     * 1. Bar type (KB, DB, BB)
     */
    console.log("getLower() df - ", df);

    if (day === 1) {
        console.log("Doing Sunday filters");
        df = filterValidValues(df, "Direction", "Pull");
    } else if (day === 2) {
        console.log("Doing Monday filters");
        let dfPush = filterValidValues(df, "Direction", "Push");
        let dfBoth = filterValidValues(df, "Direction", "Pull");
        let temp = {};
        for (let dgm in dfPush) {
            if (!(dgm in temp)) {
                temp[dgm] = [];
            }
            for (let dgm2 in dfPush[dgm]) {
                temp[dgm].push(dfPush[dgm][dgm2]);
            }
        }

        for (let dgm in dfBoth) {
            if (!(dgm in temp)) {
                temp[dgm] = [];
            }
            for (let dgm2 in dfBoth[dgm]) {
                temp[dgm].push(dfBoth[dgm][dgm2]);
            }
        }

        df = temp;
        console.log("GetLower Monday for " + week.value[day], df);
    }

    let nMajor = parseInt(nMax / 3);
    console.log("nMajor - " + nMajor);
    let nMinor = parseInt(nMax - nMajor);
    console.log("nMinor - " + nMinor);

    let dfMajor = filterValidValues(df, "Major Lift", "True");
    let dfMinor = filterValidValues(df, "Major Lift", "False");

    if (barType.value !== "") {
        dfMajor = filterValidValues(dfMajor, barType.value, "True");
    }

    let dfMajSize = 0;
    let dfMinSize = 0;
    for (let x in dfMajor["Exercise"]) {
        dfMajSize++;
    }
    for (let x in dfMinor["Exercise"]) {
        dfMinSize++;
    }

    console.log("getLower dfMajSize " + dfMajSize);
    console.log("getLower dfMinSize " + dfMinSize);
    let sampleMaj = [];
    for (let x = 0; x < nMajor; x++) {
        sampleMaj.push(Math.floor(Math.random() * dfMajSize));
    }
    let sampleMin = [];
    for (let x = 0; x < nMinor; x++) {
        sampleMin.push(Math.floor(Math.random() * dfMinSize));
    }
    console.log("getLower Major Randos", sampleMaj);
    console.log("getLower Minor Randos", sampleMin);
    dfMajor = filterSampleValues(dfMajor, sampleMaj);
    dfMinor = filterSampleValues(dfMinor, sampleMin);

    let temp = {};
    for (let dgm in dfMajor) {
        if (!(dgm in temp)) {
            temp[dgm] = [];
        }
        for (let dgm2 in dfMajor[dgm]) {
            temp[dgm].push(dfMajor[dgm][dgm2]);
        }
    }

    for (let dgm in dfMinor) {
        if (!(dgm in temp)) {
            temp[dgm] = [];
        }
        for (let dgm2 in dfMinor[dgm]) {
            temp[dgm].push(dfMinor[dgm][dgm2]);
        }
    }

    df = temp;

    return df;
}
function getCore(df, n) {
    let dfCoreSize = 0;
    for (let x in df["Exercise"]) {
        dfCoreSize++;
    }

    let sampleCore = [];
    for (let x = 0; x < n; x++) {
        sampleCore.push(Math.floor(Math.random() * dfCoreSize));
    }

    return filterSampleValues(df, sampleCore);
}
function getUpper(df, day, nGrp, nMax) {
    /**
     * BIZ RULES
     * 1. Upper Body Workouts Should have a minimum of 3 exercise per muscle group
     * 2. No more than 10 exercises per day for upper body
     * 3. Upper body day should have only 4 major lifts
     * 4. Shoulder exercises should be incorporated in every upper body workout
     * 5. When push and pull days are combined, either push or pull should be primary focus
     *
     * CONSTRAINTS
     * 1. Split into day 1, 2, 3, 4
     * 2. Day 1 & 3
     * 3. Day 2 & 4
     *
     * Optional Preferences
     * 1. Bar type (KB, DB, BB)
     */
    let major_groups = {
        Chest: "",
        Back: "",
        Shoulder: "",
    };
    let groups = { Back: "", Bicep: "", Shoulder: "" };

    if (day % 2 === 1) {
        groups = { Chest: "", Tricep: "", Shoulder: "" };
    }

    let dfMajor = filterValidValues(df, "Major Lift", "True");
    let dfMinor = filterValidValues(df, "Major Lift", "False");

    if (barType.value !== "") {
        dfMajor = filterValidValues(df, barType.value, "True");
    }

    let majorEx = [];
    let minorEx = [];

    for (let group in groups) {
        let dfGroupMinor = filterValidValues(dfMinor, "Muscle Group", group);
        if (group in major_groups) {
            let dfGroupMajor = filterValidValues(
                dfMajor,
                "Muscle Group",
                group
            );

            let dfMajSize = 0;
            let dfMinSize = 0;
            for (let x in dfGroupMajor["Exercise"]) {
                dfMajSize++;
            }
            for (let x in dfGroupMinor["Exercise"]) {
                dfMinSize++;
            }

            console.log("dfMajSize " + dfMajSize);
            let sampleMaj = [];
            for (let x = 0; x < 2; x++) {
                sampleMaj.push(Math.floor(Math.random() * dfMajSize));
            }
            let sampleMin = [];
            for (let x = 0; x < 1; x++) {
                sampleMin.push(Math.floor(Math.random() * dfMinSize));
            }
            console.log("Major Randos", sampleMaj);
            console.log("Minor Randos", sampleMin);

            dfGroupMajor = filterSampleValues(dfGroupMajor, sampleMaj);
            dfGroupMinor = filterSampleValues(dfGroupMinor, sampleMin);

            console.log("MajorEx Check - ", majorEx);

            for (let dgm in dfGroupMajor) {
                if (!(dgm in majorEx)) {
                    majorEx[dgm] = [];
                }
                for (let dgm2 in dfGroupMajor[dgm]) {
                    majorEx[dgm].push(dfGroupMajor[dgm][dgm2]);
                }
            }

            for (let dgm in dfGroupMinor) {
                if (!(dgm in minorEx)) {
                    minorEx[dgm] = [];
                }
                for (let dgm2 in dfGroupMinor[dgm]) {
                    minorEx[dgm].push(dfGroupMinor[dgm][dgm2]);
                }
            }
        } else {
            let dfMinSize = 0;
            for (let x in dfGroupMinor["Exercise"]) {
                dfMinSize++;
            }
            console.log("dfMinSize " + dfMinSize);

            let sampleMin = [];
            for (let x = 0; x < 3; x++) {
                sampleMin.push(Math.floor(Math.random() * dfMinSize));
            }
            console.log("Minor Randos", sampleMin);
            dfGroupMinor = filterSampleValues(dfGroupMinor, sampleMin);

            for (let dgm in dfGroupMinor) {
                if (!(dgm in minorEx)) {
                    minorEx[dgm] = [];
                }
                for (let dgm2 in dfGroupMinor[dgm]) {
                    minorEx[dgm].push(dfGroupMinor[dgm][dgm2]);
                }
            }
        }
    }

    let results = {};

    for (let dgm in majorEx) {
        if (!(dgm in results)) {
            results[dgm] = [];
        }
        for (let dgm2 in majorEx[dgm]) {
            results[dgm].push(majorEx[dgm][dgm2]);
        }
    }

    for (let dgm in minorEx) {
        if (!(dgm in results)) {
            results[dgm] = [];
        }
        for (let dgm2 in minorEx[dgm]) {
            results[dgm].push(minorEx[dgm][dgm2]);
        }
    }

    console.log("getUpper Results - ", results);
    return results;
}
function generateRoutine(form) {
    console.log("Setting showModal to true", form);
    showModal.value = true;

    // take the form and update the data variables here
    for (let x in form) {
        switch (x) {
            case "selectedBarType":
                barType.value = form[x];
                break;

            case "selectedBodyBand":
                bodyBand.value = form[x];
                break;

            case "selectedCoreDaily":
                coreDaily.value = form[x];
                break;

            case "selectedGoal":
                goal.value = form[x];
                break;

            case "selectedInjury":
                injury.value = form[x];
                break;

            case "selectedRest":
                rest.value = form[x];
                break;
        }
    }

    plans.value = planWeek();
}
function planWeek() {
    let results = false;

    let dfLower = lower.value;
    let dfUpper = upper.value;
    let dfCore = core.value;

    // sort if bodyband
    if (bodyBand.value) {
        dfLower = filterValidValues(dfLower, "Body or Band", "True");
        dfUpper = filterValidValues(dfUpper, "Body or Band", "True");
        dfCore = filterValidValues(dfCore, "Body or Band", "True");
    }

    // sort if injury
    if (injury.value !== "" && injury.value !== "none") {
        console.log("Applying injury considerations", {
            injury: injury.value,
            exclusions: injury.valueExclusions[injury.value],
        });
        let exclusions = injury.valueExclusions[injury.value];
        let lowTemp = [];
        for (let l in dfLower["Exercise"]) {
            if (!(dfLower["Exercise"][l] in exclusions)) {
                lowTemp.push(l);
            }
        }

        let upperTemp = [];
        for (let l in dfUpper["Exercise"]) {
            if (!(dfUpper["Exercise"][l] in exclusions)) {
                upperTemp.push(l);
            }
        }

        let coreTemp = [];
        for (let l in dfCore["Exercise"]) {
            if (!(dfCore["Exercise"][l] in exclusions)) {
                coreTemp.push(l);
            }
        }

        dfLower = filterSampleValues(dfLower, lowTemp);
        dfUpper = filterSampleValues(dfUpper, upperTemp);
        dfCore = filterSampleValues(dfCore, coreTemp);
    }

    let plan = {};
    let restDays = getSchedule;
    let dayLower = 1;
    let dayUpper = 1;
    let dayTotal = 1;

    for (let day in week.value) {
        console.log("Planning " + day);
        console.log("RestDays ", restDays);
        let coreEx = [];

        if (day in restDays) {
            plan[day] = restMessage.value;
        } else if (day in legDays.value) {
            // call getLower function
            let mainEx = getLower(dfLower, dayLower, 6);
            console.log("GetLower on " + day, mainEx);
            coreEx = [];

            if (coreDaily.value) {
                console.log("getCore on " + day);
                coreEx = getCore(dfCore, 4);
                console.log("getCore on " + day, coreEx);
            } else if (dayTotal % 2 === 1) {
                // call get core function
                coreEx = getCore(dfCore, 4);
                console.log("getCore on " + day, coreEx);
            }

            plan[day] = {};

            for (let dgm in mainEx) {
                if (!(dgm in plan[day])) {
                    plan[day][dgm] = [];
                }
                for (let dgm2 in mainEx[dgm]) {
                    plan[day][dgm].push(mainEx[dgm][dgm2]);
                }
            }

            for (let dgm in coreEx) {
                if (!(dgm in plan[day])) {
                    plan[day][dgm] = [];
                }
                for (let dgm2 in coreEx[dgm]) {
                    plan[day][dgm].push(coreEx[dgm][dgm2]);
                }
            }
            dayLower++;
            dayTotal++;
        } else {
            let mainEx = getUpper(dfUpper, dayUpper, 3, 10);
            console.log("getUpper on " + day, mainEx);
            coreEx = [];

            if (coreDaily.value) {
                coreEx = getCore(dfCore, 4);
                console.log("getCore on " + day, coreEx);
            } else if (dayTotal % 2 === 1) {
                // call get core function
                coreEx = getCore(dfCore, 4);
                console.log("getCore on " + day, coreEx);
            }

            plan[day] = {};

            for (let dgm in mainEx) {
                if (!(dgm in plan[day])) {
                    plan[day][dgm] = [];
                }
                for (let dgm2 in mainEx[dgm]) {
                    plan[day][dgm].push(mainEx[dgm][dgm2]);
                }
            }

            for (let dgm in coreEx) {
                if (!(dgm in plan[day])) {
                    plan[day][dgm] = [];
                }
                for (let dgm2 in coreEx[dgm]) {
                    plan[day][dgm].push(coreEx[dgm][dgm2]);
                }
            }

            dayUpper++;
            dayTotal++;
        }
        console.log("plan for " + day, plan[day]);

        results = plan;
    }

    return results;
}
function displayWorkout(plannedWeek) {
    let results = false;

    if (plannedWeek !== undefined) {
        let rep_dict = {
            gain: ["6-8", "3-5"],
            maintain: ["8-12", "15-20"],
        };

        let exCore = core.value;

        let coreList = {};
        for (let c in exCore["Exercise"]) {
            coreList[exCore["Exercise"][c]] = "";
        }

        console.log("CoreList - ", coreList);

        let plans = {};
        for (let day in week.value) {
            plans[day] = "";

            let exercise_list = plannedWeek[day];

            if (exercise_list === restMessage.value) {
                plans[day] = exercise_list;
            } else {
                exercise_list = plannedWeek[day]["Exercise"];
                let exCount = 0;
                plans[day] = [];
                for (let exercise in exercise_list) {
                    let ex = exercise_list[exercise];
                    if (ex in coreList) {
                        console.log(day + "- Curating Core workout:" + ex);
                        let row = {
                            exercise: ex,
                            sets: "1",
                            reps: rep_dict[goal.value][exCount % 2],
                            duration: "0:30/ea.",
                        };

                        plans[day].push(row);
                    } else {
                        console.log(day + "- Curating Non-Core workout:" + ex);
                        let row = {
                            exercise: ex,
                            sets: "3",
                            reps: rep_dict[goal.value][exCount % 2],
                            duration: "No Limit.",
                        };

                        plans[day].push(row);
                    }

                    exCount++;
                }
            }

            results = plans;
        }
    }

    return results;
}

const whichSmile = computed({
    get() {
        if (smile.value === "smile") {
            setTimeout(function () {
                smile.value = "smile-wink";
            }, 1750);
            return "smile-wink";
        } else {
            setTimeout(function () {
                smile.value = "smile";
            }, 1750);
            return "smile";
        }
    },
});

const getSchedule = computed({
    get() {
        let results = false;
        switch (parseInt(rest.value)) {
            case 1:
                results = { Wednesday: "" };
                break;
            case 2:
                results = { Tuesday: "", Friday: "" };
                break;
            case 3:
                results = { Monday: "", Wednesday: "", Friday: "" };
                break;
            case 4:
                results = {
                    Monday: "",
                    Wednesday: "",
                    Friday: "",
                    Saturday: "",
                };
                break;
            default:
        }
        return results;
    },
});
onMounted(() => {
    console.log("WorkoutGeneratorContainer mounted!", {
        core: props.core.value,
        upper: props.upper.value,
        lower: props.lower.value,
    });
});
</script>

<style scoped>
.workout-generator-container {
    width: 100%;
    height: 100%;
}

.inner-workout-container {
    display: flex;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    height: 100%;
}
</style>
