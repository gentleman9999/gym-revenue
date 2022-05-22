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

                            <div class="radio">
                                <label>
                                    <input
                                        type="radio"
                                        name="optionsRadios"
                                        id="optionsRadios1"
                                        value="true"
                                        v-model="selectedBodyBand"
                                    />
                                    Body weight- and band-based
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input
                                        type="radio"
                                        name="optionsRadios"
                                        id="optionsRadios2"
                                        value="false"
                                        v-model="selectedBodyBand"
                                    />
                                    Weighted-based
                                </label>
                            </div>
                        </div>

                        <div
                            class="form-group flex q2"
                            v-show="currentQuestion === 2"
                        >
                            <label>2. Dumbells, Barbells or Kettlebells?</label>
                            <div>
                                <select
                                    class="form-control w-1/2 mt-4"
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
                                    class="form-control w-1/2 mt-4"
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
                            <div class="radio">
                                <label>
                                    <input
                                        type="radio"
                                        name="coreOptionsRadios"
                                        id="optionsRadios3"
                                        value="true"
                                        v-model="selectedCoreDaily"
                                    />
                                    Yes
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input
                                        type="radio"
                                        name="coreOptionsRadios"
                                        id="optionsRadios4"
                                        value="false"
                                        v-model="selectedCoreDaily"
                                    />
                                    No
                                </label>
                            </div>
                        </div>

                        <div class="form-group" v-show="currentQuestion === 5">
                            <label>5. Goal Focus?</label>
                            <div>
                                <select
                                    class="form-control w-1/2 mt-4"
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
                                    class="form-control w-1/2 mt-4"
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

<script>
import { defineComponent } from "vue";
import SlideUpDown from "vue3-slide-up-down";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowCircleRight,
    faArrowCircleLeft,
} from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faArrowCircleRight, faArrowCircleLeft);

export default defineComponent({
    name: "WorkoutForm",
    components: {
        SlideUpDown,
        FontAwesomeIcon,
    },
    props: ["reset"],
    watch: {
        reset(flag) {
            if (flag) {
                this.currentQuestion = 1;
                this.selectedBodyBand = "";
                this.selectedBarType = "nothing";
                this.selectedInjury = "";
                this.selectedCoreDaily = "";
                this.selectedGoal = "";
                this.selectedRest = 0;
            }
        },
    },
    data() {
        return {
            showForm: true,
            currentQuestion: 1,

            selectedBodyBand: "",
            selectedBarType: "nothing",
            selectedInjury: "",
            selectedCoreDaily: "",
            selectedGoal: "",
            selectedRest: 0,

            drops: {
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
            },
        };
    },
    computed: {},
    methods: {
        nextQuestion() {
            if (this.validateQuestion()) {
                if (this.currentQuestion < 6) {
                    new Noty({
                        type: "info",
                        killer: true,
                        theme: "sunset",
                        text: `Awesome! Moving on to question ${
                            this.currentQuestion + 1
                        } of 6`,
                        timeout: 7500,
                    }).show();

                    this.currentQuestion++;
                }
            } else {
                new Noty({
                    type: "error",
                    theme: "sunset",
                    text: "Please complete the question before continuing.",
                    timeout: 7500,
                }).show();
            }
        },
        prevQuestion() {
            if (this.currentQuestion > 1) {
                this.currentQuestion--;
            }
        },
        submitForm() {
            if (this.currentQuestion === 6 && this.validateQuestion()) {
                this.$emit("submit", {
                    selectedBodyBand: this.selectedBodyBand,
                    selectedBarType: this.selectedBarType,
                    selectedInjury: this.selectedInjury,
                    selectedCoreDaily: this.selectedCoreDaily,
                    selectedGoal: this.selectedGoal,
                    selectedRest: this.selectedRest,
                });

                new Noty({
                    type: "success",
                    killer: true,
                    theme: "sunset",
                    text: `Great! Generating A workout for you now!`,
                    timeout: 5000,
                }).show();
            } else {
                new Noty({
                    type: "error",
                    theme: "sunset",
                    text: "Complete the whole form before continuing.",
                    timeout: 10000,
                }).show();
            }
        },
        validateQuestion() {
            let v = false;

            switch (this.currentQuestion) {
                case 1:
                    v = this.selectedBodyBand !== "";
                    break;

                case 2:
                    v = this.selectedBarType !== "nothing";
                    break;

                case 3:
                    v = this.selectedInjury !== "";
                    break;

                case 4:
                    v = this.selectedCoreDaily !== "";
                    break;

                case 5:
                    v = this.selectedGoal !== "";
                    break;

                case 6:
                    v = this.selectedRest !== 0;
                    break;
            }

            return v;
        },
    },
    mounted() {},
});
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
