<template>
    <div class="checkin-acc-form-element">
        <div class="checkin-acc-form-label">
            {{ label }}
            <span v-if="required" class="text-secondary pl-2">*</span>
        </div>
        <input
            class="checkin-acc-form-input"
            :class="{
                'bg-neutral': !value && !readonly,
                'bg-secondary': value && !readonly,
                'bg-transparent text-base-content/60': readonly,
            }"
            v-bind="props"
            readonly="readonly"
        />
    </div>
</template>
<style scoped>
.checkin-acc-form-element {
    @apply flex flex-col space-y-3 px-0;
}
.checkin-acc-form-label {
    @apply text-lg;
}
.checkin-acc-form-input {
    @apply text-lg rounded h-10 pl-1 focus:outline-none;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
</style>
<script setup>
const props = defineProps({
    label: String,
    value: String,
    required: Boolean,
    readonly: Boolean,
});
</script>
