<template>
    <inertia-link
        :href="
            route('current-location-qv.update', {
                location_id: events.location_id,
            })
        "
        method="put"
    >
        <article
            class="bg-base-content bg-opacity-80 text-base-300 p-4 h-full rounded-md border border-secondary"
        >
            <h2 class="font-bold text-xl capitalize">
                {{ events.location_name }}
            </h2>
            <span class="font-bold text-secondary">{{ getDateStr() }}</span>
            <div>
                <day-scroller :date="date" />
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
    </inertia-link>
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
    setup(props) {
        console.log("event props", props.events);

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
        const today_month = ref(names_month[today.value.getMonth()]);
        const today_weekday = ref(names_weekday[today.value.getDay()]);
        const today_date = ref(today.value.getDate());
        const today_year = ref(today.value.getFullYear());

        /** For the currently selected date & to decide if we show the event or not */
        const selected = ref(new Date());
        const selected_month = ref(names_month[selected.value.getMonth()]);
        const selected_weekday = ref(names_weekday[selected.value.getDay()]);
        const selected_date = ref(selected.value.getDate());
        const selected_year = ref(selected.value.getFullYear());

        const getDateStr = () => {
            return `${today_weekday.value}, ${today_month.value} ${today_date.value}, ${today_year.value}`;
        };

        const getWeek = (d) => {
            let weekday = today.value.getDay();
            console.log("weekday!", weekday, today_month);
        };

        const fmtTime = (d) => {
            let t = d.split(" ")[1].split(":");
            return [t[0], t[1]].join(":");
        };

        const getNewDateNow = () => {
            return new Date();
        };

        const fmtWeek = (d) => {
            let weekof = new Date(d).getDay;

            let n = new Date();
            n.setDate(n.getDate() - 5);
        };

        return {
            fmtTime,
            getDateStr,
        };
    },
});
</script>
