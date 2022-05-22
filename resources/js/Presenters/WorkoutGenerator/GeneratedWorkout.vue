<template>
    <div class="box box-solid box-success">
        <div class="box-header flex py-2">
            <h3 class="box-title ml-4">Recommended Workouts</h3>

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
                <div class="inner-box-body text-center">
                    <div class="title">
                        <div class="inner-title">
                            <h1>-- Your New Workout --</h1>
                        </div>
                    </div>

                    <div
                        class="generated-contents"
                        v-if="showForm"
                        id="generatedContents"
                    >
                        <div class="inner-contents">
                            <div class="box">
                                <div
                                    class="workout-content flex justify-between"
                                >
                                    <h3 class="current-day-text">
                                        Exercises for {{ days[selectedDay] }}
                                    </h3>

                                    <div
                                        class="flex"
                                        id="boxTools"
                                        v-if="!exporting"
                                    >
                                        <div
                                            class="tiny-box px-2"
                                            v-show="selectedDay !== 0"
                                            @click="selectedDay--"
                                        >
                                            <span class="label"
                                                ><font-awesome-icon
                                                    :icon="[
                                                        'far',
                                                        'chevron-double-left',
                                                    ]"
                                                    size="sm"
                                                    class="mt-2"
                                            /></span>
                                        </div>

                                        <div
                                            @click="
                                                selectedDay =
                                                    genThreeScrollButtons[0] - 1
                                            "
                                            :class="
                                                genThreeScrollButtons[0] ===
                                                selectedDay + 1
                                                    ? 'selected-tiny-box'
                                                    : 'tiny-box'
                                            "
                                            class="px-2"
                                        >
                                            <span class="label"
                                                ><div class="mt-1">
                                                    <small>{{
                                                        genThreeScrollButtons[0]
                                                    }}</small>
                                                </div></span
                                            >
                                        </div>
                                        <div
                                            @click="
                                                selectedDay =
                                                    genThreeScrollButtons[1] - 1
                                            "
                                            :class="
                                                genThreeScrollButtons[1] ===
                                                selectedDay + 1
                                                    ? 'selected-tiny-box'
                                                    : 'tiny-box'
                                            "
                                            class="px-2"
                                        >
                                            <span class="label"
                                                ><div class="mt-1">
                                                    <small>{{
                                                        genThreeScrollButtons[1]
                                                    }}</small>
                                                </div></span
                                            >
                                        </div>
                                        <div
                                            @click="
                                                selectedDay =
                                                    genThreeScrollButtons[2] - 1
                                            "
                                            :class="
                                                genThreeScrollButtons[2] ===
                                                selectedDay + 1
                                                    ? 'selected-tiny-box'
                                                    : 'tiny-box'
                                            "
                                            class="px-2"
                                        >
                                            <span class="label"
                                                ><div class="mt-1">
                                                    <small>{{
                                                        genThreeScrollButtons[2]
                                                    }}</small>
                                                </div></span
                                            >
                                        </div>

                                        <div
                                            class="tiny-box px-2"
                                            v-show="selectedDay !== 6"
                                            @click="selectedDay++"
                                        >
                                            <span class="label"
                                                ><font-awesome-icon
                                                    :icon="[
                                                        'far',
                                                        'chevron-double-right',
                                                    ]"
                                                    size="sm"
                                                    class="mt-2"
                                            /></span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="jk-this-is-the-workout-content flex flex-col mt-2"
                                >
                                    <div v-if="workout === false" class="mt-10">
                                        <font-awesome-icon
                                            :icon="['fad', 'bug']"
                                            size="6x"
                                            class="mb-6"
                                        />
                                        <p>
                                            I have nothing for you. Maybe
                                            there's a bug?
                                        </p>
                                    </div>
                                    <div v-else class="mt-10">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="first-column mt-2"
                                                    >
                                                        Name
                                                    </th>
                                                    <th>Sets</th>
                                                    <th>Reps</th>
                                                    <th>Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-if="
                                                        workout[
                                                            week[selectedDay]
                                                        ] === restMsg
                                                    "
                                                >
                                                    <td
                                                        class="first-column mt-2"
                                                    >
                                                        {{ restMsg }}
                                                    </td>
                                                    <td>None</td>
                                                    <td>None</td>
                                                    <td>All Day.</td>
                                                </tr>
                                                <tr
                                                    v-else
                                                    v-for="(
                                                        data, idx
                                                    ) in workout[
                                                        week[selectedDay]
                                                    ]"
                                                >
                                                    <td
                                                        class="first-column mt-2"
                                                    >
                                                        {{ data["exercise"] }}
                                                    </td>
                                                    <td>{{ data["sets"] }}</td>
                                                    <td>{{ data["reps"] }}</td>
                                                    <td>
                                                        {{ data["duration"] }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div
                                            class="flex justify-between mt-10"
                                            v-if="!exporting"
                                        >
                                            <div class="start-over-div">
                                                <button
                                                    type="button"
                                                    class="frm-btn p-2 danger"
                                                    @click="reset()"
                                                >
                                                    Start Over
                                                </button>
                                            </div>
                                            <div
                                                class="export-div flex justify-end"
                                            >
                                                <button
                                                    type="button"
                                                    class="frm-btn p-2 info"
                                                    @click="exportThisPage()"
                                                >
                                                    Export
                                                    {{ days[selectedDay] }} to
                                                    PDF
                                                </button>
                                                <button
                                                    type="button"
                                                    class="ml-4 frm-btn p-2 warning"
                                                    @click="
                                                        exportAllPages(
                                                            0,
                                                            selectedDayselectedDay
                                                        )
                                                    "
                                                >
                                                    Export All Days to PDF
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </slide-up-down>
    </div>
</template>

<script>
import SlideUpDown from "vue3-slide-up-down";

import { jsPDF } from "jspdf";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleRight,
    faChevronDoubleLeft,
} from "@fortawesome/pro-regular-svg-icons";
import { faBug } from "@fortawesome/pro-duotone-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faChevronDoubleRight, faChevronDoubleLeft, faBug);

