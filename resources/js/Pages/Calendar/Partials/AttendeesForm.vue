<template>
    <form @submit="handleSubmit" class="w-full grid grid-cols-6 gap-4">
        <div class="col-span-6" v-if="form.attendees?.length">
            <jet-label for="attendees" value="Client Attendees" />
            <table class="table table-compact w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th>Attendee</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="attendee in form.attendees" :key="attendee.id">
                        <th
                            v-if="
                                attendee.entity_type ===
                                'App\\Domain\\Users\\Models\\User'
                            "
                        >
                            <img
                                class="object-cover w-6 h-6 rounded-full"
                                :src="attendee.entity_data.profile_photo_url"
                                :alt="attendee.entity_data.name"
                            />
                        </th>
                        <th v-else></th>

                        <td
                            v-if="
                                attendee.entity_type ===
                                'App\\Domain\\Leads\\Models\\Lead'
                            "
                        >
                            <div class="flex items-center space-x-3">
                                <div>
                                    <div class="font-bold">
                                        {{ attendee.entity_data.first_name }}
                                        {{ attendee.entity_data.last_name }}
                                    </div>
                                    <div class="text-sm opacity-50">Lead</div>
                                </div>
                            </div>
                        </td>
                        <td
                            v-else-if="
                                attendee.entity_type ==
                                'App\\Models\\Endusers\\Member'
                            "
                        >
                            <div class="flex items-center space-x-3">
                                <div>
                                    <div class="font-bold">
                                        {{ attendee.entity_data.first_name }}
                                        {{ attendee.entity_data.last_name }}
                                    </div>
                                    <div class="text-sm opacity-50">Member</div>
                                </div>
                            </div>
                        </td>
                        <td v-else>
                            <div class="flex items-center space-x-3">
                                <div>
                                    <div class="font-bold">
                                        {{ attendee.entity_data.name }}
                                    </div>
                                    <div class="text-sm opacity-50">
                                        Client User
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="text-xs">
                                {{ attendee.entity_data.email }}
                            </div>
                            <span class="badge badge-ghost badge-sm">{{
                                attendee.invitation_status
                            }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <input id="client_id" type="hidden" v-model="form.client_id" />
    </form>
</template>
<style>
input {
    @apply input input-sm;
}
input,
select,
textarea {
    @apply w-full;
}
label {
    @apply mb-1;
}
</style>

<script>
import { watchEffect } from "vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import { useGymRevForm } from "@/utils";

export default {
    components: {
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DaisyModal,
    },
    props: ["calendar_event"],
    setup(props, { emit }) {
        let calendarEvent = props.calendar_event;

        const form = useGymRevForm(calendarEvent);
        watchEffect(() => {});
        return {
            form,
        };
    },
};
</script>
