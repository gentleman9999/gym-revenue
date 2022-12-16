<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title> User Profile</template>

        <template #description> Information about the user.</template>
        <template #form>
            <!-- First Name -->
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
                <jet-label for="name" value="Last Name" />
                <input
                    id="last_name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.last_name"
                />
                <jet-input-error
                    :message="form.errors.last_name"
                    class="mt-2"
                />
            </div>
            <!-- (Work) Email -->
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
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

            <!-- Contact Preference -->
            <div class="form-control col-span-6 md:col-span-2">
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

            <!-- Security Role -->
            <div
                class="form-control col-span-6 md:col-span-2"
                v-if="isClientUser"
            >
                <jet-label for="role_id" value="Security Role" />
                <select
                    id="role_id"
                    class="block w-full mt-1"
                    v-model="form.role_id"
                >
                    <option
                        v-for="role_id in roles"
                        :value="role_id.id"
                        :key="role_id.id"
                    >
                        {{ role_id.title }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.role_id" class="mt-2" />
            </div>
            <!-- Home Club -->
            <div
                class="form-control col-span-6 md:col-span-2"
                v-if="isClientUser"
            >
                <jet-label for="home_location_id" value="Home Club" />
                <select
                    id="home_location_id"
                    class="block w-full mt-1"
                    v-model="form.home_location_id"
                >
                    <option
                        v-for="{ gymrevenue_id, name } in locations"
                        :value="gymrevenue_id"
                        :key="gymrevenue_id"
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

            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-control col-span-6 md:col-span-2">
                <jet-label for="end_date" value="Date / End of Work" />
                <DatePicker
                    v-model="form['end_date']"
                    :enableTimePicker="false"
                    dark
                />
                <jet-input-error :message="form.errors.end_date" class="mt-2" />
            </div>

            <!-- Termination Date -->
            <div class="form-control col-span-6 md:col-span-2">
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
            <div class="form-divider" v-if="isClientUser" />
            <div
                class="form-control col-span-6 md:col-span-2"
                v-if="isClientUser"
            >
                <!-- <jet-label for="role_id" value="Select Departments" />
                <select
                    v-model="selectedDepartment"
                    class="mt-1 w-full form-select"
                    id="departments"
                >
                    <option
                        v-for="department in selectableDepartments"
                        :value="department.id"
                        :key="department.id"
                    >
                        {{ department.name }}
                    </option>
                </select> -->
                <DepartmentSelect v-model="selectedDepartment" />
                <jet-input-error
                    :message="form.errors.departments"
                    class="mt-2"
                />
            </div>
            <div
                class="form-control col-span-6 md:col-span-2"
                v-if="isClientUser"
            >
                <jet-label for="role_id" value="Select Positions" />
                <select
                    v-model="selectedPosition"
                    class="mt-1 w-full form-select"
                    id="positions"
                >
                    <option
                        v-for="{ label, value } in selectablePositionOptions"
                        :value="value"
                        :key="value"
                    >
                        {{ label }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.positions"
                    class="mt-2"
                />
            </div>
            <div
                class="flex justify-center items-end col-span-6 md:col-span-2"
                v-if="isClientUser"
            >
                <button
                    class="btn btn-success"
                    primary
                    @click.prevent="addDepartmentPosition"
                >
                    Add Department
                </button>
            </div>
            <div
                class="grid grid-cols-6 col-span-6 items-center"
                v-for="({ department, position }, ndx) in form.departments"
                :key="position"
            >
                <div class="col-span-6 md:col-span-2">
                    {{ getDepartment(department)?.name }}
                </div>
                <div class="col-span-6 md:col-span-2">
                    {{ getPosition(position)?.name }}
                </div>
                <div
                    class="flex justify-center items-end col-span-6 md:col-span-2"
                >
                    <button
                        class="btn btn-outline btn-error btn-sm"
                        type="button"
                        primary
                        size="sm"
                        @click="removeDepartment(ndx)"
                    >
                        Remove Department
                    </button>
                </div>
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
                    :key="note.id"
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
            <div class="form-divider" />
            <!-- Emergency Contact -->
            <jet-label for="emergency_contact" value="Emergency Contact" />
            <div class="grid grid-cols-6 col-span-6 items-center">
                <!-- First Name -->
                <div class="form-control grid-cols-2 col-span-2 m-1">
                    <jet-label for="ec_first_name" value="First Name" />
                    <input
                        id="ec_first_name"
                        type="text"
                        class="block w-full mt-1"
                        v-model="form.ec_first_name"
                        autofocus
                    />
                    <jet-input-error
                        :message="form.errors.ec_first_name"
                        class="mt-2"
                    />
                </div>
                <!-- Last Name -->
                <div class="form-control grid-cols-2 col-span-2 m-1">
                    <jet-label for="ec_last_name" value="Last Name" />
                    <input
                        id="ec_last_name"
                        type="text"
                        class="block w-full mt-1"
                        v-model="form.ec_last_name"
                    />
                    <jet-input-error
                        :message="form.errors.ec_last_name"
                        class="mt-2"
                    />
                </div>

                <!-- Contact Phone # -->
                <div class="form-control grid-cols-2 col-span-2 m-1">
                    <jet-label for="ec_phone" value="Contact Phone" />
                    <phone-input
                        id="ec_phone"
                        class="block w-full mt-1"
                        v-model="form.ec_phone"
                    />
                    <jet-input-error
                        :message="form.errors.ec_phone"
                        class="mt-2"
                    />
                </div>
            </div>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="handleClickCancel"
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
    <jet-form-section v-if="isClientUser && user?.files" class="mt-16">
        <template #title> Documents</template>

        <template #description> Documents attached to the user.</template>
        <template #form>
            <!-- Files -->
            <div class="col-span-6">
                <div class="flex flex-col gap-2">
                    <template v-if="user?.files?.length">
                        <div
                            v-for="file in user.files"
                            :key="file.id"
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
                            ></button>
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
import { ref, computed, watch, inject, watchEffect } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import DatePicker from "@vuepic/vue-datepicker";
import DepartmentSelect from "@/Pages/Departments/Partials/DepartmentSelect.vue";

import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import states from "@/Pages/Comms/States/statesOfUnited";
import FileManager from "@/Pages/Files/Partials/FileManager.vue";
import { transformDate } from "@/utils/transformDate";
import PhoneInput from "@/Components/PhoneInput.vue";
import { useModal } from "@/Components/InertiaModal";
import * as _ from "lodash";

import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

export default {
    components: {
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
        DepartmentSelect,
    },
    props: [
        "user",
        "clientName",
        "roles",
        "locations",
        "availablePositions",
        "availableDepartments",
    ],
    emits: ["success"],
    setup(props, { emit }) {
        function notesExpanded(note) {
            console.error(props.user.all_notes);
            axios.post(route("note.seen"), {
                note: note,
            });
        }

        const wantsToDeleteFile = ref(null);
        const page = usePage();
        let user = _.cloneDeep(props.user);

        const team_id = page.props.value.user.current_team_id;
        const isClientUser = page.props.value.user.is_client_user;

        let operation = "Update";
        if (user) {
            if (isClientUser) {
                user.role_id = user["role_id"];
            }
            user.contact_preference = user["contact_preference"]?.value;
            user.team_id = team_id;
            user.notes = { title: "", note: "" };
            user.ec_first_name = user["emergency_contact"]["ec_first_name"];
            user.ec_last_name;
            user["emergency_contact"]["ec_last_name"];
            user.ec_phone = user["emergency_contact"]["ec_phone"];
        } else {
            user = {
                first_name: "",
                last_name: "",
                email: "",
                alternate_email: "",
                role_id: 0,
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
                positions: [],
                departments: [],
                ec_first_name: "",
                ec_last_name: "",
                ec_phone: "",
            };
            //only add client specific when applicable to make user validation rules work better
            if (isClientUser) {
                user.home_location_id = null;
                user.notes = { title: "", note: "" };
                user.start_date = null;
                user.end_date = null;
                user.termination_date = null;
                user.role_id = null;
                // user.departments = {
                //
                // }
            }
            operation = "Create";
        }
        const form = useGymRevForm(user);

        let selectedDepartment = ref(null);
        let selectedPosition = ref(null);
        const _addDepartmentPosition = (department, position) => {
            form.departments = [
                ...form.departments,
                {
                    department,
                    position,
                },
            ];
        };

        const selectableDepartments = computed(() =>
            props.availableDepartments.data.filter(
                ({ id, positions }) =>
                    positions?.filter((p) => !selectedPosition.value !== p.id)
                        .length
            )
        );
        const renderableDepartments = ref([]);

        watch(
            [selectableDepartments],
            () => {
                if (
                    selectableDepartments.value?.length &&
                    !renderableDepartments.value?.length
                ) {
                    const department_positions = [];
                    selectableDepartments.value.forEach(({ id, positions }) => {
                        const department = props.user?.departments?.find(
                            (d) => (d.id = id)
                        );
                        const owned_positions =
                            props.user?.positions?.filter((user_pos) =>
                                positions?.find((dept_pos) => {
                                    return user_pos.id === dept_pos.id;
                                })
                            ) || [];
                        owned_positions.forEach((owned_position) => {
                            department_positions.push({
                                department: department.id,
                                position: owned_position.id,
                            });
                        });
                    });
                    renderableDepartments.value = department_positions;
                }
            },
            { immediate: true }
        );
        watch(
            renderableDepartments,
            () => {
                if (renderableDepartments.value?.length) {
                    form.departments = [...renderableDepartments.value];
                }
            },
            { immediate: true }
        );

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

        const modal = useModal();
        const { mutate: createUser } = useMutation(mutations.user.create);
        const { mutate: updateUser } = useMutation(mutations.user.update);

        let handleSubmit = async () => {
            console.log(user);
            await updateUser({
                input: {
                    id: user.id,
                    first_name: form.first_name,
                    last_name: form.last_name,
                    email: form.email,
                    alternate_email: form.alternate_email,
                    address1: form.address1,
                    address2: form.address2,
                    phone: form.phone,
                    city: form.city,
                    state: form.state,
                    zip: form.zip,
                    contact_preference: form.contact_preference,
                    start_date: form.start_date,
                    end_date: form.end_date,
                    termination_date: form.termination_date,
                    notes: form.notes,
                    client_id: null,
                    team_id: form.team_id,
                    role_id: form.role_id,
                    home_location_id: null,
                    manager: null,
                },
            });
            handleClickCancel();
            // console.log(
            //     "form",
            //     form
            //     .dirty()
            //     .transform(transformFormSubmission)
            // )

            // form
            //     .dirty()
            //     .transform(transformFormSubmission)
            //     .put(route("users.update", user.id), {
            //         onSuccess: () => (form.notes = { title: "", note: "" }),
            //     });
        };
        if (operation === "Create") {
            handleSubmit = async () => {
                // form.transform(transformFormSubmission)
                await createUser({
                    input: {
                        first_name: form.first_name,
                        last_name: form.last_name,
                        email: form.email,
                        alternate_email: form.alternate_email,
                        address1: form.address1,
                        address2: form.address2,
                        phone: form.phone,
                        city: form.city,
                        state: form.state,
                        zip: form.zip,
                        contact_preference: form.contact_preference,
                        start_date: form.start_date,
                        end_date: form.end_date,
                        termination_date: form.termination_date,
                        notes: form.notes,
                        client_id: null,
                        team_id: form.team_id,
                        role_id: form.role_id,
                        home_location_id: null,
                        manager: null,
                        departments: [],
                        positions: [],
                    },
                });
                handleClickCancel();
            };
        }

        const handleConfirmDeleteFile = () => {
            Inertia.delete(route("files.trash", wantsToDeleteFile.value), {
                preserveScroll: true,
            });
            wantsToDeleteFile.value = null;
        };

        const fileManagerModal = ref();
        const fileManager = ref();

        const closeFileManagerModal = () => {
            const reset =
                fileManager.value?.reset || fileManager?.reset || false;
            return fileManager.value?.reset;
        };

        let optionsStates = [];
        for (let x in states) {
            optionsStates.push(states[x].abbreviation);
        }

        const handleClickCancel = () => {
            emit("close");
        };

        const addDepartmentPosition = () => {
            _addDepartmentPosition(
                selectedDepartment.value,
                selectedPosition.value
            );
            selectedDepartment.value = null;
            selectedPosition.value = null;
        };
        watch(selectedDepartment, () => {
            selectedPosition.value = null;
        });
        const removeDepartment = (ndx) => {
            form.departments.splice(ndx, 1);
        };
        const getDepartment = (id) => {
            return props.availableDepartments.data.find(
                (item) => item.id === id
            );
        };
        const getPosition = (id) => {
            return props.availablePositions.data.find((item) => item.id === id);
        };

        const getPositions = (id_arr) => {
            const positions = [];
            id_arr.map((id) => {
                const position = getPosition(id);
                if (position) {
                    positions.push(position);
                }
            });
            return positions;
        };

        const all_positions_for_selected_department = computed(() => {
            const selected_department = getDepartment(selectedDepartment.value);
            return selected_department?.positions?.length
                ? selected_department?.positions
                : [];
        });
        // const selectablePositions = computed(()=>{
        //     const filtered =  all_positions_for_selected_department.value.filter( department_position =>
        //         //selected Positions does not yet have this department_position
        //         !selectedPositions.value?.includes(department_position.id)
        //     );
        //     return filtered?.length ? filtered : [];
        // })

        const selectablePositions = computed(() => {
            const filtered = all_positions_for_selected_department.value.filter(
                (department_position) =>
                    //form.departments ({departmentid, positionid{) does not yet have this dept/pos combo
                    !form.departments?.find(
                        ({ department, position }) =>
                            department === selectedDepartment.value &&
                            position === department_position.id
                    )
            );
            return filtered?.length ? filtered : [];
        });

        const selectablePositionOptions = computed(() => {
            if (selectablePositions.value?.length) {
                return selectablePositions.value?.map((position) => ({
                    label: position.name,
                    value: position.id,
                }));
            } else {
                return [];
            }
        });
        //
        // const selectablePositions = computed(() => {
        //     const selected_department = getDepartment(selectedDepartment.value);
        //     const all_departments_positions = selected_department?.positions || [];
        //     const filtered =  all_departments_positions.filter( department_position =>
        //         //selected Positions does not yet have this department_position
        //          !selectedPositions.value?.includes(department_position.id)
        //             //form.departments ({departmentid, positionid{) does not yet have this dept/pos combo
        //             && form.departments?.filter(({department, position})=> department === selected_department.id &&  position === department_position.id));
        //     return  filtered?.length ? filtered : [];
        // });

        return {
            isClientUser,
            form,
            buttonText: operation,
            operation,
            handleSubmit,
            upperCaseF,
            optionStates: optionsStates,
            multiselectClasses: getDefaultMultiselectTWClasses(),
            wantsToDeleteFile,
            handleConfirmDeleteFile,
            fileManagerModal,
            fileManager,
            closeFileManagerModal,
            notesExpanded,
            handleClickCancel,
            selectedDepartment,
            selectedPosition,
            addDepartmentPosition,
            getDepartment,
            getPosition,
            getPositions,
            removeDepartment,
            selectableDepartments,
            selectablePositions,
            renderableDepartments,
            DepartmentSelect,
            // positions_for_selected_department_without_already_assigned_positions,
            // positions_for_selected_department_excluding_currently_selected,
            all_positions_for_selected_department,
            selectablePositionOptions,
            // closeFileManagerModal: ()=> fileManagerModal.value.close(),
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
