<template>
    <ul class="flex justify-between py-4">
        <button class="text-base-100 text-opacity-50">
            <font-awesome-icon :icon="['fas', 'chevron-left']" />
        </button>
        <li v-for="num in week" class="font-bold">
            {{ num }}
        </li>
        <button class="text-secondary">
            <font-awesome-icon :icon="['fas', 'chevron-right']" />
        </button>
    </ul>
</template>

<style scoped>
.today {
    @apply rounded-full bg-secondary text-base-content h-6 w-6 text-center;
}
</style>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faChevronLeft,
    faChevronRight,
} from "@fortawesome/pro-solid-svg-icons";
import { defineComponent, ref, onMounted } from "vue";
library.add(faChevronLeft);
library.add(faChevronRight);

export default defineComponent({
    components: {
        FontAwesomeIcon,
    },
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
