<template>
    <!--   <inertia-link
        :href="
            route('current-location-qv.update', {
                location_id: events.location_id,
            })
        "
        method="put"
    > -->
    <article
        class="bg-base-content bg-opacity-80 text-base-300 p-4 h-full rounded-md border border-secondary"
    >
        <h2 class="font-bold text-xl capitalize">
            {{ events.location_name }}
        </h2>
        <span class="font-bold text-secondary">{{ selectedDateStr }}</span>
        <div>
            <day-scroller
                :selected_date="selected"
                :visible_date="date_in_visible_week"
                @advance="() => upWeek()"
                @regress="() => downWeek()"
            />
        </div>
        <ul>
            <li
                v-if="events.events && events.events.length > 0"
                v-for="e in events.events"
                class="flex items-center my-4"
            >
                {{ fmtTime(e.start) }}
                <div
                    class="w-full bg-base-content px-3 py-1 ml-2 rounded gridgrid-rows-1"
                >
                    <span
                        class="whitespace-nowrap overflow-hidden overflow-ellipsis"
                        >{{ e.title }}</span
                    >
                    <svg
                        class="inline-block flex-shrink-0"
                        width="24"
                        height="24"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="11"
                            :stroke="e.color"
                            :fill="e.color"
                            stroke-width="1"
                        />
                    </svg>
                </div>
            </li>

            <li
                v-if="events?.events?.length === 0"
                class="w-full text-center italic py-4 opacity-50"
            >
                <span> No Events Today </span>
            </li>
        </ul>
    </article>
    <!--   </inertia-link> -->
</template>

<style scoped>
li > div {
    @apply grid;
    grid-template-columns: 1fr auto;
    grid-template-rows: 1fr;
}
</style>

<script>
import { defineComponent, ref, onMounted, computed } from "vue";

import DayScroller from "./DayScroller.vue";
import { Inertia } from "@inertiajs/inertia";
export default defineComponent({
    components: { DayScroller },
    props: {
        events: {
            type: Array,
        },
    },
    computed: {
        selectedDateStr() {
            return `${this.names_weekday[this.selected.getDay()]}, ${
                this.names_month[this.selected.getMonth()]
            } ${this.selected.getDate()},  ${this.selected.getFullYear()}`;
        },
    },
    setup(props) {
        const names_weekday = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
        ];
        const names_month = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];

        /** A reference for the current point in time */
        const today = ref(new Date());

        /** some date within the week we want to show */
        const date_in_visible_week = ref(new Date());

        /** For the currently selected date */
        const selected = ref(new Date());

        /** Format an events time in hh:mm format */
        const fmtTime = (d) => {
            let t = d.split(" ")[1].split(":");
            return [t[0], t[1]].join(":");
        };

        const upWeek = () => {
            console.log("up 1 week");
            let td = new Date(date_in_visible_week.value);
            let temp = new Date(
                td.getFullYear(),
                td.getMonth(),
                td.getDate() + 7
            );
            // td.setDate(td.getDate() + 7);
            date_in_visible_week.value = temp;
        };

        const downWeek = () => {
            console.log("down 1 week");
            let td = new Date(date_in_visible_week.value);
            td.setDate(td.getDate() - 7);
            date_in_visible_week.value = td;
        };

        return {
            names_weekday,
            names_month,
            fmtTime,
            selected,
            date_in_visible_week,
            upWeek,
            downWeek,
        };
    },
});
</script>
