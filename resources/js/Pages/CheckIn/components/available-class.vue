<template>
    <div class="checkin-history-container">
        <div class="mb-5 font-semibold">Classes Available</div>
        <div class="flex flex-col space-y-5">
            <Button
                v-for="(item, ndx) in mock"
                :key="ndx"
                secondary
                class="!w-full"
                @click="$emit('showModal', 'class-signup', { title: item })"
                >{{ item }}</Button
            >
        </div>
    </div>
</template>
<style scoped>
.checkin-history-container {
    @apply flex flex-col items-center text-lg;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";

const mock = ["Cardio", "Stretching", "Intro Class", "Pilates", "Group X"];
</script>
