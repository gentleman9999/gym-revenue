<template>
    <div class="text-secondary font-bold mb-2">Record Preview</div>
    <div class="grid grid-cols-6 gap-4">
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Name"
            :value="customer.first_name + ' ' + customer.last_name"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Email"
            :value="customer.email"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 1"
            :value="customer.phone"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 2"
            :value="customer.alternate_phone"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Gender"
            :value="customer.gender"
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Birthdate"
            :value="
                new Date(customer.date_of_birth).toLocaleDateString('en-US')
            "
        />
        <customer-preview-item
            class="field col-span-6 md:col-span-3"
            label="Contact"
            :value="customer.email"
        />
        <div class="field col-span-6 md:col-span-3">
            <label>Contact</label>
            <div class="data">
                Called: {{ customer.interaction_count.calledCount }} <br />
                Emailed: {{ customer.interaction_count.emailedCount }} <br />
                Text: {{ customer.interaction_count.smsCount }} <br />
            </div>
        </div>
        <div
            class="collapse col-span-6"
            tabindex="0"
            v-if="member?.preview_note?.length"
        >
            <div class="collapse-title text-sm font-medium">
                > Existing Notes
            </div>
            <div class="flex flex-col gap-2 collapse-content">
                <div
                    v-for="(note, ndx) in customer.preview_note"
                    class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                    :key="ndx"
                >
                    {{ note["note"] }}
                </div>
            </div>
            <!--            this was on dev, not sure if we need it on gql-->
            <!--            <div v-for="(note, ndx) in data.preview_note" :key="ndx">-->
            <!--                <hr-->
            <!--                    v-if="-->
            <!--                            ndx != 0 &&-->
            <!--                            data.preview_note[ndx - 1]['lifecycle'] !=-->
            <!--                                note['lifecycle']-->
            <!--                        "-->
            <!--                    class="pb-5"-->
            <!--                />-->
            <!--                <div-->
            <!--                    class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"-->
            <!--                >-->
            <!--                    {{ note["note"] }}-->
            <!--                </div>-->
        </div>
        <div class="field col-span-6 md:col-span-3 text-secondary">
            <label>Club/ Location: {{ customer.home_location.name }}</label>
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
    customer: {
        type: Object,
    },
});
</script>
