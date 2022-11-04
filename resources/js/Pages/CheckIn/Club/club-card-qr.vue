<template>
    <div
        class="qr-container"
        :class="{
            'pt-5 pb-16': !description,
        }"
    >
        <qr />
        <div class="qr-desc" v-if="description">{{ description }}</div>
    </div>
</template>
<style scoped>
.qr-container {
    @apply flex flex-col m-auto w-fit;
}
.qr-desc {
    @apply bg-secondary rounded-b-lg text-center font-semibold p-1;
    margin-top: -8px;
}
</style>
<script setup>
import Qr from "@/Components/Icons/Qr.vue";
const props = defineProps({
    description: String,
});
</script>
