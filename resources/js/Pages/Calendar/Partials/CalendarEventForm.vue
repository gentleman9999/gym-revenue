<template>
    <form
        @submit="handleSubmit"
        class="w-full flex flex-col px-6 py-6 space-y-4"
    >
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <div class="flex flex-row justify-between">
            <div class="flex">
                <jet-label for="full_day_event" value="All Day" />
                <input
                    id="full_day_event"
                    type="checkbox"
                    class="toggle ml-2"
                    v-model="form.full_day_event"
                    :disabled="calendar_event?.type.type == 'Task'"
                />
                <jet-input-error :message="form.errors.start" class="mt-2" />
            </div>
            <div v-if="calendar_event?.event_completion == null">
                Project Status:
                <div class="badge badge-error gap-2">incomplete</div>
            </div>
            <div v-else>
                Project Status:
                <div class="badge badge-success gap-2">complete</div>
            </div>
        </div>
        <div class="flex flex-col">
            <jet-label
                for="start"
                value="Due Date"
                v-if="calendar_event?.type.type == 'Task'"
            />
            <jet-label for="start" value="Starts" v-else />
            <date-picker
                required
                id="start"
                v-model="form.start"
                :enable-time-picker="!form.full_day_event"
                :format="dateFormat"
                :month-change-on-scroll="false"
                :auto-apply="true"
                :close-on-scroll="true"
                class="bg-neutral-100 text-neutral-900"
            />
            <jet-input-error :message="form.errors.start" class="mt-2" />
        </div>
        <template v-if="calendar_event?.type.type !== 'Task'">
            <div class="flex flex-col">
                <jet-label for="end" value="Ends" />
                <date-picker
                    required
                    id="end"
                    v-model="form.end"
                    :enable-time-picker="!form.full_day_event"
                    :format="dateFormat"
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                    :disabled="calendar_event?.type.type === 'Task'"
                    class="bg-neutral-100 text-neutral-900"
                />
                <jet-input-error :message="form.errors.end" class="mt-2" />
            </div>

            <div class="flex flex-col">
                <jet-label for="user_attendees" value="Select User Attendees" />
                <multiselect
                    v-model="form.user_attendees"
                    class="bg-neutral-100 text-neutral-900 py-2"
                    id="user_attendees"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        this.$page.props.client_users.map((user) => ({
                            label: user.name,
                            value: user.id,
                        }))
                    "
                    :classes="multiselectClasses"
                    :disabled="calendar_event?.type.type === 'Task'"
                />
            </div>
        </template>

        <!-- <div class="col-span-6" v-if="calendar_event?.owner_id">
            <div class="avatar">
                <div
                    class="w-20 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2"
                >
                    <img :src="calendar_event?.event_owner.profile_photo_url" />
                </div>
            </div>
            <div>
                <h1>
                    {{ calendar_event?.event_owner.name }}
                </h1>
            </div>
        </div> -->

        <div class="col-span-6">
            <jet-label for="title" value="Title" />
            <input
                id="title"
                type="text"
                class="bg-neutral-100 text-neutral-900"
                v-model="form.title"
                autofocus
            />
            <jet-input-error :message="form.errors.title" class="mt-2" />
        </div>
        <div class="col-span-6">
            <jet-label for="description" value="Description" />
            <textarea
                id="description"
                class="bg-neutral-100 text-neutral-900"
                v-model="form.description"
            />
            <jet-input-error :message="form.errors.description" class="mt-2" />
        </div>
        <div class="col-span-6">
            <jet-label for="calendar_event_type" value="Event Type" />
            <select
                v-model="form.event_type_id"
                class="bg-neutral-100 text-neutral-900"
            >
                <option v-for="{ id, name } in calendarEventTypes" :value="id">
                    {{ name }}
                </option>
            </select>
            <jet-input-error :message="form.errors.type" class="mt-2" />
        </div>
        <div class="col-span-6"></div>

        <div class="col-span-3">
            <jet-label for="lead_attendees" value="Select Lead Attendees" />
            <multiselect
                v-model="form.lead_attendees"
                class="py-2 bg-neutral-100 text-neutral-900"
                id="lead_attendees"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                    this.$page.props.lead_users.map((user) => ({
                        label: user.first_name + ' ' + user.last_name,
                        value: user.id,
                    }))
                "
                :classes="multiselectClasses"
            />
        </div>

        <div class="col-span-3">
            <jet-label for="member_attendees" value="Select Member Attendees" />
            <multiselect
                v-model="form.member_attendees"
                class="py-2 bg-neutral-100 text-neutral-900"
                id="member_attendees"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                    this.$page.props.member_users.map((user) => ({
                        label: user.first_name + ' ' + user.last_name,
                        value: user.id,
                    }))
                "
                :classes="multiselectClasses"
            />
        </div>

        <div
            class="flex flex-row space-x-2 items-center"
            v-if="calendar_event?.attendees?.length"
        >
            <jet-label for="attendeesModal" value="View All Attendees" />
            <button
                @click.prevent="showAttendeesModal.open"
                class="btn w-max rounded btn-secondary btn-sm"
            >
                Open List
            </button>
        </div>

        <div class="flex flex-row space-x-4 items-center">
            <jet-label for="attendeesModal" value="Attachments" />
            <button @click.prevent="showFilesModal.open">
                <file-icon />
            </button>
            <button @click.prevent="handleClickUpload">
                <add-icon />
            </button>
        </div>

        <div
            class="flex flex-row space-x-2 items-center"
            v-if="
                calendar_event?.event_completion == null &&
                calendar_event?.type.type == 'Task'
            "
        >
            <jet-label for="complete_event" value="Event Completion" />
            <Button
                @click.prevent="handleCompleteTask(calendar_event.id)"
                secondary
                size="sm"
            >
                Complete Task
            </Button>
            <jet-input-error
                :message="form.errors.complete_event"
                class="mt-2"
            />
        </div>

        <template v-if="calendar_event?.im_attending">
            <div class="col-span-6 space-x-2">
                <div class="divider divider-horizontal">
                    My Meeting Settings
                </div>
            </div>

            <div
                class="col-span-2 space-x-2"
                v-if="calendar_event?.my_reminder"
            >
                <jet-label
                    for="my_reminder"
                    value="Minutes Before Event to Notify Me"
                />
                <input
                    id="my_reminder"
                    type="text"
                    class="bg-neutral-100 text-neutral-900"
                    v-model="form.my_reminder"
                />
                <jet-input-error
                    :message="form.errors.my_reminder"
                    class="mt-2"
                />
            </div>

            <div
                class="flex flex-row space-x-2 items-center"
                v-if="calendar_event?.my_reminder"
            >
                <jet-label for="reminder_option" value="Delete Reminder" />
                <Button
                    @click.prevent="
                        handleReminderDelete(calendar_event.my_reminder.id)
                    "
                    secondary
                    size="sm"
                >
                    Delete
                </Button>
                <jet-input-error
                    :message="form.errors.reminder_option"
                    class="mt-2"
                />
            </div>
            <div class="flex flex-row space-x-2 items-center" v-else>
                <jet-label for="reminder_option" value="Create Reminder" />
                <Button
                    @click.prevent="handleReminderCreate(calendar_event.id)"
                    secondary
                    size="sm"
                >
                    Create
                </Button>
                <jet-input-error
                    :message="form.errors.reminder_option"
                    class="mt-2"
                />
            </div>
        </template>

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
                size="sm"
            >
                {{ buttonText }}
            </Button>
        </div>

        <daisy-modal ref="showAttendeesModal" id="showAttendeesModal" @close="">
            <h1 class="font-bold mb-4">Attendees</h1>
            <attendees-form
                @submitted="closeModals"
                :calendar_event="calendar_event"
                ref="attendeesModal"
            />
        </daisy-modal>

        <daisy-modal ref="showFilesModal" id="showFilesModal" @close="">
            <h1 class="font-bold mb-4">File Attachments</h1>
            <files-form
                @submitted="closeModals"
                :calendar_event="calendar_event"
                ref="filesModal"
            />
        </daisy-modal>

        <daisy-modal
            ref="uploadFiles"
            id="uploadFiles"
            class="lg:max-w-5xl bg-base-300"
        >
            <file-manager
                @submitted="closeModals"
                :client_id="$page.props.client_id"
                :entity_id="calendar_event?.id"
            />
        </daisy-modal>
    </form>
