<template>
    <form @submit="handleSubmit" class="w-full grid grid-cols-6 gap-4">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <div class="col-span-6">
            <jet-label for="title" value="Title" />
            <input
                id="title"
                type="text"
                class=""
                v-model="form.title"
                autofocus
            />
            <jet-input-error :message="form.errors.title" class="mt-2" />
        </div>
        <div class="col-span-6">
            <jet-label for="description" value="Description" />
            <textarea id="description" class="" v-model="form.description" />
            <jet-input-error :message="form.errors.description" class="mt-2" />
        </div>
        <div class="col-span-6">
            <jet-label for="calendar_event_type" value="Event Type" />
            <select v-model="form.event_type_id">
                <option v-for="{ id, name } in calendarEventTypes" :value="id">
                    {{ name }}
                </option>
            </select>
            <jet-input-error :message="form.errors.type" class="mt-2" />
        </div>
        <div class="col-span-6">
            <jet-label for="full_day_event" value="All Day" />
            <input
                id="full_day_event"
                type="checkbox"
                class=""
                v-model="form.full_day_event"
            />
            <jet-input-error :message="form.errors.start" class="mt-2" />
        </div>
        <div class="col-span-3">
            <jet-label for="start" value="Start" />
            <date-picker
                required
                dark
                id="start"
                v-model="form.start"
                :enable-time-picker="!form.full_day_event"
                :format="dateFormat"
                :month-change-on-scroll="false"
                :auto-apply="true"
                :close-on-scroll="true"
            />
            <jet-input-error :message="form.errors.start" class="mt-2" />
        </div>

        <div class="col-span-3">
            <jet-label for="end" value="End" />
            <date-picker
                required
                dark
                id="end"
                v-model="form.end"
                :enable-time-picker="!form.full_day_event"
                :format="dateFormat"
                :month-change-on-scroll="false"
                :auto-apply="true"
                :close-on-scroll="true"
            />
            <jet-input-error :message="form.errors.end" class="mt-2" />
        </div>

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
                    :key="attendee.id">
                    <th>{{ attendee.id }}</th>
                    <td>{{ attendee.name }}</td>
                    <td>{{ attendee.email }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <input id="client_id" type="hidden" v-model="form.client_id" />

        <div class="flex flex-row col-span-6 mt-8">
            <div class="flex-grow" />
            <Button
                type="button"
                @click="handleSubmit"
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </div>
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

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DatePicker,
    },
    props: ["clientId", "calendar_event"],
    setup(props, { emit }) {
        const page = usePage();

        let calendarEvent = props.calendar_event;
        const calendarEventTypes = page.props.value.calendar_event_types;

        let operation = "Update";
        if (!calendarEvent) {
            calendarEvent = {
                title: null,
                description: null,
                full_day_event: false,
                start: null,
                end: null,
                event_type_id: null,
                client_id: page.props.value.user?.current_client_id,
            };
            operation = "Create";
        }

        const form = useForm(calendarEvent);

        watchEffect(() => {
            if( form.end){
                return;
            }
            let start = form.start;

            let end = form.end;
            let tempEnd = false;
            if(typeof start ==='string'){
                start = new Date(Date.parse(start));
            }

            if (form.end) {
                if(typeof end === 'string'){
                    end = new Date(Date.parse(form.end));
                    console.log({end});
                }
                console.log({end: form.end, type: typeof end})
                tempEnd = new Date(form.end).setHours(end.getHours() + 1);
            }

            if (start || (tempEnd && tempEnd < start)) {
                const newEnd = new Date(start.getTime());
                newEnd.setHours(start.getHours() + 1);
                console.log({ start, newEnd });
                form.end = newEnd;
            }
        });

        const transformDate = (date) => {
            if (!date?.toISOString) {
                return date;
            }

            return date.toISOString().slice(0, 19).replace("T", " ");
        };

        let handleSubmit = () =>
            form
                .transform((data) => ({
                    ...data,
                    start: transformDate(data.start),
                    end: transformDate(data.end),
                }))
                .put(route("calendar.event.update", calendarEvent.id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        form.reset();
                        emit("submitted")
                    },
                });

        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform((data) => ({
                        ...data,
                        start: transformDate(data.start),
                        end: transformDate(data.end),
                    }))
                    .post(route("calendar.event.store"), {
                        preserveScroll: true,
                        onSuccess: () => {
                            form.reset();
                            emit("submitted")
                        },
                    });
        }

        const dateFormat = computed(() =>
            form.full_day_event ?   "MM/dd/yyyy" : "MM/dd/yyyy hh:mm"
        );

        return {
            form,
            buttonText: operation,
            handleSubmit,
            calendarEventTypes,
            dateFormat,
        };
    },
};
</script>
