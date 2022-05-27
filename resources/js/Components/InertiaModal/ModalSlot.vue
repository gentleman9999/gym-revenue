<template>
    <div :ref="ref"></div>
</template>

<script setup>
import { injector } from "./useModalSlot";

const ref = injector();
</script>

<script>
export default {
    name: "ModalSlot",
};
</script>
