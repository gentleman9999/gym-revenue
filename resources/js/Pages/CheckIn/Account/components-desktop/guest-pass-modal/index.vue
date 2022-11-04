<template>
    <div class="guest-pass-title">Create Lead (Guest Pass)</div>
    <div class="guest-pass-container">
        <div class="guest-pass-row">
            <guest-pass-input :value="form.firt_name" label="First Name" />
            <guest-pass-input :value="form.email" label="Email" />
            <guest-date-range :value="form.date_range" />
            <guest-expire :value="form.expire" />
            <textarea
                class="guest-pass-note"
                v-model="form.note"
                placeholder="Notes"
            ></textarea>
        </div>
        <div class="guest-pass-row">
            <guest-pass-input :value="form.last_name" label="Last Name" />
            <guest-pass-input :value="form.phone" label="Phone Number" />
            <guest-pass-input :value="form.gender" label="Gender" />
            <div>
                <div class="text-lg mb-4">Position</div>
                <multiselect
                    v-model="form.location"
                    class="py-2"
                    id="locations"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        locations?.map((location) => ({
                            label: location.name,
                            value: location.id,
                        }))
                    "
                    :classes="{
                        ...getDefaultMultiselectTWClasses(),
                        container:
                            'relative mx-auto flex items-center cursor-pointer border border-base-content rounded bg-base-content/10 text-base outline-none h-11',
                    }"
                />
            </div>
            <div class="guest-past-actions">
                <Button
                    outline
                    class="!text-base-content"
                    size="sm"
                    @click="close"
                >
                    Cancel
                </Button>
                <Button
                    secondary
                    size="sm"
                    @click="$emit('show-modal', 'guest-pass-preview')"
                >
                    Cread Lead
                </Button>
            </div>
        </div>
    </div>
</template>
<style scoped>
.guest-pass-title {
    @apply text-xl text-secondary font-bold;
}
.guest-pass-container {
    @apply flex flex-row space-x-16 mt-8;
}
.guest-pass-row {
    @apply flex flex-col space-y-6;
}
.guest-pass-note {
    @apply bg-base-content text-neutral rounded;
}
.guest-past-actions {
    @apply flex flex-row space-x-4 h-full items-end justify-end;
}
</style>
<script setup>
import { computed } from "vue";
import { useGymRevForm } from "@/utils";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import Button from "@/Components/Button.vue";
import GuestPassInput from "./guest-pass-input.vue";
import GuestDateRange from "./guest-date-range.vue";
import GuestExpire from "./guest-expire.vue";

import { usePage } from "@inertiajs/inertia-vue3";
const page = usePage();
const locations = computed(() => page.props.value.locations);

const form = useGymRevForm({});
const emit = defineEmits(["close", "guest-pass-preview"]);

const close = () => {
    emit("close", "guest-pass");
};
</script>
