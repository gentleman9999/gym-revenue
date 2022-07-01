<template>
    <article
        v-if="events && events.length"
        class="bg-base-content bg-opacity-80 text-base-300 p-4 rounded-md border border-secondary"
    >
        <h2 class="font-bold text-xl capitalize">{{ title }}</h2>
        <span class="font-bold text-secondary">Today, {{ date }}</span>
        <div>
            <day-scroller day="2" />
        </div>
        <ul>
            <li v-for="e in events" class="flex items-center my-4">
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
        </ul>
    </article>
</template>

<style scoped>
li > div {
    @apply grid;
    grid-template-columns: 1fr auto;
    grid-template-rows: 1fr;
}
</style>

<script>
import { defineComponent, ref, onMounted } from "vue";

import DayScroller from "./DayScroller.vue";

export default defineComponent({
    components: { DayScroller },
    props: {
        title: {
            type: String,
            required: true,
        },
        date: {
            type: String,
        },
        events: {
            type: Array,
        },
    },
    setup(props) {
        const fmtTime = (d) => {
            let t = d.split(" ")[1].split(":");
            return [t[0], t[1]].join(":");
        };

        return {
            fmtTime,
        };
    },
});
</script>
