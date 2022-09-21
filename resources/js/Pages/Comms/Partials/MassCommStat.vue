<template>
    <div class="stat hover:bg-base-200">
        <div class="stat-title">{{ title }}</div>
        <div class="stat-value">
            {{ value }}
        </div>
        <div
            class="stat-desc"
            :class="{ 'text-success': delta > 0, 'text-error': delta < 0 }"
        >
            <template v-if="value">
                <span v-if="delta > 0">↗︎</span>
                <span v-if="delta < 0">↘︎︎</span>
                {{ parseInt(delta * value) }} ({{ parseInt(delta * 100) }}%)
            </template>
            <span v-else class="opacity-0">placeholder</span>
        </div>
    </div>
</template>
<style scoped>
.stat {
    @apply p-4 md:px-6;
}
</style>
<script>
import { defineComponent } from "vue";
export default defineComponent({
    props: {
        title: {
            type: String,
            required: true,
        },
        value: {
            type: Number,
            required: true,
        },
    },
    setup(props) {
        let delta = Math.random() * 0.3;
        if (!props.title.includes("Conversion")) {
            delta *= Math.random() > 0.8 ? -1 : 1;
        }
        return { delta };
    },
});
</script>
