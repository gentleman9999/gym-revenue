<template>
    <form
        @submit="handleSubmit"
        class="w-full flex flex-col px-6 py-6 space-y-4 mb-12"
    >
        <div class="flex flex-row justify-between">
            <div class="flex">
                <jet-label for="full_day_event" value="All Day" />
                <input
                    id="full_day_event"
                    type="checkbox"
                    class="toggle ml-2"
                    v-model="form.full_day_event"
                    :disabled="
                        calendar_event?.type.type == 'Task' ||
                        calendar_event?.editable === 0
                    "
                />
                <jet-input-error :message="form.errors.start" class="mt-2" />
            </div>
            <div v-if="calendar_event?.event_completion == null">
                Project Status:
                <div class="badge badge-error gap-2 bg-base-content">
                    incomplete
                </div>
            </div>
            <div v-else>
                Project Status:
                <div class="badge badge-success gap-2 bg-base-content">
                    complete
                </div>
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
                :disabled="calendar_event?.editable === 0"
                :enable-time-picker="!form.full_day_event"
                :format="dateFormat"
                :month-change-on-scroll="false"
                :auto-apply="true"
                :close-on-scroll="true"
                class="bg-neutral-800 text-neutral-100"
                dark
            />
            <jet-input-error :message="form.errors.start" class="mt-2" />
        </div>
        <!-- can not accurately test button disabled because of error invalid date -->
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
                    class="bg-neutral-800 text-neutral-100"
                    dark
                />
                <jet-input-error :message="form.errors.end" class="mt-2" />
            </div>

            <div class="flex flex-col">
                <jet-label for="user_attendees" value="Select User Attendees" />
                <multiselect
                    v-model="form.user_attendees"
                    class="bg-neutral-800 text-neutral-100 py-2"
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

        <div class="col-span-6">
            <jet-label for="title" value="Title" />
            <input
                id="title"
                type="text"
                class="bg-neutral text-base-content"
                v-model="form.title"
                :disabled="calendar_event?.editable === 0"
            />
            <jet-input-error :message="form.errors.title" class="mt-2" />
        </div>
        <div class="col-span-6">
            <jet-label for="description" value="Description" />
            <textarea
                id="description"
                class="bg-neutral text-base-content"
                v-model="form.description"
                :disabled="calendar_event?.editable === 0"
            />
            <jet-input-error :message="form.errors.description" class="mt-2" />
        </div>
        <div v-if="calendarEventTypes.length > 1" class="col-span-6">
            <jet-label for="calendar_event_type" value="Event Type" />
            <select
                :disabled="calendar_event?.editable === 0"
                v-model="form.event_type_id"
                class="bg-neutral text-base-content"
            >
                <option
                    v-for="{ id, name } in calendarEventTypes"
                    :value="id"
                    :key="id"
                >
                    {{ name }}
                </option>
            </select>
            <jet-input-error
                :message="form.errors.event_type_id"
                class="mt-2"
            />
            <jet-input-error
                :message="form.errors.event_type_id"
                class="mt-2"
            />
        </div>

        <div class="col-span-6">
            <jet-label for="location_id" value="Event Location" />
            <select
                :disabled="calendar_event?.editable === 0"
                v-model="form.location_id"
                class="bg-neutral text-base-content"
            >
                <option v-for="{ id, name } in locations" :value="id" :key="id">
                    {{ name }}
                </option>
            </select>
            <jet-input-error :message="form.errors.location_id" class="mt-2" />
        </div>

        <div class="col-span-6"></div>

        <div class="col-span-3">
            <jet-label for="lead_attendees" value="Select Lead Attendees" />
            <multiselect
                v-model="form.lead_attendees"
                :class="{
                    'opacity-75 cursor-not-allowed border-none':
                        calendar_event?.editable === 0,
                }"
                class="py-2 bg-neutral-800 text-neutral-100"
                id="lead_attendees"
                mode="tags"
                :disabled="calendar_event?.editable === 0"
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
                :class="{
                    'opacity-75 cursor-not-allowed border-none':
                        calendar_event?.editable === 0,
                }"
                class="py-2 bg-neutral-800 text-neutral-100"
                id="member_attendees"
                mode="tags"
                :disabled="calendar_event?.editable === 0"
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
                @click.prevent="
                    () => {
                        if (calendar_event?.editable === 1) {
                            showAttendeesModal.open;
                        }
                    }
                "
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

        <div v-if="calendar_event?.call_task === 1" class="flex flex-col">
            <label class="text-lg mt-8 mb-4" for="callOutcome"
                >Call Outcome</label
            >
            <input
                v-model="callOutcomeField"
                class="py-6 mt-2 !border-2 !border-secondary"
                type="text"
                name=""
                id="callOutcome"
            />

            <Button
                @click="handleSubmitCallOutcome"
                class="mt-4 mx-auto"
                secondary
                size="sm"
                type="button"
                >Save outcome</Button
            >
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
                    class="bg-neutral-800 text-neutral-100"
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
        <div
            v-if="
                (calendar_event?.call_task === 0 &&
                    calendar_event?.editable === 1) ||
                !calendar_event
            "
            class="flex flex-row col-span-6 mt-8"
        >
            <div class="flex-grow" />
            <Button
                type="button"
                @click="handleSubmit"
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
                size="sm"
            >
                {{ buttonText }}
            </Button>
        </div>

        <daisy-modal ref="showAttendeesModal" id="showAttendeesModal">
            <h1 class="font-bold mb-4">Attendees</h1>
            <attendees-form
                @submitted="closeModals"
                :calendar_event="calendar_event"
                ref="attendeesModal"
            />
        </daisy-modal>

        <daisy-modal ref="showFilesModal" id="showFilesModal">
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
<style scoped>
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
import axios from "axios";
import { usePage } from "@inertiajs/inertia-vue3";
import { computed, watch, watchEffect, ref } from "vue";
import { toastError, toastSuccess } from "@/utils/createToast";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import DaisyModal from "@/Components/DaisyModal.vue";
import AttendeesForm from "@/Pages/Calendar/Partials/AttendeesForm.vue";
import FilesForm from "@/Pages/Calendar/Partials/FilesForm.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses, useGymRevForm } from "@/utils";
import FileManager from "./FileManager.vue";
import { Inertia } from "@inertiajs/inertia";
import FileIcon from "@/Components/Icons/File.vue";
import AddIcon from "@/Components/Icons/Add.vue";
import { transformDate } from "@/utils/transformDate";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