export default {
    name: "GeneratedWorkout",
    components: {
        SlideUpDown,
        FontAwesomeIcon,
    },
    props: ["workout", "restMsg"],
    watch: {
        workout(exercises) {
            console.log("Retrieved new workout!", exercises);
            this.showForm = exercises !== "";
        },
    },
    data() {
        return {
            exporting: false,
            showForm: false,
            days: [
                "Day 1",
                "Day 2",
                "Day 3",
                "Day 4",
                "Day 5",
                "Day 6",
                "Day 7",
            ],
            selectedDay: 0,
            week: [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday",
            ],
            addingHTML: "",
        };
    },
    computed: {
        genThreeScrollButtons() {
            let results = [];

            switch (this.selectedDay) {
                case 1:
                    results = [1, 2, 3];
                    break;

                case 2:
                case 3:
                    results = [3, 4, 5];
                    break;

                case 4:
                    results = [4, 5, 6];
                    break;

                case 6:
                case 5:
                    results = [5, 6, 7];
                    break;

                default:
                    results = [1, 2, 3];
            }

            return results;
        },
    },
    methods: {
        setSelectedDay(day) {
            if (day <= 6 && day >= 0) {
                this.selectedDay = day;
            }
        },
        reset() {
            this.$emit("reset");
        },
        exportThisPage() {
            let _this = this;
            this.exporting = true;
            setTimeout(function () {
                let elem = document.getElementById("generatedContents");
                let HTML_Width = elem.scrollWidth;
                let HTML_Height = elem.scrollHeight;
                let top_left_margin = 15;
                let PDF_Width = HTML_Width + top_left_margin * 2;
                let PDF_Height = PDF_Width * 1.5 + top_left_margin * 2;
                let canvas_image_width = HTML_Width;
                let canvas_image_height = HTML_Height;

                let totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

                html2canvas(elem, { allowTaint: true }).then(function (canvas) {
                    canvas.getContext("2d");

                    console.log(canvas.height + "  " + canvas.width);

                    let imgData = canvas.toDataURL("image/jpeg", 1.0);
                    let pdf = new jsPDF("p", "pt", [PDF_Width, PDF_Height]);
                    pdf.addImage(
                        imgData,
                        "JPG",
                        top_left_margin,
                        top_left_margin,
                        canvas_image_width,
                        canvas_image_height
                    );

                    for (let i = 1; i <= totalPDFPages; i++) {
                        pdf.addPage(PDF_Width, PDF_Height);
                        pdf.addImage(
                            imgData,
                            "JPG",
                            top_left_margin,
                            -(PDF_Height * i) + top_left_margin * 4,
                            canvas_image_width,
                            canvas_image_height
                        );
                    }

                    pdf.save(
                        `GeneratedWorkOut${_this.days[_this.selectedDay]}.pdf`
                    );
                    setTimeout(function () {
                        _this.exporting = false;
                    }, 1000);
                });
            }, 250);
        },
        appendToFakeDiv(x, element, curDay) {
            console.log("Elem " + x);
            let crap = document.getElementById("generatedContents").innerHTML;
            let temp = document.createElement("div");
            temp.classList.add("generated-contents");
            temp.innerHTML =
                crap +
                "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
            element.appendChild(temp);

            if (x === 6) {
                this.selectedDay = 0;
                this.exporting = false;
                document.body.appendChild(element);
                this.finishAllExport(element);
            } else {
                return element;
            }
        },
        exportAllPages(page, curDay) {
            let _this = this;
            this.exporting = true;

            if (page === 0) {
                console.log("Generating new fake Div");
                this.addingHTML = document.createElement("div");
                this.addingHTML.classList.add("export-contents");
            }

            this.selectedDay = page;
            setTimeout(function () {
                _this.addingHTML = _this.appendToFakeDiv(
                    page,
                    _this.addingHTML,
                    curDay
                );
                if (page !== 6) {
                    _this.exportAllPages(page + 1, curDay);
                }
            }, 500);
        },
        finishAllExport(elem) {
            console.log("Exporting ", elem.innerHTML);
            let elem2 = document.getElementsByClassName("export-contents")[0];
            let HTML_Width = elem2.scrollWidth;
            let HTML_Height = elem2.scrollHeight;
            let top_left_margin = 15;
            let PDF_Width = HTML_Width + top_left_margin * 2;
            let PDF_Height = PDF_Width * 1.5 + top_left_margin * 2;
            let canvas_image_width = HTML_Width;
            let canvas_image_height = HTML_Height;

            let totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;
            console.log("Total pages to generate - " + totalPDFPages);

            html2canvas(elem2, { allowTaint: true }).then(function (canvas) {
                canvas.getContext("2d");

                console.log(canvas.height + "  " + canvas.width);

                let imgData = canvas.toDataURL("image/jpeg", 1.0);
                let pdf = new jsPDF("p", "pt", [PDF_Width, PDF_Height]);
                pdf.addImage(
                    imgData,
                    "JPG",
                    top_left_margin,
                    top_left_margin,
                    canvas_image_width,
                    canvas_image_height
                );

                for (let i = 1; i <= totalPDFPages; i++) {
                    pdf.addPage([PDF_Width, PDF_Height]);
                    pdf.addImage(
                        imgData,
                        "JPG",
                        top_left_margin,
                        -(PDF_Height * i) + top_left_margin * 4,
                        canvas_image_width,
                        canvas_image_height
                    );
                }

                pdf.save(`GeneratedEntireWorkOutRoutine.pdf`);
                document.body.removeChild(elem2);
            });
        },
    },
    mounted() {},
};
</script>

