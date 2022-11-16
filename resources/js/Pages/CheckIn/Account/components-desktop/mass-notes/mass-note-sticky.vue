<template>
    <div class="mass-note-sticky">{{ title }}</div>
</template>
<style scoped>
.mass-note-sticky {
    @apply flex flex-row rounded-l h-8 w-36 items-center pl-2;
}
</style>
<script setup>
const props = defineProps({
    title: String,
});
</script>
