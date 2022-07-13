<template>
    <modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-4">
            <div class="text-lg">
                <slot name="title"> </slot>
            </div>

            <div class="mt-4">
                <slot name="content"> </slot>
            </div>
        </div>

        <div class="px-6 py-4 bg-base-100 text-right">
            <slot name="footer"> </slot>
        </div>
    </modal>
</template>

<script setup>
import Modal from "./Modal.vue";
import { defineEmits } from "vue";
const emit = defineEmits(["close"]);

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: "2xl",
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit("close");
};
</script>
