<template>
    <div class="training-reminder-title">PT Training</div>
    <div class="training-reminder-date">Today</div>
    <div class="training-reminder-time">10:30am</div>
</template>
<style scoped>
.training-reminder-title {
    @apply text-2xl font-bold pb-2;
}
.training-reminder-date {
    @apply bg-base-content rounded border text-center w-3/5 py-3 text-xl text-secondary font-semibold my-3;
}
.training-reminder-time {
    @apply text-lg text-base-content;
}
</style>
