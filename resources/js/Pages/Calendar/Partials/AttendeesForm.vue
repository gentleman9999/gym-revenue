<template>
    <form @submit="handleSubmit" class="w-full grid grid-cols-6 gap-4">

        <div class="col-span-6">
            <jet-label for="attendees" value="Attendees" />
            <table class="table table-compact w-full">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="attendee in form.attendees"
                        :key="attendee.id"
                    >
                        <th>
                            <img class="object-cover w-6 h-6 rounded-full" :src="attendee.profile_photo_url"
                                     :alt="attendee.name">
                        </th>
                        <td>{{ attendee.name }}</td>
                        <td>{{ attendee.email }}</td>
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
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { computed, watchEffect, watch } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import DatePicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";
import DaisyModal from "@/Components/DaisyModal";
import AttendeesForm from "@/Pages/Calendar/Partials/AttendeesForm";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DaisyModal,
    },
    props: ["calendar_event"],
    setup(props, { emit }) {
        let calendarEvent = props.calendar_event;

        const form = useForm(calendarEvent);
        watchEffect(() => {
        });
        return {
            form,
        };
    },
};
</script>
