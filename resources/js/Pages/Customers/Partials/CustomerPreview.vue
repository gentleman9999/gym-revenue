<template>
    <div class="text-secondary font-bold mb-2">Record Preview</div>
    <div class="grid grid-cols-6 gap-4">
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Name"
            :value="data.customer.first_name + ' ' + data.customer.last_name"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Email"
            :value="data.customer.email"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 1"
            :value="data.customer.primary_phone"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 2"
            :value="data.customer.alternate_phone"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Gender"
            :value="data.customer.gender"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Birthdate"
            :value="
                new Date(data.customer.date_of_birth).toLocaleDateString(
                    'en-US'
                )
            "
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Contact"
            :value="data.customer.email"
        />
        <div class="field col-span-6 md:col-span-3">
            <label>Contact</label>
            <div class="data">
                Called: {{ data.interactionCount.calledCount }} <br />
                Emailed: {{ data.interactionCount.emailedCount }} <br />
                Text: {{ data.interactionCount.smsCount }} <br />
            </div>
        </div>
        <div
            class="collapse col-span-6"
            tabindex="0"
            v-if="data?.preview_note?.length"
        >
            <div class="collapse-title text-sm font-medium">
                > Existing Notes
            </div>
            <div class="flex flex-col gap-2 collapse-content">
                <div
                    v-for="(note, ndx) in data.preview_note"
                    class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                    :key="ndx"
                >
                    {{ note["note"] }}
                </div>
            </div>
        </div>
        <div class="field col-span-6 md:col-span-3 text-secondary">
            <label>Club/ Location: {{ data.club_location.name }}</label>
        </div>
        <div class="field col-span-6 lg:col-span-6"></div>
    </div>
</template>
<style scoped>
input {
    @apply input-xs;
}

.field {
    @apply flex flex-col gap-2;
}
.data {
    @apply border border-secondary rounded text-sm p-3;
}
</style>

<script setup>
import CustomerPreviewItem from "./CustomerPreviewItem.vue";
const props = defineProps({
    data: {
        type: Object,
    },
});
</script>
