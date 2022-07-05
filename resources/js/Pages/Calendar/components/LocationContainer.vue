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
                :selected_date="selectedDate"
                :visible_date="dateInVisibleWeek"
                @advance="$emit('advance')"
                @regress="$emit('regress')"
                @change-date="(d) => $emit('changeDate', d)"
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
        active_date: {
            type: String,
        },
        visible_date: {
            type: String,
        },
    },
    computed: {
        selectedDateStr() {
            return `${this.names_weekday[this.selectedDate.getDay()]}, ${
                this.names_month[this.selectedDate.getMonth()]
            } ${this.selectedDate.getDate()},  ${this.selectedDate.getFullYear()}`;
        },
        dateInVisibleWeek() {
            return new Date(this.visible_date);
        },
        selectedDate() {
            return new Date(this.active_date);
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

        /** Formats an events time as hh:mm */
        const fmtTime = (d) => {
            let t = d.split(" ")[1].split(":");
            return [t[0], t[1]].join(":").replace(/^0/, "");
        };

        return {
            names_weekday,
            names_month,
            fmtTime,
        };
    },
});
</script>