</template>
<style scope>
.toggle:checked:focus:focus {
    box-shadow: var(--handleoffset) 0 0 2px hsl(var(--b1)) inset,
        0 0 0 2px hsl(var(--b1)) inset, var(--focus-shadow);
}
.toggle:focus:focus {
    box-shadow: calc(var(--handleoffset) * -1) 0 0 2px hsl(var(--b1)) inset,
        0 0 0 2px hsl(var(--b1)) inset, var(--focus-shadow);
}
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
import { computed, watchEffect, watch, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import DaisyModal from "@/Components/DaisyModal";
import AttendeesForm from "@/Pages/Calendar/Partials/AttendeesForm";
import FilesForm from "@/Pages/Calendar/Partials/FilesForm";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import FileManager from "./FileManager";
import { Inertia } from "@inertiajs/inertia";
import FileIcon from "@/Components/Icons/File";
import AddIcon from "@/Components/Icons/Add";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DatePicker,
        DaisyModal,
        AttendeesForm,
        FilesForm,
        Multiselect,
        FileManager,
        FileIcon,
        AddIcon,
    },
    props: [
        "client_id",
        "calendar_event",
        "client_users",
        "lead_users",
        "member_users",
    ],
    setup(props, { emit }) {
        const page = usePage();

        const handleReminderDelete = (id) => {
            Inertia.delete(route("calendar.reminder.delete", id));
            emit("submitted");
        };
        const handleReminderCreate = (id) => {
            Inertia.put(route("calendar.reminder.create", id));
            emit("submitted");
        };

        const handleCompleteTask = (id) => {
            Inertia.put(route("calendar.complete_event", id));
            emit("submitted");
        };

        const calendar_event = props.calendar_event;

        let calendarEvent = props.calendar_event;
        const calendarEventTypes = page.props.value.calendar_event_types;

        const showAttendeesModal = ref();
        const attendeesModal = ref(null);

        const showFilesModal = ref();
        const filesModal = ref(null);

        const closeModals = () => {
            showAttendeesModal.value.close();
        };

        const uploadFiles = ref();
        const handleClickUpload = () => {
            uploadFiles.value.open();
        };

        let calendarEventForm = null;
        let operation = "Update";
        if (!calendarEvent) {
            calendarEventForm = {
                title: null,
                description: null,
                full_day_event: false,
                start: null,
                end: null,
                event_type_id: null,
                client_id: page.props.value.user?.current_client_id,
                user_attendees: [],
                lead_attendees: null,
                member_attendees: null,
                my_reminder: null,
            };
            operation = "Create";
        } else {
            calendarEventForm = {
                title: calendarEvent.title,
                description: calendarEvent.description,
                full_day_event: calendarEvent.full_day_event,
                start: calendarEvent.start,
                end: calendarEvent.end,
                event_type_id: calendarEvent.event_type_id,
                client_id: page.props.value.user?.current_client_id,
                user_attendees:
                    calendarEvent.user_attendees?.map(
                        (user_attendee) => user_attendee.id
                    ) || [],
                lead_attendees:
                    calendarEvent.lead_attendees?.map(
                        (lead_attendee) => lead_attendee.id
                    ) || [],
                member_attendees:
                    calendarEvent.member_attendees?.map(
                        (member_attendee) => member_attendee.id
                    ) || [],
                my_reminder: calendar_event?.my_reminder?.remind_time,
            };
        }

        const form = useForm(calendarEventForm);

        watchEffect(() => {
            if (form.end) {
                return;
            }
            let start = form.start;

            let end = form.end;
            let tempEnd = false;
            if (typeof start === "string") {
                start = new Date(Date.parse(start));
            }

            if (form.end) {
                if (typeof end === "string") {
                    end = new Date(Date.parse(form.end));
                    console.log({ end });
                }
                console.log({ end: form.end, type: typeof end });
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
                        emit("submitted");
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
                            emit("submitted");
                        },
                    });
        }

        const dateFormat = computed(() =>
            form.full_day_event ? "MM/dd/yyyy" : "MM/dd/yyyy hh:mm"
        );

        return {
            form,
            buttonText: operation,
            handleSubmit,
            calendarEventTypes,
            dateFormat,
            calendar_event,
            closeModals,
            attendeesModal,
            filesModal,
            showAttendeesModal,
            showFilesModal,
            uploadFiles,
            handleClickUpload,
            handleReminderDelete,
            handleReminderCreate,
            handleCompleteTask,
            multiselectClasses: {
                ...getDefaultMultiselectTWClasses(),
                dropdown:
                    "max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-base-content text-base-100 flex flex-col rounded-b",
            },
        };
    },
};
</script>
