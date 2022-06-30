<template>
    <ul class="flex justify-between py-4">
        <button>b</button>
        <li v-for="num in week" class="font-bold">
            {{ num }}
        </li>
        <button>f</button>
    </ul>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";

export default defineComponent({
    props: {
        week_start_date: {
            type: Number,
        },
        day: {
            type: Number,
        },
    },
    setup(props) {
        const week = ref([1, 2, 3, 4, 5, 6, 7]);
        return {
            week,
        };
    },
});
</script>
