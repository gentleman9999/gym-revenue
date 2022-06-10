<template>
    <slot v-if="!inModal">
        <ModalSlot />
    </slot>
    <slot v-else name="modal-only" :close="inModal.close">
        <ModalSlot />
    </slot>
    <Teleport v-if="telRef" :to="telRef">
        <slot name="modal" :close="inModal.close" />
    </Teleport>
</template>

<script setup>
import isModal from "./isModal";
import ModalSlot from "./ModalSlot.vue";
import { provider } from "./useModalSlot";

const telRef = provider();

const inModal = isModal();
</script>

<script>
export default {
    name: "ModalableWrapper",
};
</script>