export default {
    components: {
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
        "duration",
        "start_date",
        "locations",
    ],
    setup: function (props, { emit }) {
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

        const callOutcomeField = ref(
            props?.calendar_event?.callOutcome
                ? props.calendar_event.callOutcome
                : ""
        );

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
                title: "",
                description: "",
                full_day_event: "",
                start: props.duration.start ?? null,
                end: props.duration.end
                    ? props.duration.end
                    : props.duration.start,
                event_type_id:
                    calendarEventTypes.length <= 1
                        ? calendarEventTypes[0].id
                        : "",
                location_id: null,
                client_id: page.props.value.user?.client_id,
                user_attendees: [],
                lead_attendees: [] ?? "",
                member_attendees: [],
                my_reminder: "",
            };
            operation = "Create";
        } else {
            calendarEventForm = {
                id: calendarEvent.id,
                title: calendarEvent.title,
                description: calendarEvent.description,
                full_day_event: calendarEvent.full_day_event,
                start: calendarEvent.start + " UTC",
                end: calendarEvent.end + " UTC",
                event_type_id:
                    calendarEventTypes?.length <= 1
                        ? calendarEventTypes[0].id
                        : calendarEvent.event_type_id,
                location_id: calendarEvent.location_id,
                client_id: page.props.value.user?.client_id,
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

        const form = useGymRevForm(calendarEventForm);

        watch(
            () => props.duration,
            (duration, oldDuration) => {
                form.start = duration.start;
                form.end = duration.end;
                if (!form.end && form.start) {
                    const newEnd = new Date(form.start.getTime());
                    newEnd.setHours(form.start.getHours() + 1);
                    form.end = newEnd;
                }
            },
            { deep: true }
        );

        const { mutate: createTask } = useMutation(mutations.task.create);
        const { mutate: updateTask } = useMutation(mutations.task.update);

        let handleSubmit = async () => {
            await updateTask({
                input: {
                    id: form.id,
                    title: form.title,
                    description: form.description,
                    event_type_id: form.event_type_id,
                    user_attendees: form.user_attendees,
                    lead_attendees: form.lead_attendees,
                    member_attendees: form.member_attendees,
                    location_id: form.location_id,
                    start: transformDate(form.start),
                    end: transformDate(form.end),
                    full_day_event: !!form.full_day_event,
                },
            });
            emit("submitted");
        };

        if (operation === "Create") {
            handleSubmit = async () => {
                await createTask({
                    input: {
                        title: form.title,
                        description: form.description,
                        event_type_id: form.event_type_id,
                        user_attendees: form.user_attendees,
                        lead_attendees: form.lead_attendees,
                        member_attendees: form.member_attendees,
                        location_id: form.location_id,
                        start: transformDate(form.start),
                        end: transformDate(form.end),
                        full_day_event: !!form.full_day_event,
                    },
                });
                emit("submitted");
            };
        }

        const dateFormat = computed(() =>
            form.full_day_event ? "MM/dd/yyyy" : "MM/dd/yyyy hh:mm"
        );

        const handleSubmitCallOutcome = async () => {
            console.log("call outcome being sent");

            if (calendarEvent.callOutcomeId) {
                await axios
                    .post(route("tasks.call-outcome.update"), {
                        outcome: callOutcomeField.value,
                        outcomeId: calendarEvent.callOutcomeId,
                        id: calendar_event?.id,
                        lead_attendees: calendarEventForm.lead_attendees,
                        member_attendees: calendarEventForm.member_attendees,
                    })
                    .then((res) => {
                        emit("submitted");
                        toastSuccess("Call outcome saved!");
                    })
                    .catch((err) => {
                        toastError(
                            "There was a problem saving the call outcome"
                        );
                    });
            } else {
                await axios
                    .post(route("tasks.call-outcome"), {
                        outcome: callOutcomeField.value,
                        id: calendar_event?.id,
                        lead_attendees: calendarEventForm.lead_attendees,
                        member_attendees: calendarEventForm.member_attendees,
                    })
                    .then((res) => {
                        emit("submitted");
                        toastSuccess("Call outcome saved!");
                    })
                    .catch((err) => {
                        toastError(
                            "There was a problem saving the call outcome"
                        );
                    });
            }
        };

        return {
            form,
            buttonText: operation,
            handleSubmit,
            calendarEventTypes,
            callOutcomeField,
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
            handleSubmitCallOutcome,
            multiselectClasses: {
                ...getDefaultMultiselectTWClasses(),
                dropdown:
                    "max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-base-content text-base-100 flex flex-col rounded-b",
            },
        };
    },
};
</script>
