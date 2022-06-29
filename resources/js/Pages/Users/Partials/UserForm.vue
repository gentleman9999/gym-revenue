<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title> User Profile </template>

        <template #description> Information about the user. </template>
        <template #form>
            <!-- First Name -->
            <div class="form-control col-span-2">
                <jet-label for="fname" value="First Name" />
                <input
                    id="first_name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.first_name"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.first_name"
                    class="mt-2"
                />
            </div>
            <!-- Last Name -->
            <div class="form-control col-span-2">
                <jet-label for="name" value="Last Name" />
                <input
                    id="last_name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.last_name"
                />
                <jet-input-error
                    :message="form.errors.first_name"
                    class="mt-2"
                />
            </div>
            <!-- (Work) Email -->
            <div class="form-control col-span-2">
                <jet-label for="email" value="Work Email" />
                <input
                    id="email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.email"
                />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>

            <!-- Personal Email -->
            <div class="form-control col-span-2">
                <jet-label for="email" value="Personal Email" />
                <input
                    id="alternate_email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.alternate_email"
                />
                <jet-input-error
                    :message="form.errors.alternate_email"
                    class="mt-2"
                />
            </div>

            <!-- Contact Phone # -->
            <div class="form-control col-span-2">
                <jet-label for="phone" value="Contact Phone" />
                <phone-input
                    id="phone"
                    class="block w-full mt-1"
                    v-model="form.phone"
                />
                <jet-input-error :message="form.errors.phone" class="mt-2" />
            </div>

            <!-- Address 1 -->
            <div class="form-control col-span-6">
                <jet-label for="address1" value="Home Address" />
                <input
                    id="address1"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address1"
                />
                <jet-input-error :message="form.errors.address1" class="mt-2" />
            </div>
            <!-- Address 2 -->
            <div class="form-control col-span-6">
                <jet-label for="address2" value="Home Address (cont)" />
                <input
                    id="address2"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address2"
                />
                <jet-input-error :message="form.errors.address2" class="mt-2" />
            </div>
            <!-- City -->
            <div class="form-control col-span-2">
                <jet-label for="city" value="City" />
                <input
                    id="city"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.city"
                />
                <jet-input-error :message="form.errors.city" class="mt-2" />
            </div>
            <!-- State -->
            <div class="form-control col-span-2">
                <jet-label for="state" value="State" />
                <multiselect
                    id="state"
                    class="mt-1 multiselect"
                    v-model="form.state"
                    :searchable="true"
                    :create-option="true"
                    :options="optionStates"
                    :classes="multiselectClasses"
                />
                <jet-input-error :message="form.errors.state" class="mt-2" />
            </div>

            <!-- Zip -->
            <div class="form-control col-span-2">
                <jet-label for="zip" value="Zip Code" />
                <input
                    id="zip"
                    type="number"
                    class="block w-full mt-1"
                    v-model="form.zip"
                />
                <jet-input-error :message="form.errors.zip" class="mt-2" />
            </div>

            <div class="form-divider" />

            <!-- Official Position -->
            <div class="form-control col-span-2">
                <jet-label for="job_title" value="Official Position" />
                <input
                    id="job_title"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.job_title"
                />
                <jet-input-error
                    :message="form.errors.job_title"
                    class="mt-2"
                />
            </div>

            <!-- Contact Preference -->
            <div class="form-control col-span-2">
                <jet-label
                    for="contact_preference"
                    value="Contact Preference"
                />
                <select
                    id="contact_preference"
                    class="block w-full mt-1"
                    v-model="form.contact_preference"
                >
                    <option value="email">Email</option>
                    <option value="sms">Text Message</option>
                </select>
                <jet-input-error
                    :message="form.errors.contact_preference"
                    class="mt-2"
                />
            </div>

            <!-- Classifications -->
            <div class="form-control col-span-2" v-if="clientId">
                <jet-label for="classification" value="Classification" />
                <select
                    id="classification"
                    class="block w-full mt-1"
                    v-model="form.classification_id"
                >
                    <option
                        v-for="classy in classifications"
                        :value="classy.id"
                    >
                        {{ classy.title }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.classification_id"
                    class="mt-2"
                />
            </div>

            <!-- Security Role -->
            <div class="form-control col-span-2" v-if="clientId">
                <jet-label for="role_id" value="Security Role" />
                <select
                    id="role_id"
                    class="block w-full mt-1"
                    v-model="form.role_id"
                >
                    <option v-for="role_id in roles" :value="role_id.id">
                        {{ role_id.title }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.role_id" class="mt-2" />
            </div>

            <!-- Home Club -->
            <div class="form-control col-span-2" v-if="clientId">
                <jet-label for="home_location_id" value="Home Club" />
                <select
                    id="home_location_id"
                    class="block w-full mt-1"
                    v-model="form.home_location_id"
                >
                    <option
                        v-for="{ gymrevenue_id, name } in locations"
                        :value="gymrevenue_id"
                    >
                        {{ name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.home_location_id"
                    class="mt-2"
                />
            </div>

            <!-- Start Date -->

            <div class="form-control col-span-2">
                <jet-label for="start_date" value="Date / Start of Work" />
                <DatePicker
                    v-model="form['start_date']"
                    :enableTimePicker="false"
                    dark
                />
                <jet-input-error
                    :message="form.errors.start_date"
                    class="mt-2"
                />
            </div>

            <!-- End Date -->
            <div class="form-control col-span-2">
                <jet-label for="end_date" value="Date / End of Work" />
                <DatePicker
                    v-model="form['end_date']"
                    :enableTimePicker="false"
                    dark
                />
                <jet-input-error :message="form.errors.end_date" class="mt-2" />
            </div>

            <!-- Termination Date -->
            <div class="form-control col-span-2">
                <jet-label for="termination_date" value="Date of Termination" />
                <DatePicker
                    v-model="form['termination_date']"
                    :enableTimePicker="false"
                    dark
                />
                <jet-input-error
                    :message="form.errors.termination_date"
                    class="mt-2"
                />
            </div>

            <div class="form-divider" />
            <!-- Notes -->
            <jet-label for="notes" value="Notes" />
            <div class="form-control col-span-6">
                <jet-label for="notes.title" value="Title" />
                <input
                    type="text"
                    v-model="form['notes'].title"
                    id="notes.title"
                />
                <jet-input-error
                    :message="form.errors['notes']?.title"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-6">
                <jet-label for="notes" value="Body" />
                <textarea v-model="form['notes'].note" id="notes" />
                <jet-input-error
                    :message="form.errors['notes']?.note"
                    class="mt-2"
                />
            </div>
            <template v-if="user?.all_notes?.length">
                <div class="text-sm font-medium col-span-6">Existing Notes</div>
                <div
                    class="collapse col-span-6"
                    tabindex="0"
                    v-for="note in user.all_notes"
                >
                    <div
                        class="collapse-title text-sm font-medium"
                        v-on:click="notesExpanded(note)"
                    >
                        > {{ note.title }}
                        <div
                            v-if="note.read == false"
                            class="badge badge-secondary"
                        >
                            unread
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 collapse-content">
                        <div
                            class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                        >
                            {{ note.note }}
                        </div>
                    </div>
                </div>
            </template>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="$inertia.visit(route('users'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
    <jet-form-section v-if="clientId && user?.files" class="mt-16">
        <template #title> Documents </template>

        <template #description> Documents attached to the user. </template>
        <template #form>
            <!-- Files -->
            <div class="col-span-6">
                <div class="flex flex-col gap-2">
                    <template v-if="user?.files?.length">
                        <div
                            v-for="file in user.files"
                            class="rounded-lg bg-base-100 p-2 flex items-center"
                        >
                            <div class="flex-grow">
                                <a
                                    :href="file.url"
                                    :download="file.filename"
                                    target="_blank"
                                    >{{ file.filename }}</a
                                >
                            </div>
                            <button
                                type="button"
                                class="p-2"
                                @click="wantsToDeleteFile = file"
                            >
                                x
                            </button>
                        </div>
                    </template>
                    <div v-else class="opacity-50">No documents found.</div>
                </div>
            </div>
            <daisy-modal
                ref="fileManagerModal"
                class="lg:max-w-5xl bg-base-300"
                @close="resetFileManager"
            >
                <file-manager
                    ref="fileManager"
                    :client-id="clientId"
                    :user="user"
                    :form-submit-options="{ preserveScroll: true }"
                    @submitted="closeFileManagerModal"
                    :handleCancel="closeFileManagerModal"
                />
            </daisy-modal>
            <confirm
                title="Really Delete File?"
                v-if="wantsToDeleteFile"
                @confirm="handleConfirmDeleteFile"
                @cancel="wantsToDeleteFile = null"
            >
                Are you sure you want to delete file '{{
                    wantsToDeleteFile.filename
                }}'? This is permanent, and cannot be undone.
            </confirm>
        </template>
        <template #actions>
            <button
                class="btn btn-secondary"
                type="button"
                @click="fileManagerModal.open"
            >
                Upload Files
            </button>
        </template>
    </jet-form-section>
</template>

<script>
import { ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import DatePicker from "@vuepic/vue-datepicker";

import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";
import DaisyModal from "@/Components/DaisyModal";
import states from "@/Pages/Comms/States/statesOfUnited";
import FileManager from "@/Pages/Files/Partials/FileManager";
import { transformDate } from "@/utils/transformDate";
import PhoneInput from "@/Components/PhoneInput";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DatePicker,
        Multiselect,
        Confirm,
        DaisyModal,
        FileManager,
        PhoneInput,
    },
    props: ["clientId", "user", "clientName"],
    emits: ["success"],
    setup(props, { emit }) {
        function notesExpanded(note) {
            console.error(props.user.all_notes);
            axios.post(route("note.seen"), {
                client_id: props.user.clientId,
                note: note,
            });
        }

        const wantsToDeleteFile = ref(null);
        const page = usePage();
        let user = props.user;
        const roles = page.props.value.roles;
        const classifications = page.props.value.classifications;
        const locations = page.props.value.locations;

        const team_id = page.props.value.user.current_team_id;

        let operation = "Update";
        if (user) {
            user.role_id = user["role_id"];
            user.classification_id = user.classification_id;
            user.contact_preference = user["contact_preference"]?.value;
            user.team_id = team_id;
            user.first_name = user["first_name"];
            user.last_name = user["last_name"];
            user.alternate_email = user["alternate_email"];
            user.address1 = user["address1"];
            user.address2 = user["address2"];
            user.city = user["city"];
            user.state = user["state"];
            user.zip = user["zip"];
            user.job_title = user["job_title"];
            user.phone = user["phone"];
            user.start_date = user["start_date"];
            user.end_date = user["end_date"];
            user.termination_date = user["termination_date"];
            user.notes = { title: "", note: "" };
        } else {
            user = {
                first_name: "",
                last_name: "",
                email: "",
                alternate_email: "",
                role_id: 0,
                classification_id: "",
                contact_preference: null,
                phone: "",
                address1: "",
                address2: "",
                city: "",
                team_id,
                notes: { title: "", note: "" },
                start_date: "",
                end_date: "",
                termination_date: "",
                state: "",
                zip: "",
                job_title: "",
                client_id: props.clientId,
            };
            //only add clientId when applicable to make user validation rules work better
            if (props.clientId) {
                user.client_id = props.clientId;
                user.home_location_id = null;
                user.notes = { title: "", note: "" };
                user.start_date = null;
                user.end_date = null;
                user.termination_date = null;
            }
            operation = "Create";
        }
        const form = useGymRevForm(user);
        let upperCaseF = (text) => {
            form.state = text.toUpperCase();
        };

        const transformFormSubmission = (data) => {
            if (!data.notes?.title) {
                delete data.notes;
            }
            if (data?.start_date) {
                data.start_date = transformDate(data.start_date);
                data.end_date = transformDate(data.end_date);
                data.termination_date = transformDate(data.termination_date);
            }
            return data;
        };

        let handleSubmit = () =>
            form
                .dirty()
                .transform(transformFormSubmission)
                .put(route("users.update", user.id), {
                    onSuccess: () => (form.notes = { title: "", note: "" }),
                });
        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform(transformFormSubmission)
                    .post(route("users.store"), {
                        onSuccess: () => (form.notes = { title: "", note: "" }),
                    });
        }

        const handleConfirmDeleteFile = () => {
            Inertia.delete(route("files.trash", wantsToDeleteFile.value), {
                preserveScroll: true,
            });
            wantsToDeleteFile.value = null;
        };

        const fileManagerModal = ref();
        const fileManager = ref();

        // const closeFileManagerModal = computed(()=>{
        //     console.log({fileManager: fileManager.value})
        //     const reset = fileManager.value?.reset || fileManager?.reset || false;
        //     console.log({reset})
        //     return fileManager.value?.reset;
        // })

        const closeFileManagerModal = () => {
            console.log({ fileManager: fileManager.value });
            const reset =
                fileManager.value?.reset || fileManager?.reset || false;
            console.log({ reset });
            return fileManager.value?.reset;
        };

        let optionsStates = [];
        for (let x in states) {
            optionsStates.push(states[x].abbreviation);
        }

        return {
            form,
            buttonText: operation,
            operation,
            handleSubmit,
            roles,
            classifications,
            upperCaseF,
            locations,
            optionStates: optionsStates,
            multiselectClasses: getDefaultMultiselectTWClasses(),
            wantsToDeleteFile,
            handleConfirmDeleteFile,
            fileManagerModal,
            fileManager,
            closeFileManagerModal,
            notesExpanded,
            // closeFileManagerModal: ()=> fileManagerModal.value.close(),
            // resetFileManager: () => console.log(fileManager.value)
            // resetFileManager: () => fileManager.value?.reset()
        };
    },
};
</script>

<style scoped>
input[type="text"],
input[type="email"],
select {
    @apply w-full;
}

.form-divider {
    @apply col-span-6 border-t-2 border-secondary relative;
}

.form-divider > span {
    @apply absolute inset-0  transform -translate-y-1/2 text-xs text-opacity-30 bg-base-300 block;
}
</style>
