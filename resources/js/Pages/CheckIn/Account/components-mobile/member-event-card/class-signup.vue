<template>
    <div class="class-signup-title">Class Sign Up</div>
    <div
        class="class-signup-available"
        @click="$emit('show-modal', 'available-class')"
    >
        5 available
    </div>
</template>
<style scoped>
.class-signup-title {
    @apply text-2xl font-semibold pb-2 mt-4;
}
.class-signup-available {
    @apply text-xl mb-4 border px-5 py-3 rounded;
}
</style>