<style scoped>
@media screen {
    .label {
        cursor: pointer;
    }

    .box-header {
        justify-content: space-between;
        background-color: #00a65a;
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

    .inner-title h1 {
        font-size: 2em;
    }

    .current-day-text {
        color: #000;
        font-size: 1.5em;
    }

    .tiny-box {
        border: 1px solid #000;
        background-color: lightgray;
    }

    .selected-tiny-box {
        border: 1px solid #000;
        background-color: slategray;
    }

    .tiny-box:hover {
        background-color: slategray;
    }

    .jk-this-is-the-workout-content {
        border-top: 1px solid #000;
    }

    .table {
        width: 100%;
    }

    .first-column {
        float: left;
    }

    tr {
        border-bottom: 1px solid lightgray;
    }

    .frm-btn {
        border: 2px solid #000;
        cursor: pointer;
    }

    .frm-btn.warning {
        background-color: yellow;
        color: #000;
    }

    .frm-btn.warning:hover {
        background-color: goldenrod;
        color: #fff;
    }

    .frm-btn.info {
        background-color: blue;
        color: white;
    }

    .frm-btn.info:hover {
        background-color: cyan;
        color: #000;
    }

    .frm-btn.danger {
        background-color: red;
        color: white;
    }

    .frm-btn.danger:hover {
        background-color: pink;
        color: #000;
    }
}
</style>
