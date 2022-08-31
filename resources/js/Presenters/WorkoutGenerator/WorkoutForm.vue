<template>
    <div class="box box-solid box-primary">
        <div class="box-header flex py-2">
            <h3 class="box-title ml-4">Interview</h3>
            <div class="box-tools pull-right mr-4 px-2">
                <!-- Buttons, labels, and many other things can be placed here! -->
                <!-- Here is a label for example -->
                <span class="label" @click="showForm = !showForm"
                    ><small>{{ showForm ? "Hide" : "Show" }}</small></span
                >
            </div>
        </div>

        <slide-up-down v-model="showForm">
            <div class="box-body p-6">
                <div class="inner-box-body">
                    <form role="form" class="text-center">
                        <div class="mb-6">
                            <p>
                                <i
                                    >Directions: Fill out this short
                                    questionnaire and click
                                    <b>Get Workout Schedule</b> at the end to
                                    generate a customized workout routine for
                                    the week.</i
                                >
                            </p>
                        </div>
                        <div class="form-group" v-show="currentQuestion === 1">
                            <div class="pb-4">
                                <label
                                    :style="{
                                        'font-weight': 800,
                                        'font-size': '1.25em',
                                        'text-decoration': 'underline',
                                    }"
                                    >1. How should exercises be based?</label
                                >
                            </div>

                            <div class="flex space-x-2 mb-2">
                                <input
                                    type="radio"
                                    name="optionsRadios"
                                    id="optionsRadios1"
                                    value="true"
                                    class="radio"
                                    v-model="selectedBodyBand"
                                />
                                <div>Body weight- and band-based</div>
                            </div>

                            <div class="flex space-x-2 mb-2">
                                <input
                                    class="radio"
                                    type="radio"
                                    name="optionsRadios"
                                    id="optionsRadios2"
                                    value="false"
                                    v-model="selectedBodyBand"
                                />
                                <div>Weighted-based</div>
                            </div>
                        </div>

                        <div
                            class="form-group flex q2"
                            v-show="currentQuestion === 2"
                        >
                            <label>2. Dumbells, Barbells or Kettlebells?</label>
                            <div>
                                <select
                                    class="form-control w-1/2 mt-4 mb-2"
                                    v-model="selectedBarType"
                                >
                                    <option
                                        v-for="(val, col) in drops.barType"
                                        :value="col"
                                    >
                                        {{ val }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" v-show="currentQuestion === 3">
                            <label
                                >3. Any specific injuries to take into account?
                                (Select Only One)</label
                            >
                            <div>
                                <select
                                    class="form-control w-1/2 mt-4 mb-2"
                                    v-model="selectedInjury"
                                >
                                    <option
                                        v-for="(val, col) in drops.injury"
                                        :value="col"
                                    >
                                        {{ val }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" v-show="currentQuestion === 4">
                            <label>4. Throw in Daily Core Workouts?</label>
                            <div class="flex space-x-2 mb-2">
                                <input
                                    type="radio"
                                    name="coreOptionsRadios"
                                    id="optionsRadios3"
                                    value="true"
                                    class="radio"
                                    v-model="selectedCoreDaily"
                                />
                                <div>Yes</div>
                            </div>

                            <div class="flex space-x-2 mb-2">
                                <input
                                    type="radio"
                                    name="coreOptionsRadios"
                                    id="optionsRadios4"
                                    value="false"
                                    class="radio"
                                    v-model="selectedCoreDaily"
                                />
                                <div>No</div>
                            </div>
                        </div>

                        <div class="form-group" v-show="currentQuestion === 5">
                            <label>5. Goal Focus?</label>
                            <div>
                                <select
                                    class="form-control w-1/2 mt-4 mb-2"
                                    v-model="selectedGoal"
                                >
                                    <option
                                        v-for="(val, col) in drops.goal"
                                        :value="col"
                                    >
                                        {{ val }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" v-show="currentQuestion === 6">
                            <label>6. # of Rest Days?</label>
                            <div>
                                <select
                                    class="form-control w-1/2 mt-4 mb-2"
                                    v-model="selectedRest"
                                >
                                    <option
                                        v-for="(val, col) in drops.rest"
                                        :value="col"
                                    >
                                        {{ val }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div
                            :class="
                                currentQuestion !== 1
                                    ? 'box-footer'
                                    : 'box-footer-just-next'
                            "
                            class="flex py-2"
                        >
                            <div
                                class="box-tools pull-right ml-4 px-2"
                                v-show="currentQuestion !== 1"
                                @click="prevQuestion()"
                            >
                                <!-- Buttons, labels, and many other things can be placed here! -->
                                <!-- Here is a label for example -->
                                <span class="label"
                                    ><small
                                        ><font-awesome-icon
                                            icon="arrow-circle-left"
                                        />
                                        Prev</small
                                    ></span
                                >
                            </div>

                            <div
                                class="box-tools pull-right mr-4 px-2"
                                @click="nextQuestion()"
                                v-show="currentQuestion !== 6"
                            >
                                <!-- Buttons, labels, and many other things can be placed here! -->
                                <!-- Here is a label for example -->
                                <span class="label"
                                    ><small
                                        >Next
                                        <font-awesome-icon
                                            icon="arrow-circle-right" /></small
                                ></span>
                            </div>
                            <div
                                class="box-tools pull-right mr-4 px-2"
                                @click="submitForm()"
                                v-show="currentQuestion === 6"
                            >
                                <!-- Buttons, labels, and many other things can be placed here! -->
                                <!-- Here is a label for example -->
                                <span class="label"
                                    ><small
                                        >Get Workout Schedule
                                        <font-awesome-icon
                                            icon="arrow-circle-right" /></small
                                ></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </slide-up-down>
    </div>
</template>
<style scoped>
input.radio:focus:focus {
    box-shadow: 0 0 0 4px hsl(var(--b1)) inset, 0 0 0 4px hsl(var(--b1)) inset,
        var(--focus-shadow);
}
</style>
<script setup>
import { ref, onMounted, watch, defineEmits } from "vue";
import { toastInfo, toastError, toastSuccess } from "@/utils/createToast";
import SlideUpDown from "vue3-slide-up-down";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowCircleRight,
    faArrowCircleLeft,
} from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faArrowCircleRight, faArrowCircleLeft);

const props = defineProps({
    reset: {
        type: Boolean,
    },
});
const showForm = ref(true);
const currentQuestion = ref(1);
const selectedBodyBand = ref("");
const selectedBarType = ref("nothing");
const selectedInjury = ref("");
const selectedCoreDaily = ref("");
const selectedGoal = ref("");
const selectedRest = ref(0);
const drops = ref({
    barType: {
        "": "None",
        BB: "Barbell",
        DB: "Dumbell",
        KB: "Kettlebell",
    },
    injury: {
        none: "None",
        neck: "Neck",
        back: "Back",
        shoulder: "Shoulder",
        knee: "Knee",
        ankle: "Ankle",
    },
    goal: {
        gain: "Gain",
        maintain: "Maintain",
    },
    rest: {
        1: "1 Day",
        2: "2 Days",
        3: "3 Days",
        4: "4 Days",
    },
});
watch([props.reset], () => {
    if (reset.value) {
        currentQuestion.value = 1;
        selectedBodyBand.value = "";
        selectedBarType.value = "nothing";
        selectedInjury.value = "";
        selectedCoreDaily.value = "";
        selectedGoal.value = "";
        selectedRest.value = 0;
    }
});
const nextQuestion = () => {
    if (validateQuestion()) {
        if (currentQuestion.value < 6) {
            const toastText = `Awesome! Moving on to question ${
                currentQuestion.value + 1
            } of 6`;
            toastInfo(toastText);
            currentQuestion.value++;
        }
    } else {
        toastError("Please complete the question before continuing.");
    }
};

const prevQuestion = () => {
    if (currentQuestion.value > 1) {
        currentQuestion.value--;
    }
};

const emit = defineEmits(["submit"]);

const submitForm = () => {
    if (currentQuestion.value === 6 && validateQuestion()) {
        emit("submit", {
            selectedBodyBand: selectedBodyBand.value,
            selectedBarType: selectedBarType.value,
            selectedInjury: selectedInjury.value,
            selectedCoreDaily: selectedCoreDaily.value,
            selectedGoal: selectedGoal.value,
            selectedRest: selectedRest.value,
        });

        toastSuccess(`Great! Generating A workout for you now!`, {
            timeout: 5000,
        });
    } else {
        toastError("Complete the whole form before continuing.", {
            timeout: 10000,
        });
    }
};
const validateQuestion = () => {
    let v = false;

    switch (currentQuestion.value) {
        case 1:
            v = selectedBodyBand.value !== "";
            break;

        case 2:
            v = selectedBarType.value !== "nothing";
            break;

        case 3:
            v = selectedInjury.value !== "";
            break;

        case 4:
            v = selectedCoreDaily.value !== "";
            break;

        case 5:
            v = selectedGoal.value !== "";
            break;

        case 6:
            v = selectedRest.value !== 0;
            break;
    }

    return v;
};
</script>

<style scoped>
@media screen {
    .label {
        cursor: pointer;
    }

    .box-header {
        justify-content: space-between;
        background-color: #3c8dbc;
    }

    .box-footer {
        justify-content: space-between;
        background-color: #fff;
    }

    .box-tools:hover {
        border: 2px solid #000;
        cursor: pointer;
    }

    .box-footer-just-next {
        justify-content: flex-end;
        background-color: #fff;
    }

    .pull-right {
        background-color: orange;
        color: #fff;
        font-weight: 800;
    }

    .box-title {
        color: #fff;
        font-weight: 800;
    }

    .q2 {
        flex-flow: column;
    }

    .form-group > label {
        font-weight: 800;
        font-size: 1.25em;
        text-decoration: underline;
    }
}
</style>
