<template>
    <article
        class="bg-base-content bg-opacity-80 text-base-300 p-4 rounded-md border border-secondary"
    >
        <h2 class="font-bold text-xl capitalize">{{ title }}</h2>
        <span class="font-bold text-secondary">Today, {{ date }}</span>
        <div>
            <day-scroller day="2" />
        </div>
        <ul>
            <li v-for="e in events" class="flex items-center my-4">
                time
                <div class="w-full bg-base-content px-3 py-1 ml-2 rounded">
                    <span>{{ e.title }}</span>
                </div>
            </li>
        </ul>
    </article>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";

import DayScroller from "./DayScroller";

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
        return {};
    },
});
</script>
