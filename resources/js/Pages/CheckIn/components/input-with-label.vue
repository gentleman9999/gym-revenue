<template>
    <div class="input-with-label-container">
        <div class="input-label">
            {{ label }}
        </div>
        <input
            v-bind="props"
            :class="{
                'bg-neutral': !value && !secondary,
                'bg-secondary': value || secondary,
            }"
        />
    </div>
</template>
<style scoped>
.input-with-label-container {
    @apply flex flex-col space-y-3 px-0;
    .input-label {
        @apply text-lg;
    }
    input {
        @apply text-lg rounded h-9 pl-2 focus:outline-none;
        &:focus:focus {
            --tw-ring-shadow: transparent;
        }
    }
}
</style>
<script setup>
const props = defineProps({
    label: String,
    value: String,
    secondary: Boolean,
});

const value = ref(null);
</script>
