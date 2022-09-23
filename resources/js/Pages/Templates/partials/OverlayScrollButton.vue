<template>
    <button
        :class="{
            rightside: side === 'right',
            leftside: side === 'left',
        }"
        class="totalcenter"
        ref="btn"
        @click="handleClick"
        type="button"
    >
        <slot />
    </button>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    handleClick: {
        type: Function,
        default: () => $emit("click"),
    },
    idleOpacity: {
        type: Number,
        default: 0.4,
    },
    idleWidth: {
        type: String,
        default: "2.25rem",
    },
    side: {
        type: String,
        default: "right",
    },
});

const btn = ref(null);

watch([props], () => {
    btn.value.style.opacity = props.idleOpacity;
    btn.value.style.width = props.idleWidth;
});
</script>

<style scoped>
button {
    @apply absolute opacity-40 top-0 bottom-0 h-full w-8 rounded-md;
    @apply fill-white bg-black transition-all duration-200;
    @apply hover:!w-16 hover:!opacity-60 z-[999];
}

button.rightside {
    @apply right-0;
}

button.leftside {
    @apply left-0;
}
</style>
