<template>
    <teleport to="body">
        <div ref="modalWrapper" class="w-full h-full fixed">
            <slot></slot>
        </div>
    </teleport>
</template>

<script setup>
/**
 * Unopinionated Modal
 *
 * unopinionated modal to avoid fighting with styles & selector specification. instead style it how you want it in the
 * component - including any wrapper styles or layout styles. all this does is give you a blank canvas over the entire
 * screen to display elements the way they're needed.
 *
 * handle display via the consumer
 *
 */

import { ref, onMounted } from "vue";

const modalWrapper = ref();

/**
 * zindex is an optional prop for cases you may run into when you have to set it manually.
 * for instance if you're using it over an existing modal.
 */
const props = defineProps({
    zindex: {
        type: [Number, String],
        default: 500,
    },
});

onMounted(() => {
    modalWrapper.value.style.zIndex = props.zindex;
});
</script>
