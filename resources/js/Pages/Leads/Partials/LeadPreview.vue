<template>
    <div class="grid grid-cols-6 gap-4">
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Name"
            :value="lead.first_name + ' ' + lead.last_name"
        />
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Email"
            :value="lead.email"
        />
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 1"
            :value="lead.phone"
        />
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 2"
            :value="lead.alternate_phone"
        />
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Gender"
            :value="lead.gender"
        />
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Birthdate"
            :value="new Date(lead.date_of_birth).toLocaleDateString('en-US')"
        />
        <lead-preview-item
            class="field col-span-6 md:col-span-3"
            label="Lead Owner Email"
            :value="lead?.owner ? lead.owner.email : 'Not Yet Claimed'"
        />
        <div class="field col-span-6 lg:col-span-3">
            <label>Contact</label>
            <div class="data">
                Called: {{ lead.interaction_count.calledCount }} <br />
                Emailed: {{ lead.interaction_count.emailedCount }} <br />
                Text: {{ lead.interaction_count.smsCount }} <br />
            </div>
        </div>
        <div
            class="collapse col-span-6"
            tabindex="0"
            v-if="lead.preview_note?.length"
        >
            <div class="collapse-title text-sm font-medium">
                > Existing Notes
            </div>
            <div class="flex flex-col gap-2 collapse-content">
                <div
                    v-for="(note, ndx) in lead.preview_note"
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
            <label>Club/ Location: {{ lead.club_location.name }}</label>
            <Button size="xs" primary v-if="assigning" disabled
                >Assigning...</Button
            >
            <Button
                size="xs"
                primary
                v-else-if="lead.owner_user_id === $page.props.user.id"
                @click="handleContact"
                >Contact</Button
            >
            <Button
                size="xs"
                primary
                v-else-if="!lead.owner_user_id"
                @click="handleClaim"
                >Claim</Button
            >
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
    lead: {
        type: Object,
    },
});

const assigning = ref(false);

const handleClaim = async () => {
    assigning.value = true;
    Inertia.visit(route("data.leads.assign", props.lead.id), {
        method: "put",
        preserveScroll: true,
        onFinish: () => {
            assigning.value = false;
        },
    });
};
const handleContact = () => {
    Inertia.visit(route("data.leads.show", props.lead.id));
};
</script>
