<template>
    <div class="grid grid-cols-6 gap-4">
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Name"
            :value="data.lead.first_name + ' ' + data.lead.last_name"
        />
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Email"
            :value="data.lead.email"
        />
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Phone 1"
            :value="data.lead.primary_phone"
        />
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Phone 2"
            :value="data.lead.alternate_phone"
        />
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Gender"
            :value="data.lead.gender"
        />
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Birthdate"
            :value="
                new Date(data.lead.date_of_birth).toLocaleDateString('en-US')
            "
        />
        <lead-preview-item
            class="field col-span-6 lg:col-span-3"
            label="Lead Owner Email"
            :value="
                data.lead?.owner ? data.lead.owner.email : 'Not Yet Claimed'
            "
        />
        <div class="field col-span-6 lg:col-span-3">
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
                    :key="ndx"
                    class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                >
                    {{ note["note"] }}
                </div>
            </div>
        </div>
        <div
            class="flex lg:flex-row flex-col justify-between col-span-6 lg:col-span-6 text-secondary"
        >
            <label>Club/ Location: {{ data.club_location.name }}</label>
            <Button size="xs" primary v-if="assign" disabled
                >Assigning...</Button
            >
            <Button
                size="xs"
                primary
                v-else-if="data.lead.owner_user_id === $page.props.user.id"
                @click="handleContact"
                >Contact</Button
            >
            <Button size="xs" primary v-else @click="handleClaim">Claim</Button>
        </div>
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
import { ref } from "vue";
import LeadPreviewItem from "./LeadPreviewItem.vue";
import Button from "@/Components/Button.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    data: {
        type: Object,
    },
});

const assigning = ref(false);

const handleClaim = async () => {
    assigning.value = true;
    Inertia.visit(route("data.leads.assign", props.data.lead.id), {
        method: "put",
        preserveScroll: true,
        onFinish: () => {
            assigning.value = false;
        },
    });
};
const handleContact = () => {
    Inertia.visit(route("data.leads.show", props.data.lead.id));
};
</script>
