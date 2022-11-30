<template>
    <div class="club-card-tab-container">
        <div
            v-for="tab in tabs"
            :key="tab.key"
            @click="$emit('toggleTab', tab.key)"
            :class="{
                active: active === tab.key,
            }"
        >
            {{ tab.label }}
        </div>
    </div>
</template>
<style scoped>
.club-card-tab-container {
    @apply flex flex-row space-x-7 justify-center mt-5 pb-9;
    .active {
        @apply text-secondary underline underline-offset-4;
    }
}
</style>
<script setup>
const props = defineProps({
    active: String,
});
const tabs = [
    {
        key: "member",
        label: "Member",
    },
    {
        key: "guest",
        label: "Guest",
    },
    {
        key: "employee",
        label: "Employee",
    },
];
</script>
