<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title>
            User Profile
        </template>

        <template #description>
            Information about the user.
        </template>
        <template #form>
            <!-- First Name -->
            <div class="form-control col-span-4">
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
            <div class="form-control col-span-4">
                <jet-label for="name" value="Last Name" />
                <input
                    id="last_name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.last_name"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.first_name"
                    class="mt-2"
                />
            </div>
            <!-- (Work) Email -->
            <div class="form-control col-span-4">
                <jet-label for="email" value="Work Email" />
                <input
                    id="email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.email"
                    autofocus
                />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>

            <!-- Personal Email -->
            <div class="form-control col-span-4">
                <jet-label for="email" value="Personal Email" />
                <input
                    id="altEmail"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.altEmail"
                    autofocus
                />
                <jet-input-error :message="form.errors.altEmail" class="mt-2" />
            </div>

            <!-- Address 1 -->
            <div class="form-control col-span-9">
                <jet-label for="address1" value="Home Address" />
                <input
                    id="address1"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address1"
                    autofocus
                />
                <jet-input-error :message="form.errors.address1" class="mt-2" />
            </div>
            <!-- Address 2 -->
            <div class="form-control col-span-9">
                <jet-label for="address2" value="Home Address (cont)" />
                <input
                    id="address2"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address2"
                    autofocus
                />
                <jet-input-error :message="form.errors.address2" class="mt-2" />
            </div>
            <!-- City -->
            <div class="form-control col-span-3">
                <jet-label for="city" value="City" />
                <input
                    id="city"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.city"
                    autofocus
                />
                <jet-input-error :message="form.errors.city" class="mt-2" />
            </div>
            <!-- State -->
            <div class="form-control col-span-3">
                <jet-label for="state" value="State" />
                    <multiselect
                        id="state"
                        class="mt-1 multiselect-search"
                        v-model="form.state"
                        :searchable="true"
                        :create-option="true"
                        :options="optionStates"
                        :classes="multiselectClasses"
                    />
                <jet-input-error :message="form.errors.state" class="mt-2" />
            </div>

            <!-- Zip -->
            <div class="form-control col-span-3">
                <jet-label for="zip" value="Zip Code" />
                <input
                    id="zip"
                    type="number"
                    class="block w-full mt-1"
                    v-model="form.zip"
                    autofocus
                />
                <jet-input-error :message="form.errors.zip" class="mt-2" />
            </div>

            <!-- Official Position -->
            <div class="form-control col-span-3">
                <jet-label for="jobTitle" value="Official Position" />
                <input
                    id="jobTitle"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.jobTitle"
                    autofocus
                />
                <jet-input-error :message="form.errors.jobTitle" class="mt-2" />
            </div>

            <!-- Contact Phone # -->
            <div class="form-control col-span-3">
                <jet-label for="phone" value="Contact Phone" />
                <input
                    id="phone"
                    type="tel"
                    class="block w-full mt-1"
                    v-model="form.phone"
                    autofocus
                />
                <jet-input-error :message="form.errors.phone" class="mt-2" />
            </div>


            <!-- Security Role -->
            <div class="form-control col-span-3" v-if="clientId">
                <jet-label for="classification" value="Classifications" />
                <select
                    id="classification"
                    class="block w-full mt-1"
                    v-model="form.classification"
                >
                    <option
                        v-for="classy in classifications"
                        :value="classy.id"
                    >
                        {{ classy.title }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.classification"
                    class="mt-2"
                />
            </div>



            <!-- Security Role -->
            <div class="form-control col-span-3" v-if="clientId">
                <jet-label for="role" value="Role" />
                <select
                    id="role"
                    class="block w-full mt-1"
                    v-model="form.role"
                >
                    <option
                        v-for="role in securityRoles"
                        :value="role.id"
                    >
                        {{ role.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.role"
                    class="mt-2"
                />
            </div>


            <!-- Home Club -->
            <div class="form-control col-span-3" v-if="clientId">
                <jet-label for="role" value="Home Club" />
                <select
                    id="home_club"
                    class="block w-full mt-1"
                    v-model="form.home_club"
                >
                    <option
                        v-for="{ gymrevenue_id, name } in locations"
                        :value="gymrevenue_id"
                    >
                        {{ name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.home_club"
                    class="mt-2"
                />
            </div>

            <!-- Start Date -->

            <div class="form-control col-span-3">
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
            <div class="form-control col-span-3">
                <jet-label for="end_date" value="Date / End of Work" />
                <DatePicker
                    v-model="form['end_date']"
                    :enableTimePicker="false"
                    dark
                />
                <jet-input-error :message="form.errors.end_date" class="mt-2" />
            </div>

            <!-- Termination Date -->
            <div class="form-control col-span-3">
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

            <!-- Notes -->
            <div class="form-control col-span-9">
                <jet-label for="notes" value="Notes" />
                <textarea v-model="form['notes']" dark rows="5" cols="33" />
                <jet-input-error :message="form.errors.notes" class="mt-2" />
            </div>
            <div class="collapse col-span-9" tabindex="0" v-if="user?.all_notes?.length">
                <div class="collapse-title text-sm font-medium">
                    > Existing Notes
                </div>
                <div class="flex flex-col  gap-2 collapse-content">
                    <div v-for="note in user.all_notes" class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2">
                        {{note}}
                    </div>
                </div>
            </div>
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
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
    <jet-form-section v-if="clientId && user?.files" class="mt-16">
        <template #title>
            Documents
        </template>

        <template #description>
            Documents attached to the user.
        </template>
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
                    <div v-else class="opacity-50"> No documents found. </div>

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
import { ref, computed } from "vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import DatePicker from "vue3-date-time-picker";

import "vue3-date-time-picker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import {getDefaultMultiselectTWClasses} from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";
import DaisyModal from "@/Components/DaisyModal";
import states from "@/Pages/Comms/States/statesOfUnited";
import FileManager from "@/Pages/Files/Partials/FileManager";

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
    },
    props: ["clientId", "user", "clientName"],
    emits: ["success"],
    setup(props, { emit }) {
        const wantsToDeleteFile = ref(null);
        const page = usePage();
        let user = props.user;
        const securityRoles = page.props.value.securityRoles;
        const classifications = page.props.value.classifications;
        const locations = page.props.value.locations;

        const team_id = page.props.value.user.current_team_id;
        let phone =
            user !== undefined &&
            "phone_number" in user &&
            user["phone_number"] &&
            "value" in user["phone_number"]
                ? user["phone_number"].value
                : null;

        let operation = "Update";
        if (user) {
            user.role =
                "role" in user && user["role"] !== null
                    ? user["role"].value ?? ""
                    : "";
            user.classification =
                "classification" in user && user["classification"] !== null
                    ? user["classification"].value ?? ""
                    : "";
            user.team_id = team_id;
            user.first_name = user["first_name"];
            user.last_name = user["last_name"];
            user.altEmail =
                "alt_email" in user && user["alt_email"] !== null
                    ? user["alt_email"].value ?? ""
                    : "";
            user.address1 =
                "address1" in user && user["address1"] !== null
                    ? user["address1"].value
                    : "";
            user.address2 =
                "address2" in user && user["address2"] !== null
                    ? user["address2"].value
                    : "";
            user.city =
                "city" in user && user["city"] !== null
                    ? user["city"].value
                    : "";
            user.state =
                "state" in user && user["state"] !== null
                    ? user["state"].value
                    : "";
            user.zip =
                "zip" in user && user["zip"] !== null ? user["zip"].value : "";
            user.jobTitle =
                "job_title" in user && user["job_title"] !== null
                    ? user["job_title"].value
                    : "";
            user.home_club =
                "home_club" in user && user["home_club"] !== null
                    ? user["home_club"].value
                    : "";
            user.phone = phone;
            user.start_date =
                "start_date" in user && user["start_date"] !== null
                    ? user["start_date"].value ?? ""
                    : "";
            user.end_date =
                "end_date" in user && user["end_date"] !== null
                    ? user["end_date"].value ?? ""
                    : "";
            user.termination_date =
                "termination_date" in user && user["termination_date"] !== null
                    ? user["termination_date"].value ?? ""
                    : "";
            user.notes =
                "notes" in user && user["notes"] !== null
                    ? user["notes"].value ?? ""
                    : "";
            console.log({ user });
        } else {
            user = {
                first_name: "",
                last_name: "",
                email: "",
                altEmail: "",
                role: "",
                classification: "",
                phone: "",
                address1: "",
                address2: "",
                city: "",
                team_id,
                notes: "",
                start_date: "",
                end_date: "",
                termination_date: "",
                state: "",
                zip: "",
                jobTitle: "",
                client_id: props.clientId,
            };
            //only add clientId when applicable to make user validation rules work better
            if (props.clientId) {
                user.client_id = props.clientId;
                user.home_club = null;
                user.notes = null;
                user.start_date = null;
                user.end_date = null;
                user.termination_date = null;
            }
            operation = "Create";
        }
        const form = useForm(user);
        let upperCaseF = (text) => {
            form.state = text.toUpperCase();
        };

        let handleSubmit = () => form.put(route("users.update", user.id), {onSuccess: () => form.notes=''});
        if (operation === "Create") {
            handleSubmit = () => form.post(route("users.store"), {onSuccess: () => form.notes=''});
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
        for(let x in states) {
            optionsStates.push(states[x].abbreviation);
        }

        return {
            form,
            buttonText: operation,
            operation,
            handleSubmit,
            securityRoles,
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
            // closeFileManagerModal: ()=> fileManagerModal.value.close(),
            // resetFileManager: () => console.log(fileManager.value)
            // resetFileManager: () => fileManager.value?.reset()
        };
    },
};
</script>
